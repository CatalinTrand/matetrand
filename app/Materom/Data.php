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
        else $order = $orders[0];
        $norder = new \stdClass();
        $now = new Carbon();
        $erdat = new Carbon();
        $norder->ebeln = $ebeln;
        $norder->lifnr = $saphdr["LIFNR"];
        $norder->ekgrp = $saphdr["EKGRP"];
        if (!DB::table("users")->where(["lifnr" => $norder->lifnr, "role" => "Furnizor", "active" => 1])->exists())
            if (!DB::table("users")->where(["ekgrp" => $norder->ekgrp, "role" => "Referent", "active" => 1])->exists())
                return "OK";
        $erdat->hour = $now->hour;
        $erdat->minute = $now->minute;
        $erdat->second = $now->second;
        $norder->erdat = $erdat->toDateTimeString();
        $norder->ernam = $saphdr["ERNAM"];
        $norder->curr = $saphdr["CURR"];
        $norder->fxrate = $saphdr["FXRATE"];
        $norder->nof = true;
        $now->addHours(12);
        $norder->wtime = $now->toDateTimeString();
        $now->addHours(24);
        $norder->ctime = $now->toDateTimeString();
        $norder->changed = '0';
        $norder->status = '';

        DB::beginTransaction();

        if (is_null($order)) {
            $sql = "insert into porders (ebeln, nof, wtime, ctime, lifnr, ekgrp, erdat, ernam, curr, fxrate, changed, status) values " .
                "('$norder->ebeln', '$norder->nof', '$norder->wtime', '$norder->ctime', '$norder->lifnr', " .
                "'$norder->ekgrp', $norder->erdat', '$norder->ernam', '$norder->curr', '$norder->fxrate', '$norder->changed', '$norder->status')";
            DB::insert($sql);
        } else {
            $sql = "update porders set nof = '$norder->nof', " .
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
            $nitem->idnlf = $sapitm["IDNLF"];
            $nitem->mtext = $sapitm["MTEXT"];
            $nitem->qty = $sapitm["MENGE"];
            $nitem->qty_uom = $sapitm["MEINS"];
            $nitem->lfdat = $sapitm["LFDAT"];
            $lfdat = new Carbon();
            $lfdat->year = substr($nitem->lfdat, 0, 4);
            $lfdat->month = substr($nitem->lfdat, 4, 2);
            $lfdat->day = substr($nitem->lfdat, 6, 2);
            $nitem->lfdat = $lfdat->toDateTimeString();
            $nitem->mfrnr = $sapitm["MFRNR"];
            $nitem->mfrnr_name = $sapitm["MFRNR_NAME"];
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

            $users = DB::select("select * from users where sapuser = '$nitem->ctv' and role = 'CTV'");
            if (count($users) > 0) $nitem->ctv = $users[0]->id;
            if (is_null($citem)) {
                $sql = "insert into pitems (ebeln, ebelp, idnlf, mtext, qty, qty_uom, lfdat, mfrnr, mfrnr_name,".
                                           "purch_price, purch_curr, purch_prun, purch_puom, ".
                                           "sales_price, sales_curr, sales_prun, sales_puom, ".
                                           "vbeln, posnr, kunnr, shipto, ctv, ctv_name, stage, changed, status ".
                                           ") values (".
                       "'$nitem->ebeln', '$nitem->ebelp', '$nitem->idnlf', '" . substr($nitem->mtext, 0, 35) . "',$nitem->qty, '$nitem->qty_uom', ".
                       "'$nitem->lfdat', '$nitem->mfrnr', '$nitem->mfrnr_name',".
                       "'$nitem->purch_price', '$nitem->purch_curr', ".
                       "$nitem->purch_prun, '$nitem->purch_puom', ".
                       "'$nitem->sales_price', '$nitem->sales_curr', $nitem->sales_prun, ".
                       "'$nitem->sales_puom', '$nitem->vbeln', '$nitem->posnr', '$nitem->kunnr', ".
                       "'$nitem->shipto', '$nitem->ctv', '$nitem->ctv_name', ".
                       "'$nitem->stage', 0, '$nitem->status')";

                DB::insert($sql);

            } else {
                $sql = "update pitems set idnlf = '$nitem->idnlf', " .
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
        Mailservice::sendNotification($norder->lifnr,$ebeln);
        return "OK";
    }

}