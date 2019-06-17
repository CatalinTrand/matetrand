<?php
/**
 * Created by PhpStorm.
 * User: Radu
 * Date: 05.06.2019
 * Time: 20:21
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

class SupReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'materom:supreminders {system} {user?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends open orders/positions reminder one or all Suppliers';

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
        $system = $this->argument("system");
        if ($system == null) {
            $this->error(__("Please specify the system to send the reminders for"));
            return;
        }
        if (("X".$system != "X200") && ("X".$system != "X300")) {
            $this->error(__("Wrong system specified"));
            return;
        }
        System::init($system);

        $user = $this->argument("user");
        if ($user != null) {
            if (!DB::table("users")->where([["id", "=", $user], ["role", "=", "Furnizor"], ["sap_system", "=", System::$system]])->exists()) {
                $this->error(__("Specified user does not exist in the given system or is not a Supplier"));
                return;
            }
        }

        $info = ["mode" => 2, "user" => $user];
        Mailservice::sendRefSupReminderMail($info);
        $this->info("Supplier reminder mails (if any) have been sent");
    }

}