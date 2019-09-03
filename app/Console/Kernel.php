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
        'App\Console\Commands\Download',
        'App\Console\Commands\MailSend',
        'App\Console\Commands\Cleanup',
        'App\Console\Commands\UserInfo',
//        'App\Console\Commands\UserWareHouse',
    ];


    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('command:Download')->everyThirtyMinutes();
        $schedule->command('command:MailSend')->everyThirtyMinutes();
        $schedule->command('command:Cleanup')->daily();
        $schedule->command('command:UserInfo')->twiceDaily(8, 12);
//        $schedule->command('command:UserWareHouse')->twiceDaily(8, 12);
        // $schedule->command('inspire')
        //          ->hourly();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
