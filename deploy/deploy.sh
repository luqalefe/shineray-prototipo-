#!/bin/bash
# deploy.sh — deploy rotineiro do Shineray
# Uso: bash deploy/deploy.sh        (no diretório /var/www/shineray)
#  ou: sudo -u www-data bash deploy/deploy.sh

set -euo pipefail

REPO_DIR="/var/www/shineray"      # raiz do git (onde está o docker-compose.yml e a pasta src/)
APP_DIR="$REPO_DIR/src"            # raiz do Laravel
PHP_FPM_SERVICE="php8.3-fpm"
QUEUE_SERVICE="shineray-queue"     # opcional — só reinicia se existir

echo "🚀 Iniciando deploy do Shineray..."

cd "$REPO_DIR"

echo "📥 Puxando código..."
git pull origin main

cd "$APP_DIR"

echo "📦 Instalando dependências PHP..."
composer install --no-dev --optimize-autoloader

echo "🎨 Compilando assets..."
npm install
npm run build

echo "🗄️  Rodando migrations..."
php artisan migrate --force

echo "🔗 Garantindo storage link..."
php artisan storage:link 2>/dev/null || true

echo "⚡ Otimizando cache..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "🔄 Reiniciando PHP-FPM..."
sudo systemctl restart "$PHP_FPM_SERVICE"

if systemctl list-unit-files | grep -q "^${QUEUE_SERVICE}.service"; then
    echo "🐎 Reiniciando queue worker..."
    sudo systemctl restart "$QUEUE_SERVICE"
fi

echo "✅ Deploy concluído com sucesso!"
