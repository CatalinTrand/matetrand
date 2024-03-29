<?php
/**
 * Created by PhpStorm.
 * User: Radu
 * Date: 12.10.2018
 * Time: 11:35
 */

namespace App\Materom\Orders;


use App\Materom\SAP\MasterData;
use App\Materom\System;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class POrder
{
    public $ebeln;    // EKKO-EBELN
    public $items;    // visible items belonging to this
    public $allitems; // all items belonging to this

    // purchase order related information
    public $bukrs;    // EKKO-BUKRS
    public $lifnr;    // EKKO-LIFNR
    public $ekgrp;    // EKKO-EKGRP
    public $bedat;    // EKKO-BEDAT
    public $bedat_out;// EKKO-BEDAT
    public $erdat;    // EKKO-ERDAT
    public $erdat_out;// EKKO-ERDAT
    public $curr;     // EKKO-WAERS
    public $fxrate;   // EKKO-WKURS
    public $wtime;    // processing warning date
    public $ctime;    // processing critical date
    // status fields
    public $changed;  // 0=no, 1=direct, 2=indirect
    public $status;   // empty/Z

    public $qty_ordered;   // header status
    public $qty_delivered; // header status
    public $qty_open;      // header status
    public $qty_invoiced;  // header status

    // computed/determined fields
    public $lifnr_name;
    public $ekgrp_name;
    public $salesorders; // all sales orders belonging to this
    public $klabc;       // highest classification

    // status icons
    public $info;     // 0=empty, 1=new order, 2=warning, 3=critical, 4=new message
    public $owner;    // 0=no, 1=direct, 2=indirect
    public $accepted; // 0=no, 1=direct, 2=wip
    public $rejected; // 0=no, 1=direct, 2=wip
    public $inquired; // 0=no, 1=tentatively accepted, 2=rejected, 3=simple message
    public $inq_reply; // 0=no reply, 1=with reply

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
        $this->bukrs = $porder->bukrs;
        $this->lifnr = $porder->lifnr;
        $this->ekgrp = $porder->ekgrp;
        $this->bedat = $porder->bedat;
        $this->bedat_out = (new Carbon($this->bedat))->format("Y-m-d");
        $this->erdat = $porder->erdat;
        $this->erdat_out = (new Carbon($this->erdat))->format("Y-m-d H:i:s");
        $this->curr = $porder->curr;
        $this->fxrate = $porder->fxrate;
        $this->wtime = $porder->wtime;
        $this->ctime = $porder->ctime;
        $this->changed = $porder->changed;
        $this->status = $porder->status;
        $this->qty_ordered = $porder->qty_ordered;
        $this->qty_delivered = $porder->qty_delivered;
        $this->qty_open = $porder->qty_open;
        $this->qty_invoiced = $porder->qty_invoiced;
        $this->items = array();
        $this->klabc = "";
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
            if (!empty($item->klabc))
                if (empty($this->klabc) || $item->klabc < $this->klabc) $this->klabc = $item->klabc;
        }
        $this->info = 0;
        if (empty($this->status)) $this->info = 1;
        if (($this->status != 'A') && ($this->status != 'X') && ($this->status != 'Z')) {
            if ($this->wtime < Carbon::now()) $this->info = 2;
            if ($this->ctime < Carbon::now()) $this->info = 3;
        }

        $history = Session::get("filter_history");
        if (!isset($history)) $history = 1;
        else $history = intval($history);

        $this->allitems = array();
        $items_table = $history == 1 ? System::$table_pitems : System::$table_pitems . "_arch";
        $items = DB::select("select * from $items_table where ebeln = '$this->ebeln' order by ebelp");
        foreach($items as $item) {
            if (isset($this->items[$item->ebelp])) {
                $pitem = $this->items[$item->ebelp];
                if ($pitem->info == 0) $this->info = 0;
            }
            else {
                $pitem = new POrderItem($item);
                $pitem->fill($this);
            }
            $this->allitems[$item->ebelp] = $pitem;
        }

        $this->owner = 0;
        if ($this->items != null) {
            $this->owner = reset($this->items)->owner;
            if ($this->owner != 0) {
                foreach ($this->items as $item) {
                    if ($item->info == 4) {$this->info = 4; break;}
                    if ($item->info == 5) {$this->info = 5; break;}
                }
            }
        }

        $this->accepted = 0;
        $this->rejected = 0;
        $this->inquired = 0;
        $this->accept = 0;
        $this->reject = 0;
        $this->inquire = 0;


        $count_accepted = 0;
        $count_rejected = 0;
        foreach ($this->allitems as $item) {
            if ($item->accepted == 1) {
                $count_accepted++;
                $this->accepted = 2;
            }
            if ($item->rejected == 1) {
                $count_rejected++;
                $this->rejected = 2;
            }
            if ($item->inquired == 1)
                if (($this->inquired == 0) || ($this->inquired == 1)) $this->inquired = 1;
                elseif ($this->inquired == 2) $this->inquired = 3;
            if ($item->inquired == 2)
                if (($this->inquired == 0) || ($this->inquired == 2)) $this->inquired = 2;
                elseif ($this->inquired == 1) $this->inquired = 3;

            if ($item->accept == 1) {
                if (($this->accept == 0) || ($this->accept == 1)) $this->accept = 1;
                else $this->accept = -1;
            }
            if ($item->accept == 2) {
                if (($this->accept == 0) || ($this->accept == 2)) $this->accept = 2;
                else $this->accept = -1;
            }
            if ($item->accept == 3) {
                if (($this->accept == 0) || ($this->accept == 3)) $this->accept = 3;
                else $this->accept = -1;
            }
            if ($item->reject == 1) $this->reject = 1;
        }
        if ($this->accept == -1) $this->accept = 0;
        if (count($this->allitems) == ($count_accepted + $count_rejected)) {
            if ($this->accepted == 2) $this->accepted = 1;
            if ($this->rejected == 2) $this->rejected = 1;
        }

        if ($history == 2) {
            $this->info = 0;
            $this->accept = 0;
            $this->reject = 0;
            $this->inquire = 0;
            $this->inquired = 0;
            $this->inq_reply = 0;
        }
    }
}