<?php
/**
 * Created by PhpStorm.
 * User: Radu
 * Date: 12.10.2018
 * Time: 12:01
 */

namespace App\Materom;

use App\Materom\Orders\POrder;
use App\Materom\Orders\POrderItem;
use App\Materom\Orders\POrderItemChg;
use App\Materom\SAP\MasterData;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class Orders
{
    public const salesorder = "SALESORDER";
    public const stockorder = "!REPLENISH";

    public static $overdue_items;
    public static $overdue_orders;

    public static function newCacheToken($length = 40) // after an idea by Scott Arciszewski
    {
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet .= "0123456789";
        $max = strlen($codeAlphabet);

        for ($i = 0; $i < $length; $i++) {
            $token .= $codeAlphabet[random_int(0, $max - 1)];
        }
        return $token;
    }

    private static function addFilters(string ...$filters)
    {
        $filter_sum = "";
        foreach ($filters as $filter)
            $filter_sum = self::addFilter($filter_sum, trim($filter));
//      if (!empty($filter_sum)) $filter_sum = "(" . $filter_sum . ")";
        return $filter_sum;
    }

    private static function addFilter($filter_sum, $filter)
    {
        if (empty($filter)) return $filter_sum;
        if (empty($filter_sum)) return $filter;
        return $filter_sum . " and " . $filter;
    }

    private static function processFilter($field, $filter_val, $mode = 0)
    {
        if (is_null($filter_val) || empty(trim($filter_val))) return "";
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

    public static function fillCache()
    {

        $groupByPO = Session::get('groupOrdersBy');
        if (!isset($groupByPO)) {
            $groupByPO = 1;
            if (Auth::user()->role == "CTV") $groupByPO = 4;
        }

        $history = Session::get("filter_history");
        if (!isset($history)) $history = 1;
        else $history = intval($history);

        $backorders = Session::get("filter_backorders");

        $goodsreceipt = Session::get("filter_goodsreceipt");
        if (!isset($goodsreceipt) || $goodsreceipt == null) $goodsreceipt = 0;

        $filter_deldate_low = Session::get("filter_deldate_low");
        if (!isset($filter_deldate_low) || $filter_deldate_low == null || $filter_deldate_low == "") $filter_deldate_low = "2000-01-01";
        $filter_deldate_high = Session::get("filter_deldate_high");
        if (!isset($filter_deldate_high) || $filter_deldate_high == null || $filter_deldate_high == "") $filter_deldate_high = "2199-12-31";
        $filter_deldate_high .= " 23:59:59";

        $filter_etadate_low = Session::get("filter_etadate_low");
        if (!isset($filter_etadate_low) || $filter_etadate_low == null || $filter_etadate_low == "") $filter_etadate_low = "2000-01-01";
        $filter_etadate_high = Session::get("filter_etadate_high");
        if (!isset($filter_etadate_high) || $filter_etadate_high == null || $filter_etadate_high == "") $filter_etadate_high = "2199-12-31";
        $filter_etadate_high .= " 23:59:59";

        $time_limit = Session::get("filter_archdate");
        $filter_vbeln = Session::get("filter_vbeln");
        $filter_ebeln = Session::get("filter_ebeln");
        $filter_matnr = Session::get("filter_matnr");
        $filter_mtext = trim(Session::get("filter_mtext"));
        if (!empty($filter_mtext) && strpos($filter_mtext, "*") === false)
            $filter_mtext = "*" . $filter_mtext . "*";
        $filter_ekgrp = Session::get("filter_ekgrp");
        $filter_lifnr = Session::get("filter_lifnr");
        $filter_lifnr_name = trim(Session::get("filter_lifnr_name"));
        if (!empty($filter_lifnr_name) && strpos($filter_lifnr_name, "*") === false)
            $filter_lifnr_name = "*" . $filter_lifnr_name . "*";
        $filter_kunnr = Session::get("filter_kunnr");
        $filter_kunnr_name = trim(Session::get("filter_kunnr_name"));
        if (!empty($filter_kunnr_name) && strpos($filter_kunnr_name, "*") === false)
            $filter_kunnr_name = "*" . $filter_kunnr_name . "*";

        $cacheid = Session::get('materomdbcache');
        if (!isset($cacheid) || empty($cacheid)) return;

        $orders_table = $history == 1 ? System::$table_porders : System::$table_porders . "_arch";
        $items_table = $history == 1 ? System::$table_pitems : System::$table_pitems . "_arch";

        $filter_vbeln_sql = self::processFilter($items_table . ".vbeln", $filter_vbeln, 10);
        $filter_ebeln_sql = self::processFilter($orders_table . ".ebeln", $filter_ebeln, 10);
        $filter_matnr_sql = self::processFilter($items_table . ".idnlf", $filter_matnr);
        $filter_mtext_sql = self::processFilter($items_table . ".mtext", $filter_mtext);
        $filter_ekgrp_sql = self::processFilter($orders_table . ".ekgrp", $filter_ekgrp);
        $filter_lifnr_sql = self::processFilter($orders_table . ".lifnr", $filter_lifnr, 10);
        $filter_lifnr_name_sql = self::processFilter(System::$table_sap_lfa1.".name1", $filter_lifnr_name);
        $filter_kunnr_sql = self::processFilter($items_table . ".kunnr", $filter_kunnr, 10);
        $filter_kunnr_name_sql = self::processFilter(System::$table_sap_kna1.".name1", $filter_kunnr_name);

        $filter_sql = $time_limit === null ? "" : "$items_table.archdate >= '$time_limit 00:00:00'";
        if ($history != 2) $filter_sql = "";
        $filter_sql = self::addFilters($filter_sql, $filter_ebeln_sql, $filter_ekgrp_sql,
            $filter_lifnr_sql, $filter_lifnr_name_sql, $filter_kunnr_sql, $filter_kunnr_name_sql);
        $filter_sql = self::addFilters($filter_sql, $filter_vbeln_sql, $filter_matnr_sql, $filter_mtext_sql);

        if (Auth::user()->role == "Furnizor") {
            $manufacturers = DB::select("select distinct mfrnr from ". System::$table_users_sel ." where id ='" . Auth::user()->id . "'");
            $sql = "";
            foreach ($manufacturers as $manufacturer) {
                if (empty(trim($manufacturer->mfrnr))) continue;
                $sel1 = $items_table . ".mfrnr = '$manufacturer->mfrnr'";
                if (empty($sql)) $sql = $sel1;
                else $sql .= ' or ' . $sel1;
            }
            if (!empty($sql)) $sql = "(" . $sql . ")";
            $filter_sql = self::addFilter($filter_sql,
                self::processFilter($orders_table . ".lifnr", Auth::user()->lifnr, 10), $sql);
            $filter_sql = self::addFilter($filter_sql,
                "($items_table.werks <> 'D000' and $items_table.werks <> 'G000')");
        } elseif (Auth::user()->role == "Referent") {
/*
            $refs = DB::select("select distinct users_ref.id, users.lifnr from users_ref " .
                "join users using (id) " .
                "where refid ='" . Auth::user()->id . "'");
            $filter_refs_sql = "";
            foreach ($refs as $ref) {
                $manufacturers = DB::select("select distinct mfrnr from ". System::$table_users_sel ." where id ='" . $ref->id . "'");
                $sql = "";
                foreach ($manufacturers as $manufacturer) {
                    if (empty(trim($manufacturer->mfrnr))) continue;
                    $sel1 = $items_table . ".mfrnr = '$manufacturer->mfrnr'";
                    if (empty($sql)) $sql = $sel1;
                    else $sql .= ' or ' . $sel1;
                }
                if (!empty($sql)) $sql = "( $sql )";
                $sql = self::addFilter($sql, self::processFilter($orders_table . ".lifnr", $ref->lifnr, 10));
                $filter_refs_sql .= " or ( $sql )";
            }
            if (!empty($filter_refs_sql))
                $filter_refs_sql = "((" . substr($filter_refs_sql, 4) . ") or " .
                    self::processFilter($orders_table . ".ekgrp", Auth::user()->ekgrp) . ")";
            else
*/
            $filter_refs_sql = "(" . self::processFilter($orders_table . ".ekgrp", Auth::user()->ekgrp) . ")";
            $filter_sql = self::addFilter($filter_sql, $filter_refs_sql);
        } elseif (Auth::user()->role == "CTV") {
            $clients = DB::select("select distinct kunnr from ". System::$table_user_agent_clients ." where id = '" .
                Auth::user()->id . "'");
            $sql = "";
            foreach ($clients as $client) {
                $sel1 = $items_table . ".kunnr = '$client->kunnr'";
                if (empty($sql)) $sql = $sel1;
                else $sql .= ' or ' . $sel1;
            }
            if (!empty($sql)) {
                $sql = "( $sql )";
                $filter_sql = self::addFilter($filter_sql, $sql);
            }
            $filter_sql = self::addFilter($filter_sql,
                "($items_table.werks <> 'D000' and $items_table.werks <> 'G000')");
        }

        if ($groupByPO == 2) $filter_sql = self::addFilter($filter_sql, "$items_table.vbeln <> '" . Orders::stockorder . "'");
        elseif ($groupByPO == 3) $filter_sql = self::addFilter($filter_sql, "$items_table.vbeln = '" . Orders::stockorder . "'");

        $backorder_sql = "";
        if ($backorders == "1") $backorder_sql = "$items_table.backorder = 1";
        if ($backorders == "2") $backorder_sql = "$items_table.backorder = 0";
        if (!empty($backorder_sql)) {
            if (empty($filter_sql)) $filter_sql = $backorder_sql;
            else $filter_sql .= " and " . $backorder_sql;
        }

        $goodsreceipt_sql = "";
        if ($goodsreceipt <> 0) $goodsreceipt_sql = "$items_table.grdate is not NULL";
        if (!empty($goodsreceipt_sql)) {
            if (empty($filter_sql)) $filter_sql = $goodsreceipt_sql;
            else $filter_sql .= " and " . $goodsreceipt_sql;
        }

        $deldate_sql = "$items_table.lfdat between '$filter_deldate_low' and '$filter_deldate_high'";
        if (empty($filter_sql)) $filter_sql = $deldate_sql;
        else $filter_sql .= " and " . $deldate_sql;

        $etadate_sql = "$items_table.etadt between '$filter_etadate_low' and '$filter_etadate_high'";
        if (empty($filter_sql)) $filter_sql = $etadate_sql;
        else $filter_sql .= " and " . $etadate_sql;

        // final sql build
        $sql = "select " . $items_table . ".ebeln, " . $items_table . ".ebelp, " . $items_table . ".vbeln " .
            " from " . $items_table .
            " join " . $orders_table . " using (ebeln)";
        if (!empty($filter_lifnr_name_sql)) $sql .= " join " .System::$table_sap_lfa1. " using (lifnr)";
        if (!empty($filter_kunnr_name_sql)) $sql .= " join " .System::$table_sap_kna1. " using (kunnr)";
        if (!empty($filter_sql)) $sql .= " where " . $filter_sql;
        $sql .= " order by " . $items_table . ".ebeln, " . $items_table . ".ebelp";
//      Log::debug($sql);
        // ...and run
        $items = DB::select($sql);

        // Fill the cache
        $cache_date = now();
        DB::beginTransaction();
        DB::delete("delete from ". System::$table_porders_cache ." where session = '$cacheid'");
        DB::delete("delete from ". System::$table_pitems_cache ." where session = '$cacheid'");

        if (!empty($items)) {

            // Order cache
            $psql = "insert into " . System::$table_porders_cache . " (session, ebeln, cache_date) values ";
            $isql = "insert into " . System::$table_pitems_cache . " (session, ebeln, ebelp, vbeln, cache_date) values ";

            $prev_ebeln = '$#$#$#$#$#';
            foreach ($items as $item) {
                if ($item->ebeln != $prev_ebeln) {
                    $prev_ebeln = $item->ebeln;
                    $psql .= " ('$cacheid', '$prev_ebeln', '$cache_date'),";
                }
                $isql .= " ('$cacheid', '$item->ebeln', '$item->ebelp', '$item->vbeln', '$cache_date'),";
            }

            DB::insert(substr($psql, 0, -1) . ';');
            DB::insert(substr($isql, 0, -1) . ';');
        }

        DB::commit();

    }

    static public function loadFromCache($s_order = null, $p_order = null, $refresh_dlv = false)
    {
        // if (Auth::user()->role == "Administrator") Log::debug("Performance check: start loadFromCache");

        $result = array();
        $cacheid = Session::get('materomdbcache');
        if (!isset($cacheid) || empty($cacheid)) return;
        set_time_limit(120);

        $history = Session::get("filter_history");
        $filter_status = Session::get("filter_status");
        $inquirements = Session::get("filter_inquirements");
        $overdue = Session::get("filter_overdue");
        if (!isset($overdue) || $overdue == null) $overdue = 0;
        $overdue_low = Session::get("filter_overdue_low");
        if (!isset($overdue_low) || $overdue_low == null || $overdue_low == "") $overdue_low = 0;
        $overdue_high = Session::get("filter_overdue_high");
        if (!isset($overdue_high) || $overdue_high == null || $overdue_high == "") $overdue_high = 99999;

        if ($history == null) $history = 1;
        else $history = intval($history);

        $orders_table = $history == 1 ? System::$table_porders : System::$table_porders . "_arch";
        $items_table = $history == 1 ? System::$table_pitems : System::$table_pitems . "_arch";
        $itemchanges_table = $history == 1 ? System::$table_pitemchg : System::$table_pitemchg . "_arch";

        $status_filter = "";
        if ($filter_status == 'AP') {
            $status_filter = " and $items_table.status = 'A' ";
        }
        if ($filter_status == 'RE') {
            if (Auth::user()->role == 'Furnizor')
                $status_filter = " and ( $items_table.status = 'X' OR $items_table.status = 'R' )";
            else
                $status_filter = " and $items_table.status = 'X' ";
        }

        if ($history == 1 && $s_order == null && $p_order == null && $refresh_dlv) {
            $items = DB::select("select ebeln, ebelp from ". System::$table_pitems_cache ." where session = '$cacheid'");
            if ($items != null) SAP::refreshDeliveryStatus(1, $items);
        }

        $porders_sql = "";
        $pitems_sql = "";
        if ($p_order != null) {
            $porders_sql = " and ". System::$table_porders_cache .".ebeln = '$p_order'";
            $pitems_sql = " and ". System::$table_pitems_cache .".ebeln = '$p_order'";
        }
        if ($s_order != null) {
            if ((Auth::user()->role == 'Furnizor') && ($s_order == self::salesorder))
                $pitems_sql .= " and ". System::$table_pitems_cache .".vbeln <> '" . self::stockorder . "'";
            else $pitems_sql .= " and ". System::$table_pitems_cache .".vbeln = '$s_order'";
            $pitems = DB::select("select distinct ebeln, ebelp from ". System::$table_pitems_cache .
                " where session = '$cacheid'" . $pitems_sql . " order by ebeln, ebelp");
            if (empty($pitems)) return $result;
            $pitems_sql .= " and (";
            $porders_sql .= " and (";
            $prev_ebeln = "";
            foreach ($pitems as $pitem) {
                if ($prev_ebeln != $pitem->ebeln) {
                    $prev_ebeln = $pitem->ebeln;
                    $porders_sql .= System::$table_porders_cache .".ebeln = '$pitem->ebeln' or ";
                }
                $pitems_sql .= "(". System::$table_pitems_cache .".ebeln = '$pitem->ebeln' and ". System::$table_pitems_cache .".ebelp = '$pitem->ebelp') or ";
            }
            $porders_sql = substr($porders_sql, 0, -4) . ")";
            $pitems_sql = substr($pitems_sql, 0, -4) . ")";
        }

        $porders = DB::select("select $orders_table.* from $orders_table join ". System::$table_porders_cache ." using (ebeln) " .
            "where ". System::$table_porders_cache .".session = '$cacheid' " . $porders_sql .
            "order by $orders_table.ebeln");
        if (empty($porders)) return $result;

        $pitems = DB::select("select $items_table.* from $items_table join ". System::$table_pitems_cache ." using (ebeln, ebelp) " .
            " where ". System::$table_pitems_cache .".session = '$cacheid'" . $pitems_sql . $status_filter .
            " order by $items_table.ebeln, $items_table.ebelp");
        if (empty($pitems)) return $result;

        $pitemschg = DB::select("select $itemchanges_table.* from $itemchanges_table " .
            "join ". System::$table_pitems_cache ." using (ebeln, ebelp)" .
            " where ". System::$table_pitems_cache .".session = '$cacheid'" . $pitems_sql .
            " order by $itemchanges_table.ebeln, $itemchanges_table.ebelp, $itemchanges_table.cdate desc");

        $xitem = 0;
        $xitemchg = 0;
        $now = Carbon::now();
        $now->hour = 0;
        $now->minute = 0;
        $now->second = 0;
        self::$overdue_orders = 0;
        self::$overdue_items = 0;
        foreach ($porders as $porder) {
            $_porder = new POrder($porder);
            $overdueitems = false;
            while ($xitem < count($pitems) && ($pitem = $pitems[$xitem])->ebeln == $porder->ebeln) {
                $xitem++;
                $_pitem = new POrderItem($pitem);
                while ($xitemchg < count($pitemschg)
                    && (($pitemchg = $pitemschg[$xitemchg])->ebeln == $pitem->ebeln)
                    && (($pitemchg = $pitemschg[$xitemchg])->ebelp == $pitem->ebelp)) {
                    $xitemchg++;
                    $_pitemchg = new POrderItemChg($pitemchg);
                    $_pitemchg->fill($_pitem);
                    $_pitem->appendChange($_pitemchg);
                }
                $_pitem->fill($_porder);
                if (($inquirements != 1) ||
                    ((($_pitem->inq_reply == 1) || (Auth::user()->role == "Furnizor")
                                                || ((Auth::user()->role == "Referent") && ($_pitem->crefo == 1))) &&
                     (($_pitem->owner != 0) || (Auth::user()->role == "Administrator") ||
                      (((Auth::user()->role == "Furnizor") || ((Auth::user()->role == "Referent") && ($_pitem->crefo == 1))) && (($_pitem->info == 4) || ($_pitem->info == 5)))
                     )
                    )
                   )
                    if ($overdue == 0 || (($_pitem->lfdat < $now) && (($_pitem->dodays > $overdue_low) && ($_pitem->dodays < $overdue_high)))) {
                        $_porder->appendItem($_pitem);
                        self::$overdue_items++;
                        $overdueitems = true;
                    }
            }
            $_porder->fill();
            if ($_porder->items != null) {
                $result[$porder->ebeln] = $_porder;
                if ($overdueitems) self::$overdue_orders++;
            }
        }

        // if (Auth::user()->role == "Administrator") Log::debug("Performance check: end loadFromCache");

        return $result;
    }

    public static function overdues()
    {
        return "" . self::$overdue_orders . "/" . self::$overdue_items;
    }

    public static function readPOrder($ebeln)
    {
        $porder = DB::table(System::$table_porders)->where("ebeln", $ebeln)->first();
        if ($porder == null) return null;
        $_porder = new POrder($porder);
        $pitems = DB::table(System::$table_pitems)->where("ebeln", $ebeln)->get();
        if ($pitems != null)
        foreach ($pitems as $pitem) {
            $_pitem = new POrderItem($pitem);
            $pichgs = DB::select("select * from ". System::$table_pitemchg ." where ebeln = '$pitem->ebeln' and ebelp = '$pitem->ebelp' order by ebeln, ebelp, cdate desc");
            if ($pichgs != null)
            foreach ($pichgs as $pichg) {
                $_pitemchg = new POrderItemChg($pichg);
                $_pitemchg->fill($_pitem);
                $_pitem->appendChange($_pitemchg);
            }
            $_pitem->fill($_porder);
            $_porder->appendItem($_pitem);
        }
        $_porder->fill();
        return $_porder;
    }

    public static function getOrderList($groupByPO = null)
    {

        if (!isset($groupByPO) || $groupByPO == null)
            $groupByPO = Session::get("groupOrdersBy");
        if (!isset($groupByPO)) $groupByPO = 1;

        $result = self::loadFromCache(null, null, true);

        if ($groupByPO == 4) {
            $orders = array();
            if (($result != null) && !empty($result))
            foreach ($result as $porder) {
                foreach ($porder->salesorders as $vbeln => $ebelp) {
                    $sorder = isset($orders[$vbeln]) ? $orders[$vbeln] : null;
                    if (!isset($sorder)) {
                        $sorder = new \stdClass();
                        $sorder->info = $porder->info;
                        $sorder->wtime = $porder->wtime;
                        $sorder->ctime = $porder->ctime;
                        $sorder->vbeln = $vbeln;
                        $pitem = $porder->items[$ebelp];
                        $sorder->kunnr = $pitem->kunnr;
                        $sorder->kunnr_name = $pitem->kunnr_name;
                        $sorder->shipto = $pitem->shipto;
                        $sorder->shipto_name = $pitem->shipto_name;
                        $sorder->ctv = $pitem->ctv;
                        $sorder->ctv_name = $pitem->ctv_name;

                        $sorder->info = 0;     // 0=empty, 1=new order, 2=warning, 3=critical, 4=new message
                        $sorder->owner = 0;    // 0=no, 1=direct, 2=indirect
                        $sorder->changed = 0;  // 0=no, 1=direct, 2=indirect
                        $sorder->accepted = 0; // 0=no, 1=direct, 2=indirect
                        $sorder->rejected = 0; // 0=no, 1=direct, 2=indirect
                        $sorder->inquired = 0; // 0=no, 1=tentatively accepted, 2=rejected, 3=simple message
                        $sorder->inq_reply = 0;

                        // buttons
                        $sorder->accept = 0;   // 0-no, 1=display
                        $sorder->reject = 0;   // 0=no, 1=display
                        $sorder->inquire = 0;  // 0=no

                        $orders[$sorder->vbeln] = $sorder;

                    } else {
                        if ($porder->info > $sorder->info) $sorder->info = $porder->info;
                        if ($porder->wtime < $sorder->wtime) $sorder->wtime = $porder->wtime;
                        if ($porder->ctime < $sorder->ctime) $sorder->ctime = $porder->ctime;
                    }
                }
            }

        } else $orders = $result;

        if ($orders != null) ksort($orders);
        else $orders = array();
        return $orders;
    }

    static function cmp_ebeln($a, $b)
    {
        return strcmp($a->ebeln, $b->ebeln);
    }

    static function cmp_cdate($a, $b)
    {
        return strcmp($b->cdate, $a->cdate);
    }

    static function cmp_cuser($a, $b)
    {
        return strcmp($a->cuser, $b->cuser);
    }


    public static function unreadMessageCount()
    {
        $count = DB::select("select count(*) as count from ". System::$table_pitemchg ." where duser ='" . Auth::user()->id . "' and acknowledged = 0 and ctype = 'E'");
        if ($count == null || empty($count)) return 0;
        return $count[0]->count;
    }

    public static function getMessageList($sorting)
    {

        $inquirements = Session::get("filter_inquirements");
        Session::put("filter_inquirements", "0");
        $result = self::loadFromCache();
        Session::put("filter_inquirements", $inquirements);

        $messages = array();
        if ($result != null && !empty($result)) {
            foreach ($result as $order) {
                foreach ($order->items as $item) {
                    foreach ($item->changes as $item_chg) {
                        if ($item_chg->duser == Auth::user()->id && $item_chg->acknowledged == '0' && $item_chg->ctype == 'E')
                            array_push($messages, $item_chg);
                    }
                }
            }

            if (strcmp($sorting, "ebeln") == 0)
                usort($messages, array("App\Materom\Orders", "cmp_ebeln"));
            else if (strcmp($sorting, "cuser") == 0)
                usort($messages, array("App\Materom\Orders", "cmp_cuser"));
            else
                usort($messages, array("App\Materom\Orders", "cmp_cdate"));
        }
        return $messages;

    }

    public static function getProposalsList()
    {
        //TODO - filtrari?
        $proposals = DB::select("select * from ". System::$table_pitemchg_proposals ." where type = 'O'");
        return $proposals;
    }
}