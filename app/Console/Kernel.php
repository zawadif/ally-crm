<?php

namespace App\Console;

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
        Commands\SchedulePoolMatch::class,
        Commands\SchedulePreMatch::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        if (env('IS_WEEK_TO_HOUR') == true) {

            $schedule->command('schedule-pool:match')->everyMinute();
            $schedule->command('schedule-pre:match')->everyMinute();
        } else {
            $schedule->command('schedule-pool:match')->daily();
            $schedule->command('schedule-pre:match')->daily();
        }

        // Example code.
        // $schedule->command('schedule-pool:match')
        // ->emailOutputTo('zainulabdeen@techswivel.com');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
