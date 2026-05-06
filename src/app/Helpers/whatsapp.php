<?php

use App\Models\Moto;

if (! function_exists('whatsapp_link')) {
    function whatsapp_link(?string $message = null): string
    {
        $number = preg_replace('/\D/', '', (string) config('store.whatsapp'));
        $base = "https://wa.me/{$number}";
        if ($message === null || $message === '') {
            return $base;
        }
        return $base.'?text='.rawurlencode($message);
    }
}

if (! function_exists('whatsapp_link_for_moto')) {
    function whatsapp_link_for_moto(Moto $moto): string
    {
        $msg = sprintf(
            "Olá! Tenho interesse na moto %s (%s) — vi no site da %s. Pode me passar mais informações?",
            $moto->name,
            $moto->formatted_price,
            config('store.name'),
        );
        return whatsapp_link($msg);
    }
}
