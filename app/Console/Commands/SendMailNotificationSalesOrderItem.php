<?php

namespace App\Console\Commands;

use App\Materom\Mailservice;
use App\Materom\SAP;
use App\Materom\System;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendMailNotificationSalesOrderItem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'materom:sendmail2 {userid} {sorder} {soitem}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify a user by mail about a cancelled sales order item';

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
        $userid = $this->argument("userid");
        if ($userid == null) {
            $this->error(__("Please specify the user id to send the mail to"));
            return;
        }
        $user = User::where("id", "=", $userid)->first();
        if ($user == null) {
            $this->error(__("The specified user id does not exist"));
            return;
        }
        System::init($user->sap_system);

        $sorder = $this->argument("sorder");
        if ($sorder == null) {
            $this->error(__("Please specify the sales order for which the mail is being sent for"));
            return;
        }
        $sorder = SAP::alpha_input($sorder);

        $soitem = $this->argument("soitem");
        if ($soitem == null) {
            $this->error(__("Please specify the sales order item for which the mail is being sent for"));
            return;
        }
        $soitem = str_pad($soitem, 6, "0", STR_PAD_LEFT);

        if (!DB::table(System::$table_pitems)->where(["vbeln" => $sorder, "posnr" => $soitem])->exists()) {
            $this->error(__("The specified sales order item does not exist ($sorder/$soitem)"));
            return;
        }
        Mailservice::sendSalesOrderNotification($user->id, $sorder, $soitem, true);
        $this->info("The mail was successfully sent to " . $user->email);
    }
}
