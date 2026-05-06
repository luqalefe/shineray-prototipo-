<?php

namespace Database\Seeders;

use App\Models\Moto;
use Illuminate\Database\Seeder;

class MotoSeeder extends Seeder
{
    public function run(): void
    {
        $motos = [
            [
                'name' => 'JET 50',
                'category' => 'ciclomotor',
                'displacement_cc' => 49,
                'price' => 11090.00,
                'short_description' => 'Ciclomotor com visual de moto, ideal para a cidade e quem busca economia.',
                'image' => 'motos/jet-50.png',
                'highlights' => ['Painel digital', 'Iluminação em LED', 'Porta-volume frontal', '49cc — sem habilitação A'],
                'featured' => true,
                'sort_order' => 10,
            ],
            [
                'name' => 'PHOENIX S',
                'category' => 'ciclomotor',
                'displacement_cc' => 49,
                'price' => 8790.00,
                'short_description' => 'Ciclomotor que entrega alta economia com visual arrojado.',
                'image' => 'motos/phoenix-s.webp',
                'highlights' => ['Excelente consumo', 'Design jovem', 'Suspensão reforçada', 'Categoria ACC'],
                'featured' => false,
                'sort_order' => 20,
            ],
            [
                'name' => 'JET 125',
                'category' => 'street',
                'displacement_cc' => 125,
                'price' => 11490.00,
                'short_description' => 'Street 125cc que combina desempenho urbano e economia inteligente.',
                'image' => 'motos/jet-125.webp',
                'highlights' => ['Painel digital', 'Iluminação full LED', '7 cores disponíveis', 'Cavalete central'],
                'featured' => true,
                'sort_order' => 30,
            ],
            [
                'name' => 'JET 125SS EFI',
                'category' => 'street',
                'displacement_cc' => 125,
                'price' => 12990.00,
                'short_description' => 'Versão sport com injeção eletrônica e visual agressivo.',
                'image' => 'motos/jet-125ss.webp',
                'highlights' => ['Injeção eletrônica EFI', 'Freio a disco dianteiro/traseiro', 'Visual sport', 'Baixo consumo'],
                'featured' => true,
                'sort_order' => 40,
            ],
            [
                'name' => 'JEF 150',
                'category' => 'street',
                'displacement_cc' => 150,
                'price' => 14790.00,
                'short_description' => 'Design moderno e tecnologia para o uso diário, com painel digital intuitivo.',
                'image' => 'motos/jef-150.webp',
                'highlights' => ['Painel digital', 'Farol em LED', 'Suspensão reforçada', 'Acessórios de série'],
                'featured' => true,
                'sort_order' => 50,
            ],
            [
                'name' => 'URBAN LITE',
                'category' => 'scooter',
                'displacement_cc' => 150,
                'price' => 12490.00,
                'short_description' => 'Scooter automática 150cc com câmbio CVT, perfeita para o trânsito urbano.',
                'image' => 'motos/urban-lite.webp',
                'highlights' => ['Transmissão automática CVT', 'Porta-objetos amplo', 'Conforto urbano', '13,5 cv'],
                'featured' => false,
                'sort_order' => 60,
            ],
            [
                'name' => 'SHI 175',
                'category' => 'trail',
                'displacement_cc' => 175,
                'price' => 16490.00,
                'short_description' => 'Trail versátil que enfrenta a cidade e estradas variadas com confiança.',
                'image' => 'motos/shi-175.webp',
                'highlights' => ['Farol em "X" full LED', 'Painel TFT', 'Suspensão de longo curso', '15,6 cv'],
                'featured' => true,
                'sort_order' => 70,
            ],
            [
                'name' => 'STORM 200 EFI',
                'category' => 'trail',
                'displacement_cc' => 200,
                'price' => 21590.00,
                'short_description' => 'Trail 200cc com injeção eletrônica e estilo aventureiro.',
                'image' => 'motos/storm-200.webp',
                'highlights' => ['Injeção eletrônica', 'Pneus mistos', 'Suspensão off-road', 'Tanque de longo curso'],
                'featured' => false,
                'sort_order' => 80,
            ],
            [
                'name' => 'SHI 250',
                'category' => 'street',
                'displacement_cc' => 250,
                'price' => 21490.00,
                'short_description' => 'Street premium 250cc com tecnologia e desempenho de alto nível.',
                'image' => 'motos/shi-250.webp',
                'highlights' => ['250cc bicilíndrica', 'Freios ABS', 'Painel TFT colorido', 'Desempenho premium'],
                'featured' => true,
                'sort_order' => 90,
            ],
            [
                'name' => 'IRON',
                'category' => 'custom',
                'displacement_cc' => 250,
                'price' => 21990.00,
                'short_description' => 'Custom cruiser 250cc com visual clássico e postura de pilotagem relaxada.',
                'image' => 'motos/iron-250.webp',
                'highlights' => ['Estilo cruiser clássico', 'Banco baixo', 'Guidão alto', 'Acabamento premium'],
                'featured' => false,
                'sort_order' => 100,
            ],
        ];

        foreach ($motos as $data) {
            Moto::updateOrCreate(
                ['name' => $data['name']],
                $data,
            );
        }
    }
}
