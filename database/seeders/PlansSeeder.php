<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Básico',
                'slug' => 'basic',
                'description' => 'Ideal para começar',
                'price_monthly' => 29.00,
                'price_semiannual' => 150.00,
                'price_annual' => 290.00,
                'screens' => 1,
                'features' => json_encode([
                    '1 tela por usuário',
                    '+2000 filmes e séries',
                    'Assista na TV, computador',
                    'Programas Ao Vivo',
                    'HD Ultra HD'
                ]),
                'is_popular' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Intermediário',
                'slug' => 'intermediate',
                'description' => 'Melhor custo benefício',
                'price_monthly' => 59.00,
                'price_semiannual' => 300.00,
                'price_annual' => 590.00,
                'screens' => 5,
                'features' => json_encode([
                    'Todos os benefícios do plano básico',
                    '5 telas por usuário',
                    'Cursos de Inglês e Espanhol',
                    'Ultra HD e 4K',
                    'Acesso pelo celular',
                    'Maiores campeonatos de futebol'
                ]),
                'is_popular' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Avançado',
                'slug' => 'advanced',
                'description' => 'Para quem tem bom gosto',
                'price_monthly' => 99.00,
                'price_semiannual' => 500.00,
                'price_annual' => 990.00,
                'screens' => 10,
                'features' => json_encode([
                    'Todos os benefícios do plano intermediário',
                    '10 telas por usuário',
                    'Cursos de Alemão e Francês',
                    'PayPerView 24/7',
                    'Tela 4K e 8K',
                    'Veja programas inéditos',
                    'IA para recomendação de filmes',
                    'Treinamento incluído',
                    'Acesso Ilimitado'
                ]),
                'is_popular' => false,
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            DB::table('plans')->insert($plan);
        }
    }
}
