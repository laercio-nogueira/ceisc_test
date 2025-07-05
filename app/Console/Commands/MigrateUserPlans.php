<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserPlan;
use Illuminate\Console\Command;
use Carbon\Carbon;

class MigrateUserPlans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:user-plans';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate existing user plans to the new user_plans table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting migration of user plans...');

        $usersWithPlans = User::whereNotNull('plan_id')->get();

        if ($usersWithPlans->isEmpty()) {
            $this->info('No users with plans found to migrate.');
            return;
        }

        $bar = $this->output->createProgressBar($usersWithPlans->count());
        $bar->start();

        foreach ($usersWithPlans as $user) {
            $status = 'active';
            if ($user->plan_expires_at) {
                if ($user->plan_expires_at->isPast()) {
                    $status = 'expired';
                }
            }

            UserPlan::create([
                'user_id' => $user->id,
                'plan_id' => $user->plan_id,
                'status' => $status,
                'started_at' => $user->created_at,
                'expires_at' => $user->plan_expires_at,
                'billing_period' => 'monthly',
                'amount_paid' => 0,
                'notes' => 'Migrado da estrutura antiga'
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Migration completed successfully!');
        $this->info("Migrated {$usersWithPlans->count()} user plans.");
    }
}
