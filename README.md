# Shineray Rio Branco

Catálogo de motos + simulador de financiamento + CRM leve para a **Arroxa Motores** (concessionária Shineray autorizada em Rio Branco/AC). Construído como protótipo full-stack em Laravel + Livewire + Filament, totalmente dockerizado.

O fluxo de negócio é simples e típico de concessionária regional: o cliente navega pelo catálogo, simula financiamento na página da moto e abre uma conversa no WhatsApp já com sua simulação anexada. Cada interação vira um lead distribuído automaticamente entre vendedores ativos por round-robin, com notificação por e-mail.

---

## Stack

| Camada | Tecnologia | Versão |
|---|---|---|
| Backend | Laravel | 13.x |
| Runtime | PHP-FPM | 8.4 (Alpine) |
| Front-reativo | Livewire | 3.8 |
| Painel admin | Filament | 4.x |
| Banco | MySQL | 8.0 |
| Webserver | Nginx | Alpine |
| CSS | Tailwind | via Play CDN |
| E-mail dev | Mailpit | latest |
| Build assets | Node | 20 (Vite — disponível mas opcional pro protótipo) |

**Decisão chave**: sem framework JavaScript (sem React/Vue). Toda interatividade é Livewire SSR + Alpine inline para microinterações (máscara de telefone). Tailwind via CDN no protótipo evita dependência de build step — quando for pra produção, basta executar `npm run build`.

---

## Como rodar localmente

### Requisitos
- Docker 20+ e Docker Compose v2

### Subir do zero

```bash
git clone <repo> shineray
cd shineray

# .env do Laravel (vendor/, node_modules/ e src/.env são gitignored)
cp src/.env.example src/.env

# Builda imagem PHP custom (php:8.4-fpm-alpine + extensões pdo_mysql, gd, intl, zip, bcmath, opcache)
docker compose build app

# Sobe os 5 containers: app (php-fpm), nginx, db (mysql), node (vite), mailpit
docker compose up -d

# Instala dependências PHP dentro do container (vendor/ não é versionado)
docker compose exec app composer install --no-interaction --prefer-dist --optimize-autoloader

# Gera APP_KEY no .env
docker compose exec app php artisan key:generate --force

# Aguarda MySQL aceitar conexões e roda migrations
docker compose exec app php artisan migrate --force

# Popula motos, vendedores e settings do simulador
docker compose exec app php artisan db:seed --class=MotoSeeder --force
docker compose exec app php artisan db:seed --class=SalespersonSeeder --force
docker compose exec app php artisan db:seed --class=SimulatorSettingSeeder --force

# Cria usuário admin (uma vez)
docker compose exec app php artisan tinker --execute="\App\Models\User::updateOrCreate(['email' => 'admin@shinerayriobranco.com.br'], ['name' => 'Admin Shineray', 'password' => bcrypt('shineray123')]);"

# Gera o symlink storage/app/public → public/storage para imagens das motos
docker compose exec app php artisan storage:link

# Copia as imagens originais das motos pra dentro do storage público
# (o seeder grava só o caminho `motos/<slug>.webp`; o arquivo precisa existir no disco)
mkdir -p src/storage/app/public/motos && cp assets/motos/* src/storage/app/public/motos/
```

### Endereços

| URL | Serviço |
|---|---|
| http://localhost:8080 | Site público (catálogo) |
| http://localhost:8080/moto/{slug} | Página de detalhe da moto |
| http://localhost:8080/admin | Painel admin Filament |
| http://localhost:8025 | Mailpit (caixa de e-mails de dev) |
| http://localhost:5173 | Vite dev server (não usado no protótipo) |
| http://localhost:3306 | MySQL (user `shineray` / pass `shineray`) |

### Login admin
```
E-mail: admin@shinerayriobranco.com.br
Senha:  shineray123
```

---

## Estrutura

```
shineray/
├── docker/
│   ├── php/Dockerfile          # PHP 8.4-fpm-alpine + extensões
│   └── nginx/default.conf      # config nginx
├── docker-compose.yml          # 5 serviços
├── .env.example                # vars do compose
├── assets/motos/               # imagens originais baixadas do shineray.com.br
└── src/                        # projeto Laravel
    ├── app/
    │   ├── Filament/
    │   │   ├── Pages/Dashboard.php           # custom com filtro de período
    │   │   ├── Resources/
    │   │   │   ├── Motos/                    # CRUD catálogo
    │   │   │   ├── Leads/                    # CRUD leads (com seção simulação)
    │   │   │   ├── Salespeople/              # CRUD vendedores + actions de reset
    │   │   │   └── SimulatorSettings/        # singleton edit-only
    │   │   └── Widgets/                      # dashboard
    │   │       ├── SalesStatsOverview.php
    │   │       ├── LeadsPerDayChart.php
    │   │       ├── ConversionFunnelChart.php
    │   │       ├── TopMotosWidget.php
    │   │       └── SalespeopleConversionTable.php
    │   ├── Helpers/whatsapp.php              # whatsapp_link, whatsapp_link_for_moto
    │   ├── Livewire/
    │   │   ├── Catalog.php                   # home com filtros
    │   │   ├── MotoDetail.php                # página da moto
    │   │   ├── LeadForm.php                  # form simples de contato
    │   │   └── FinancingSimulator.php        # simulador Tabela Price
    │   ├── Mail/NewLeadMail.php              # email pro vendedor
    │   ├── Models/
    │   │   ├── Moto.php
    │   │   ├── Lead.php
    │   │   ├── Salesperson.php
    │   │   └── SimulatorSetting.php
    │   ├── Observers/LeadObserver.php        # round-robin + envio email
    │   ├── Services/
    │   │   ├── FinancingCalculator.php       # Tabela Price (com testes)
    │   │   └── LeadAssigner.php              # round-robin com lockForUpdate
    │   └── Support/PeriodFilter.php          # filtro de período compartilhado
    ├── config/store.php                      # vars da loja (whatsapp, email, endereço)
    ├── database/
    │   ├── migrations/                       # 9 migrations
    │   └── seeders/                          # MotoSeeder, SalespersonSeeder, SimulatorSettingSeeder
    ├── public/img/logo-shineray.png          # logo oficial da marca
    ├── resources/views/
    │   ├── components/
    │   │   ├── icons/whatsapp.blade.php      # SVG oficial WhatsApp (reutilizável)
    │   │   └── layouts/app.blade.php         # layout master + máscara phoneMaskBR
    │   ├── emails/new-lead.blade.php         # template HTML do email
    │   └── livewire/
    │       ├── catalog.blade.php
    │       ├── moto-detail.blade.php
    │       ├── lead-form.blade.php
    │       ├── financing-simulator.blade.php
    │       └── partials/moto-card.blade.php
    └── tests/Unit/FinancingCalculatorTest.php
```

---

## Banco de dados

```
motos                   leads                      salespeople
─────                   ─────                      ───────────
id                      id                         id
name                    name                       name
slug (unique)           phone                      email (unique)
category                email                      phone
displacement_cc         message                    active
price                   moto_id ──FK              last_assigned_at
short_description       salesperson_id ──FK       leads_count
description             source                     timestamps
image                   status
gallery (json)          notes
highlights (json)       ip
featured                user_agent
active                  ── campos de simulação ──
sort_order              vehicle_price
timestamps              down_payment
                        financed_amount
                        installments
                        interest_rate
                        installment_value
                        total_amount
                        whatsapp_clicked
                        timestamps

simulator_settings (singleton, id=1)         users (admin Filament)
──────────────────                           ─────
id                                           id, name, email, password, ...
default_interest_rate (decimal:4)
min_installments / max_installments
installments_step
min_down_payment_percent / max_down_payment_percent
disclaimer_text
active
```

**Decisão**: simulação não é entidade separada — é um **lead com source=`simulador`** + colunas de cálculo preenchidas. Vantagem: funil unificado no Filament; sem JOIN extra; filtro "apenas simulações" é só `whereNotNull('installments')`.

---

## Funcionalidades

### 1. Catálogo público (`/`)

- **Hero escuro** com gradiente vermelho + CTA "Ver catálogo" / "Falar no WhatsApp"
- **Destaques** (motos com `featured = true`)
- **Catálogo completo** com filtros reativos por categoria (chip de pílulas) e busca por nome
- **Botão flutuante WhatsApp** persistente (FAB) com link `wa.me`
- **Seção "Não achou o que procurava?"** com `<livewire:lead-form source="home" />`

Componente: `App\Livewire\Catalog` (full-page) com layout `components.layouts.app`.

### 2. Página de detalhe da moto (`/moto/{slug}`)

- Hero com imagem grande, descrição, highlights (lista de checks vermelhos), card de preço
- 2 CTAs: **Simular financiamento** (âncora `#simulador`) + **WhatsApp** (link direto pré-preenchido)
- Seção dedicada do **simulador de financiamento**
- "Outros modelos da categoria"

Componente: `App\Livewire\MotoDetail` com route binding por slug (`Moto::getRouteKeyName() = 'slug'`).

### 3. Captura de leads simples

- Form Livewire (`App\Livewire\LeadForm`) com nome, WhatsApp (com máscara `phoneMaskBR`), e-mail opcional, mensagem, checkbox de consentimento
- Validação no servidor com mensagens em PT
- Após submit: salva em `leads`, mostra tela de sucesso, abre WhatsApp em nova aba com saudação personalizada
- **Aceita `:moto`** opcional pra associar ao lead

### 4. Simulador de financiamento (Tabela Price)

- Componente `App\Livewire\FinancingSimulator` com:
  - Slider **reativo** de parcelas (`wire:model.live="installments"`) — limites e step vêm de `simulator_settings`
  - Input de **entrada** com auto-clamp: valor abaixo do mínimo ou acima do máximo é ajustado automaticamente e mostra um aviso amarelo (`downPaymentNotice`); campo vazio é preenchido com o mínimo. Tudo logado via `Log::info` pra auditoria.
  - **Gate da parcela**: enquanto o cliente não preenche `nome + WhatsApp` e clica "Ver minha parcela", o valor da parcela aparece ofuscado (`R$ ••••,••`) com a copy "Preencha seus dados abaixo pra liberar a simulação". Só após `simulate()` (que valida + cria o Lead) o card de sucesso revela o valor real e libera o WhatsApp.
  - Submit → salva como `Lead` com `source='simulador'` + todos os campos da simulação (`vehicle_price`, `down_payment`, `financed_amount`, `installments`, `interest_rate`, `installment_value`, `total_amount`)
  - WhatsApp pré-formatado com mensagem rica (emojis + dados em markdown WhatsApp), gerado on-demand via `#[Computed] whatsappLink()`
  - Tracking de `whatsapp_clicked` via `wire:click="trackWhatsappClick"` no botão final
  - `resetSimulation()` permite refazer a simulação sem refresh da página

**Fórmula** (Tabela Price):  PMT = PV × (i × (1+i)^n) / ((1+i)^n − 1). Cobertura de testes em `tests/Unit/FinancingCalculatorTest.php` (4 cenários: com juros, juros zero, input inválido, tabela de amortização).

### 5. Notificação por e-mail pro vendedor

- `App\Mail\NewLeadMail` (Mailable) → template HTML em `resources/views/emails/new-lead.blade.php`
- Disparado por `App\Observers\LeadObserver::created()` (registrado via atributo `#[ObservedBy]` no Lead)
- **Subject inteligente**:
  - Lead simples → `[Novo lead] Maria — JET 50`
  - Simulação → `[Simulação] João — SHI 175 — 36x R$ 541,41`
- **Reply-To** = e-mail do cliente (se preenchido) → resposta do vendedor volta direto pro cliente
- 2 CTAs no email: **"Responder no WhatsApp"** (saudação personalizada com o nome do vendedor designado) + **"Abrir no painel"**

### 6. Round-robin de vendedores

- `App\Services\LeadAssigner::assign(Lead $lead)` em transação com `lockForUpdate`
- Critérios de seleção (com tiebreakers):
  1. `active = true`
  2. `last_assigned_at` ASC com **NULLs primeiro** (vendedor novo entra na próxima rodada)
  3. `leads_count` ASC (em caso de empate de timestamp por resolução de 1s)
  4. `id` ASC (determinismo total)
- Atualiza `last_assigned_at = now()` e incrementa `leads_count` no escolhido
- **Fallback**: se nenhum vendedor ativo, e-mail vai pra `config('store.sales_email')` (caixa comercial)

### 7. Painel admin (`/admin`)

#### Dashboard
Filtro de período no topo (Hoje · 7d · 30d · 90d · Este mês · Mês passado · Tudo) que afeta **todos os widgets** simultaneamente via `InteractsWithPageFilters`.

Widgets em ordem (`sort`):
1. **Stats Overview** — 4 cards: total leads, em atendimento, vendas fechadas, % cliques WhatsApp
2. **Leads por dia** (`LeadsPerDayChart`) — bar chart com 1 barra/dia, autoSkip de labels
3. **Funil de conversão** (`ConversionFunnelChart`) — bar horizontal com 4 etapas (Recebidos → Engajaram → Atendidos → Ganhos), cor degradê cinza→vermelho→verde
4. **Motos mais simuladas** (`TopMotosWidget`) — ranking com thumb, simulações, interessados, vendidas, parcela média
5. **Conversão por vendedor** (`SalespeopleConversionTable`) — tabela com totais e taxa de conversão (ganhos / fechados × 100)

#### Resources
- **Motos** — CRUD com upload de imagem, galeria múltipla, tags input pra highlights, toggle ativo/destaque
- **Leads** — listagem com badges de status/origem/vendedor, filtros, action de reatribuição (Select de vendedor no edit), seção "Simulação" colapsável (read-only)
- **Vendedores** — CRUD com badge de leads_count, filtros, **3 actions de reset**:
  - Header: "Resetar rodízio" (todos os ativos)
  - Linha: "Resetar" (1 vendedor)
  - Bulk: "Resetar contadores" (selecionados)
- **Simulador** — single-record (página de edit que sempre carrega o registro 1) com seções "Taxa e parcelas", "Entrada", "Texto e ativação"

### 8. Identidade visual

Logo oficial Shineray (`public/img/logo-shineray.png`) usada em:
- Header e footer do site público
- Header e tela de login do painel admin (`brandLogo`)
- Favicon (aba do navegador)

Paleta extraída de `shineray.com.br`:
- **Vermelho oficial** `#C8080E` (primário no admin e no front)
- **Antracite** `#212121` (texto SHINERAY na logo, hero do site, header do admin)
- Light mode no front (fundo branco com hero escuro), light mode no admin

---

## Variáveis de ambiente

Em `src/.env`:

```ini
APP_NAME="Shineray Rio Branco"
APP_LOCALE=pt_BR
APP_URL=http://localhost:8080

DB_CONNECTION=mysql
DB_HOST=db                           # nome do container
DB_DATABASE=shineray
DB_USERNAME=shineray
DB_PASSWORD=shineray

# E-mail (mailpit em dev; trocar pra SMTP real em produção)
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_FROM_ADDRESS="site@arroxamotores.com.br"

# Loja (lidos via config('store.*'))
STORE_NAME="Arroxa Motores - Shineray Acre"
STORE_ADDRESS="Via Chico Mendes, 1640 - Triângulo Velho, Rio Branco/AC"
STORE_PHONE="(68) 3224-1815"
STORE_WHATSAPP="556832241815"        # só dígitos, com 55 do BR
STORE_EMAIL="financeiro@arroxamotores.com.br"
STORE_SALES_EMAIL="vendas@arroxamotores.com.br"   # destino fallback de leads
STORE_INSTAGRAM="shinerayacre"
```

---

## Comandos comuns

```bash
# Subir / parar / rebuild
docker compose up -d
docker compose down
docker compose build app          # após mudar Dockerfile/extensão PHP

# Artisan
docker compose exec app php artisan migrate
docker compose exec app php artisan tinker
docker compose exec app php artisan optimize:clear   # após editar config/route/views
docker compose exec app php artisan test --filter FinancingCalculator

# Composer (via container — host não precisa de PHP)
docker run --rm -v "$(pwd)/src:/app" -w /app -u $(id -u):$(id -g) \
  composer:2 require <pacote> --ignore-platform-req=ext-intl

# Resetar contadores de vendedores via tinker
docker compose exec app php artisan tinker --execute="\App\Models\Salesperson::query()->update(['last_assigned_at' => null, 'leads_count' => 0]);"

# Permissões (caso o container app reclame de storage/cache)
chown -R 1000:1000 src/   # uid 1000 = user "app" dentro do container
```

---

## Testes

```bash
docker compose exec app php artisan test
```

Cobertura atual:
- `tests/Unit/FinancingCalculatorTest` — Tabela Price (4 testes)

Não há testes de Livewire/Filament neste protótipo. Recomendado em produção: testes Livewire para componentes do simulador e formulário de lead.

---

## Deploy em VPS

A pasta `deploy/` tem os scripts prontos pra colocar o protótipo numa VPS Ubuntu/Debian (ex: `https://shineray.estevo.tech`):

| Script | Pra quê |
|---|---|
| `deploy/setup-vps.sh` | **Primeira vez** — configura swap (2 GB), Redis, OPcache, PHP-FPM, Nginx e roda o primeiro deploy. **Idempotente**: detecta o que já está configurado e pula. |
| `deploy/deploy.sh` | **Rotina** — `git pull` + `composer install` + `vite build` + `php artisan optimize` + `systemctl reload php-fpm`. |
| `deploy/nginx.conf.example` | Vhost de exemplo pra `/etc/nginx/sites-available/shineray`. |
| `deploy/shineray-queue.service` | Systemd unit pro queue worker (opcional). |

Roteiro completo passo-a-passo (clone → DNS → Nginx → Certbot → setup-vps): **veja [`deploy/README.md`](deploy/README.md)**.

> O `setup-vps.sh` força `CACHE_STORE=redis`, `SESSION_DRIVER=redis` e `QUEUE_CONNECTION=redis` no `.env` — isso invalida sessões existentes do `/admin`, todo mundo precisa logar de novo após o primeiro setup.

---

## Caminho pra produção

Checklist mínimo antes de deploy (complementa o `deploy/README.md`):

1. **`.env`**:
   - `APP_ENV=production`, `APP_DEBUG=false`
   - Gerar nova `APP_KEY` (`php artisan key:generate`)
   - `MAIL_MAILER` → SMTP real (Resend, Sendgrid, Mailgun, SES...)
   - Trocar `STORE_*` para os valores reais da loja
2. **Build de assets**: trocar `<script src="https://cdn.tailwindcss.com">` no layout por `@vite(['resources/css/app.css', 'resources/js/app.js'])`. Configurar `tailwind.config.js` e `npm run build`. O container `node` no compose já está pronto pra isso.
3. **Banco**: trocar credenciais MySQL; em produção usar provider gerenciado (RDS, Cloud SQL, Hetzner, etc.).
4. **HTTPS**: nginx atrás de Cloudflare/Caddy/Traefik com cert TLS. O nginx aqui só serve HTTP.
5. **Queue**: marcar `NewLeadMail implements ShouldQueue` e configurar worker (Redis ou DB queue). Atualmente é envio síncrono — em volume alto pode estourar timeout do request.
6. **Backup**: cron de `mysqldump` no container `db` (ou snapshot do volume `dbdata`).
7. **Monitoramento**: pelo menos um pingback `/admin/login` e `/` pra detectar 5xx.
8. **Senha admin**: trocar `shineray123` por algo forte. O dono cria mais usuários pelo `users` table ou via `make:filament-user`.

---

## Decisões de arquitetura registradas

- **Sem JS framework**: Livewire 3 SSR + máscara de telefone em JS puro (`window.phoneMaskBR`). Tailwind via CDN. Tudo testado em browsers modernos.
- **Simulação é Lead**: campos de cálculo no `leads` ao invés de tabela `simulations` separada — mantém o funil unificado e elimina JOIN.
- **Auto-discovery Filament**: panel discovery em `app/Filament/{Resources,Pages,Widgets}`. Widgets registrados explicitamente no `AdminPanelProvider` para controle de ordem.
- **Round-robin com 3 tiebreakers**: `last_assigned_at ASC` → `leads_count ASC` → `id ASC`. O segundo tiebreaker é necessário porque `timestamp` no MySQL tem resolução de 1s e múltiplos leads no mesmo segundo deixariam o sort indefinido.
- **PeriodFilter centralizado**: `App\Support\PeriodFilter::range($period)` retorna `[$from, $to]`. Reusado por 4 widgets — sem duplicação de match/switch em cada widget.
- **Fallback de e-mail**: se não houver vendedor ativo, lead vai pra `config('store.sales_email')`. Garantia de que **nenhum lead se perde**, mesmo com a equipe inteira "off".

---

## Documentação adicional

| Arquivo | Conteúdo |
|---|---|
| [`deploy/README.md`](deploy/README.md) | Roteiro detalhado do deploy em VPS (clone, DNS, Nginx, Certbot, setup) |
| [`docs/briefing-plano-vendas-produto.md`](docs/briefing-plano-vendas-produto.md) | Briefing de produto: posicionamento, ICP, jornada de compra e métricas que o painel admin reporta |
| [`docs/briefing-roteiro-vendas.md`](docs/briefing-roteiro-vendas.md) | Roteiro de vendas / playbook pros vendedores que recebem leads do site |
| [`src/README.md`](src/README.md) | README curto do app Laravel (estrutura interna do `src/`) |

---

## Roadmap / próximos passos

Sugestões organizadas por valor:

**Curto prazo**
- Tempo médio entre etapas (precisa coluna `status_changed_at` em leads)
- Notificação SLA: alertar se lead novo > 1h sem atendimento
- Testes Livewire para o simulador
- Rate limit em `LeadForm.submit()` por IP (anti-spam)

**Médio prazo**
- Captura de UTM no front (Google Analytics, Meta Pixel) e gravação no Lead
- Distribuição de leads por categoria de moto (vendedor X só pega leads de scooter)
- Relatório mensal por e-mail pro gestor (cron + Mailable)

**Longo prazo**
- Integração WhatsApp Business API (Z-API ou similar) — disparar mensagem no WhatsApp do vendedor automaticamente sem o cliente clicar
- Webhook pra CRM externo (RD Station, Pipedrive)
- Multi-loja: tabela `stores` com `salespeople` e `motos` por loja
