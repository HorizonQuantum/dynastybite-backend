<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Tempat menjadwalkan task, contoh:
        $schedule->call(function () {
            \App\Models\Order::where('is_paid', false)
                ->where('expired_at', '<', now())
                ->delete();
        })->everyMinute();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
