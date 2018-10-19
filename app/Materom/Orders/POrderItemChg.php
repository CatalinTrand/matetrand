<?php
/**
 * Created by PhpStorm.
 * User: Radu
 * Date: 12.10.2018
 * Time: 11:36
 */

namespace App\Materom\Orders;


class POrderItemChg
{

    public $ebeln;      // purchase order
    public $ebelp;      // purchase order item
    public $cdate;      // change datetime
    public $vbeln;

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
    public $oldval;     // old value
    public $newval;     // new value
    public $reason;     // reason/message text
    public $acknowledged; // message was acknowledged

    public $oebeln;     // originating purchase number (split)
    public $oebelp;     // originating purchase item (split)

    // computed/determined fields
    public $text;       // message text

    function __construct($pitemchg)
    {
        $this->ebeln = $pitemchg->ebeln;
        $this->vbeln = $pitemchg->vbeln;
        $this->ebelp = $pitemchg->ebelp;
        $this->internal = $pitemchg->internal;
        $this->stage = $pitemchg->stage;
        $this->cdate = $pitemchg->cdate;
        $this->ctype = $pitemchg->ctype;
        $this->cuser = $pitemchg->cuser;
        $this->cuser_name = $pitemchg->cuser_name;
        $this->oldval = $pitemchg->oldval;
        $this->newval = $pitemchg->newval;
        $this->reason = $pitemchg->reason;
        $this->oebeln = $pitemchg->oebeln;
        $this->oebelp = $pitemchg->oebelp;
        $this->acknowledged = $pitemchg->acknowledged;
    }

    public function fill($pitem) {

        switch ($this->ctype) {
            case "A":
                $this->text = __("Accepted");
                break;
            case "T":
                $this->text = __("Accepted after change");
                break;
            case "X":
                $this->text = __("Rejected");
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
                $this->text = $this->reason;
                break;
            case "S":
                $this->text = __("Originating from a split");
                break;
        }

    }

    public function changed()
    {
        if ((($this->stage == 'F') ||
             ($this->stage == 'R'))
            &&
            (($this->ctype == 'M') ||
             ($this->ctype == 'Q') ||
             ($this->ctype == 'P') ||
             ($this->ctype == 'D') ||
             ($this->ctype == 'S'))) return 1;
        return 0;
    }
}