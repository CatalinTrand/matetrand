<?php
/**
 * Created by PhpStorm.
 * User: Radu
 * Date: 12.10.2018
 * Time: 11:35
 */

namespace App\Materom\Orders;


use App\Materom\SAP\MasterData;

class POrder
{
    public $ebeln;    // EKKO-EBELN
    public $items;    // all items belonging to this

    // purchase order related information
    public $lifnr;    // EKKO-LIFNR
    public $ekgrp;    // EKKO-EKGRP
    public $erdat;    // EKKO-ERDAT
    public $curr;     // EKKO-WAERS
    public $fxrate;   // EKKO-WKURS
    public $wtime;    // processing warning date
    public $ctime;    // processing critical date

    // computed/determined fields
    public $lifnr_name;
    public $ekgrp_name;
    public $salesorders; // all sales orders belonging to this

    // status icons
    public $info;     // 0=empty, 1=new order, 2=warning, 3=critical, 4=new message
    public $owner;    // 0=no, 1=direct, 2=indirect
    public $changed;  // 0=no, 1=direct, 2=indirect
    public $accepted; // 0=no, 1=direct, 2=indirect
    public $rejected; // 0=no, 1=direct, 2=indirect
    public $inquired; // 0=no, 1=tentatively accepted, 2=rejected, 3=simple message

    // buttons
    public $accept;   // 0-no, 1=display
    public $reject;   // 0=no, 1=display
    public $inquire;  // 0=no
                      // 1=send message to supplier
                      // 2=send message to reference
                      // 3=send message to sales representative
                      // 4=
                      // 8=

    function __construct($porder)
    {
        $this->ebeln = $porder->ebeln;
        $this->lifnr = $porder->lifnr;
        $this->ekgrp = $porder->ekgrp;
        $this->erdat = $porder->erdat;
        $this->curr = $porder->curr;
        $this->fxrate = $porder->fxrate;
        $this->nof = $porder->nof;
        $this->wtime = $porder->wtime;
        $this->ctime = $porder->ctime;
        $this->items = array();
    }

    public function appendItem($pitem)
    {
        $this->items[$pitem->ebelp] = $pitem;
    }

    public function fill()
    {
        $this->lifnr_name = MasterData::getLifnrName($this->lifnr, 2);
        $this->ekgrp_name = MasterData::getEkgrpName($this->ekgrp, 2);
        $this->salesorders = array();
        foreach ($this->items as $item) {
            $this->salesorders[$item->sorder] = $item->ebelp;
        }
    }
}