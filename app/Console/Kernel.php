<?php

namespace App\Console;

use App\Materom\Data;
use App\Materom\SAP\MasterData;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

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
        $schedule->call(function () {

            // refresh internal cache
            DB::delete("delete from pitems_cache");
            DB::delete("delete from porders_cache");

            // refresh SAP table caches
            MasterData::refreshCustomerCache();
            MasterData::refreshVendorCache();
            MasterData::refreshPurchGroupsCache();

            // push finally processed orders to archive
            Data::performArchiving();

        })->dailyAt("01:00");
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
