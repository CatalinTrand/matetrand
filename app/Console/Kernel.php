<?php

namespace App\Console;

use App\Materom\Data;
use App\Materom\SAP\MasterData;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\SendMailPurchOrder',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {

            Log::info("Daily cleanup job has started");

            // refresh internal cache
            $pdate = now()->subDays(1);
            DB::delete("delete from pitems_cache where cache_date <= '$pdate'");
            DB::delete("delete from porders_cache where cache_date <= '$pdate'");

            // refresh SAP table caches
            MasterData::refreshCustomerCache();
            MasterData::refreshVendorCache();
            MasterData::refreshPurchGroupsCache();

            // push finally processed orders to archive
            Data::performArchiving();

            Log::info("Daily cleanup job has ended");

        })->dailyAt("03:00");
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
