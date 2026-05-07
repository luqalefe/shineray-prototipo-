# Deploy — Shineray Rio Branco

Scripts pra subir o projeto numa VPS Ubuntu/Debian em `https://shineray.estevo.tech`.

## Estrutura

| Arquivo | Pra quê |
|---|---|
| `setup-vps.sh` | **Primeira vez.** Configura swap, Redis, OPcache, PHP-FPM, Nginx e roda o primeiro deploy. |
| `deploy.sh` | **Rotina.** `git pull` + `composer` + `vite build` + cache + restart FPM. |
| `nginx.conf.example` | Vhost de exemplo pra colar em `/etc/nginx/sites-available/shineray`. |
| `shineray-queue.service` | Systemd unit do queue worker (opcional). |

## Pré-requisitos da VPS

- Ubuntu 22.04+ ou Debian 12
- PHP 8.3 + extensões (`mbstring`, `xml`, `mysql`, `redis`, `gd`, `bcmath`, `zip`, `curl`)
- Nginx, MySQL 8, Composer 2, Node 20+, Git
- DNS de `shineray.estevo.tech` apontando pro IP da VPS

## Roteiro do primeiro deploy

```bash
# 1. Clonar o repo
sudo mkdir -p /var/www
sudo git clone git@github.com:SEU_USUARIO/shineray.git /var/www/shineray
sudo chown -R www-data:www-data /var/www/shineray

# 2. Criar .env (copie do .env.example e ajuste DB/APP_URL/MAIL)
sudo -u www-data cp /var/www/shineray/src/.env.example /var/www/shineray/src/.env
sudo -u www-data nano /var/www/shineray/src/.env
# APP_URL=https://shineray.estevo.tech
# APP_ENV=production
# APP_DEBUG=false
# DB_*  …

# 3. Gerar APP_KEY
cd /var/www/shineray/src && sudo -u www-data php artisan key:generate

# 4. Configurar Nginx
sudo cp /var/www/shineray/deploy/nginx.conf.example /etc/nginx/sites-available/shineray
sudo ln -s /etc/nginx/sites-available/shineray /etc/nginx/sites-enabled/shineray
sudo nginx -t && sudo systemctl reload nginx

# 5. HTTPS com Let's Encrypt
sudo certbot --nginx -d shineray.estevo.tech

# 6. Setup completo (idempotente — pode rodar de novo se algo falhar)
sudo bash /var/www/shineray/deploy/setup-vps.sh

# 7. (Opcional) Queue worker
sudo cp /var/www/shineray/deploy/shineray-queue.service /etc/systemd/system/
sudo systemctl daemon-reload
sudo systemctl enable --now shineray-queue
```

## Deploys seguintes

```bash
cd /var/www/shineray && bash deploy/deploy.sh
```

Ou, mais limpo, via SSH a partir da sua máquina:

```bash
ssh root@SUA_VPS "cd /var/www/shineray && bash deploy/deploy.sh"
```

## Notas

- O Laravel mora em `src/` dentro do repo. `git pull` roda na raiz, comandos `artisan`/`composer`/`npm` rodam em `src/`.
- O `setup-vps.sh` força `CACHE_STORE`, `SESSION_DRIVER` e `QUEUE_CONNECTION` pra `redis` no `.env` — sessões antigas viram pó, todo mundo do `/admin` precisa logar de novo.
- O `setup-vps.sh` é idempotente: detecta swap/Redis/snippet já configurados e pula.
- Se o build do Vite estourar memória na VPS pequena, o swap de 2 GB criado no passo 1/8 cobre.
