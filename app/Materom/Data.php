<?php
/**
 * Created by PhpStorm.
 * User: Radu
 * Date: 26.09.2018
 * Time: 08:58
 */

namespace App\Materom;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Data
{

    private static function addFilters(string ...$filters) {
        $filter_sum = "";
        foreach($filters as $filter)
            $filter_sum = self::addFilter($filter_sum, trim($filter));
        if (!empty($filter_sum)) $filter_sum = "(" . $filter_sum . ")";
        return $filter_sum;
    }

    private static function addFilter($filter_sum, $filter) {
        if (empty($filter)) return $filter_sum;
        if (empty($filter_sum)) return $filter;
        return $filter_sum . " and " . $filter;
    }

    private static function processFilter($field, $filter_val, $mode) {
        if (is_null($filter_val) || empty($filter_val)) return "";
        $val = trim($filter_val);
        if ($mode != 0) {
          if (ctype_digit($val))
              $val = str_pad($val, $mode, "0", STR_PAD_LEFT);
        }
        if (strchr($val, "*") != null) {
            $val = str_replace("*","%", $filter_val);
            return "$field like '$val'";
        } else {
            return "$field = '$val'";
        }
    }

    static public function processPOdata($ebeln, $data) {
        if (empty($data)) return "OK";
        if (!array_key_exists("ES_HEADER", $data)) return $data;
        $saphdr = $data["ES_HEADER"];
        if ($ebeln != $saphdr["EBELN"]) return "Wrong purchase order";
        $orders = DB::select("select * from porders where ebeln = '$ebeln'");
        if (count($orders) == 0) $order = null;
        else return "OK";
        $norder = new \stdClass();
        $now = new Carbon();
        $erdat = new Carbon();
        $norder->ebeln = $ebeln;
        $norder->lifnr = $saphdr["LIFNR"];
        $norder->ekgrp = $saphdr["EKGRP"];
        $user = DB::table("users")->where(["lifnr" => $norder->lifnr, "role" => "Furnizor", "active" => 1])->value("id")->first();
        if ($user == null) return "";
        if ($user->activated_at == null) return "";
        $userid = $user->id;

        $sbedat = $saphdr["BEDAT"];
        $bedat = new Carbon();
        $bedat->day = substr($sbedat, 6, 2);
        $bedat->month = substr($sbedat, 4, 2);
        $bedat->year = substr($sbedat, 0, 4);
        if ($sbedat < $user->activated_at) {
            Log::debug("Purchase order $ebeln (document date $bedat) rejected due to user $userid activation date of $user->activated_at");
            return "";
        }

        $erdat->hour = $now->hour;
        $erdat->minute = $now->minute;
        $erdat->second = $now->second;
        $norder->erdat = $erdat->toDateTimeString();
        $norder->ernam = $saphdr["ERNAM"];
        $norder->curr = $saphdr["CURR"];
        $norder->fxrate = $saphdr["FXRATE"];
        $now->addHours(12);
        $norder->wtime = $now->toDateTimeString();
        $now->addHours(24);
        $norder->ctime = $now->toDateTimeString();
        $norder->changed = '0';
        $norder->status = '';

        $new_order_item = false;
        $send_mail = true;

        DB::beginTransaction();

        if (is_null($order)) {
            $new_order_item = true;
            $sql = "insert into porders (ebeln, wtime, ctime, lifnr, ekgrp, erdat, ernam, curr, fxrate, changed, status) values " .
                "('$norder->ebeln', '$norder->wtime', '$norder->ctime', '$norder->lifnr', " .
                "'$norder->ekgrp', '$norder->erdat', '$norder->ernam', '$norder->curr', '$norder->fxrate', '$norder->changed', '$norder->status')";
            DB::insert($sql);
        } else {
            $sql = "update porders set " .
                "lifnr = '$norder->lifnr', ".
                "ekgrp = '$norder->ekgrp', ".
                "curr = '$norder->curr', " .
                "fxrate = '$norder->fxrate' " .
                "where ebeln = '$norder->ebeln'";
            DB::update($sql);
        }

        $items = DB::select("select * from pitems where ebeln = '$ebeln' order by ebelp");
        $sapitms = $data["ET_ITEMS"];
        $citem = null;
        foreach($sapitms as $sapitm) {
            foreach($items as $item) {
                if ($item->ebelp == $sapitm["EBELP"]) {
                    $citem = $item;
                    break;
                }
            }
            $nitem = new \stdClass();
            $nitem->ebeln = $sapitm["EBELN"];
            if ($ebeln != $nitem->ebeln) return "Wrong purchase order items";
            $nitem->ebelp = $sapitm["EBELP"];
            $nitem->matnr = $sapitm["MATNR"];
            $nitem->idnlf = $sapitm["IDNLF"];
            $nitem->mtext = $sapitm["MTEXT"];
            $nitem->mtext = str_replace("'", "\'", $nitem->mtext);
            $nitem->qty = $sapitm["MENGE"];
            $nitem->qty_uom = $sapitm["MEINS"];
            $nitem->lfdat = $sapitm["LFDAT"];
            $lfdat = new Carbon();
            $lfdat->year = substr($nitem->lfdat, 0, 4);
            $lfdat->month = substr($nitem->lfdat, 4, 2);
            $lfdat->day = substr($nitem->lfdat, 6, 2);
            $nitem->lfdat = $lfdat->toDateTimeString();
            $nitem->mfrnr = $sapitm["MFRNR"];
            $nitem->werks = $sapitm["WERKS"];
            if ($nitem->werks == "D000" || $nitem->werks != "G000") $send_mail = false;
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
            $nitem->ctv = $sapitm["CTV"];
            $nitem->ctv_name = $sapitm["CTV_NAME"];
            $nitem->stage = 'F';
            $nitem->changed = false;
            $nitem->status = '';
            $nitem->nof = true;

            $users = DB::select("select * from users where sapuser = '$nitem->ctv' and role = 'CTV'");
            if (count($users) > 0) $nitem->ctv = $users[0]->id;
            if (is_null($citem)) {
                $new_order_item = true;
                $sql = "insert into pitems (ebeln, ebelp, matnr, idnlf, mtext, qty, qty_uom, lfdat, mfrnr, werks, ".
                                           "purch_price, purch_curr, purch_prun, purch_puom, ".
                                           "sales_price, sales_curr, sales_prun, sales_puom, ".
                                           "vbeln, posnr, kunnr, shipto, ctv, ctv_name, stage, changed, status, ".
                                           "orig_matnr, orig_idnlf, orig_purch_price, orig_qty, orig_lfdat, nof) values (".
                       "'$nitem->ebeln', '$nitem->ebelp', '$nitem->matnr', '$nitem->idnlf', '" . substr($nitem->mtext, 0, 40) . "',$nitem->qty, '$nitem->qty_uom', ".
                       "'$nitem->lfdat', '$nitem->mfrnr', '$nitem->werks', ".
                       "'$nitem->purch_price', '$nitem->purch_curr', ".
                       "$nitem->purch_prun, '$nitem->purch_puom', ".
                       "'$nitem->sales_price', '$nitem->sales_curr', $nitem->sales_prun, ".
                       "'$nitem->sales_puom', '$nitem->vbeln', '$nitem->posnr', '$nitem->kunnr', ".
                       "'$nitem->shipto', '$nitem->ctv', '$nitem->ctv_name', ".
                       "'$nitem->stage', 0, '$nitem->status', " .
                       "'$nitem->matnr', '$nitem->idnlf', '$nitem->purch_price', $nitem->qty, '$nitem->lfdat', '$nitem->nof')";

                DB::insert($sql);

            } else {
                $sql = "update pitems set idnlf = '$nitem->idnlf', nof = '$nitem->nof', " .
                    "mtext = '$nitem->mtext', ".
                    "qty = $nitem->qty, ".
                    "qty_uom = '$nitem->qty_uom', ".
                    "lfdat = '$nitem->lfdat', ".
                    "purch_price = '$nitem->purch_price', " .
                    "purch_curr = '$nitem->purch_curr', " .
                    "purch_prun = $nitem->purch_prun, " .
                    "purch_puom = '$nitem->purch_puom', " .
                    "sales_price = '$nitem->sales_price', " .
                    "sales_curr = '$nitem->sales_curr', " .
                    "sales_prun = $nitem->sales_prun, " .
                    "sales_puom = '$nitem->sales_puom' " .
                    "where ebeln = '$nitem->ebeln' and ebelp = '$nitem->ebelp'";
                DB::update($sql);
            }
        }

        DB::commit();
        foreach($sapitms as $sapitm) {
            SAP::acknowledgePOItem($sapitm["EBELN"], $sapitm["EBELP"], "X");
        }
        if ($new_order_item && $send_mail) Mailservice::sendNotification($userid, $ebeln);
        return "OK";
    }

    public static function archiveItem($ebeln, $ebelp, $nodel = false)
    {
        $porder = DB::table("porders")->where("ebeln", $ebeln)->first();
        if ($porder == null) return "Purchase order not found";
        $pitem = DB::table("pitems")->where([["ebeln", "=", $ebeln],["ebelp", "=", $ebelp]])->first();
        if ($pitem == null) return "Purchase order item not found";
        $pichanges = DB::table("pitemchg")->where([["ebeln", "=", $ebeln],["ebelp", "=", $ebelp]])->get();
        $piproposals = DB::table("pitemchg_proposals")->where([["ebeln", "=", $ebeln],["ebelp", "=", $ebelp]])->get();
        $archdate = now();

        DB::beginTransaction();

        if (!$nodel) {
            DB::delete("delete from porders_arch where ebeln = '$ebeln'");
            DB::delete("delete from pitems_cache where ebeln = '$ebeln' and ebelp = '$ebelp'");
            DB::delete("delete from pitems where ebeln = '$ebeln' and ebelp = '$ebelp'");
            DB::delete("delete from pitems_arch where ebeln = '$ebeln' and ebelp = '$ebelp'");
            DB::delete("delete from pitemchg where ebeln = '$ebeln' and ebelp = '$ebelp'");
            DB::delete("delete from pitemchg_arch where ebeln = '$ebeln' and ebelp = '$ebelp'");
            DB::delete("delete from pitemchg_proposals where ebeln = '$ebeln' and ebelp = '$ebelp'");
            DB::delete("delete from pitemchg_proposals_arch where ebeln = '$ebeln' and ebelp = '$ebelp'");
        }

        foreach($piproposals as $proposal) {
            DB::insert("INSERT INTO pitemchg_proposals_arch (type, ebeln, ebelp, cdate, pos, lifnr, idnlf, matnr, mtext, lfdat, qty, qty_uom, purch_price, purch_curr, sales_price, sales_curr, infnr, source, accepted, archdate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [$proposal->type, $proposal->ebeln, $proposal->ebelp, $proposal->cdate, $proposal->pos,
                 $proposal->lifnr, $proposal->idnlf, $proposal->matnr, $proposal->mtext, $proposal->lfdat,
                 $proposal->qty, $proposal->qty_uom, $proposal->purch_price, $proposal->purch_curr, $proposal->sales_price,
                 $proposal->sales_curr, $proposal->infnr, $proposal->source, $proposal->accepted, $archdate]);
        }

        foreach($pichanges as $pichange) {
            DB::insert("INSERT INTO pitemchg_arch (ebeln, ebelp, cdate, internal, ctype, stage, cuser, cuser_name, duser, oldval, newval, oebeln, oebelp, reason, acknowledged, archdate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [$pichange->ebeln, $pichange->ebelp, $pichange->cdate, $pichange->internal, $pichange->ctype,
                 $pichange->stage, $pichange->cuser, $pichange->cuser_name, $pichange->duser, $pichange->oldval,
                 $pichange->newval, $pichange->oebeln, $pichange->oebelp, $pichange->reason, $pichange->acknowledged,
                 $archdate]);
        }

        DB::insert("INSERT INTO pitems_arch (ebeln, ebelp, matnr, vbeln, posnr, idnlf, mtext, mfrnr, werks, purch_price, purch_curr, purch_prun, purch_puom, sales_price, sales_curr, sales_prun, sales_puom, qty, qty_uom, kunnr, shipto, ctv, ctv_name, lfdat, deldate, delqty, grdate, grqty, gidate, stage, pstage, changed, status, orig_matnr, orig_idnlf, orig_purch_price, orig_qty, orig_lfdat, nof, new_lifnr, archdate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [$pitem->ebeln, $pitem->ebelp, $pitem->matnr, $pitem->vbeln, $pitem->posnr,
             $pitem->idnlf, $pitem->mtext, $pitem->mfrnr, $pitem->werks, $pitem->purch_price, $pitem->purch_curr,
             $pitem->purch_prun, $pitem->purch_puom, $pitem->sales_price, $pitem->sales_curr,
             $pitem->sales_prun, $pitem->sales_puom, $pitem->qty, $pitem->qty_uom, $pitem->kunnr,
             $pitem->shipto, $pitem->ctv, $pitem->ctv_name, $pitem->lfdat, $pitem->deldate,
             $pitem->delqty, $pitem->grdate, $pitem->grqty, $pitem->gidate, $pitem->stage,
             $pitem->pstage, $pitem->changed, $pitem->status, $pitem->orig_matnr, $pitem->orig_idnlf,
             $pitem->orig_purch_price, $pitem->orig_qty, $pitem->orig_lfdat, $pitem->nof, $pitem->new_lifnr, $archdate]);

        DB::insert("INSERT INTO porders_arch (ebeln, wtime, ctime, lifnr, ekgrp, erdat, ernam, curr, fxrate, changed, status, archdate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [$porder->ebeln, $porder->wtime, $porder->ctime, $porder->lifnr,
             $porder->ekgrp, $porder->erdat, $porder->ernam, $porder->curr,
             $porder->fxrate, $porder->changed, $porder->status, $archdate]);

        DB::commit();

        if (!$nodel && !DB::table("pitems")->where([["ebeln", "=", $ebeln]])->exists()) {
            DB::beginTransaction();
            DB::delete("delete from porders_cache where ebeln = '$ebeln'");
            DB::delete("delete from porders where ebeln = '$ebeln'");
            DB::commit();
        }

        return "OK";
    }

    public static function unArchiveItem($ebeln, $ebelp)
    {
        $porder = DB::table("porders_arch")->where("ebeln", $ebeln)->first();
        if ($porder == null) return "Archived purchase order does not exist";
        $pitem = DB::table("pitems_arch")->where([["ebeln", "=", $ebeln],["ebelp", "=", $ebelp]])->first();
        if ($pitem == null) return "Archived purchase order item does not exist";
        $pichanges = DB::table("pitemchg_arch")->where([["ebeln", "=", $ebeln],["ebelp", "=", $ebelp]])->get();
        $piproposals = DB::table("pitemchg_proposals_arch")->where([["ebeln", "=", $ebeln],["ebelp", "=", $ebelp]])->get();

        DB::beginTransaction();

        DB::delete("delete from pitems_arch where ebeln = '$ebeln' and ebelp = '$ebelp'");
        DB::delete("delete from pitemchg_arch where ebeln = '$ebeln' and ebelp = '$ebelp'");
        DB::delete("delete from pitemchg_proposals_arch where ebeln = '$ebeln' and ebelp = '$ebelp'");

        foreach($piproposals as $proposal) {
            DB::insert("INSERT INTO pitemchg_proposals (type, ebeln, ebelp, cdate, pos, lifnr, idnlf, matnr, mtext, lfdat, qty, qty_uom, purch_price, purch_curr, sales_price, sales_curr, infnr, source, accepted) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [$proposal->type, $proposal->ebeln, $proposal->ebelp, $proposal->cdate, $proposal->pos,
                    $proposal->lifnr, $proposal->idnlf, $proposal->matnr, $proposal->mtext, $proposal->lfdat,
                    $proposal->qty, $proposal->qty_uom, $proposal->purch_price, $proposal->purch_curr, $proposal->sales_price,
                    $proposal->sales_curr, $proposal->infnr, $proposal->source, $proposal->accepted]);
        }

        foreach($pichanges as $pichange) {
            DB::insert("INSERT INTO pitemchg (ebeln, ebelp, cdate, internal, ctype, stage, cuser, cuser_name, duser, oldval, newval, oebeln, oebelp, reason, acknowledged) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [$pichange->ebeln, $pichange->ebelp, $pichange->cdate, $pichange->internal, $pichange->ctype,
                    $pichange->stage, $pichange->cuser, $pichange->cuser_name, $pichange->duser, $pichange->oldval,
                    $pichange->newval, $pichange->oebeln, $pichange->oebelp, $pichange->reason, $pichange->acknowledged]);
        }

        DB::insert("INSERT INTO pitems (ebeln, ebelp, matnr, vbeln, posnr, idnlf, mtext, mfrnr, werks, purch_price, purch_curr, purch_prun, purch_puom, sales_price, sales_curr, sales_prun, sales_puom, qty, qty_uom, kunnr, shipto, ctv, ctv_name, lfdat, deldate, delqty, grdate, grqty, gidate, stage, pstage, changed, status, orig_matnr, orig_idnlf, orig_purch_price, orig_qty, orig_lfdat, nof, new_lifnr) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
               [$pitem->ebeln, $pitem->ebelp, $pitem->matnr, $pitem->vbeln, $pitem->posnr,
                $pitem->idnlf, $pitem->mtext, $pitem->mfrnr, $pitem->werks, $pitem->purch_price, $pitem->purch_curr,
                $pitem->purch_prun, $pitem->purch_puom, $pitem->sales_price, $pitem->sales_curr,
                $pitem->sales_prun, $pitem->sales_puom, $pitem->qty, $pitem->qty_uom, $pitem->kunnr,
                $pitem->shipto, $pitem->ctv, $pitem->ctv_name, $pitem->lfdat, $pitem->deldate,
                $pitem->delqty, $pitem->grdate, $pitem->grqty, $pitem->gidate, $pitem->stage,
                $pitem->pstage, $pitem->changed, $pitem->status, $pitem->orig_matnr, $pitem->orig_idnlf,
                $pitem->orig_purch_price, $pitem->orig_qty, $pitem->orig_lfdat, $pitem->nof, $pitem->new_lifnr]);

        if (!DB::table("porders")->where("ebeln", $ebeln)->exists())
            DB::insert("INSERT INTO porders (ebeln, wtime, ctime, lifnr, ekgrp, erdat, ernam, curr, fxrate, changed, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [$porder->ebeln, $porder->wtime, $porder->ctime, $porder->lifnr,
                    $porder->ekgrp, $porder->erdat, $porder->ernam, $porder->curr,
                    $porder->fxrate, $porder->changed, $porder->status]);

        DB::commit();

        if (!DB::table("pitems_arch")->where([["ebeln", "=", $ebeln]])->exists()) {
            DB::beginTransaction();
            DB::delete("delete from porders_cache where ebeln = '$ebeln'");
            DB::delete("delete from porders_arch where ebeln = '$ebeln'");
            DB::commit();
        }

        return "OK";
    }

    public static function performArchiving()
    {
        SAP::refreshDeliveryStatus(2);
        $pitems = DB::table("pitems")->where("grdate", "<>", "null")->get();
        foreach($pitems as $pitem) {
            Log::info("Archiving " . $pitem->ebeln . "/" . SAP::alpha_output($pitem->ebelp) . " (goods received)");
            self::archiveItem($pitem->ebeln, $pitem->ebelp);
        }

        $pitems = DB::select("select distinct ebeln, ebelp from pitems where stage = 'Z' and status = 'X'");
        foreach ($pitems as $pitem) {
            Log::info("Archiving " . $pitem->ebeln . "/" . SAP::alpha_output($pitem->ebelp) . " (item rejected)");
            self::archiveItem($pitem->ebeln, $pitem->ebelp);
        }

        // Delayed archiving
        $pitems = null;
        $pitems = DB::table("pitems")->where("new_lifnr", "<>", "")->get();
        foreach($pitems as $pitem) {
            if (DB::table("pitems_arch")->where([["ebeln", "=", $pitem->ebeln],["ebelp", "=", $pitem->ebelp]])->exists()) {
                Log::info("Archiving " . $pitem->ebeln . "/" . SAP::alpha_output($pitem->ebelp) . " (delayed)");
                self::archiveItem($pitem->ebeln, $pitem->ebelp);
            } else {
                Log::info("Delayed archiving " . $pitem->ebeln . "/" . SAP::alpha_output($pitem->ebelp));
                self::archiveItem($pitem->ebeln, $pitem->ebelp, true);
            }

        }
    }

}