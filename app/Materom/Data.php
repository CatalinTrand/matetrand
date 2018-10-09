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

    public static function getOrders($id, $mode, $history, $time_limit, $filter_category, $filter_barrier) {

        $orders_table = $history == 1 ? "porders" : "porders_arch";
        $items_table = $history == 1 ? "pitems" : "pitems_arch";
        $itemchg_table = $history == 1 ? "pitemchg" : "pitemchg_arch";

        //$_time = null;
        //if($time_limit != null){
        //    $parts = explode("/",$time_limit);
        //    $_time = mktime(0, 0, 0, $parts[1]  , $parts[0] , $parts[2]);
        //}

        $filter_sql = "";

        if($filter_category != null) {

            switch ($filter_category) {
                case 1:
                    $filter_sql .= " vbeln ";
                    break;
                case 2:
                    $filter_sql .= " ebeln ";
                    break;
                case 3:
                    $filter_sql .= " lifnr ";
                    break;
                case 4:
                    $filter_sql .= " lifnr_name ";
                    break;
            }

            if (strchr($filter_barrier, "*") != null){
                $replaced_barrier = str_replace("*","%",$filter_barrier);
                $filter_sql .= " like '$replaced_barrier'";
            } else
                $filter_sql .= " = '$filter_barrier'";

        }

        $time_search_sql = $time_limit === null ? "" : " where creation >= '$time_limit 00:00:00' ";

        $users = DB::select("select * from users where id ='$id'");
        if (count($users) == 0) return array();
        $user = $users[0];

        if(strlen($filter_sql) > 0)
            if(strlen($time_search_sql) == 0)
                $filter_sql = " where" . $filter_sql;
            else
                $filter_sql = " and" . $filter_sql;

        if ($user->role == "Administrator") {
            if ($mode) { // purchase orders
                $porders =  DB::select("select * from ".$orders_table . $time_search_sql . $filter_sql ." order by ebeln ");
                foreach($porders as $porder) $porder->vbeln = '';
                return $porders;
            } else {     // sales orders
                $result = array();
                $sorders = DB::select("select distinct vbeln from ". $items_table . $time_search_sql ." order by vbeln ");
                foreach($sorders AS $sorder) {
                    $porders = Data::getSalesOrderFlow($sorder->vbeln, $history);
                    foreach($porders AS $porder) {
                        if($filter_sql != null && strlen($filter_sql > 7))
                            $filter_sql = " and " . $filter_sql[6] . substr($filter_sql,6);
                        $orders = DB::select("select * from $orders_table where ebeln = '$porder->ebeln' $filter_sql order by ebeln");
                        foreach($orders as $order) {
                            $order->vbeln = $sorder->vbeln;
                            $order->ebeln .= $porder->ebeln_id;

                            $ssorder = DB::select("select * from $items_table where vbeln='$order->vbeln'")[0];
                            $order->kunnr = $ssorder->kunnr;
                            $order->kunnr_name = $ssorder->kunnr_name;
                            $order->ctv = $ssorder->ctv;
                            $order->ctv_name = $ssorder->ctv_name;
                            $order->shipto = $ssorder->shipto;
                            $order->shipto_name = $ssorder->shipto_name;
                        }
                        $result = array_merge($result, $orders);
                    }
                }
                return $result;
            }
        }

        if ($user->role == "CTV") {
            if ($mode) { // purchase orders \
                $porders2 = DB::select("select distinct ebeln from $items_table $time_search_sql and ctv = '$id'");
                $porders = array();
                foreach($porders2 as $porder2) {
                    $result = DB::select("select * from $orders_table where ebeln = '$porder2->ebeln' $filter_sql");
                    $porders = array_merge($porders, $result);
                }
                foreach($porders as $porder) $porder->vbeln = '';
                return $porders;
            } else {     // sales orders
                $result = array();
                $sorders = DB::select("select distinct vbeln from $items_table $time_search_sql and ctv = '$id' order by vbeln");
                foreach($sorders AS $sorder) {
                    $porders = Data::getSalesOrderFlow($sorder->vbeln, $history);
                    foreach($porders AS $porder) {
                        $orders = DB::select("select * from $orders_table where ebeln = '$porder->ebeln' $filter_sql order by ebeln");
                        foreach($orders as $order) {
                            $order->vbeln = $sorder->vbeln;
                            $order->ebeln .= $porder->ebeln_id;

                            $ssorder = DB::select("select * from $items_table where vbeln='$order->vbeln'")[0];
                            $order->kunnr = $ssorder->kunnr;
                            $order->kunnr_name = $ssorder->kunnr_name;
                            $order->ctv = $ssorder->ctv;
                            $order->ctv_name = $ssorder->ctv_name;
                            $order->shipto = $ssorder->shipto;
                            $order->shipto_name = $ssorder->shipto_name;
                        }
                        $result = array_merge($result, $orders);
                    }
                }
                return $result;
            }
        }

        if ($user->role == "Referent") {
            if ($mode) { // purchase orders \
                $porders = DB::select("select * from $orders_table $time_search_sql $filter_sql and = '$user->ekgrp' order by ebeln");
                foreach($porders as $porder) $porder->vbeln = '';
                return $porders;
            } else {     // sales orders
                $result = array();
                $porders = DB::select("select distinct ebeln from $orders_table $time_search_sql $filter_sql and ekgrp = '$user->ekgrp'");
                if (count($porders) == 0) return $porders;
                $sql = "";
                foreach($porders as $porder) {
                    if (!empty($sql)) $sql .= " or";
                    $sql .= " ebeln = '$porder->ebeln'";
                }
                $sql = "select distinct vbeln from $orders_table where" . $sql . " $filter_sql order by vbeln";
                $sorders = DB::select($sql);
                foreach($sorders AS $sorder) {
                    $porders = Data::getSalesOrderFlow($sorder->vbeln, $history);
                    foreach($porders AS $porder) {
                        $orders = DB::select("select * from $orders_table where ebeln = '$porder->ebeln' and ekgrp = '$user->ekgrp' $filter_sql order by ebeln");
                        foreach($orders as $order) {
                            $order->vbeln = $sorder->vbeln;
                            $order->ebeln .= $porder->ebeln_id;

                            $ssorder = DB::select("select * from $orders_table where vbeln='$order->vbeln'")[0];
                            $order->kunnr = $ssorder->kunnr;
                            $order->kunnr_name = $ssorder->kunnr_name;
                            $order->ctv = $ssorder->ctv;
                            $order->ctv_name = $ssorder->ctv_name;
                            $order->shipto = $ssorder->shipto;
                            $order->shipto_name = $ssorder->shipto_name;
                        }
                        $result = array_merge($result, $orders);
                    }
                }
                return $result;
            }
        }

        // Furnizor
        $brands = DB::select("select * from users_sel where id ='$id'");
        $xsql = "";
        foreach($brands as $brand) {
            $sel1 = "";
            if (empty(trim($brand->mfrnr))) continue;
            $sel1 = "mfrnr = '$brand->mfrnr'";
            $sel1 = "(". $sel1 . ")";
            if (empty($sql)) $xsql = $sel1;
            else $xsql .= ' or ' . $sel1;
        }
        if (!empty($xsql)) $xsql = " and (" . $xsql . ")";

        if ($mode) { // purchase orders \
            $porders = DB::select("select * from $orders_table $time_search_sql $filter_sql and lifnr = '$user->lifnr'" . $xsql . " order by ebeln");
            foreach($porders as $porder) {
                $sorders = DB::select("select distinct vbeln from $items_table where ebeln = '$porder->ebeln' order by vbeln");
                $porder->vbeln = $sorders[0]->vbeln;
                $ssorder = DB::select("select * from $items_table where vbeln='$porder->vbeln'")[0];
                if (strtoupper($sorders[0]->vbeln) != 'REPLENISH') $porder->vbeln = "SALESORDER";
                $porder->kunnr = $ssorder->kunnr;
                $porder->kunnr_name = $ssorder->kunnr_name;
                $porder->ctv = $ssorder->ctv;
                $porder->ctv_name = $ssorder->ctv_name;
                $porder->shipto = $ssorder->shipto;
                $porder->shipto_name = $ssorder->shipto_name;
            }
            return $porders;
        } else {     // sales orders
            $result = array();
            $porders = DB::select("select distinct ebeln from $orders_table $time_search_sql $filter_sql and lifnr = '$user->lifnr'" . $xsql . " order by ebeln");
            if (count($porders) == 0) return $porders;
            $sql = "";
            foreach($porders as $porder) {
                if (!empty($sql)) $sql .= " or";
                $sql .= " ebeln = '$porder->ebeln'";
            }
            $sql = "select distinct vbeln from $items_table where" . $sql . " order by vbeln";
            $sorders = DB::select($sql);
            foreach($sorders AS $sorder) {
                $porders = Data::getSalesOrderFlow($sorder->vbeln, $history);
                foreach($porders AS $porder) {
                    $orders = DB::select("select * from $orders_table where ebeln = '$porder->ebeln'".
                        " and lifnr = '$user->lifnr'".$xsql.
                        " $filter_sql order by ebeln");
                    foreach($orders as $order) {
                        $order->vbeln = $sorder->vbeln;
                        $order->ebeln .= $porder->ebeln_id;

                        $ssorder = DB::select("select * from $items_table where vbeln='$order->vbeln'")[0];
                        $order->kunnr = $ssorder->kunnr;
                        $order->kunnr_name = $ssorder->kunnr_name;
                        $order->ctv = $ssorder->ctv;
                        $order->ctv_name = $ssorder->ctv_name;
                        $order->shipto = $ssorder->shipto;
                        $order->shipto_name = $ssorder->shipto_name;
                        if ($order->vbeln != 'REPLENISH') $order->vbeln = "SALESORDER";
                    }
                    $result = array_merge($result, $orders);
                }
            }
            return $result;
        }
        $sql = "select * from $orders_table $time_search_sql $filter_sql and id ='$id'";
        return DB::select($sql);
    }

    static public function getSalesOrderFlow($vbeln, $history) {

        $orders_table = $history == 1 ? "porders" : "porders_arch";
        $items_table = $history == 1 ? "pitems" : "pitems_arch";
        $itemchg_table = $history == 1 ? "pitemchg" : "pitemchg_arch";

        $xsql = "";
        if ($vbeln == "SALESORDER") {
            $brands = DB::select("select * from users_sel where id ='" . Auth::user()->id . "'");
            $xsql = "";
            foreach($brands as $brand) {
                $sel1 = "";
                if (empty(trim($brand->mfrnr))) continue;
                $sel1 = "mfrnr = '$brand->mfrnr'";
                $sel1 = "(". $sel1 . ")";
                if (empty($sql)) $xsql = $sel1;
                else $xsql .= ' or ' . $sel1;
            }
            if (!empty($xsql)) $xsql = " and (" . $xsql . ")";
            $porders = DB::select("select distinct ebeln from $orders_table where lifnr = '" . Auth::user()->lifnr . "' ". $xsql . " order by ebeln");
        } else {
            if (Auth::user()->role == "Furnizor") {
                $brands = DB::select("select * from users_sel where id ='" . Auth::user()->id . "'");
                $xsql = "";
                foreach($brands as $brand) {
                    $sel1 = "";
                    if (empty(trim($brand->mfrnr))) continue;
                    $sel1 = "mfrnr = '$brand->mfrnr'";
                    $sel1 = "(". $sel1 . ")";
                    if (empty($sql)) $xsql = $sel1;
                    else $xsql .= ' or ' . $sel1;
                }
                if (!empty($xsql)) $xsql = " and (" . $xsql . ")";
                $porders = DB::select("select distinct ebeln from $orders_table where lifnr = '" . Auth::user()->lifnr . "' ". $xsql . " order by ebeln");
                $xsql = "";
                foreach($porders as $porder) {
                    $sel1 = "ebeln = '$porder->ebeln'";
                    $sel1 = "(". $sel1 . ")";
                    if (empty($xsql)) $xsql = $sel1;
                    else $xsql .= ' or ' . $sel1;
                }
                if (!empty($xsql)) $xsql = " and (" . $xsql . ")";
            } elseif (Auth::user()->role == "Referent") {
                $porders = DB::select("select distinct ebeln from $orders_table where ekgrp = '" . Auth::user()->ekgrp . "' order by ebeln");
                $xsql = "";
                foreach($porders as $porder) {
                    $sel1 = "ebeln = '$porder->ebeln'";
                    $sel1 = "(". $sel1 . ")";
                    if (empty($xsql)) $xsql = $sel1;
                    else $xsql .= ' or ' . $sel1;
                }
                if (!empty($xsql)) $xsql = " and (" . $xsql . ")";
            }
            $porders = DB::select("select distinct ebeln from $items_table where vbeln = '$vbeln'" . $xsql . " order by ebeln");
        }
        $sql = "";
        foreach($porders as $porder) {
            if (!empty($sql)) $sql .= " or";
            $sql .= " ebeln = '$porder->ebeln'";
        }
        $sql = "select distinct vbeln, ebeln from $items_table where" . $sql . " order by ebeln, vbeln";
        $sorders = DB::select($sql);
        if ($vbeln == "SALESORDER") {
            foreach($sorders as $sorder) {
                if ($sorder->vbeln != "REPLENISH") $sorder->vbeln = $vbeln;
            }
            asort($sorders);
            $prev_sorder = '##########';
            $prev_porder = '##########';
            $i = 0;
            foreach($sorders as $sorder) {
                if ($prev_sorder != $sorder->vbeln) {
                    $prev_sorder = $sorder->vbeln;
                    $prev_porder = $sorder->ebeln;
                    $i = $i + 1;
                } else {
                    if ($prev_porder != $sorder->ebeln) {
                        $prev_porder = $sorder->ebeln;
                        $i = $i + 1;
                    } else {
                        unset($sorders[$i]);
                        $i = $i + 1;
                    }
                }
            }
        }
        $prev_porder = '##########';
        $result = array();
        foreach($sorders as $sorder) {
            if ($sorder->ebeln != $prev_porder) {
                $prev_porder = $sorder->ebeln;
                $xid = 65; // ascii A
            }
            $sorder->ebeln_id = chr($xid);
            $xid = $xid + 1;
            if ($sorder->vbeln == $vbeln) array_push($result, $sorder);
        }
        return $result;
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
        $norder->lifnr_name = $saphdr["LIFNR_NAME"];
        $norder->ekgrp = $saphdr["EKGRP"];
        $norder->ekgrp_name = $saphdr["EKGRP_NAME"];
        $erdat->hour = $now->hour;
        $erdat->minute = $now->minute;
        $erdat->second = $now->second;
        $norder->erdat = $erdat->toDateTimeString();
        $norder->ernam = $saphdr["ERNAM"];
        $norder->curr = $saphdr["CURR"];
        $norder->fxrate = $saphdr["FXRATE"];
        $norder->nof = true;
        $now->addHours(24);
        $norder->wtime = $now->toDateTimeString();
        $now->addHours(24);
        $norder->ctime = $now->toDateTimeString();

        DB::beginTransaction();

        if (is_null($order)) {
            $sql = "insert into porders (ebeln, nof, wtime, ctime, lifnr, lifnr_name, ekgrp, ekgrp_name, " .
                "erdat, ernam, curr, fxrate) values " .
                "('$norder->ebeln', '$norder->nof', '$norder->wtime', '$norder->ctime', '$norder->lifnr', " .
                "'$norder->lifnr_name', '$norder->ekgrp', '$norder->ekgrp_name', '$norder->erdat', " .
                "'$norder->ernam', '$norder->curr', '$norder->fxrate')";
            DB::insert($sql);
        } else {
            $sql = "update porders set nof = '$norder->nof', " .
                "lifnr = '$norder->lifnr', ".
                "lifnr_name = '$norder->lifnr_name', ".
                "ekgrp = '$norder->ekgrp', ".
                "ekgrp_name = '$norder->ekgrp_name', " .
                "curr = '$norder->curr', " .
                "fxrate = '$norder->fxrate') " .
                "where $ebeln = '$norder->ebeln'";
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
            $nitem->mtext = $sapitm["MAT_TEXT"];
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
            $nitem->vbeln = trim($sapitm["VBELN"]);
            if (is_null($nitem->vbeln) || empty($nitem->vbeln)) $nitem->vbeln = "REPLENISH";
            $nitem->posnr = $sapitm["POSNR"];
            $nitem->sales_price = $sapitm["SALES_PRICE"];
            $nitem->sales_curr = $sapitm["SALES_CURR"];
            $nitem->sales_prun = $sapitm["SALES_PRUN"];
            $nitem->sales_puom = $sapitm["SALES_PUOM"];
            $nitem->kunnr = $sapitm["KUNNR"];
            $nitem->kunnr_name = $sapitm["KUNNR_NAME"];
            $nitem->shipto = $sapitm["SHIPTO"];
            $nitem->shipto_name = $sapitm["SHIPTO_NAME"];
            $nitem->ctv = $sapitm["CTV"];
            $nitem->ctv_name = $sapitm["CTV_NAME"];
            $nitem->stage = 'F';
            $nitem->changed = false;

            $users = DB::select("select * from users where sapuser = '$nitem->ctv' and role = 'CTV'");
            if (count($users) > 0) $nitem->ctv = $users[0]->id;
            $sql = "insert into pitems (ebeln, ebelp, idnlf, mtext, qty, qty_uom, lfdat, mfrnr, mfrnr_name,".
                                       "purch_price, purch_curr, purch_prun, purch_puom, ".
                                       "sales_price, sales_curr, sales_prun, sales_puom, ".
                                       "kunnr, kunnr_name, shipto, shipto_name, ctv, ctv_name, stage, changed ".
                                       ") values (".
                   "'$nitem->ebeln', '$nitem->ebelp', '$nitem->idnlf', '$nitem->mtext',$nitem->qty, '$nitem->qty_uom', ".
                   "'$nitem->lfdat', '$nitem->mfrnr', '$nitem->mfrnr_name',".
                   "'$nitem->purch_price', '$nitem->purch_curr', ".
                   "$nitem->purch_prun, '$nitem->purch_puom', ".
                   "'$nitem->sales_price', '$nitem->sales_curr', $nitem->sales_prun, ".
                   "'$nitem->sales_puom', '$nitem->kunnr', '$nitem->kunnr_name', ".
                   "'$nitem->shipto', '$nitem->shipto_name', '$nitem->ctv', '$nitem->ctv_name', ".
                   "'$nitem->stage', 0)";

            DB::insert($sql);
        }

        DB::commit();
        return "OK";
    }
}