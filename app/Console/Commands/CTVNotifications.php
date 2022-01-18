<?php
/**
 * Created by PhpStorm.
 * User: Radu
 * Date: 16.12.2021
 * Time: 22:59
 */

namespace App\Console\Commands;

use App\Materom\Data;
use App\Materom\Mailservice;
use App\Materom\RFCData;
use App\Materom\SAP;
use App\Materom\System;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CTVNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'materom:ctvnotifications {ctv?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends notifications to one or all CTVs';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        System::init("200");

        $ctv = $this->argument("ctv");
        if ($ctv != null) {
            if (!DB::table("users")->where([["id", "=", $ctv], ["role", "=", "CTV"], ["sap_system", "=", System::$system]])->exists()) {
                $this->error(__("Specified user does not exist in the given system or is not a CTV"));
                return;
            }
        }

        Mailservice::sendCTVNotifications($ctv);
        $this->info("CTV notifications mails (if any) have been sent");
    }

}