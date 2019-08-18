<?php
/**
 * Created by PhpStorm.
 * User: Radu
 * Date: 26.09.2018
 * Time: 08:58
 */

namespace App\Materom;

use App\Materom\SAP\MasterData;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Data
{

    private static function addFilters(string ...$filters)
    {
        $filter_sum = "";
        foreach ($filters as $filter)
            $filter_sum = self::addFilter($filter_sum, trim($filter));
        if (!empty($filter_sum)) $filter_sum = "(" . $filter_sum . ")";
        return $filter_sum;
    }

    private static function addFilter($filter_sum, $filter)
    {
        if (empty($filter)) return $filter_sum;
        if (empty($filter_sum)) return $filter;
        return $filter_sum . " and " . $filter;
    }

    private static function processFilter($field, $filter_val, $mode)
    {
        if (is_null($filter_val) || empty($filter_val)) return "";
        $val = trim($filter_val);
        if ($mode != 0) {
            if (ctype_digit($val))
                $val = str_pad($val, $mode, "0", STR_PAD_LEFT);
        }
        if (strchr($val, "*") != null) {
            $val = str_replace("*", "%", $filter_val);
            return "$field like '$val'";
        } else {
            return "$field = '$val'";
        }
    }

    static public function processPOdata($ebeln, $data)
    {
        if (empty($data)) {
            Log::channel("poevent")->info("No data");
            return "OK";
        }
        if (!array_key_exists("ES_HEADER", $data)) {
            Log::channel("poevent")->info("Wrong data");
            Log::channel("poevent")->info($data);
            return $data;
        }
        $saphdr = $data["ES_HEADER"];
        if ($ebeln != $saphdr["EBELN"]) {
            Log::channel("poevent")->info("Wrong purchase order $ebeln vs. " . $saphdr["EBELN"]);
            return "Wrong purchase order";
        }
        $orders = DB::select("select * from " . System::$table_porders . " where ebeln = '$ebeln'");
        if (count($orders) == 0) {
            $order = null;
            if (DB::table(System::$table_porders . "_arch")->where("ebeln", $ebeln)->exists()) {
                Log::channel("poevent")->info("Purchase order $ebeln not processed: it is already archived");
                return "";
            } else {
                Log::channel("poevent")->info("New purchase order $ebeln received");
            }
        } else {
            $order = $orders[0];
            Log::channel("poevent")->info("Existing purchase order $ebeln received");
        }
        $norder = new \stdClass();
        $now = new Carbon();
        $norder->ebeln = $ebeln;
        $norder->lifnr = $saphdr["LIFNR"];
        $norder->ekgrp = $saphdr["EKGRP"];
        $user = DB::table("users")->where(["lifnr" => $norder->lifnr, "role" => "Furnizor", "active" => 1, "sap_system" => Auth::user()->sap_system])->first();
        if ($user == null) {
            Log::channel("poevent")->info("Purchase order $ebeln not processed: no suitable supplier user found");
            return "";
        }
        if ($user->activated_at == null) {
            Log::channel("poevent")->info("Purchase order $ebeln not processed: user '$user->id' is not active");
            return "";
        }
        $userid = $user->id;

        $serdat = $saphdr["ERDAT"];
        $erdat = new Carbon();
        $erdat->day = 1;
        $erdat->year = substr($serdat, 0, 4);
        $erdat->month = substr($serdat, 4, 2);
        $erdat->day = substr($serdat, 6, 2);
        $erdat->hour = $now->hour;
        $erdat->minute = $now->minute;
        $erdat->second = $now->second;
        if ($erdat < $user->activated_at) {
            Log::channel("poevent")->info("Purchase order $ebeln (created on $erdat) rejected due to user $userid activation date of $user->activated_at");
            return "";
        }
        $norder->erdat = $erdat->toDateTimeString();

        $sbedat = $saphdr["BEDAT"];
        $bedat = new Carbon();
        $bedat->day = 1;
        $bedat->year = substr($sbedat, 0, 4);
        $bedat->month = substr($sbedat, 4, 2);
        $bedat->day = substr($sbedat, 6, 2);
        $norder->bedat = $bedat->toDateTimeString();

        $norder->ernam = $saphdr["ERNAM"];
        $norder->curr = $saphdr["CURR"];
        $norder->fxrate = $saphdr["FXRATE"];
        $now->addHours(4);
        $norder->wtime = $now->toDateTimeString();
        $now->addHours(8);
        $norder->ctime = $now->toDateTimeString();
        $norder->changed = '0';
        $norder->status = '';

        $new_order_item = false;
        $send_mail = true;

        DB::beginTransaction();

        if (is_null($order)) {
            $new_order_item = true;
            $sql = "insert into " . System::$table_porders . " (ebeln, wtime, ctime, lifnr, ekgrp, bedat, erdat, ernam, curr, fxrate, changed, status) values " .
                "('$norder->ebeln', '$norder->wtime', '$norder->ctime', '$norder->lifnr', " .
                "'$norder->ekgrp', '$norder->bedat', '$norder->erdat', '$norder->ernam', '$norder->curr', '$norder->fxrate', '$norder->changed', '$norder->status')";
            DB::insert($sql);
        } else {
            $send_mail = false;
            $sql = "update " . System::$table_porders . " set " .
                "ekgrp = '$norder->ekgrp' " .
                "where ebeln = '$norder->ebeln'";
            DB::update($sql);
        }

        $items_arch = DB::select("select * from " . System::$table_pitems . "_arch where ebeln = '$ebeln' order by ebelp");
        $items = DB::select("select * from " . System::$table_pitems . " where ebeln = '$ebeln' order by ebelp");
        $sapitms = $data["ET_ITEMS"];
        foreach ($sapitms as $sapitm) {
            $citem = null;
            foreach ($items as $item) {
                if ($item->ebelp == $sapitm["EBELP"]) {
                    $citem = $item;
                    break;
                }
            }
            if ($citem == null) {
                foreach ($items_arch as $item_arch) {
                    if ($item_arch->ebelp == $sapitm["EBELP"]) {
                        $citem = $item_arch;
                        break;
                    }
                }
                if ($citem != null) {
                    Log::channel("poevent")->info("Purchase order item $ebeln/$citem->ebelp not processed: it is already archived");
                    continue;
                }
            }
            $nitem = new \stdClass();
            $nitem->ebeln = $sapitm["EBELN"];
            if ($ebeln != $nitem->ebeln) return "Wrong purchase order items";
            $nitem->ebelp = $sapitm["EBELP"];
            $nitem->matnr = trim($sapitm["MATNR"]);
            $nitem->idnlf = trim($sapitm["IDNLF"]);
            $nitem->mtext = trim($sapitm["MTEXT"]);
            $nitem->mtext = str_replace("'", "\'", $nitem->mtext);
            $nitem->qty = $sapitm["MENGE"];
            $nitem->qty_uom = $sapitm["MEINS"];
            $nitem->lfdat = $sapitm["LFDAT"];
            $lfdat = new Carbon();
            $lfdat->day = 1;
            $lfdat->year = substr($nitem->lfdat, 0, 4);
            $lfdat->month = substr($nitem->lfdat, 4, 2);
            $lfdat->day = substr($nitem->lfdat, 6, 2);
            $nitem->lfdat = $lfdat->toDateTimeString();
            $nitem->etadt = $nitem->lfdat;
            $nitem->mfrnr = $sapitm["MFRNR"];
            $nitem->werks = $sapitm["WERKS"];
            if ($nitem->werks == "D000" || $nitem->werks == "G000") {
                DB::rollBack();
                return "nOK";
            }
            $nitem->purch_price = $sapitm["PURCH_PRICE"];
            $nitem->purch_curr = $sapitm["PURCH_CURR"];
            $nitem->purch_prun = $sapitm["PURCH_PRUN"];
            $nitem->purch_puom = $sapitm["PURCH_PUOM"];
            $nitem->vbeln = $sapitm["VBELN"];
            $nitem->posnr = $sapitm["POSNR"];
            $nitem->sales_price = $sapitm["SALES_PRICE"];
            $nitem->sales_curr = $sapitm["SALES_CURR"];
            $nitem->sales_prun = $sapitm["SALES_PRUN"];
            $nitem->sales_puom = $sapitm["SALES_PUOM"];
            $nitem->vbeln = trim($nitem->vbeln);
            $nitem->kunnr = $sapitm["KUNNR"];
            $nitem->shipto = $sapitm["SHIPTO"];
            $nitem->ctv = "";
            $nitem->ctv_name = "";
            $nitem->ctv_man = 0;
            if (!empty(trim($nitem->kunnr))) {
                $ctv = MasterData::getAgentForClient($nitem->kunnr);
                $nitem->ctv = $ctv->agent;
                $nitem->ctv_name = $ctv->agent_name;
            }
            $nitem->stage = 'F';
            $nitem->changed = false;
            $nitem->status = '';
            $nitem->nof = true;

            if (is_null($citem)) {
                $new_order_item = true;
                $sql = "insert into " . System::$table_pitems .
                    " (ebeln, ebelp, matnr, idnlf, mtext, qty, qty_uom, lfdat, etadt, mfrnr, werks, " .
                    "purch_price, purch_curr, purch_prun, purch_puom, " .
                    "sales_price, sales_curr, sales_prun, sales_puom, " .
                    "vbeln, posnr, kunnr, shipto, ctv, ctv_name, ctv_man, stage, changed, status, " .
                    "orig_matnr, orig_idnlf, orig_purch_price, orig_qty, orig_lfdat, nof) values (" .
                    "'$nitem->ebeln', '$nitem->ebelp', '$nitem->matnr', '$nitem->idnlf', '" . substr($nitem->mtext, 0, 40) . "',$nitem->qty, '$nitem->qty_uom', " .
                    "'$nitem->lfdat', '$nitem->etadt', '$nitem->mfrnr', '$nitem->werks', " .
                    "'$nitem->purch_price', '$nitem->purch_curr', " .
                    "$nitem->purch_prun, '$nitem->purch_puom', " .
                    "'$nitem->sales_price', '$nitem->sales_curr', $nitem->sales_prun, " .
                    "'$nitem->sales_puom', '$nitem->vbeln', '$nitem->posnr', '$nitem->kunnr', " .
                    "'$nitem->shipto', '$nitem->ctv', '$nitem->ctv_name', $nitem->ctv_man, " .
                    "'$nitem->stage', 0, '$nitem->status', " .
                    "'$nitem->matnr', '$nitem->idnlf', '$nitem->purch_price', $nitem->qty, '$nitem->lfdat', '$nitem->nof')";

                DB::insert($sql);

            } else {
                $sql = "update " . System::$table_pitems . " set " .
                    "ctv = '$nitem->ctv', " .
                    "ctv_name = '$nitem->ctv_name', " .
                    "ctv_man = $nitem->ctv_man " .
                    "where ebeln = '$nitem->ebeln' and ebelp = '$nitem->ebelp'";
                DB::update($sql);
            }
        }

        DB::commit();
        Log::channel("poevent")->info("Purchase order $ebeln successfully created/updated");
        if ("X" . System::$system != "X300") {
            foreach ($sapitms as $sapitm) {
//              SAP::acknowledgePOItem($sapitm["EBELN"], $sapitm["EBELP"], "X");
            }
        }
        if ($new_order_item) {
            if ($send_mail) Mailservice::sendNotification($userid, $ebeln);
            $refuser = DB::table("users")->where(["ekgrp" => $norder->ekgrp, "role" => "Referent", "active" => 1, "sap_system" => Auth::user()->sap_system])->first();
            if ($refuser != null) Mailservice::sendNotification($refuser->id, $ebeln);
        }
        return "OK";
    }

    public static function archiveItem($ebeln, $ebelp, $nodel = false)
    {
        $porder = DB::table(System::$table_porders)->where("ebeln", $ebeln)->first();
        if ($porder == null) return "Purchase order not found";
        $pitem = DB::table(System::$table_pitems)->where([["ebeln", "=", $ebeln], ["ebelp", "=", $ebelp]])->first();
        if ($pitem == null) return "Purchase order item not found";
        $pichanges = DB::table(System::$table_pitemchg)->where([["ebeln", "=", $ebeln], ["ebelp", "=", $ebelp]])->get();
        $piproposals = DB::table(System::$table_pitemchg_proposals)->where([["ebeln", "=", $ebeln], ["ebelp", "=", $ebelp]])->get();
        $archdate = now();

        DB::beginTransaction();

        if (!$nodel) {
            DB::delete("delete from " . System::$table_porders . "_arch where ebeln = '$ebeln'");
            DB::delete("delete from " . System::$table_pitems_cache . " where ebeln = '$ebeln' and ebelp = '$ebelp'");
            DB::delete("delete from " . System::$table_pitems . " where ebeln = '$ebeln' and ebelp = '$ebelp'");
            DB::delete("delete from " . System::$table_pitems . "_arch where ebeln = '$ebeln' and ebelp = '$ebelp'");
            DB::delete("delete from " . System::$table_pitemchg . " where ebeln = '$ebeln' and ebelp = '$ebelp'");
            DB::delete("delete from " . System::$table_pitemchg . "_arch where ebeln = '$ebeln' and ebelp = '$ebelp'");
            DB::delete("delete from " . System::$table_pitemchg_proposals . " where ebeln = '$ebeln' and ebelp = '$ebelp'");
            DB::delete("delete from " . System::$table_pitemchg_proposals . "_arch where ebeln = '$ebeln' and ebelp = '$ebelp'");
        }

        foreach ($piproposals as $proposal) {
            DB::insert("INSERT INTO " . System::$table_pitemchg_proposals . "_arch (type, ebeln, ebelp, cdate, pos, lifnr, idnlf, matnr, mtext, lfdat, qty, qty_uom, purch_price, purch_curr, sales_price, sales_curr, infnr, source, accepted, archdate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [$proposal->type, $proposal->ebeln, $proposal->ebelp, $proposal->cdate, $proposal->pos,
                    $proposal->lifnr, $proposal->idnlf, $proposal->matnr, $proposal->mtext, $proposal->lfdat,
                    $proposal->qty, $proposal->qty_uom, $proposal->purch_price, $proposal->purch_curr, $proposal->sales_price,
                    $proposal->sales_curr, $proposal->infnr, $proposal->source, $proposal->accepted, $archdate]);
        }

        foreach ($pichanges as $pichange) {
            DB::insert("INSERT INTO " . System::$table_pitemchg . "_arch (ebeln, ebelp, cdate, internal, ctype, stage, cuser, cuser_name, duser, oldval, newval, oebeln, oebelp, reason, acknowledged, archdate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [$pichange->ebeln, $pichange->ebelp, $pichange->cdate, $pichange->internal, $pichange->ctype,
                    $pichange->stage, $pichange->cuser, $pichange->cuser_name, $pichange->duser, $pichange->oldval,
                    $pichange->newval, $pichange->oebeln, $pichange->oebelp, $pichange->reason, $pichange->acknowledged,
                    $archdate]);
        }

        DB::insert("INSERT INTO " . System::$table_pitems . "_arch (ebeln, ebelp, matnr, vbeln, posnr, idnlf, mtext, mfrnr, werks, purch_price, purch_curr, purch_prun, purch_puom, sales_price, sales_curr, sales_prun, sales_puom, qty, qty_uom, kunnr, shipto, ctv, ctv_name, ctv_man, lfdat, backorder, deldate, delqty, grdate, grqty, gidate, qty_diff, qty_damaged, qty_details, qty_solution, stage, pstage, changed, status, orig_matnr, orig_idnlf, orig_purch_price, orig_qty, orig_lfdat, nof, new_lifnr, elikz, etadt, archdate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [$pitem->ebeln, $pitem->ebelp, $pitem->matnr, $pitem->vbeln, $pitem->posnr,
                $pitem->idnlf, $pitem->mtext, $pitem->mfrnr, $pitem->werks, $pitem->purch_price, $pitem->purch_curr,
                $pitem->purch_prun, $pitem->purch_puom, $pitem->sales_price, $pitem->sales_curr,
                $pitem->sales_prun, $pitem->sales_puom, $pitem->qty, $pitem->qty_uom, $pitem->kunnr,
                $pitem->shipto, $pitem->ctv, $pitem->ctv_name, $pitem->ctv_man, $pitem->lfdat, $pitem->backorder, $pitem->deldate,
                $pitem->delqty, $pitem->grdate, $pitem->grqty, $pitem->gidate,
                $pitem->qty_diff, $pitem->qty_damaged, $pitem->qty_details, $pitem->qty_solution,
                $pitem->stage, $pitem->pstage, $pitem->changed, $pitem->status, $pitem->orig_matnr, $pitem->orig_idnlf,
                $pitem->orig_purch_price, $pitem->orig_qty, $pitem->orig_lfdat, $pitem->nof, $pitem->new_lifnr,
                $pitem->elikz, $pitem->etadt, $archdate]);

        DB::insert("INSERT INTO " . System::$table_porders . "_arch (ebeln, wtime, ctime, lifnr, ekgrp, bedat, erdat, ernam, curr, fxrate, changed, status, qty_ordered, qty_delivered, qty_open, qty_invoiced, archdate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [$porder->ebeln, $porder->wtime, $porder->ctime, $porder->lifnr,
                $porder->ekgrp, $porder->bedat, $porder->erdat, $porder->ernam, $porder->curr,
                $porder->fxrate, $porder->changed, $porder->status,
                $porder->qty_ordered, $porder->qty_delivered, $porder->qty_open, $porder->qty_invoiced,
                $archdate]);

        DB::commit();

        if (!$nodel && !DB::table(System::$table_pitems)->where([["ebeln", "=", $ebeln]])->exists()) {
            DB::beginTransaction();
            DB::delete("delete from " . System::$table_porders_cache . " where ebeln = '$ebeln'");
            DB::delete("delete from " . System::$table_porders . " where ebeln = '$ebeln'");
            DB::commit();
        }

        return "OK";
    }

    public static function unArchiveItem($ebeln, $ebelp)
    {
        $porder = DB::table(System::$table_porders . "_arch")->where("ebeln", $ebeln)->first();
        if ($porder == null) return "Archived purchase order does not exist";
        $pitem = DB::table(System::$table_pitems . "_arch")->where([["ebeln", "=", $ebeln], ["ebelp", "=", $ebelp]])->first();
        if ($pitem == null) return "Archived purchase order item does not exist";
        $pichanges = DB::table(System::$table_pitemchg . "_arch")->where([["ebeln", "=", $ebeln], ["ebelp", "=", $ebelp]])->get();
        $piproposals = DB::table(System::$table_pitemchg_proposals . "_arch")->where([["ebeln", "=", $ebeln], ["ebelp", "=", $ebelp]])->get();

        DB::beginTransaction();

        DB::delete("delete from " . System::$table_pitems . "_arch where ebeln = '$ebeln' and ebelp = '$ebelp'");
        DB::delete("delete from " . System::$table_pitemchg . "_arch where ebeln = '$ebeln' and ebelp = '$ebelp'");
        DB::delete("delete from " . System::$table_pitemchg_proposals . "_arch where ebeln = '$ebeln' and ebelp = '$ebelp'");

        foreach ($piproposals as $proposal) {
            DB::insert("INSERT INTO " . System::$table_pitemchg_proposals . " (type, ebeln, ebelp, cdate, pos, lifnr, idnlf, matnr, mtext, lfdat, qty, qty_uom, purch_price, purch_curr, sales_price, sales_curr, infnr, source, accepted) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [$proposal->type, $proposal->ebeln, $proposal->ebelp, $proposal->cdate, $proposal->pos,
                    $proposal->lifnr, $proposal->idnlf, $proposal->matnr, $proposal->mtext, $proposal->lfdat,
                    $proposal->qty, $proposal->qty_uom, $proposal->purch_price, $proposal->purch_curr, $proposal->sales_price,
                    $proposal->sales_curr, $proposal->infnr, $proposal->source, $proposal->accepted]);
        }

        foreach ($pichanges as $pichange) {
            DB::insert("INSERT INTO " . System::$table_pitemchg . " (ebeln, ebelp, cdate, internal, ctype, stage, cuser, cuser_name, duser, oldval, newval, oebeln, oebelp, reason, acknowledged) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [$pichange->ebeln, $pichange->ebelp, $pichange->cdate, $pichange->internal, $pichange->ctype,
                    $pichange->stage, $pichange->cuser, $pichange->cuser_name, $pichange->duser, $pichange->oldval,
                    $pichange->newval, $pichange->oebeln, $pichange->oebelp, $pichange->reason, $pichange->acknowledged]);
        }

        DB::insert("INSERT INTO " . System::$table_pitems . " (ebeln, ebelp, matnr, vbeln, posnr, idnlf, mtext, mfrnr, werks, purch_price, purch_curr, purch_prun, purch_puom, sales_price, sales_curr, sales_prun, sales_puom, qty, qty_uom, kunnr, shipto, ctv, ctv_name, ctv_man, lfdat, backorder, deldate, delqty, grdate, grqty, gidate, qty_diff, qty_damaged, qty_details, qty_solution, stage, pstage, changed, status, orig_matnr, orig_idnlf, orig_purch_price, orig_qty, orig_lfdat, nof, new_lifnr, elikz, etadt) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [$pitem->ebeln, $pitem->ebelp, $pitem->matnr, $pitem->vbeln, $pitem->posnr,
                $pitem->idnlf, $pitem->mtext, $pitem->mfrnr, $pitem->werks, $pitem->purch_price, $pitem->purch_curr,
                $pitem->purch_prun, $pitem->purch_puom, $pitem->sales_price, $pitem->sales_curr,
                $pitem->sales_prun, $pitem->sales_puom, $pitem->qty, $pitem->qty_uom, $pitem->kunnr,
                $pitem->shipto, $pitem->ctv, $pitem->ctv_name, $pitem->ctv_man, $pitem->lfdat, $pitem->backorder, $pitem->deldate,
                $pitem->delqty, $pitem->grdate, $pitem->grqty, $pitem->gidate,
                $pitem->qty_diff, $pitem->qty_damaged, $pitem->qty_details, $pitem->qty_solution,
                $pitem->stage, $pitem->pstage, $pitem->changed, $pitem->status, $pitem->orig_matnr, $pitem->orig_idnlf,
                $pitem->orig_purch_price, $pitem->orig_qty, $pitem->orig_lfdat, $pitem->nof, $pitem->new_lifnr, $pitem->elikz, $pitem->etadt]);

        if (!DB::table(System::$table_porders)->where("ebeln", $ebeln)->exists())
            DB::insert("INSERT INTO " . System::$table_porders . " (ebeln, wtime, ctime, lifnr, ekgrp, bedat, erdat, ernam, curr, fxrate, changed, status, qty_ordered, qty_delivered, qty_open, qty_invoiced) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [$porder->ebeln, $porder->wtime, $porder->ctime, $porder->lifnr,
                    $porder->ekgrp, $porder->bedat, $porder->erdat, $porder->ernam, $porder->curr,
                    $porder->fxrate, $porder->changed, $porder->status,
                    $porder->qty_ordered, $porder->qty_delivered, $porder->qty_open, $porder->qty_invoiced]);

        DB::commit();

        if (!DB::table(System::$table_pitems . "_arch")->where([["ebeln", "=", $ebeln]])->exists()) {
            DB::beginTransaction();
            DB::delete("delete from " . System::$table_porders_cache . " where ebeln = '$ebeln'");
            DB::delete("delete from " . System::$table_porders . "_arch where ebeln = '$ebeln'");
            DB::commit();
        }

        return "OK";
    }

    public static function performArchiving()
    {
        SAP::refreshDeliveryStatus(2);

        $pitems = DB::table(System::$table_pitems)->where("grdate", "<>", "null")->get();
        foreach ($pitems as $pitem) {
            if ("" . $pitem->qty == explode(" ", $pitem->grqty)[0]) {
                Log::info("Archiving " . $pitem->ebeln . "/" . SAP::alpha_output($pitem->ebelp) . " (goods received)");
                self::archiveItem($pitem->ebeln, $pitem->ebelp);
            }
        }

        $pitems = DB::table(System::$table_pitems)->where("elikz", "=", "X")->get();
        foreach ($pitems as $pitem) {
            Log::info("Archiving " . $pitem->ebeln . "/" . SAP::alpha_output($pitem->ebelp) . " (fully delivered)");
            self::archiveItem($pitem->ebeln, $pitem->ebelp);
        }

        $pitems = DB::select("select distinct ebeln, ebelp from " . System::$table_pitems . " where stage = 'Z' and status = 'X'");
        foreach ($pitems as $pitem) {
            Log::info("Archiving " . $pitem->ebeln . "/" . SAP::alpha_output($pitem->ebelp) . " (item rejected)");
            self::archiveItem($pitem->ebeln, $pitem->ebelp);
        }

        // Delayed archiving
        $pitems = null;
        $pitems = DB::table(System::$table_pitems)->where("new_lifnr", "<>", "")->get();
        foreach ($pitems as $pitem) {
            if (DB::table(System::$table_pitems . "_arch")->where([["ebeln", "=", $pitem->ebeln], ["ebelp", "=", $pitem->ebelp]])->exists()) {
                Log::info("Archiving " . $pitem->ebeln . "/" . SAP::alpha_output($pitem->ebelp) . " (delayed)");
                self::archiveItem($pitem->ebeln, $pitem->ebelp);
            } else {
                Log::info("Delayed archiving " . $pitem->ebeln . "/" . SAP::alpha_output($pitem->ebelp));
                self::archiveItem($pitem->ebeln, $pitem->ebelp, true);
            }

        }
    }

    public static function gatherStatistics()
    {
        $lifnrs = DB::select("select lifnr, ekgrp, count(*) as cnt_all_orders from ". System::$table_porders ." group by lifnr, ekgrp order by lifnr, ekgrp");
        $cdate = Carbon::now();
        DB::beginTransaction();
        foreach($lifnrs as $lifnr) {
            // Stock orders
            $cursor = DB::select("select ebeln, count(*) as cnt_items from ". System::$table_pitems. " join ". System::$table_porders ." using (ebeln) where ".
                System::$table_porders. ".lifnr = '$lifnr->lifnr' and ". System::$table_porders. ".ekgrp = '$lifnr->ekgrp' and ". System::$table_pitems. ".vbeln = '" .Orders::stockorder. "' ".
                "group by ebeln");
            $cnt_total_orders = 0;
            $cnt_total_items = 0;
            foreach ($cursor as $record) {
                $cnt_total_orders++;
                $cnt_total_items += $record->cnt_items;
            }
            $cursor = DB::select("select ebeln, count(*) as cnt_delayed_items from ". System::$table_pitems. " join ". System::$table_porders ." using (ebeln) where ".
                System::$table_porders. ".lifnr = '$lifnr->lifnr' and ". System::$table_porders. ".ekgrp = '$lifnr->ekgrp' and ".
                System::$table_pitems. ".vbeln = '" .Orders::stockorder. "' and " . System::$table_pitems. ".lfdat > '$cdate' ".
                "group by ". System::$table_porders  .".ebeln");
            $cnt_delayed_items = 0;
            $cnt_delayed_orders = 0;
            foreach ($cursor as $record) {
                $cnt_delayed_orders++;
                $cnt_delayed_items += $record->cnt_delayed_items;
            }
            DB::insert("insert into ". System::$table_stat_orders.
                " (lifnr, ekgrp, otype, date, cnt_total_orders, cnt_delayed_orders, cnt_total_items, cnt_delayed_items)".
                " values ('$lifnr->lifnr', '$lifnr->ekgrp', 'S', '$cdate', $cnt_total_orders, $cnt_delayed_orders, $cnt_total_items, $cnt_delayed_items)");

            // Client orders
            $cursor = DB::select("select ebeln, count(*) as cnt_items from ". System::$table_pitems. " join ". System::$table_porders ." using (ebeln) where ".
                System::$table_porders. ".lifnr = '$lifnr->lifnr' and ". System::$table_porders. ".ekgrp = '$lifnr->ekgrp' and ".
                System::$table_pitems. ".vbeln <> '" .Orders::stockorder. "' group by ebeln");
            $cnt_total_orders = 0;
            $cnt_total_items = 0;
            foreach ($cursor as $record) {
                $cnt_total_orders++;
                $cnt_total_items += $record->cnt_items;
            }
            $cursor = DB::select("select ebeln, count(*) as cnt_delayed_items from ". System::$table_pitems. " join ". System::$table_porders ." using (ebeln) where ".
                System::$table_porders. ".lifnr = '$lifnr->lifnr' and ". System::$table_porders. ".ekgrp = '$lifnr->ekgrp' and ".
                System::$table_pitems. ".vbeln <> '" .Orders::stockorder. "' and " . System::$table_pitems. ".lfdat > '$cdate' ".
                "group by ". System::$table_porders  .".ebeln");
            $cnt_delayed_items = 0;
            $cnt_delayed_orders = 0;
            foreach ($cursor as $record) {
                $cnt_delayed_orders++;
                $cnt_delayed_items += $record->cnt_delayed_items;
            }
            DB::insert("insert into ". System::$table_stat_orders.
                " (lifnr, ekgrp, otype, date, cnt_total_orders, cnt_delayed_orders, cnt_total_items, cnt_delayed_items)".
                " values ('$lifnr->lifnr', '$lifnr->ekgrp', 'C', '$cdate', $cnt_total_orders, $cnt_delayed_orders, $cnt_total_items, $cnt_delayed_items)");
        }
        DB::commit();
        Log::info("Statistics level=1 collected (" . count($lifnrs) . " records)");
    }
}