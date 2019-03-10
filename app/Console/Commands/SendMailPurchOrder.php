<?php

namespace App\Console\Commands;

use App\Materom\Mailservice;
use App\Materom\System;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendMailPurchOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'materom:sendmail {userid} {porder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify a vendor by mail about a new purchase order';

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

        $porder = $this->argument("porder");
        if ($porder == null) {
            $this->error(__("Please specify the purchase order for which the mail is being sent for"));
            return;
        }
        if (!DB::table(System::$table_porders)->where("ebeln", $porder)->exists()) {
            $this->error(__("The specified purchase order does not exist"));
            return;
        }
        Mailservice::sendNotification($user->id, $porder);
        $this->info("The mail was successfully sent to " . $user->email);
    }
}
