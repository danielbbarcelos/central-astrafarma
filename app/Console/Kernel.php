<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Http\Controllers\Mobile\VexSyncController as MobileVexSync;
use App\Http\Controllers\Erp\VexSyncController as ErpVexSync;
use \GuzzleHttp\Client as Guzzle;

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
        if(env('VEXSYNC') == true)
        {
            $schedule->call(function(){
                MobileVexSync::sincroniza();
            })->everyMinute();

            $schedule->call(function(){
                ErpVexSync::buscaPendencia();
            })->everyMinute();

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
