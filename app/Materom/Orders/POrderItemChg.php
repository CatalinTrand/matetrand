<?php
/**
 * Created by PhpStorm.
 * User: Radu
 * Date: 12.10.2018
 * Time: 11:36
 */

namespace App\Materom\Orders;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class POrderItemChg
{

    public $ebeln;      // purchase order
    public $ebelp;      // purchase order item
    public $cdate;      // change datetime

    public $internal;   // internal change/message
    public $ctype;      // change type
                        // A-accepted
                        // X-rejected
                        // M-material code change
                        // Q-quantity
                        // P-price
                        // D-delivery date
                        // E-message
                        // S-split
    public $stage;      // changing user stage
    public $cuser;      // changing user
    public $cuser_name; // changing user name
    public $duser;      // target user
    public $oldval;     // old value
    public $newval;     // new value
    public $reason;     // reason/message text
    public $acknowledged; // message was acknowledged

    public $oebeln;     // originating purchase number (split)
    public $oebelp;     // originating purchase item (split)

    // computed/determined fields
    public $vbeln;
    public $text;       // message text

    function __construct($pitemchg)
    {
        $this->ebeln = $pitemchg->ebeln;
        $this->ebelp = $pitemchg->ebelp;
        $this->internal = $pitemchg->internal;
        $this->stage = $pitemchg->stage;
        $this->cdate = $pitemchg->cdate;
        $this->ctype = $pitemchg->ctype;
        $this->cuser = $pitemchg->cuser;
        $this->cuser_name = $pitemchg->cuser_name;
        $this->duser = $pitemchg->duser;
        $this->oldval = $pitemchg->oldval;
        $this->newval = $pitemchg->newval;
        $this->reason = $pitemchg->reason;
        $this->oebeln = $pitemchg->oebeln;
        $this->oebelp = $pitemchg->oebelp;
        $this->acknowledged = $pitemchg->acknowledged;

        if (DB::table("users")->where("id", $this->cuser)->value("role") == "Furnizor" &&
            Auth::user()->role == "CTV")
            $this->cuser_name = __("Vendor") . " " . $this->cuser;
    }

    public function fill($pitem)
    {

        $this->vbeln = $pitem->vbeln;
        $texts = array();
        $texts[] = __("Reason 1");
        $texts[] = __("Reason 2");
        $texts[] = __("Miscellaneous");
        $texts[] = __("Other");

        switch ($this->ctype) {
            case "A":
                $this->text = __("Accepted");
                if ($this->oldval == 'F') $this->text = __("Cancellation not accepted by vendor");
                elseif ($this->stage == 'Z' && $this->oldval == 'C') $this->text = __("Proposal accepted");
                elseif ($this->stage == 'Z' && $this->oldval == 'U') $this->text = __("Split request accepted");
                break;
            case "T":
                $this->text = __("Acceptance requested");
                break;
            case "X":
                $this->text = __("Rejected");
                if ($this->oldval == 'G') $this->text = __("Cancellation accepted by vendor");
                elseif (($this->oldval != null) && ($this->oldval != '0') && ($this->oldval != 'C'))
                    $this->text = __("Rejected") . " (" . $texts[intval($this->oldval) - 1] . ")";
                elseif ($this->stage == 'Z' && $this->oldval == 'C') $this->text = __("Proposal rejected");
                elseif ($this->stage == 'Z' && $this->oldval == 'W') $this->text = __("Split request rejected");
                break;
            case "R":
                $this->text = __("Rejection requested");
                if ($this->oldval == 'F') $this->text = __("Cancellation requested after initial approval");
                elseif ($this->oldval == 'G') $this->text = __("Cancellation requested to vendor");
                elseif (($this->oldval != null) && ($this->oldval != '0') && ($this->oldval != 'C'))
                    $this->text = __("Rejection requested") . " (" . $texts[intval($this->oldval) - 1] . ")";
                break;
            case "O":
                $this->text = __("Proposal issued");
                break;
            case "M":
                $this->text =  __("Material code modified from") . " " . $this->oldval . " " . __("to") . " " . $this->newval;
                break;
            case "Q":
                $this->text = __("Quantity modified from") . " " . $this->oldval . " " . __("to") . " " . $this->newval;
                break;
            case "P":
                $this->text = __("Price modified from") . " " . $this->oldval . " " . __("to") . " " . $this->newval;
                break;
            case "D":
                $this->text = __("Delivery date modified from") . " " . $this->oldval . " " . __("to") . " " . $this->newval;
                break;
            case "E":
                $this->text = __("Message to ") . $this->duser;
                break;
            case "S":
                $this->text = __("Item split proposed");
                break;
        }

    }

}