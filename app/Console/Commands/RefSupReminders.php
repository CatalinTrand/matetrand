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

class RefSupReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'materom:refsupreminders {system} {userid?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends open orders/positions reminder to one or all References/Suppliers';

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

        $mode = 0;
        $userid = $this->argument("userid");
        if ($userid != null) {
            $user = DB::table("users")->where([["id", "=", $userid], ["sap_system", "=", System::$system]])->first();
            if ($user == null) {
                $this->error(__("Specified user does not exist in the given system"));
                return;
            }
            if ($user->role == "Referent") $mode = 1;
            elseif ($user->role == "Furnizor") $mode = 2;
            else {
                $this->error(__("Specified user does is neither a Reference nor a Supplier"));
                return;
            }
        }

        $info = ["mode" => $mode, "user" => $userid];
        Mailservice::sendRefSupReminderMail($info);
        $this->info("Reference/Supplier reminder mails (if any) have been sent");
    }

}