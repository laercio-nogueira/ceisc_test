<?php

namespace App\Console\Commands;

use App\Models\UserPlan;
use Illuminate\Console\Command;
use Carbon\Carbon;

class UpdateExpiredPlans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plans:update-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update expired plans status to expired';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired plans...');

        $expiredPlans = UserPlan::where('status', 'active')
                                ->whereNotNull('expires_at')
                                ->where('expires_at', '<', Carbon::now())
                                ->get();

        if ($expiredPlans->isEmpty()) {
            $this->info('No expired plans found.');
            return;
        }

        $this->info("Found {$expiredPlans->count()} expired plans.");

        $bar = $this->output->createProgressBar($expiredPlans->count());
        $bar->start();

        foreach ($expiredPlans as $userPlan) {
            $userPlan->update(['status' => 'expired']);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Expired plans updated successfully!');
    }
}
