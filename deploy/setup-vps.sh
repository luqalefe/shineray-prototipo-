#!/bin/bash
# setup-vps.sh — prepara a VPS pra rodar o Shineray (primeira vez)
# Uso: sudo bash deploy/setup-vps.sh
#
# Requisitos prévios:
#   - Ubuntu 22.04+ ou Debian 12
#   - Repo clonado em /var/www/shineray (git@github.com:.../shineray.git)
#   - PHP 8.3, Nginx, MySQL e Composer já instalados
#   - Vhost Nginx criado em /etc/nginx/sites-available/shineray
#   - DNS de shineray.estevo.tech apontando pra esta VPS
#   - .env já configurado em /var/www/shineray/src/.env
set -euo pipefail

# ── helpers ───────────────────────────────────────────────────────────────
RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'
BLUE='\033[0;34m'; CYAN='\033[0;36m'; BOLD='\033[1m'; RESET='\033[0m'

info()    { echo -e "${CYAN}[INFO]${RESET}  $*"; }
ok()      { echo -e "${GREEN}[ OK ]${RESET}  $*"; }
warn()    { echo -e "${YELLOW}[WARN]${RESET}  $*"; }
fail()    { echo -e "${RED}[ERRO]${RESET}  $*"; exit 1; }
section() { echo -e "\n${BOLD}${BLUE}━━━ $* ━━━${RESET}"; }

DOMAIN="shineray.estevo.tech"
REPO_DIR="/var/www/shineray"
APP_DIR="$REPO_DIR/src"
NGINX_CONF="/etc/nginx/sites-available/shineray"
PHP_FPM_POOL="/etc/php/8.3/fpm/pool.d/www.conf"
PHP_OPCACHE_CONF="/etc/php/8.3/fpm/conf.d/10-opcache.ini"
QUEUE_SERVICE_FILE="/etc/systemd/system/shineray-queue.service"
MAINT_SECRET="shineray-$(openssl rand -hex 6)"

# ── pré-checks ────────────────────────────────────────────────────────────
echo -e "\n${BOLD}${GREEN}"
echo "  ╔════════════════════════════════════════════╗"
echo "  ║      Setup VPS — Shineray Rio Branco       ║"
echo "  ╚════════════════════════════════════════════╝"
echo -e "${RESET}"

[[ $EUID -ne 0 ]]       && fail "Execute como root: sudo bash deploy/setup-vps.sh"
[[ ! -d "$REPO_DIR" ]]  && fail "Repo não encontrado: $REPO_DIR (clone primeiro)"
[[ ! -d "$APP_DIR" ]]   && fail "App Laravel não encontrado: $APP_DIR"
[[ ! -f "$NGINX_CONF" ]] && fail "Vhost Nginx não encontrado: $NGINX_CONF (crie e rode certbot antes)"
[[ ! -f "$APP_DIR/.env" ]] && fail "Arquivo .env não encontrado em $APP_DIR"

info "Domínio:           ${BOLD}https://${DOMAIN}${RESET}"
info "Secret de manutenção: ${BOLD}${MAINT_SECRET}${RESET}"
info "Durante o deploy acesse com:"
info "  https://${DOMAIN}/${MAINT_SECRET}\n"

# ─────────────────────────────────────────────────────────────────────────
section "1/8  Swap (evita OOM durante npm/vite)"
# ─────────────────────────────────────────────────────────────────────────

if swapon --show | grep -q '/swapfile'; then
    ok "Swap já existe, pulando."
else
    info "Criando swap de 2 GB..."
    fallocate -l 2G /swapfile
    chmod 600 /swapfile
    mkswap  /swapfile
    swapon  /swapfile
    grep -q '/swapfile' /etc/fstab \
        || echo '/swapfile none swap sw 0 0' >> /etc/fstab
    grep -q 'vm.swappiness' /etc/sysctl.conf \
        || echo 'vm.swappiness=10' >> /etc/sysctl.conf
    sysctl -p -q
    ok "Swap de 2 GB criado e persistido."
fi

# ─────────────────────────────────────────────────────────────────────────
section "2/8  Redis (cache + sessions + queue)"
# ─────────────────────────────────────────────────────────────────────────

if systemctl is-active --quiet redis-server 2>/dev/null; then
    ok "Redis já está rodando."
else
    info "Instalando Redis..."
    apt-get install -y -q redis-server
    systemctl enable --now redis-server
    ok "Redis instalado e iniciado."
fi

# ─────────────────────────────────────────────────────────────────────────
section "3/8  OPcache"
# ─────────────────────────────────────────────────────────────────────────

cat > "$PHP_OPCACHE_CONF" << 'EOF'
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.revalidate_freq=0
opcache.validate_timestamps=0
opcache.save_comments=1
opcache.fast_shutdown=1
EOF
ok "OPcache configurado em $PHP_OPCACHE_CONF"

# ─────────────────────────────────────────────────────────────────────────
section "4/8  PHP-FPM Pool"
# ─────────────────────────────────────────────────────────────────────────

cp "$PHP_FPM_POOL" "${PHP_FPM_POOL}.bak.$(date +%Y%m%d%H%M%S)"

_fpm_set() {
    local key="$1" val="$2"
    if grep -qE "^;?\s*${key}\s*=" "$PHP_FPM_POOL"; then
        sed -i "s|^;*\s*${key}\s*=.*|${key} = ${val}|" "$PHP_FPM_POOL"
    else
        echo "${key} = ${val}" >> "$PHP_FPM_POOL"
    fi
}

_fpm_set "pm"                   "dynamic"
_fpm_set "pm.max_children"      "10"
_fpm_set "pm.start_servers"     "3"
_fpm_set "pm.min_spare_servers" "2"
_fpm_set "pm.max_spare_servers" "5"
_fpm_set "pm.max_requests"      "500"

systemctl reload php8.3-fpm
ok "PHP-FPM pool recarregado (graceful)."

# ─────────────────────────────────────────────────────────────────────────
section "5/8  Nginx (gzip + cache de assets)"
# ─────────────────────────────────────────────────────────────────────────

cp "$NGINX_CONF" "${NGINX_CONF}.bak.$(date +%Y%m%d%H%M%S)"

mkdir -p /etc/nginx/snippets
cat > /etc/nginx/snippets/shineray-performance.conf << 'EOF'
# Gzip
gzip on;
gzip_comp_level 5;
gzip_min_length 256;
gzip_proxied any;
gzip_vary on;
gzip_types text/plain text/css application/json application/javascript
           text/xml application/xml image/svg+xml font/woff2;

# Cache longo para assets do Vite (nome com hash — nunca mudam)
location ~* \.(css|js|woff2?|ttf|eot)$ {
    expires 1y;
    add_header Cache-Control "public, immutable";
    access_log off;
}

# Cache para imagens
location ~* \.(jpg|jpeg|png|gif|ico|webp|svg)$ {
    expires 30d;
    add_header Cache-Control "public";
    access_log off;
}

# FastCGI buffers
fastcgi_buffers 16 16k;
fastcgi_buffer_size 32k;
fastcgi_read_timeout 60;
EOF

if ! grep -q 'shineray-performance' "$NGINX_CONF"; then
    python3 - "$NGINX_CONF" << 'PYEOF'
import sys, re
path = sys.argv[1]
content = open(path).read()
content = re.sub(r'\n\s*gzip\s+on;\n', '\n', content)
content = re.sub(r'\n\s*gzip_types[^\n]+;\n', '\n', content)
last_brace = content.rfind('}')
if last_brace != -1:
    content = (content[:last_brace]
               + '    include snippets/shineray-performance.conf;\n'
               + content[last_brace:])
open(path, 'w').write(content)
print("Snippet injetado.")
PYEOF
else
    ok "Snippet já estava incluído."
fi

nginx -t && systemctl reload nginx
ok "Nginx recarregado (graceful)."

# ─────────────────────────────────────────────────────────────────────────
section "6/8  Permissões"
# ─────────────────────────────────────────────────────────────────────────

chown -R www-data:www-data "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"
find "$APP_DIR/storage" -type d -exec chmod 775 {} \;
find "$APP_DIR/bootstrap/cache" -type d -exec chmod 775 {} \;
ok "Permissões de storage e bootstrap/cache ajustadas."

# ─────────────────────────────────────────────────────────────────────────
section "7/8  Deploy da Aplicação"
# ─────────────────────────────────────────────────────────────────────────

cd "$REPO_DIR"

info "Ativando modo manutenção..."
( cd "$APP_DIR" && php artisan down --secret="$MAINT_SECRET" 2>/dev/null || true )

info "Atualizando código (git pull)..."
git pull origin main

cd "$APP_DIR"

info "Instalando dependências PHP..."
composer install --no-dev --optimize-autoloader -q

info "Compilando assets frontend (npm install + vite build)..."
npm install --silent
npm run build

info "Rodando migrations..."
php artisan migrate --force

info "Atualizando .env — drivers para Redis..."
_env_set() {
    local key="$1" val="$2"
    if grep -q "^${key}=" .env; then
        sed -i "s|^${key}=.*|${key}=${val}|" .env
    else
        echo "${key}=${val}" >> .env
    fi
}
_env_set "CACHE_STORE"      "redis"
_env_set "SESSION_DRIVER"   "redis"
_env_set "QUEUE_CONNECTION" "redis"
_env_set "REDIS_CLIENT"     "phpredis"

info "Garantindo storage link..."
php artisan storage:link 2>/dev/null || true

info "Reconstruindo cache Laravel..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

info "Voltando ao ar..."
php artisan up
ok "Aplicação online."

# ─────────────────────────────────────────────────────────────────────────
section "8/8  Verificação Final"
# ─────────────────────────────────────────────────────────────────────────

FAIL=0
for svc in nginx php8.3-fpm mysql redis-server; do
    if systemctl is-active --quiet "$svc" 2>/dev/null; then
        ok "$svc está rodando"
    else
        warn "$svc NÃO está rodando — verifique com: systemctl status $svc"
        FAIL=1
    fi
done

if systemctl list-unit-files | grep -q '^shineray-queue.service'; then
    if systemctl is-active --quiet shineray-queue 2>/dev/null; then
        ok "shineray-queue está rodando"
    else
        warn "shineray-queue não está ativo (rode: systemctl start shineray-queue)"
    fi
else
    info "shineray-queue.service não instalado (opcional — ver deploy/shineray-queue.service)"
fi

echo
if [[ $FAIL -eq 0 ]]; then
    echo -e "${BOLD}${GREEN}"
    echo "  ╔════════════════════════════════════════════╗"
    echo "  ║         Tudo pronto! Site no ar.           ║"
    echo "  ╚════════════════════════════════════════════╝"
    echo -e "${RESET}"
else
    echo -e "${YELLOW}Setup concluído com avisos. Verifique os serviços acima.${RESET}"
fi

echo -e "  ${BOLD}Site:${RESET}     https://${DOMAIN}"
echo -e "  ${BOLD}Admin:${RESET}    https://${DOMAIN}/admin"
echo
echo -e "  ${YELLOW}Sessões anteriores foram invalidadas — faça login novamente.${RESET}"
echo
