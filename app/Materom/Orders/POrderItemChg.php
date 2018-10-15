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

    public $internal;   // internal change/message
    public $ctype;      // change type
                        // A-accepted
                        // X-rejected
                        // M-material code change
                        // Q-quantity
                        // P-price
                        // D-delivery date
                        // M-message
                        // S-split
    public $stage;      // changing user stage
    public $cuser;      // changing user
    public $cuser_name; // changing user name
    public $oldval;     // old value
    public $newval;     // new value
    public $reason;     // reason/message text

    public $oebeln;     // originating purchase number (split)
    public $oebelp;     // originating purchase item (split)

    // computed/determined fields
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
        $this->oldval = $pitemchg->oldval;
        $this->newval = $pitemchg->newval;
        $this->reason = $pitemchg->reason;
        $this->oebeln = $pitemchg->oebeln;
        $this->oebelp = $pitemchg->nebelp;
        $this->acknowledged = $pitemchg->acknowledged;
    }

    public function fill() {

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