<?php
/**
 * Created by PhpStorm.
 * User: Radu
 * Date: 18.11.2018
 * Time: 19:51
 */

namespace App\Console\Commands;

use App\Materom\Data;
use App\Materom\SAP;
use App\Materom\System;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ArchivePurchaseOrderItem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'materom:archiveitem {system} {purch_order} {purch_item}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform unchecked archiving of a purchase order item';

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
            $this->error(__("Please specify the system to be maintained"));
            return;
        }
        if (("X".$system != "X200") && ("X".$system != "X300")) {
            $this->error(__("Wrong system specified"));
            return;
        }
        System::init($system);

        $purch_order = $this->argument("purch_order");
        if ($purch_order == null) {
            $this->error(__("Please specify the purchase order to be acrhived"));
            return;
        }
        $porder = DB::table(System::$table_porders)->where("ebeln", $purch_order)->first();
        if ($porder == null) {
            $this->error(__("The specified purchase order does not exist"));
            return;
        }
        $purch_item = $this->argument("purch_item");
        if ($purch_item == null) {
            $this->error(__("Please specify the purchase order item to be archived"));
            return;
        }
        $purch_item = trim($purch_item);
        if ($purch_item != "*") {
            $purch_item = str_pad($purch_item, 5, "0", STR_PAD_LEFT);
            $pitems = DB::table(System::$table_pitems)->where([["ebeln", "=", $porder->ebeln], ["ebelp", "=", $purch_item]])->get();
        } else
            $pitems = DB::table(System::$table_pitems)->where([["ebeln", "=", $porder->ebeln]])->get();
        if ($pitems == null) {
            $this->error(__("The specified purchase order item/s does/do not exist"));
            return;
        }

        $okcount = 0;
        foreach($pitems as $pitem) {
            $result = Data::archiveItem($pitem->ebeln, $pitem->ebelp);
            if ($result == "OK") $okcount++;
            $this->info("Archiving item " . SAP::alpha_output($pitem->ebelp). ": " . $result);
        }

        $this->info("Archiving ended (" . $okcount . "/". count($pitems) . " item(s) archived)");
    }

}