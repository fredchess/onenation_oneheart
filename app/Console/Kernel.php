<?php

namespace App\Console;

use App\Models\AppSetting;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        if (AppSetting::get('auto_payout_enabled', false)) {
            $cmd = $schedule->command('onoh:auto-payout');
            if (AppSetting::get('auto_payout_interval') === 'weekly') {
                $cmd->weekly()->days([AppSetting::get('auto_payout_day_of_week', 1)]);
            } else {
                $cmd->monthlyOn((int) AppSetting::get('auto_payout_day_of_month', 1));
            }
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
