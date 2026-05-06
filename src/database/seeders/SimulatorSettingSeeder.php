<?php

namespace Database\Seeders;

use App\Models\SimulatorSetting;
use Illuminate\Database\Seeder;

class SimulatorSettingSeeder extends Seeder
{
    public function run(): void
    {
        SimulatorSetting::updateOrCreate(
            ['id' => 1],
            [
                'default_interest_rate' => 0.0270,
                'min_installments' => 12,
                'max_installments' => 48,
                'installments_step' => 6,
                'min_down_payment_percent' => 10.00,
                'max_down_payment_percent' => 80.00,
                'disclaimer_text' => 'Valor estimado a 2,7% a.m. — sujeito a aprovação de crédito pela financeira parceira.',
                'active' => true,
            ],
        );
    }
}
