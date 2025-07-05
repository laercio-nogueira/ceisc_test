<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Plan;
use App\Models\UserPlan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class UserPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $plans = Plan::all();

        if ($users->isEmpty() || $plans->isEmpty()) {
            $this->command->info('No users or plans found. Skipping user plans seeding.');
            return;
        }

        // Criar alguns planos de usuário para teste
        $testData = [
            [
                'user' => $users->first(),
                'plan' => $plans->first(), // Básico
                'status' => 'active',
                'billing_period' => 'monthly',
                'amount_paid' => 29.00,
                'expires_at' => Carbon::now()->addMonth(),
            ],
            [
                'user' => $users->first(),
                'plan' => $plans->where('slug', 'intermediate')->first(), // Intermediário
                'status' => 'expired',
                'billing_period' => 'semiannual',
                'amount_paid' => 300.00,
                'expires_at' => Carbon::now()->subDays(5),
            ],
        ];

        foreach ($testData as $data) {
            if ($data['plan']) {
                UserPlan::create([
                    'user_id' => $data['user']->id,
                    'plan_id' => $data['plan']->id,
                    'status' => $data['status'],
                    'started_at' => Carbon::now()->subDays(30),
                    'expires_at' => $data['expires_at'],
                    'billing_period' => $data['billing_period'],
                    'amount_paid' => $data['amount_paid'],
                    'notes' => "Plano de teste - {$data['plan']->name}"
                ]);
            }
        }

        $this->command->info('User plans seeded successfully!');
    }
}
