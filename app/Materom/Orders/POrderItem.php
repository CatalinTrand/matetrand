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
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

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
    public $stage;       // workflow position F/R/C/Z=out of flow
    public $pstage;      // previous workflow position F/R/C/Z=out of flow
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
    public $deldate;     // inbound delivery confirmation date
    public $delqty;      // inbound qty
    public $grdate;      // goods receipt date
    public $grqty;       // goods receipt quantity
    public $gidate;      // goods issue quantity

    // computed/determined fields
    public $sorder;      // sales order to be displayed
    public $kunnr_name;
    public $shipto_name;
    public $wtime;    // processing warning date
    public $ctime;    // processing critical date

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
    public $inquired; // 0=no, 1=tentatively accepted, 2=tentatively rejected, 3=simple message
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
        $this->pstage = $pitem->pstage;
        $this->status = $pitem->status;
        $this->nof = $pitem->nof;
        $this->changes = array();
    }

    public function appendChange($pitemchg)
    {
        $this->changes[$pitemchg->cdate] = $pitemchg;
    }

    public function fill($porder)
    {

        $history = Session::get("filter_history");
        if (!isset($history)) $history = 1;
        else $history = intval($history);

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
        $this->wtime = $porder->wtime;
        $this->ctime = $porder->ctime;

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
                    $manufacturers = DB::select("select distinct mfrnr from users_sel where id = '$supplier->id'");
                    if (empty($manufacturers)) {$this->owner = 2; break;}
                    if (isset(array_flip($manufacturers)[$porder->mfrnr])) {$this->owner = 2; break;}
                }
            }
        } elseif (Auth::user()->role == 'CTV') {
            if ($this->stage == 'C') {
                if (DB::table("user_agent_clients")->where([["id", "=", Auth::user()->id],
                    ["kunnr", "=", $this->kunnr]])->exists())
                    $this->owner = 1;
            }
        }

        $this->info = 0;
        if ($this->owner != 0) {
            if (empty($this->status) && $this->nof) $this->info = 4;
        }
        if (($this->status != 'A') && ($this->status != 'X')) {
            if ($this->wtime < Carbon::now()) $this->info = 2;
            if ($this->ctime < Carbon::now()) $this->info = 3;
        }

        $this->accepted = 0;
        if ($this->status == 'A' ) $this->accepted = 1;

        $this->rejected = 0;
        if (Auth::user()->role == 'Furnizor') {
            if (($this->status == 'X' || $this->status == 'R') && ($this->pstage != 'Z')) $this->rejected = 1;
        } else {
            if ($this->status == 'X') $this->rejected = 1;
        }

        $this->inquired = 0;
        if (Auth::user()->role == 'Furnizor') {
            if ($this->status == 'T') $this->inquired = 1;
            // message = yellow
        } else {
            if ($this->status == 'T') {
                $this->inquired = 1;
                if (($this->owner != 0) || (Auth::user()->role == "Administrator")) $this->inq_reply = 1;
            }
            if ($this->status == 'R') {
                $this->inquired = 2;
                if (($this->owner != 0) || (Auth::user()->role == "Administrator")) $this->inq_reply = 1;
            }
        }

        $this->matnr_changed = 0;
        $this->quantity_changed = 0;
        $this->price_changed = 0;
        $this->delivery_date_changed = 0;
        $this->position_splitted = 0;

        $first = true;
        foreach ($this->changes as $itemchg) {
            if ($itemchg->ctype == 'E') continue;
            if (($itemchg->stage == Auth::user()->role[0]) && ($itemchg->acknowledged == 0)) {
                // $this->inquired = 3;
            }
            if ($itemchg->ctype == "M") $this->matnr_changed = 1;
            if ($itemchg->ctype == "Q") $this->quantity_changed = 1;
            if ($itemchg->ctype == "P") $this->price_changed = 1;
            if ($itemchg->ctype == "D") $this->delivery_date_changed = 1;
            if ($itemchg->ctype == "S") $this->position_splitted = 1;

            if ($first && ($itemchg->ctype == "S") && ($this->inquired == 1) && ($this->inq_reply == 1))
                $this->inquired = 3;

            if ($first && ($itemchg->acknowledged == 0)
                && (Auth::user()->role == 'Furnizor')
                && ($this->pstage != ' ') && ($this->pstage != '') && ($this->pstage != 'F')) {
                if ($itemchg->ctype == "A") $this->info = 4;
                if ($itemchg->ctype == "X") $this->info = 5;
            }
            $first = false;
        }

        $this->accept = 0;
        $this->reject = 0;
        $this->inquire = 1;

        $this->matnr_changeable = 0;
        $this->quantity_changeable = 0;
        $this->price_changeable = 0;
        $this->delivery_date_changeable = 0;
        $this->position_splittable = 0;
        if ($history == 1) {
            if (Auth::user()->role == 'Furnizor') {
                if ($this->stage == "F" && ($this->owner == 1)) {
                    if (empty(trim($this->status))) {
                        if ($this->position_splitted == 0) {
                            if ($this->changed < 2) {
                                $this->matnr_changeable = 1;
                                $this->quantity_changeable = 1;
                                $this->position_splittable = 1;
                            }
                            $this->price_changeable = 1;
                            $this->delivery_date_changeable = 1;
                        }
                        $this->accept = 1;
                        $this->reject = 1;
                    } elseif (($this->status == 'R') && ($this->pstage != 'F')) {
                        // cancellation asked by REF/CTV after initial approval
                        $this->inquired = 4;
                        $this->inq_reply = 1;
                    }
                }
            } elseif (Auth::user()->role == 'Referent') {
                if ((($this->stage == "R") && ($this->owner == 1)) ||
                     (($this->stage == "F") && ($this->owner == 2))) {
                    if (empty($this->status)) {
                        if ($this->position_splitted == 0) {
                            if ($this->changed < 2) {
                                $this->matnr_changeable = 1;
                                $this->quantity_changeable = 1;
                                $this->position_splittable = 1;
                            }
                            $this->price_changeable = 1;
                            $this->delivery_date_changeable = 1;
                        }
                    }
                    if ((empty($this->status) || ($this->status == 'T'))) {
                        $this->accept = 1;
                        $this->reject = 1;
                        if ($this->inquired != 0 ) $this->inq_reply = 1;
                    } elseif ($this->status == 'R') {
                        $this->accept = 0;
                        $this->reject = 2;
                        if ($this->inquired != 0 ) $this->inq_reply = 1;
                    }
                }
            } elseif (Auth::user()->role == 'Administrator') {
                if (empty($this->status)) {
                    if ($this->position_splitted == 0) {
                        if ($this->changed < 2) {
                            $this->matnr_changeable = 1;
                            $this->quantity_changeable = 1;
                            $this->position_splittable = 1;
                        }
                        $this->price_changeable = 1;
                        $this->delivery_date_changeable = 1;
                    }
                }
                if (empty($this->status) || ($this->status == 'T')) {
                    $this->accept = 1;
                    $this->reject = 1;
                    if ($this->inquired != 0 ) $this->inq_reply = 1;
                } elseif ($this->status == 'R') {
                    $this->accept = 0;
                    $this->reject = 2;
                    if ($this->inquired != 0 ) $this->inq_reply = 1;
                }
            }  elseif (Auth::user()->role == 'CTV') {

            }
        } elseif (($history == 2) && (Auth::user()->role == 'Administrator')) {
            if ($this->status == 'A')
                $this->matnr_changeable = 1;
        }

        $this->x_delivery_date = substr($this->lfdat, 0, 10);
        $this->x_quantity = trim($this->qty) . " " . trim($this->qty_uom);
        $this->x_purchase_price = trim($this->purch_price) . " " . trim($this->purch_curr);
        if ((Auth::user()->role == 'Furnizor') || ($this->vbeln == Orders::stockorder)) $this->x_sales_price = "";
        else $this->x_sales_price = trim($this->sales_price) . " " . trim($this->sales_curr);

        // Post-processing options
        if (($this->stage == 'Z') && ($this->status == 'A') && ($this->grdate == null)) {
            if (Auth::user()->role == 'Furnizor') {
              if ($porder->lifnr == Auth::user()->lifnr) {
                    $this->price_changeable = 2;
                    $this->delivery_date_changeable = 2;
                    $this->reject = 3;
                }
            } elseif ((Auth::user()->role == 'Administrator') || (Auth::user()->role[0] == $this->pstage)) {
                $this->reject = 4;
            }
        }

    }


}