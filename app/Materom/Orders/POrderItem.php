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
use Illuminate\Support\Facades\DB;

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
    // status fields
    public $changed;     // 0=no, 1=yes
    public $status;      // A-accepted, X-rejected,
                         // T-tentatively accepted,
                         // R-tentatively rejected

    // sales order position related information
    public $sales_price; // VBAP-NETPR
    public $sales_curr;  // VBPA-WAERK
    public $sales_prun;  // VBAP-KPEIN
    public $sales_puom;  // VBAP-KPEIN
    public $kunnr;       // VBAK-KUNNR
    public $shipto;      // VBPA-KUNNR WE
    public $ctv;         // VBAK-ERNAM
    public $ctv_name;    // just in case it is deleted from USR02
    public $deldate;    // inbound delivery confirmation date
    public $delqty;     // inbound qty
    public $grdate;     // goods receipt date
    public $grqty;      // goods receipt quantity
    public $gidate;     // goods issue quantity

    // computed/determined fields
    public $sorder;      // sales order to be displayed
    public $kunnr_name;
    public $shipto_name;

    // external representations
    public $x_quantity;
    public $x_delivery_date;
    public $x_purchase_price;
    public $x_sales_price;

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
    public $matnr_changeable;         // 0=no, 1=yes
    public $matnr_changed;            // 0=no, 1=yes
    public $quantity_changeable;      // 0=no, 1=yes
    public $quantity_changed;         // 0=no, 1=yes
    public $price_changeable;         // 0=no, 1=yes
    public $price_changed;            // 0=no, 1=yes
    public $delivery_date_changeable; // 0=no, 1=yes
    public $delivery_date_changed;    // 0=no, 1=yes
    public $position_splittable;      // 0=no, 1=yes
    public $position_splitted;        // 0=no, 1=yes

    function __construct($pitem)
    {
        $this->ebeln = $pitem->ebeln;
        $this->ebelp = $pitem->ebelp;
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
        $this->deldate = $pitem->deldate;
        $this->delqty = $pitem->delqty;
        $this->grdate = $pitem->grdate;
        $this->grqty = $pitem->grqty;
        $this->gidate = $pitem->gidate;
        $this->changed = $pitem->changed;
        $this->stage = $pitem->stage;
        $this->status = $pitem->status;
        $this->changes = array();
    }

    public function appendChange($pitemchg)
    {
        $this->changes[$pitemchg->cdate] = $pitemchg;
    }

    public function fill($porder)
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
            if ($this->sorder != Orders::stockorder) $this->sorder = Orders::salesorder;
        } else {
            $this->kunnr_name = MasterData::getKunnrName($this->kunnr, 2);
            $this->shipto_name = MasterData::getKunnrName($this->shipto, 2);
        }

        $this->info = 0;

        $this->owner = 0;
        if (Auth::user()->role == 'Furnizor') {
            if ($this->stage == 'F') $this->owner = 1;
        } elseif (Auth::user()->role == 'Referent') {
            if ($this->stage == 'R') $this->owner = 1;
            elseif ($this->stage == 'F') {
                $suppliers = DB::select("select distinct users.id from users ".
                    " join users_ref using (id)" .
                    " where users.role = 'Furnizor' and users.lifnr = '$porder->lifnr' ".
                    "       and users_ref.refid = '" . Auth::user()->id . "'" .
                    " order by id");
                foreach ($suppliers as $supplier) {
                    $manufacturers = DB::select("select distinct mfrnr from materom_srm.users_sel where id = '$supplier->id'");
                    if (empty($manufacturers)) {$this->owner = 2; break;}
                    if (isset(array_flip($manufacturers)[$porder->mfrnr])) {$this->owner = 2; break;}
                }
            }
        } elseif (Auth::user()->role == 'CTV') {
            if ($this->ctv == Auth::user()->sapuser) $this->owner = 1;
        }

        $this->accepted = 0;
        $this->rejected = 0;
        $this->inquired = 0;
        $this->accept = 1;
        $this->reject = 1;
        $this->inquire = 1;

        $this->matnr_changeable = 1;
        $this->matnr_changed = 0;
        $this->quantity_changeable = 1;
        $this->quantity_changed = 0;
        $this->price_changeable = 1;
        $this->price_changed = 0;
        $this->delivery_date_changeable = 1;
        $this->delivery_date_changed = 0;
        $this->position_splittable = 1;
        $this->position_splitted = 0;

        $this->x_delivery_date = substr($this->lfdat, 0, 10);
        $this->x_quantity = trim($this->qty) . " " . trim($this->qty_uom);
        $this->x_purchase_price = trim($this->purch_price) . " " . trim($this->purch_curr);
        if ((Auth::user()->role == 'Furnizor') || ($this->vbeln == Orders::stockorder)) $this->x_sales_price = "";
        else $this->x_sales_price = trim($this->sales_price) . " " . trim($this->sales_curr);
    }


}