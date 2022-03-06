<?php

namespace App\Console;

use App\Console\Commands\Maintenance;
use App\Materom\Data;
use App\Materom\SAP\MasterData;
use App\Materom\System;
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
        // Commands\CTVNotifications::class
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

            $pdate = now()->subDays(1);
            ini_set('memory_limit', '512M');
            System::init();

            Log::info("Daily cleanup job has started in system ". System::$system_name);
            // refresh internal cache
            DB::delete("delete from ". System::$table_pitems_cache ." where cache_date <= '$pdate'");
            DB::delete("delete from ". System::$table_porders_cache ." where cache_date <= '$pdate'");
            // refresh SAP table caches
            MasterData::refreshCustomerCache();
            MasterData::refreshVendorCache();
            MasterData::refreshPurchGroupsCache();
            MasterData::refreshZPretAdaos();
            // push finally processed orders to archive
            Data::performArchiving();
            // gather statistics
            Data::gatherStatistics();
            Maintenance::update_all_sap_client_agents();
            Maintenance::update_all_ctvs();
            Log::info("Daily cleanup job has ended in system ". System::$system_name);

            if (System::is300) {
                System::init("300");
                Log::info("Daily cleanup job has started in system ". System::$system_name);
                // refresh internal cache
                DB::delete("delete from ". System::$table_pitems_cache ." where cache_date <= '$pdate'");
                DB::delete("delete from ". System::$table_porders_cache ." where cache_date <= '$pdate'");
                // refresh SAP table caches
                MasterData::refreshCustomerCache();
                MasterData::refreshVendorCache();
                MasterData::refreshPurchGroupsCache();
                MasterData::refreshZPretAdaos();
                // push finally processed orders to archive
                Data::performArchiving();
                // gather statistics
                Data::gatherStatistics();
                Log::info("Daily cleanup job has ended in system ". System::$system_name);

            }

        })->dailyAt(env("MATEROM_JOB_TIME", "03:00"));


        $schedule->command("materom:ctvreminders 200")->weekdays()->hourly()->between("09:00", "18:01");
        $schedule->command("materom:ctvnotifications")->weekdays()->hourly()->between("09:00", "18:01");

        $schedule->command("materom:refsupreminders 200")->weekdays()->hourly()->between("09:00", "18:01");
//        $schedule->command("materom:refsupreminders 300")->weekdays()->hourly()->between("09:00", "18:01");

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
