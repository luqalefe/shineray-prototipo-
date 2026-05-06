@php
/** @var \App\Models\Lead $lead */
$isSimulation = $lead->isSimulation();
$seller = $lead->salesperson;
$greeting = $seller ? explode(' ', $seller->name)[0] : explode(' ', $lead->name)[0];
$customerWhatsapp = 'https://wa.me/'.preg_replace('/\D/', '', '55'.$lead->phone)
    .'?text='.rawurlencode("Olá ".explode(' ', $lead->name)[0]."! Aqui é ".($seller ? $seller->name : 'o time')." da ".config('store.name').". Recebi seu contato pelo site, posso te ajudar?");
$adminUrl = url('/admin/leads/'.$lead->id.'/edit');
$money = fn ($v) => 'R$ '.number_format((float) $v, 2, ',', '.');
@endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>{{ $isSimulation ? 'Nova simulação' : 'Novo lead' }}</title>
</head>
<body style="margin:0;padding:0;background:#f5f5f5;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#212121;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:24px 0;">
<tr><td align="center">
    <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.06);">

        <tr><td style="background:#212121;padding:20px 28px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="font-family:'Barlow Condensed',Impact,sans-serif;font-weight:800;font-size:24px;color:#ffffff;letter-spacing:1px;">
                        <span style="background:#C8080E;color:#ffffff;padding:4px 10px;display:inline-block;">SHINERAY</span>
                        <span style="font-size:11px;color:#b2b0b0;letter-spacing:2px;margin-left:8px;text-transform:uppercase;">Rio Branco · AC</span>
                    </td>
                    <td align="right" style="font-size:11px;color:#b2b0b0;text-transform:uppercase;letter-spacing:1.5px;">
                        {{ $lead->created_at->format('d/m/Y H:i') }}
                    </td>
                </tr>
            </table>
        </td></tr>

        <tr><td style="padding:32px 28px 8px;">
            @if($seller)
            <p style="margin:0 0 16px;padding:10px 14px;background:#fff1f2;border-left:3px solid #C8080E;font-size:14px;color:#212121;">
                Olá <strong>{{ $greeting }}</strong>, este lead foi atribuído a você.
            </p>
            @endif
            <p style="margin:0 0 4px;font-size:11px;color:#C8080E;letter-spacing:2px;text-transform:uppercase;font-weight:700;">
                {{ $isSimulation ? 'Nova simulação de financiamento' : 'Novo contato pelo site' }}
            </p>
            <h1 style="margin:0;font-family:'Barlow Condensed',Impact,sans-serif;font-size:32px;color:#212121;text-transform:uppercase;line-height:1.1;">
                {{ $lead->name }}
            </h1>
            @if($lead->moto)
            <p style="margin:8px 0 0;color:#6b6b6b;font-size:14px;">
                Interessado em <strong style="color:#212121;">{{ $lead->moto->name }}</strong>
                @if($lead->moto->displacement_cc) · {{ $lead->moto->displacement_cc }}cc @endif
                · {{ $money($lead->moto->price) }}
            </p>
            @endif
        </td></tr>

        <tr><td style="padding:16px 28px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#fafafa;border:1px solid #eeeeee;border-radius:8px;">
                <tr><td style="padding:16px 20px;">
                    <p style="margin:0 0 8px;font-size:11px;color:#6b6b6b;text-transform:uppercase;letter-spacing:1.5px;font-weight:700;">Contato</p>
                    <p style="margin:0;font-size:15px;color:#212121;">
                        📱 <a href="tel:+55{{ preg_replace('/\D/','',$lead->phone) }}" style="color:#212121;text-decoration:none;"><strong>{{ $lead->phone }}</strong></a>
                    </p>
                    @if($lead->email)
                    <p style="margin:4px 0 0;font-size:14px;color:#6b6b6b;">
                        ✉️ <a href="mailto:{{ $lead->email }}" style="color:#6b6b6b;text-decoration:none;">{{ $lead->email }}</a>
                    </p>
                    @endif
                    @if($lead->message)
                    <p style="margin:12px 0 0;padding:12px;background:#ffffff;border-left:3px solid #C8080E;color:#3a3a3a;font-size:14px;line-height:1.5;font-style:italic;">
                        "{{ $lead->message }}"
                    </p>
                    @endif
                </td></tr>
            </table>
        </td></tr>

        @if($isSimulation)
        <tr><td style="padding:0 28px 16px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#fff1f2;border:1px solid #fce4e4;border-radius:8px;">
                <tr><td style="padding:18px 20px;">
                    <p style="margin:0 0 12px;font-size:11px;color:#C8080E;text-transform:uppercase;letter-spacing:1.5px;font-weight:700;">Simulação de financiamento</p>

                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td style="padding:6px 0;font-size:13px;color:#6b6b6b;">Valor da moto</td>
                            <td align="right" style="padding:6px 0;font-size:14px;color:#212121;font-weight:600;">{{ $money($lead->vehicle_price) }}</td>
                        </tr>
                        <tr>
                            <td style="padding:6px 0;font-size:13px;color:#6b6b6b;">Entrada</td>
                            <td align="right" style="padding:6px 0;font-size:14px;color:#212121;font-weight:600;">{{ $money($lead->down_payment) }}</td>
                        </tr>
                        <tr>
                            <td style="padding:6px 0;font-size:13px;color:#6b6b6b;">Valor financiado</td>
                            <td align="right" style="padding:6px 0;font-size:14px;color:#212121;font-weight:600;">{{ $money($lead->financed_amount) }}</td>
                        </tr>
                        <tr>
                            <td style="padding:6px 0;font-size:13px;color:#6b6b6b;">Taxa aplicada</td>
                            <td align="right" style="padding:6px 0;font-size:14px;color:#212121;font-weight:600;">{{ number_format((float) $lead->interest_rate * 100, 2, ',', '.') }}% a.m.</td>
                        </tr>
                    </table>

                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:12px;border-top:1px solid #fce4e4;">
                        <tr>
                            <td style="padding:14px 0 0;font-size:12px;color:#6b6b6b;text-transform:uppercase;letter-spacing:1px;">Parcela</td>
                            <td align="right" style="padding:14px 0 0;font-family:'Barlow Condensed',Impact,sans-serif;font-size:28px;color:#C8080E;font-weight:800;line-height:1;">
                                {{ $lead->installments }}x {{ $money($lead->installment_value) }}
                            </td>
                        </tr>
                    </table>
                </td></tr>
            </table>
        </td></tr>
        @endif

        <tr><td style="padding:8px 28px 24px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="padding:0 4px 0 0;width:50%;">
                        <a href="{{ $customerWhatsapp }}" style="display:block;background:#22c55e;color:#ffffff;text-align:center;padding:14px 16px;text-decoration:none;font-weight:700;border-radius:6px;font-size:14px;">
                            Responder no WhatsApp
                        </a>
                    </td>
                    <td style="padding:0 0 0 4px;width:50%;">
                        <a href="{{ $adminUrl }}" style="display:block;background:#212121;color:#ffffff;text-align:center;padding:14px 16px;text-decoration:none;font-weight:700;border-radius:6px;font-size:14px;">
                            Abrir no painel
                        </a>
                    </td>
                </tr>
            </table>
        </td></tr>

        <tr><td style="background:#fafafa;border-top:1px solid #eeeeee;padding:16px 28px;text-align:center;font-size:11px;color:#b2b0b0;">
            Origem: {{ $lead->source_label }}
            @if($lead->ip) · IP {{ $lead->ip }} @endif
            <br>
            @if($seller)
                Atribuído a <strong>{{ $seller->name }}</strong> via round-robin.
            @else
                Sem vendedor designado — caixa comercial recebendo direto.
            @endif
        </td></tr>

    </table>
</td></tr>
</table>
</body>
</html>
