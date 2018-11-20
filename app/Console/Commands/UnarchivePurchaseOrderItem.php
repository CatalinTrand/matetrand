<?php
/**
 * Created by PhpStorm.
 * User: Radu
 * Date: 18.11.2018
 * Time: 20:37
 */

namespace App\Console\Commands;

use App\Materom\Data;
use App\Materom\SAP;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;


class UnarchivePurchaseOrderItem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'materom:unarchiveitem {purch_order} {purch_item}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform unarchiving of a purchase order item';

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
        $purch_order = $this->argument("purch_order");
        if ($purch_order == null) {
            $this->error(__("Please specify the purchase order to be acrhived"));
            return;
        }
        $porder = DB::table("porders_arch")->where("ebeln", $purch_order)->first();
        if ($porder == null) {
            $this->error(__("The specified archived purchase order does not exist"));
            return;
        }
        $purch_item = $this->argument("purch_item");
        if ($purch_item == null) {
            $this->error(__("Please specify the purchase order item to be acrhived"));
            return;
        }
        $purch_item = trim($purch_item);
        if ($purch_item != "*") {
            $purch_item = str_pad($purch_item, 5, "0", STR_PAD_LEFT);
            $pitems = DB::table("pitems_arch")->where([["ebeln", "=", $porder->ebeln], ["ebelp", "=", $purch_item]])->get();
        } else
            $pitems = DB::table("pitems_arch")->where([["ebeln", "=", $porder->ebeln]])->get();
        if ($pitems == null) {
            $this->error(__("The specified archived purchase order item/s does/do not exist"));
            return;
        }

        $okcount = 0;
        foreach($pitems as $pitem) {
            $result = Data::unArchiveItem($pitem->ebeln, $pitem->ebelp);
            if ($result == "OK") $okcount++;
            $this->info("Unarchiving item " . SAP::alpha_output($pitem->ebelp). ": " . $result);
        }

        $this->info("Unarchiving ended (" . $okcount . "/". count($pitems) . " item(s) archived)");
    }

}