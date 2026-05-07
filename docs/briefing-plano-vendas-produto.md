# Briefing — Plano de Vendas da Plataforma (Catálogo + Simulador + CRM)

> **Como usar este arquivo**
> 1. Abra https://claude.ai no navegador
> 2. Cole este arquivo inteiro na conversa
> 3. Use o **Prompt final** da seção 11 como pedido
> 4. Itere até ficar bom — peça ajustes de tom/preço/concorrência
> 5. Exporte o resultado pro PDF (Google Docs → Download → PDF, ou StackEdit/Dillinger)

> **Importante:** este briefing NÃO é sobre vender motos. É sobre vender **a plataforma** — o sistema Laravel/Livewire/Filament construído pro Arroxa Motores — pra outras concessionárias regionais como produto turnkey.

---

## 1. O produto em uma frase

**Site de vitrine + simulador de financiamento + CRM leve, pronto pra concessionária regional rodar em uma semana, com lead já chegando no WhatsApp do vendedor com a simulação anexada.**

Não é "site institucional". Não é "CRM genérico". É a junção dos dois, focada exclusivamente no fluxo "cliente vê moto online → simula → vira lead com dados completos → vendedor responde".

---

## 2. O problema que o produto resolve

Concessionárias regionais (50% do mercado nacional fora de SP/RJ) sofrem com:

1. **Lead solto no Instagram/WhatsApp avulso** — DM cai no celular pessoal do vendedor, vira caos. Sem rastreio, sem distribuição, sem follow-up.
2. **Site institucional sem catálogo** — feito por agência local em WordPress. Bonito, vazio, "fale conosco" como única conversão. Não vende.
3. **Simulador de financiamento na mão** — vendedor calcula no Excel ou no app do banco enquanto o cliente espera no balcão. Demora, erra, perde negócio.
4. **Distribuição de lead injusta** — vendedor "favorito" pega tudo, vendedores novos morrem na praia. Cria atrito interno, gestor não sabe quem está convertendo.
5. **Falta de visão de funil** — gestor não sabe quantos leads chegaram esse mês, quantos viraram venda, quanto custou. Decisão de marketing é no chute.

**Resultado típico:** dono da loja sente que "tem demanda no Instagram" mas não consegue medir nem capturar. Investe em ads sem saber o ROAS. Vendedor anota lead em caderno.

---

## 3. Público-alvo (persona)

### Persona primária — "Dono de concessionária regional"

- 35–55 anos, normalmente é o próprio fundador da loja
- 1 a 5 vendedores
- Faturamento R$ 200 mil a R$ 2 milhões/mês em motos
- Marcas: Shineray, Haojue, Dafra, Mottu, Honda autorizada small, Yamaha autorizada small, multimarcas
- Cidades: capitais do N/NE/CO + interior de SP/MG/RS
- Já investe em tráfego (Meta Ads, Google Ads), mas não mede conversão direito
- Não tem time de TI. Tem no máximo um sobrinho que mexe no Instagram da loja
- Compra software por indicação ("o cara da concessionária X usa")

### Persona secundária — "Gerente comercial"

- Foi vendedor que cresceu, virou supervisor
- Quer mostrar resultado pro dono e justificar comissão
- Vai ser o **usuário principal do painel admin**
- Métrica que ele quer: "quantos leads cada vendedor pegou, quantos fecharam, taxa de conversão"

### Quem NÃO é público

- Concessionária Honda/Yamaha de capital com >10 vendedores e ERP integrado (já usa Sebrae/Linx/Bravosul)
- Loja de carros (foi pensado pra moto — preço, simulação, fluxo todo é moto)
- Marketplace ("seu produto + de outras lojas") — é vitrine de uma loja só

---

## 4. Features completas (esses são seus argumentos de venda)

### 4.1 Catálogo público responsivo

- Mobile-first (90% do tráfego em concessionária regional vem do celular)
- Filtro reativo por categoria (street, scooter, trail, custom, ciclomotor)
- Busca por nome em tempo real
- Hero escuro com gradiente vermelho + 2 CTAs ("Ver catálogo" / "Falar no WhatsApp")
- Seção "Destaques" pra puxar modelos com margem maior
- Botão flutuante WhatsApp (FAB) persistente em todo scroll
- Sem framework JS pesado — carrega em <1s mesmo em 3G do interior

### 4.2 Página de detalhe da moto

- Foto grande + galeria
- Descrição, highlights (lista de checks vermelhos)
- Card de preço destacado
- 2 CTAs claros: **Simular financiamento** ou **Falar no WhatsApp**
- Seção "Outros modelos da categoria" pra cross-sell

### 4.3 Simulador de financiamento Tabela Price

- Cálculo real, com fórmula matemática correta (PMT = PV × (i × (1+i)^n) / ((1+i)^n − 1))
- Slider de parcelas (12 a 48x, configurável)
- Input de entrada com mínimo/máximo configuráveis (10% a 80% por padrão)
- **Lead-capture wall:** mostra "Valor financiado" pra dar gosto, mas a parcela final fica bloqueada com cadeado até o cliente preencher nome + WhatsApp + e-mail
- Validação server-side dos limites de entrada (não dá pra simular com R$ 1)
- Cobertura de testes automatizados (4 cenários no PHPUnit)

### 4.4 Captura de lead com contexto rico

- Form simples (nome, WhatsApp, e-mail opcional, mensagem)
- Máscara de telefone BR automática
- Checkbox de consentimento LGPD
- Validação no servidor com mensagens em PT-BR
- Após submit: WhatsApp abre em nova aba com **mensagem pré-formatada** tipo:
  > Olá! Acabei de simular um financiamento no site da [Loja].
  > 🏍️ Modelo: SHI 175
  > 💰 Valor: R$ 16.490
  > 💵 Entrada: R$ 3.298
  > 📅 Parcelas: 36x R$ 541,41

### 4.5 Distribuição automática (round-robin) de leads

- Algoritmo com 3 critérios em transação atômica (lockForUpdate):
  1. Vendedor com `last_assigned_at` mais antigo (NULLs primeiro — vendedor novo entra rápido)
  2. Em empate, menor `leads_count`
  3. Em empate total, menor ID
- Vendedor desativado é pulado automaticamente
- Fallback: se ninguém ativo, lead vai pra caixa comercial geral — **nenhum lead se perde**
- Reset manual do rodízio: por vendedor, em massa ou todos os ativos (action no painel)

### 4.6 Notificação inteligente por e-mail

- Subject mudanca conforme o tipo:
  - Lead simples: `[Novo lead] Maria — JET 50`
  - Simulação: `[Simulação] João — SHI 175 — 36x R$ 541,41`
- **Reply-To = e-mail do cliente** — vendedor responde direto pelo Gmail e cai no e-mail do cliente
- 2 CTAs no e-mail: "Responder no WhatsApp" (com saudação personalizada com nome do vendedor) + "Abrir no painel"
- Template HTML responsivo (vê bonito no celular do vendedor)

### 4.7 Painel admin completo (Filament)

- **Login com logo Shineray** (ou da loja contratante — branding configurável)
- **Dashboard com filtro de período no topo** (Hoje · 7d · 30d · 90d · Este mês · Mês passado · Tudo) que afeta todos os widgets
- **5 widgets prontos:**
  1. **Stats Overview** — total leads, em atendimento, vendas fechadas, % cliques WhatsApp
  2. **Leads por dia** — bar chart com 1 barra/dia
  3. **Funil de conversão** — bar horizontal com 4 etapas (Recebidos → Engajaram → Atendidos → Ganhos)
  4. **Motos mais simuladas** — ranking com thumb, simulações, interessados, vendidas, parcela média
  5. **Conversão por vendedor** — tabela com totais e taxa de conversão
- **CRUD de motos** com upload de imagem, galeria, tags de highlights, toggle ativo/destaque
- **CRUD de leads** com badges de status/origem/vendedor, filtros, ação de reatribuir, seção colapsável "Simulação"
- **CRUD de vendedores** com badge de leads_count e 3 actions de reset (header, linha, bulk)
- **Configurações do simulador** (taxa de juros, mín/máx de parcelas, mín/máx de entrada, texto de disclaimer)

### 4.8 WhatsApp em tudo

- Site público (FAB, CTAs)
- Cards de moto
- Página de detalhe
- Pós-simulação (mensagem rica)
- E-mail pro vendedor (link "Responder no WhatsApp" com saudação personalizada)
- Tracking de cliques (`whatsapp_clicked` por lead — sabe quem abriu o WhatsApp)

### 4.9 Identidade visual customizável

- Logo da loja em header, footer, login admin e favicon
- Paleta de cores configurável (vermelho Shineray + antracite no padrão)
- Fontes web (Anton + Inter no padrão, customizável)

### 4.10 Operação simples

- Self-hostable em qualquer VPS Linux com PHP 8.3+ e MySQL (Hetzner, Hostinger, AWS Lightsail, Locaweb)
- Scripts de deploy prontos (`deploy/setup-vps.sh` e `deploy/deploy.sh`)
- Stack mainstream (Laravel 13 + Filament 4 + Livewire 3) — qualquer dev PHP do Brasil consegue dar manutenção
- Backup é mysqldump + pasta storage. Sem dependência de serviço SaaS proprietário.

---

## 5. Diferenciais competitivos (use ao confrontar com a concorrência)

| Concorrente | Por que perde |
|---|---|
| **WordPress + plugin de classificados** (agência local) | Não tem CRM. Não tem simulador. WhatsApp é "fale conosco". Lead vai pro e-mail e some. |
| **Pipedrive / RD Station** | Não tem catálogo. Não tem simulador. Não capta direto do site. Mensalidade alta (R$ 300+/mês por usuário). |
| **Vrum / Webmotors / iCarros** (marketplace) | Lead vai pro marketplace, não pro site da loja. Loja paga por lead "compartilhado" com concorrentes. |
| **Site DIY (Webnode, Wix, Builderall)** | Sem simulador real. Sem distribuição de lead. Sem dashboard. Cliente preenche form genérico, vendedor não sabe quem é. |
| **App vertical automotivo (Whatica, Autocom)** | Caro (R$ 800+/mês), foco SP/RJ, mais complexo do que loja regional precisa. |

**O ângulo de venda:** "Tudo que esses caros fazem juntos, mas pra concessionária de moto regional, em uma fração do preço, com simulador real e WhatsApp first."

---

## 6. Modelo comercial sugerido (pra Claude validar/comparar)

Trabalhar dois cenários no plano:

### Cenário A — SaaS mensal

- **Setup:** R$ 3.500 (configuração de domínio, importação do catálogo da loja, customização de cor/logo, treinamento de 2h)
- **Mensalidade:** R$ 299/mês (hospedagem incluída, atualizações, suporte WhatsApp em horário comercial)
- **Up-sell:** R$ 99/mês adicional por feature: integração Meta Pixel, relatório mensal por e-mail, multi-loja

**Quando faz sentido:** loja sem time de TI, prefere "tô pagando pra alguém cuidar".

### Cenário B — Licença + manutenção

- **Setup + licença:** R$ 7.500 (one-shot, código entregue, hospedado na VPS do cliente)
- **Manutenção mensal:** R$ 199/mês (suporte, atualizações de segurança, novas features)
- **Sem manutenção:** o cliente roda por conta. Não recomendado mas é uma opção.

**Quando faz sentido:** loja maior, quer controle do código, tem alguém de TI ou parceiro.

### Cenário C — Custom/grande conta (multi-loja, bandeira regional)

- **A combinar:** > R$ 20k de setup + mensalidade casada com volume

---

## 7. Provas / dados pra usar no plano

Use só números que dá pra defender. Exemplos a citar:

- "**Lead com simulação anexa fecha 3-4x mais rápido** que lead frio." (verdade pesquisada — taxa do mercado de financiamento BR)
- "**60% das visitas de site de concessionária regional vem por mobile.**" (dados Google Brasil)
- "**WhatsApp é o canal de fechamento de 90%+ das vendas de moto no BR.**" (verdade prática)
- "Caso Arroxa Motores (Rio Branco/AC) — protótipo rodando em 1 semana, capturando lead com simulação completa, distribuição automática entre vendedores."

**Não invente números** que não tem como provar. Se Claude tentar criar "aumentou vendas em 40%", peça pra remover ou marcar como projeção/exemplo.

---

## 8. Tom de voz e posicionamento

- **Direto e prático.** Dono de concessionária não tem paciência pra papo de tech. "Você vai ter X, ele faz Y, custa Z."
- **Honesto sobre escopo.** Não é solução pra Honda do Brasil. É solução pra loja regional.
- **WhatsApp first.** Toda apresentação termina com "fala comigo no WhatsApp pra eu te mostrar o demo".
- **Demo por cima de tudo.** O melhor argumento é mostrar a tela rodando. O plano deve indicar momentos pra agendar demo.

**Evitar:**
- Buzzword de SaaS ("end-to-end pipeline", "go-to-market velocity")
- Prometer "aumento garantido em vendas"
- Comparações depreciativas ("WordPress é uma porcaria")
- Dependência de comparação com gigantes ("Salesforce ensina que…")

---

## 9. Estrutura desejada do plano de vendas

Quero um documento usável por **um vendedor solo** que vai abordar concessionárias por WhatsApp, ligação, ou visita presencial. Estrutura:

1. **Capa** — nome do produto (a definir), tagline, contato.
2. **Sumário executivo** (1 página) — o que é, quem precisa, ROI esperado.
3. **O problema** (1-2 páginas) — os 5 pontos de dor da seção 2 deste briefing, em linguagem de dono de loja.
4. **A solução** (1 página) — visão de produto, com os 4 fluxos principais (cliente vê catálogo → simula → vira lead → vendedor fecha).
5. **Features detalhadas** (3-4 páginas) — cada uma das 10 features da seção 4 com:
   - Título da feature
   - 1 frase de pitch ("ataque" da feature)
   - Como funciona (3-5 bullets técnicos pro decisor)
   - O ganho prático ("o que muda no seu dia-a-dia")
6. **Comparativo competitivo** (1 página) — tabela da seção 5 expandida, com coluna "ganhos do nosso lado".
7. **Modelo comercial** (1 página) — cenários A, B, C da seção 6 em formato de cards.
8. **Roadmap visível** (1 página) — o que já tem, o que está vindo (multi-loja, integração WhatsApp Business API, integração com bancos).
9. **Implantação em 7 dias** (1 página) — cronograma dia-a-dia: D1 setup VPS, D2 catálogo, D3 customização visual, D4 cadastro vendedores, D5 treinamento, D6 ajustes, D7 go-live.
10. **Casos de uso** (1 página) — 2-3 perfis de loja com simulação de retorno (ex.: "loja com 80 leads/mês — 1 lead a mais por dia paga a mensalidade no primeiro mês").
11. **Próximo passo** (½ página) — CTA único: "agende um demo de 20 min no meu WhatsApp [número]".

**Tamanho-alvo:** 14 a 18 páginas A4 em PDF. Pode ter capa colorida, mas o miolo deve ser legível impresso preto-e-branco (vendedor pode imprimir e levar pra reunião).

---

## 10. Perguntas que vão aparecer e o plano precisa preparar

Inclua um **anexo de FAQ** com pelo menos:

1. *"Eu já tenho site, não posso usar?"* — Pode, mas o site atual não tem CRM nem simulador. Esta plataforma substitui ou convive com redirect.
2. *"E se quebrar?"* — Hospedagem com uptime monitorado, backup diário automático, suporte WhatsApp em horário comercial.
3. *"E se eu quiser cancelar?"* — SaaS: aviso de 30 dias, exporta seus dados em CSV. Licença: o código é seu, fica.
4. *"Funciona pra carro?"* — Não no momento — foi feito pra moto. Roadmap de carro está em estudo.
5. *"Funciona com a fábrica X?"* — Sim, é agnóstico. Você cadastra os modelos manualmente. Importação por planilha do catálogo é feature de setup.
6. *"E LGPD?"* — Coleta com checkbox de consentimento, dados ficam só no banco da loja, não compartilha com terceiros.
7. *"Treina os meus vendedores?"* — Sim, treinamento de 2h presencial ou online no setup. Material de apoio em PDF.
8. *"E integração com banco/financeira?"* — Roadmap. Hoje a simulação é referencial — fechamento real continua com o financeiro do banco.
9. *"E se eu tiver mais de uma loja?"* — Roadmap multi-loja em desenvolvimento. Hoje, multi-loja é múltiplas instâncias.
10. *"Posso testar antes?"* — Demo de 20 min com seus modelos cadastrados. Se fechar, conta a partir do go-live, não do demo.

---

## 11. Prompt final (cole isto no Claude depois do briefing acima)

```
Com base em TODO o briefing acima, escreva o plano de vendas completo
no formato Markdown, pronto pra exportar como PDF.

Regras:
- Siga exatamente a estrutura da seção 9 (11 partes + anexo de FAQ).
- Tom de voz da seção 8 (direto, prático, sem buzzword, WhatsApp first).
- Use as features da seção 4 sem inventar nenhuma. Se quiser sugerir
  uma feature nova, marque entre colchetes "[SUGESTÃO — não está pronta]".
- Use os preços dos cenários A, B, C da seção 6 — pode arredondar pra
  comunicação se ficar mais limpo, mas mantenha proporção.
- No comparativo competitivo (seção 6 da estrutura), monte uma tabela
  com 4 colunas: Solução / Catálogo / Simulador / CRM com lead — e
  marque ✔/✗ pra cada concorrente.
- No item "Implantação em 7 dias", escreva entregáveis concretos por dia
  (ex.: "D2: catálogo de até 30 modelos importado, fotos otimizadas").
- Nos casos de uso, calcule retorno simples: se loja captura X leads/mês
  e fecha 5%, quantos negócios extras vêm? Se cada moto dá margem média
  de R$ 1.500, qual o retorno mensal? Mostre os números.
- Não use emoji no corpo do texto (ok no e-mail/WhatsApp, no plano não).
- Escreva já formatado pra ficar bonito convertido em PDF: cabeçalhos
  hierárquicos, tabelas markdown, listas, separadores, sem parágrafos
  longos demais.

Quando terminar, responda com o plano em UM ÚNICO bloco markdown
copiável.
```

**Como exportar pro PDF depois:**
- Cole o markdown gerado em https://stackedit.io ou no Google Docs
- Ajuste a capa (suba o logo do seu produto)
- Arquivo → Download → PDF (Google Docs) ou "Export PDF" (StackEdit)

---

## 12. Variações pra pedir depois

Quando o plano principal estiver pronto, peça versões derivadas:

- **"Pitch de 2 minutos"** — script de áudio pra mandar em WhatsApp pro dono de loja indicado.
- **"E-mail frio"** — sequência de 3 e-mails (assunto + corpo) pra prospecção.
- **"Apresentação do demo"** — roteiro de 20 minutos da reunião de demonstração, com timestamps.
- **"Proposta comercial"** — modelo de proposta enviável após o demo, com escopo + preço + cronograma + termos.
- **"Carta de intenção"** — pré-contrato que pode mandar pro cliente que diz "manda alguma coisa por escrito".
- **"Posts LinkedIn"** — 5 posts curtos pra plantar autoridade durante a prospecção.
