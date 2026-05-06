<?php

namespace Database\Seeders;

use App\Models\Salesperson;
use Illuminate\Database\Seeder;

class SalespersonSeeder extends Seeder
{
    public function run(): void
    {
        $sellers = [
            ['name' => 'Ricardo Almeida',  'email' => 'ricardo@arroxamotores.com.br',  'phone' => '(68) 9 9111-1111'],
            ['name' => 'Juliana Bezerra',  'email' => 'juliana@arroxamotores.com.br',  'phone' => '(68) 9 9222-2222'],
            ['name' => 'Pedro Henrique',   'email' => 'pedro@arroxamotores.com.br',    'phone' => '(68) 9 9333-3333'],
        ];

        foreach ($sellers as $data) {
            Salesperson::updateOrCreate(
                ['email' => $data['email']],
                array_merge($data, ['active' => true]),
            );
        }
    }
}
