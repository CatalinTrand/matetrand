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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class Orders
{
    public const salesorder = "SALESORDER";
    public const stockorder = "!REPLENISH";

    public static function newCacheToken($length = 40) // after an idea by Scott Arciszewski
    {
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet.= "0123456789";
        $max = strlen($codeAlphabet);

        for ($i=0; $i < $length; $i++) {
            $token .= $codeAlphabet[random_int(0, $max - 1)];
        }
        return $token;
    }

    private static function addFilters(string ...$filters)
    {
        $filter_sum = "";
        foreach($filters as $filter)
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

    public static function fillCache()
    {
        $history = Session::get("filter_history");
        if (!isset($history)) $history = 1;
        else $history = intval($history);

        $time_limit = Session::get("filter_archdate");
        $filter_vbeln = Session::get("filter_vbeln");
        $filter_ebeln = Session::get("filter_ebeln");
        $filter_matnr = Session::get("filter_matnr");
        $filter_mtext = Session::get("filter_mtext");
        $filter_lifnr = Session::get("filter_lifnr");
        $filter_lifnr_name = Session::get("filter_lifnr_name");

        $cacheid = Session::get('materomdbcache');
        if (!isset($cacheid) || empty($cacheid)) return;

        $orders_table = $history == 1 ? "porders" : "porders_arch";
        $items_table = $history == 1 ? "pitems" : "pitems_arch";

        DB::beginTransaction();
        DB::delete("delete from porders_cache where session = '$cacheid'");
        DB::delete("delete from pitems_cache where session = '$cacheid'");
        DB::commit();

        $filter_vbeln_sql = self::processFilter($items_table . ".vbeln", $filter_vbeln, 10);
        $filter_ebeln_sql = self::processFilter($orders_table . ".ebeln", $filter_ebeln, 10);
        $filter_matnr_sql = self::processFilter($items_table . ".idnlf", $filter_matnr);
        $filter_mtext_sql = self::processFilter($items_table . ".mtext", $filter_mtext);
        $filter_lifnr_sql = self::processFilter($orders_table . ".lifnr", $filter_lifnr, 10);
        $filter_lifnr_name_sql = self::processFilter("sap_lfa1.name1", $filter_lifnr_name);

        $filter_sql = $time_limit === null ? "" : "(archdate >= '$time_limit 00:00:00')";
        $filter_sql = self::addFilters($filter_sql, $filter_ebeln_sql, $filter_lifnr_sql, $filter_lifnr_name_sql);
        $filter_sql = self::addFilters($filter_sql, $filter_vbeln_sql, $filter_matnr_sql, $filter_mtext_sql);

        if (Auth::user()->role == "Furnizor") {
            $manufacturers = DB::select("select distinct mfrnr from users_sel where id ='" . Auth::user()->id . "'");
            $sql = "";
            foreach($manufacturers as $manufacturer) {
                if (empty(trim($manufacturer->mfrnr))) continue;
                $sel1 = $items_table . ".mfrnr = '$manufacturer->mfrnr'";
                if (empty($sql)) $sql = $sel1;
                  else $sql .= ' or ' . $sel1;
            }
            if (!empty($sql)) $sql = "(" . $sql . ")";
            $filter_sql = self::addFilter($filter_sql,
                self::processFilter($orders_table . ".lifnr", Auth::user()->lifnr, 10),
                $sql);
        } elseif (Auth::user()->role == "Referent") {
            $filter_sql = self::addFilters($filter_sql, self::processFilter($orders_table . ".ekgrp", Auth::user()->ekgrp));
            $refs = DB::select("select distinct users_ref.id, users.lifnr from users_ref ".
                "join users using (id) ".
                "where refid ='" . Auth::user()->id . "'");
            $filter_refs_sql = "";
            foreach ($refs as $ref) {
                $manufacturers = DB::select("select distinct mfrnr from users_sel where id ='" . $ref->id . "'");
                $sql = "";
                foreach($manufacturers as $manufacturer) {
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
                $filter_sql = self::addFilter($filter_sql, "(" . substr($filter_refs_sql, 4) . ")");
        } elseif (Auth::user()->role == "CTV") {
            $filter_sql = self::addFilter($filter_sql,
                self::processFilter("ctv", Auth::user()->sapuser));
        }

        // final sql build
        $sql = "select " . $items_table . ".ebeln, " . $items_table . ".ebelp, " . $items_table . ".vbeln " .
                " from " . $items_table .
                " join " . $orders_table . " using (ebeln)";
        if (!empty($filter_lifnr_name_sql)) $sql .= " join sap_lfa1 using (lifnr)";
        if (!empty($filter_sql)) $sql .= " where " . $filter_sql;
        $sql .= " order by " . $items_table . ".ebeln, " . $items_table . ".ebelp";
        // ...and run
        $items = DB::select($sql);
        if (empty($items)) return;

        // Fill the cache
        $cache_date = now();
        DB::beginTransaction();

        // Order cache
        $psql = "insert into porders_cache (session, ebeln, cache_date) values ";
        $isql = "insert into pitems_cache (session, ebeln, ebelp, vbeln, cache_date) values ";

        $prev_ebeln = '$#$#$#$#$#';
        foreach($items as $item) {
            if ($item->ebeln != $prev_ebeln) {
                $prev_ebeln = $item->ebeln;
                $psql .= " ('$cacheid', '$prev_ebeln', '$cache_date'),";
            }
            $isql .= " ('$cacheid', '$item->ebeln', '$item->ebelp', '$item->vbeln', '$cache_date'),";
        }

        DB::insert(substr($psql, 0, -1) . ';');
        DB::insert(substr($isql, 0, -1) . ';');
        DB::commit();
    }

    static public function loadFromCache($s_order = null, $p_order = null)
    {
        $result = array();
        $cacheid = Session::get('materomdbcache');
        if (!isset($cacheid) || empty($cacheid)) return;

        $history = Session::get("filter_history");
        if ($history == null) $history = 1;
        else $history = intval($history);

        $orders_table = $history == 1 ? "porders" : "porders_arch";
        $items_table = $history == 1 ? "pitems" : "pitems_arch";
        $itemchanges_table = $history == 1 ? "pitemchg" : "pitemchg_arch";

        $porders_sql = "";
        $pitems_sql = "";
        if ($p_order != null) {
            $porders_sql = " and porders_cache.ebeln = '$p_order'";
            $pitems_sql = " and pitems_cache.ebeln = '$p_order'";
        }
        if ($s_order != null) {
            if ((Auth::user()->role == 'Furnizor') && ($s_order == self::salesorder))
                $pitems_sql .= " and pitems_cache.vbeln <> '" . self::stockorder. "'";
            else $pitems_sql .= " and pitems_cache.vbeln = '$s_order'";
            $pitems = DB::select("select distinct ebeln, ebelp from pitems_cache ".
                " where session = '$cacheid'" . $pitems_sql . " order by ebeln, ebelp");
            if (empty($pitems)) return $result;
            $pitems_sql .= " and (";
            $porders_sql .= " and (";
            $prev_ebeln = "";
            foreach($pitems as $pitem) {
                if ($prev_ebeln != $pitem->ebeln) {
                    $prev_ebeln = $pitem->ebeln;
                    $porders_sql .= "porders_cache.ebeln = '$pitem->ebeln' or ";
                }
                $pitems_sql .= "(pitems_cache.ebeln = '$pitem->ebeln' and pitems_cache.ebelp = '$pitem->ebelp') or ";
            }
            $porders_sql = substr($porders_sql, 0, -4) . ")";
            $pitems_sql = substr($pitems_sql, 0, -4) . ")";
        }

        $porders = DB::select("select $orders_table.* from $orders_table join porders_cache using (ebeln) ".
                                     "where porders_cache.session = '$cacheid' " . $porders_sql .
                                     "order by $orders_table.ebeln");
        if (empty($porders)) return $result;

        $pitems = DB::select("select $items_table.* from $items_table join pitems_cache using (ebeln, ebelp) ".
            " where pitems_cache.session = '$cacheid'" . $pitems_sql .
            " order by $items_table.ebeln, $items_table.ebelp");
        if (empty($pitems)) return $result;

        $pitemschg = DB::select("select $itemchanges_table.* from $itemchanges_table " .
            "join pitems_cache using (ebeln, ebelp)" .
            " where pitems_cache.session = '$cacheid'" . $pitems_sql .
            " order by $itemchanges_table.ebeln, $itemchanges_table.ebelp, $itemchanges_table.cdate desc");

        $xitem = 0;
        $xitemchg = 0;
        foreach($porders as $porder) {
            $_porder = new POrder($porder);
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
                $_pitem->fill();
                $_porder->appendItem($_pitem);
            }
            $_porder->fill();
            $result[$porder->ebeln] = $_porder;
        }

        return $result;
    }

    public static function getOrderList($groupByPO = null) {

        if (!isset($groupByPO))
            $groupByPO = Session::get("groupOrdersBy");
        if (!isset($groupByPO)) $groupByPO = 1;

        $result = self::loadFromCache();

        if ($groupByPO == 0) {
            $orders = array();
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

        ksort($orders);
        return $orders;
    }

    static function cmp_ebeln($a, $b){
        return strcmp($a->ebeln, $b->ebeln);
    }

    static function cmp_cdate($a, $b){
        return strcmp($b->cdate, $a->cdate);
    }

    static function cmp_cuser($a, $b){
        return strcmp($a->cuser, $b->cuser);
    }

    public static function getMessageList($sorting) {

        $result = self::loadFromCache();

        $messages = array();

        foreach ($result as $order){
            foreach ($order->items as $item){
                foreach ($item->changes as $item_chg){
                    $item_chg->vbeln = $item->vbeln;
                    if($item_chg->acknowledged == 0){
                        array_push($messages,$item_chg);
                    }
                }
            }
        }

        if(strcmp($sorting,"ebeln") == 0)
            usort($messages, array("App\Materom\Orders", "cmp_ebeln"));
        if(strcmp($sorting,"cdate") == 0)
            usort($messages, array("App\Materom\Orders", "cmp_cdate"));
        if(strcmp($sorting,"cuser") == 0)
            usort($messages, array("App\Materom\Orders", "cmp_cuser"));

        return $messages;

    }


}