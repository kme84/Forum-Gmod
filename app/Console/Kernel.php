<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Storage;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //$schedule->command('inspire')->hourly();
 
        // Удаление изображений из папки temp старше 1 часа
        $schedule->call(function () { 
            $files = Storage::disk('public')->allFiles('temp');

            foreach ($files as $key => $value) {
                $time = Storage::disk('public')->lastModified($value);
                if(now()->timestamp-$time > 3600)
                {
                    Storage::disk('public')->delete($value);
                }
            }

        })->everyMinute();
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
