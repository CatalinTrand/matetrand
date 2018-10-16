<?php
/**
 * Created by PhpStorm.
 * User: Radu
 * Date: 12.10.2018
 * Time: 11:36
 */

namespace App\Materom\Orders;


use App\Materom\Orders;
use App\Materom\SAP\MasterData;
use Illuminate\Support\Facades\Auth;

class POrderItem
{

    public $ebeln;       // EKPO-EBELN
    public $ebelp;       // EKPO-EBELP
    public $changes;     // all changes belonging to this

    // purchase order item related information
    public $vbeln;       // EKKN-VBELN
    public $posnr;       // EKKN-VBELP
    public $idnlf;       // EKPO-IDNLF
    public $mtext;       // EKPO-TXZ01
    public $mfrnr;       // EKPO-MFRNR (VBAP-ZZMFRNR)
    public $qty;         // EKPO-MENGE
    public $qty_uom;     // EKPO-MEINS
    public $lfdat;       // EKET-EINDT
    public $purch_price; // EKPO-NETPR
    public $purch_curr;  // EKPO-WAERS
    public $purch_prun;  // EKPO-PEINH
    public $purch_puom;  // EKPO-BPRME
    public $stage;       // workflow position F/R/C/A-released/D-delivered/X-closed
    public $status;      // A-accepted, X-rejected,
                         // T-tentatively accepted,
                         // R-tentatively rejected
    public $changed;  // 0=no, 1=yes

    // sales order position related information
    public $sales_price; // VBAP-NETPR
    public $sales_curr;  // VBPA-WAERK
    public $sales_prun;  // VBAP-KPEIN
    public $sales_puom;  // VBAP-KPEIN
    public $kunnr;       // VBAK-KUNNR
    public $shipto;      // VBPA-KUNNR WE
    public $ctv;         // VBAK-ERNAM
    public $ctv_name;    // just in case it is deleted from USR02

    // computed/determined fields
    public $sorder;      // sales order to be displayed
    public $kunnr_name;
    public $shipto_name;

    // status icons
    public $info;     // 0=empty, 1=new item, 2=warning, 3=critical, 4=new message
    public $owner;    // 0=no, 1=direct, 2=indirect
    // changed:  $this->changed
    public $accepted; // $this->status = A
    public $rejected; // $this->status = X
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

    // flags
    public $can_change_matnr;         // 0=no, 1=yes
    public $matnr_changed;            // 0=no, 1=yes
    public $can_change_quantity;      // 0=no, 1=yes
    public $quantity_changed;         // 0=no, 1=yes
    public $can_change_price;         // 0=no, 1=yes
    public $price_changed;            // 0=no, 1=yes
    public $can_change_delivery_date; // 0=no, 1=yes
    public $delivery_date_changed;    // 0=no, 1=yes
    public $can_split_position;       // 0=no, 1=yes
    public $position_splitted;        // 0=no, 1=yes

    function __construct($pitem)
    {
        $this->ebeln = $pitem->ebeln;
        $this->ebelp = $pitem->ebelp;
        $this->status = $pitem->status;
        $this->changed = $pitem->changed;
        $this->vbeln = $pitem->vbeln;
        $this->posnr = $pitem->posnr;
        $this->idnlf = $pitem->idnlf;
        $this->mtext = $pitem->mtext;
        $this->mfrnr = $pitem->mfrnr;
        $this->qty = $pitem->qty;
        $this->qty_uom = $pitem->qty_uom;
        $this->lfdat = $pitem->lfdat;
        $this->purch_price = $pitem->purch_price;
        $this->purch_curr = $pitem->purch_curr;
        $this->purch_prun = $pitem->purch_prun;
        $this->purch_puom = $pitem->purch_puom;
        $this->sales_price = $pitem->sales_price;
        $this->sales_curr = $pitem->sales_curr;
        $this->sales_prun = $pitem->sales_prun;
        $this->sales_puom = $pitem->sales_puom;
        $this->kunnr = $pitem->kunnr;
        $this->shipto = $pitem->shipto;
        $this->ctv = $pitem->ctv;
        $this->ctv_name = $pitem->ctv_name;
        $this->changes = array();
    }

    public function appendChange($pitemchg)
    {
        $this->changes[$pitemchg->cdate] = $pitemchg;
    }

    public function fill()
    {
        $this->sorder = $this->vbeln;
        if (Auth::user()->role == 'Furnizor') {
            $this->sales_price = "";
            $this->sales_curr = "";
            $this->sales_prun = "";
            $this->sales_puom = "";
            $this->kunnr = "";
            $this->kunnr_name = "";
            $this->shipto = "";
            $this->shipto_name = "";
            $this->ctv = "";
            $this->ctv_name = "";
            if (!empty($this->sorder)) $this->sorder = Orders::salesorder;
        } else {
            $this->kunnr_name = MasterData::getKunnrName($this->kunnr, 2);
            $this->shipto_name = MasterData::getKunnrName($this->shipto, 2);
        }

        $this->info = 0;
        $this->owner = 0;
        $this->accepted = 0;
        $this->rejected = 0;
        $this->inquired = 0;
        $this->accept = 1;
        $this->reject = 1;
        $this->inquire = 1;
    }


}