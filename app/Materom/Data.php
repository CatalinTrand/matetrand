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

    public static function getOrders($id, $mode, $history, $time_limit,
                                     $filter_vbeln, $filter_ebeln, $filter_matnr, $filter_mtext,
                                     $filter_lifnr, $filter_lifnr_name) {

        $orders_table = $history == 1 ? "porders" : "porders_arch";
        $items_table = $history == 1 ? "pitems" : "pitems_arch";
        $itemchg_table = $history == 1 ? "pitemchg" : "pitemchg_arch";

        $filter_vbeln_sql = self::processFilter("vbeln", $filter_vbeln, 10);
        $filter_ebeln_sql = self::processFilter("ebeln", $filter_ebeln, 10);;
        $filter_matnr_sql = self::processFilter("idnlf", $filter_matnr, 0);
        $filter_mtext_sql = self::processFilter("mtext", $filter_mtext, 0);
        $filter_lifnr_sql = self::processFilter("lifnr", $filter_lifnr, 10);
        $filter_lifnr_name_sql = self::processFilter("lifnr_name", $filter_lifnr_name, 0);

        $time_search_sql = $time_limit === null ? "" : " where creation >= '$time_limit 00:00:00' ";

        $users = DB::select("select * from users where id ='$id'");
        if (count($users) == 0) return array();
        $user = $users[0];

        if ($user->role == "Administrator") {
            if ($mode) { // purchase orders
                $filter_sql = self::addFilters($filter_ebeln_sql, $filter_lifnr_sql, $filter_lifnr_name_sql);
                if (empty($time_search_sql)) {
                    if (!empty($filter_sql))
                        $filter_sql = " where " . $filter_sql;
                } else {
                    if (!empty($filter_sql))
                        $filter_sql = $time_search_sql . " and " . $filter_sql;
                    else
                        $filter_sql = $time_search_sql;
                }
                $porders = DB::select("select * from ".$orders_table . $filter_sql ." order by ebeln ");
                foreach($porders as $porder) $porder->vbeln = '';
                return $porders;
            } else {     // sales orders
                $filter_sql = self::addFilters($filter_ebeln_sql, $filter_vbeln_sql, $filter_matnr_sql, $filter_mtext_sql);
                if (empty($time_search_sql)) {
                    if (!empty($filter_sql))
                        $filter_sql = " where " . $filter_sql;
                } else {
                    if (!empty($filter_sql))
                        $filter_sql = $time_search_sql . " and " . $filter_sql;
                    else
                        $filter_sql = $time_search_sql;
                }
                $result = array();
                $sorders = DB::select("select distinct vbeln from ". $items_table . $filter_sql ." order by vbeln ");
                foreach($sorders AS $sorder) {
                    $porders = Data::getSalesOrderFlow($sorder->vbeln, $history, $time_limit,
                        $filter_vbeln, $filter_ebeln, $filter_matnr, $filter_mtext,
                        $filter_lifnr, $filter_lifnr_name);
                    foreach($porders AS $porder) {
                        $orders = DB::select("select * from $orders_table where ebeln = '$porder->ebeln' order by ebeln");
                        foreach($orders as $order) {
                            $order->vbeln = $sorder->vbeln;
                            $order->ebeln .= $porder->ebeln_id;

                            $ssorder = DB::select("select * from $items_table where ebeln = '$porder->ebeln' and vbeln='$order->vbeln'")[0];
                            $order->kunnr = $ssorder->kunnr;
                            $order->kunnr_name = $ssorder->kunnr_name;
                            $order->ctv = $ssorder->ctv;
                            $order->ctv_name = $ssorder->ctv_name;
                            $order->shipto = $ssorder->shipto;
                            $order->shipto_name = $ssorder->shipto_name;
                            $order->stage = $ssorder->stage;
                        }
                        $result = array_merge($result, $orders);
                    }
                }
                return $result;
            }
        }

        if ($user->role == "CTV") {
            $filter_sql = self::addFilters($filter_ebeln_sql, $filter_vbeln_sql, $filter_matnr_sql, $filter_mtext_sql);
            if (empty($time_search_sql)) {
                if (!empty($filter_sql))
                    $filter_sql = " where " . $filter_sql;
            } else {
                if (!empty($filter_sql))
                    $filter_sql = $time_search_sql . " and " . $filter_sql;
                else
                    $filter_sql = $time_search_sql;
            }
            if ($mode) { // purchase orders \
                if (empty($filter_sql)) {
                    $porders2 = DB::select("select distinct ebeln from $items_table where ctv = '$id'");
                } else {
                    $porders2 = DB::select("select distinct ebeln from $items_table $filter_sql and ctv = '$id'");
                }
                $porders = array();
                foreach($porders2 as $porder2) {
                    $result = DB::select("select * from $orders_table where ebeln = '$porder2->ebeln'");
                    $porders = array_merge($porders, $result);
                }
                foreach($porders as $porder) $porder->vbeln = '';
                return $porders;
            } else {     // sales orders
                $result = array();
                $filter_sql = self::addFilters($filter_ebeln_sql, $filter_vbeln_sql, $filter_matnr_sql, $filter_mtext_sql);
                if (empty($time_search_sql)) {
                    if (!empty($filter_sql))
                        $filter_sql = " where " . $filter_sql;
                } else {
                    if (!empty($filter_sql))
                        $filter_sql = $time_search_sql . " and " . $filter_sql;
                    else
                        $filter_sql = $time_search_sql;
                }
                if (empty($filter_sql)) {
                    $sorders = DB::select("select distinct vbeln from $items_table where ctv = '$id' order by vbeln");
                } else {
                    $sorders = DB::select("select distinct vbeln from $items_table $filter_sql and ctv = '$id' order by vbeln");
                }
                foreach($sorders AS $sorder) {
                    $porders = Data::getSalesOrderFlow($sorder->vbeln, $history, $time_limit,
                        $filter_vbeln, $filter_ebeln, $filter_matnr, $filter_mtext,
                        $filter_lifnr, $filter_lifnr_name);
                    foreach($porders AS $porder) {
                        $orders = DB::select("select * from $orders_table where ebeln = '$porder->ebeln' order by ebeln");
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
                            $order->stage = $ssorder->stage;
                        }
                        $result = array_merge($result, $orders);
                    }
                }
                return $result;
            }
        }

        if ($user->role == "Referent") {
            $filter_sql = self::addFilters($filter_ebeln_sql, $filter_lifnr_sql, $filter_lifnr_name_sql);
            if (empty($time_search_sql)) {
                if (!empty($filter_sql))
                    $filter_sql = " where " . $filter_sql;
            } else {
                if (!empty($filter_sql))
                    $filter_sql = $time_search_sql . " and " . $filter_sql;
                else
                    $filter_sql = $time_search_sql;
            }
            if ($mode) { // purchase orders
                if (empty($filter_sql)) {
                    $porders = DB::select("select * from $orders_table where ekgrp = '$user->ekgrp' order by ebeln");
                } else {
                    $porders = DB::select("select * from $orders_table $filter_sql and ekgrp = '$user->ekgrp' order by ebeln");
                }
                foreach($porders as $porder) $porder->vbeln = '';
                return $porders;
            } else {     // sales orders
                $result = array();
                if (empty($filter_sql)) {
                    $porders = DB::select("select distinct ebeln from $orders_table where ekgrp = '$user->ekgrp' order by ebeln");
                } else {
                    $porders = DB::select("select distinct ebeln from $orders_table $filter_sql and ekgrp = '$user->ekgrp'");
                }
                if (count($porders) == 0) return $porders;
                $sql = "";
                foreach($porders as $porder) {
                    if (!empty($sql)) $sql .= " or";
                    $sql .= " ebeln = '$porder->ebeln'";
                }
                $sql = "select distinct vbeln from $orders_table where" . $sql . " order by vbeln";
                $sorders = DB::select($sql);
                foreach($sorders AS $sorder) {
                    $porders = Data::getSalesOrderFlow($sorder->vbeln, $history, $time_limit,
                        $filter_vbeln, $filter_ebeln, $filter_matnr, $filter_mtext,
                        $filter_lifnr, $filter_lifnr_name);
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
                            $order->stage = $ssorder->stage;
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

        $filter_sql = self::addFilters($filter_ebeln_sql, $filter_lifnr_sql, $filter_lifnr_name_sql);
        if (empty($time_search_sql)) {
            if (!empty($filter_sql))
                $filter_sql = " where " . $filter_sql;
        } else {
            if (!empty($filter_sql))
                $filter_sql = $time_search_sql . " and " . $filter_sql;
            else
                $filter_sql = $time_search_sql;
        }

        if ($mode) { // purchase orders
            if (empty($filter_sql)) {
                $porders = DB::select("select * from $orders_table where lifnr = '$user->lifnr'" . $xsql . " order by ebeln");
            } else {
                $porders = DB::select("select * from $orders_table $filter_sql and lifnr = '$user->lifnr'" . $xsql . " order by ebeln");
            }
            foreach($porders as $porder) {
                $sorders = DB::select("select distinct vbeln from $items_table where ebeln = '$porder->ebeln' order by vbeln");
                $porder->vbeln = $sorders[0]->vbeln;
                $ssorder = DB::select("select * from $items_table where vbeln='$porder->vbeln'")[0];
                if (strtoupper(trim($sorders[0]->vbeln)) != '!REPLENISH') $porder->vbeln = "SALESORDER";
                $porder->kunnr = $ssorder->kunnr;
                $porder->kunnr_name = $ssorder->kunnr_name;
                $porder->ctv = $ssorder->ctv;
                $porder->ctv_name = $ssorder->ctv_name;
                $porder->shipto = $ssorder->shipto;
                $porder->shipto_name = $ssorder->shipto_name;
                $porder->stage = $ssorder->stage;
            }
            return $porders;
        } else {     // sales orders
            $result = array();
            if (empty($filter_sql)) {
                $porders = DB::select("select distinct ebeln from $orders_table where lifnr = '$user->lifnr'" . $xsql . " order by ebeln");
            } else {
                $porders = DB::select("select distinct ebeln from $orders_table $filter_sql and lifnr = '$user->lifnr'" . $xsql . " order by ebeln");
            }
            if (count($porders) == 0) return $porders;
            $sql = "";
            foreach($porders as $porder) {
                if (!empty($sql)) $sql .= " or";
                $sql .= " ebeln = '$porder->ebeln'";
            }
            $sql = "select distinct vbeln from $items_table where" . $sql . " order by vbeln";
            $sorders = DB::select($sql);
            foreach($sorders AS $sorder) {
                $porders = Data::getSalesOrderFlow($sorder->vbeln, $history, $time_limit,
                    $filter_vbeln, $filter_ebeln, $filter_matnr, $filter_mtext,
                    $filter_lifnr, $filter_lifnr_name);
                foreach($porders AS $porder) {
                    $orders = DB::select("select * from $orders_table where ebeln = '$porder->ebeln'".
                        " and lifnr = '$user->lifnr'".$xsql. " order by ebeln");
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
                        $order->stage = $ssorder->stage;
                        if (trim($order->vbeln) != '!REPLENISH') $order->vbeln = "SALESORDER";
                    }
                    $result = array_merge($result, $orders);
                }
            }
            return $result;
        }

        return array();

    }

    static public function getSalesOrderFlow($vbeln, $history, $time_limit,
                                             $filter_vbeln, $filter_ebeln, $filter_matnr, $filter_mtext,
                                             $filter_lifnr, $filter_lifnr_name) {

        $orders_table = $history == 1 ? "porders" : "porders_arch";
        $items_table = $history == 1 ? "pitems" : "pitems_arch";
        $itemchg_table = $history == 1 ? "pitemchg" : "pitemchg_arch";

        $filter_vbeln_sql = self::processFilter("vbeln", $filter_vbeln, 10);
        $filter_ebeln_sql = self::processFilter("ebeln", $filter_ebeln, 10);;
        $filter_matnr_sql = self::processFilter("idnlf", $filter_matnr, 0);
        $filter_mtext_sql = self::processFilter("mtext", $filter_mtext,0);
        $filter_lifnr_sql = self::processFilter("lifnr", $filter_lifnr, 10);
        $filter_lifnr_name_sql = self::processFilter("lifnr_name", $filter_lifnr_name, 0);

        $time_search_sql = $time_limit === null ? "" : " where creation >= '$time_limit 00:00:00' ";

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
            $filter_sql = self::addFilters($filter_ebeln_sql);
            if (empty($time_search_sql)) {
                if (!empty($filter_sql))
                    $filter_sql = " where " . $filter_sql;
            } else {
                if (!empty($filter_sql))
                    $filter_sql = $time_search_sql . " and " . $filter_sql;
                else
                    $filter_sql = $time_search_sql;
            }
            if (empty($filter_sql)) {
                $porders = DB::select("select distinct ebeln from $orders_table where lifnr = '" . Auth::user()->lifnr . "' " . $xsql . " order by ebeln");
            } else {
                $porders = DB::select("select distinct ebeln from $orders_table $filter_sql and lifnr = '" . Auth::user()->lifnr . "' " . $xsql . " order by ebeln");
            }
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
                $filter_sql = self::addFilters($filter_ebeln_sql);
                if (empty($time_search_sql)) {
                    if (!empty($filter_sql))
                        $filter_sql = " where " . $filter_sql;
                } else {
                    if (!empty($filter_sql))
                        $filter_sql = $time_search_sql . " and " . $filter_sql;
                    else
                        $filter_sql = $time_search_sql;
                }
                if (empty($filter_sql)) {
                    $porders = DB::select("select distinct ebeln from $orders_table where lifnr = '" . Auth::user()->lifnr . "' " . $xsql . " order by ebeln");
                } else {
                    $porders = DB::select("select distinct ebeln from $orders_table $filter_sql and lifnr = '" . Auth::user()->lifnr . "' ". $xsql . " order by ebeln");
                }
                $xsql = "";
                foreach($porders as $porder) {
                    $sel1 = "ebeln = '$porder->ebeln'";
                    $sel1 = "(". $sel1 . ")";
                    if (empty($xsql)) $xsql = $sel1;
                    else $xsql .= ' or ' . $sel1;
                }
                if (!empty($xsql)) $xsql = " and (" . $xsql . ")";
            } elseif (Auth::user()->role == "Referent") {
                $filter_sql = self::addFilters($filter_ebeln_sql, $filter_lifnr_sql, $filter_lifnr_name_sql);
                if (empty($time_search_sql)) {
                    if (!empty($filter_sql))
                        $filter_sql = " where " . $filter_sql;
                } else {
                    if (!empty($filter_sql))
                        $filter_sql = $time_search_sql . " and " . $filter_sql;
                    else
                        $filter_sql = $time_search_sql;
                }
                if (empty($filter_sql)) {
                    $porders = DB::select("select distinct ebeln from $orders_table where ekgrp = '" . Auth::user()->ekgrp . "' order by ebeln");
                } else {
                    $porders = DB::select("select distinct ebeln from $orders_table $filter_sql and ekgrp = '" . Auth::user()->ekgrp . "' order by ebeln");
                }
                $xsql = "";
                foreach($porders as $porder) {
                    $sel1 = "ebeln = '$porder->ebeln'";
                    $sel1 = "(". $sel1 . ")";
                    if (empty($xsql)) $xsql = $sel1;
                    else $xsql .= ' or ' . $sel1;
                }
                if (!empty($xsql)) $xsql = " and (" . $xsql . ")";
            }
            $filter_sql = self::addFilters($filter_vbeln_sql, $filter_ebeln_sql, $filter_matnr_sql, $filter_mtext_sql);
            if (empty($time_search_sql)) {
                if (!empty($filter_sql))
                    $filter_sql = " where " . $filter_sql;
            } else {
                if (!empty($filter_sql))
                    $filter_sql = $time_search_sql . " and " . $filter_sql;
                else
                    $filter_sql = $time_search_sql;
            }
            if (empty($filter_sql)) {
                $porders = DB::select("select distinct ebeln from $items_table where vbeln = '$vbeln'" . $xsql . " order by ebeln");
            } else {
                $porders = DB::select("select distinct ebeln from $items_table $filter_sql and vbeln = '$vbeln'" . $xsql . " order by ebeln");
            }
        }
        $sql = "";
        foreach($porders as $porder) {
            if (!empty($sql)) $sql .= " or";
            $sql .= " ebeln = '$porder->ebeln'";
        }
        $filter_sql = self::addFilters($filter_vbeln_sql, $filter_ebeln_sql, $filter_matnr_sql, $filter_mtext_sql);
        if (empty($time_search_sql)) {
            if (!empty($filter_sql))
                $filter_sql = " where " . $filter_sql;
        } else {
            if (!empty($filter_sql))
                $filter_sql = $time_search_sql . " and " . $filter_sql;
            else
                $filter_sql = $time_search_sql;
        }
        if (empty($filter_sql)) {
            $sql = "select distinct vbeln, ebeln from $items_table where" . $sql . " order by ebeln, vbeln";
        } else {
            $sql = "select distinct vbeln, ebeln from $items_table $filter_sql and " . $sql . " order by ebeln, vbeln";
        }
        $sorders = DB::select($sql);
        if ($vbeln == "SALESORDER") {
            foreach($sorders as $sorder) {
                if (trim($sorder->vbeln) != "!REPLENISH") $sorder->vbeln = $vbeln;
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
        $norder->ekgrp = $saphdr["EKGRP"];
        if (!DB::table("users")->where(["lifnr" => $norder->lifnr, "role" => "Furnizor", "active" => 1])->exists())
            if (!DB::table("users")->where(["ekgrp" => $norder->ekgrp, "role" => "Referent", "active" => 1])->exists())
                return "OK";
        $norder->lifnr_name = $saphdr["LIFNR_NAME"];
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
            $nitem->kunnr_name = $sapitm["KUNNR_NAME"];
            $nitem->shipto = $sapitm["SHIPTO"];
            $nitem->shipto_name = $sapitm["SHIPTO_NAME"];
            $nitem->ctv = $sapitm["CTV"];
            $nitem->ctv_name = $sapitm["CTV_NAME"];
            $nitem->stage = 'F';
            $nitem->changed = false;

            $users = DB::select("select * from users where sapuser = '$nitem->ctv' and role = 'CTV'");
            if (count($users) > 0) $nitem->ctv = $users[0]->id;
            if (is_null($citem)) {
                $sql = "insert into pitems (ebeln, ebelp, idnlf, mtext, qty, qty_uom, lfdat, mfrnr, mfrnr_name,".
                                           "purch_price, purch_curr, purch_prun, purch_puom, ".
                                           "sales_price, sales_curr, sales_prun, sales_puom, ".
                                           "vbeln, posnr, kunnr, kunnr_name, shipto, shipto_name, ctv, ctv_name, stage, changed ".
                                           ") values (".
                       "'$nitem->ebeln', '$nitem->ebelp', '$nitem->idnlf', '$nitem->mtext',$nitem->qty, '$nitem->qty_uom', ".
                       "'$nitem->lfdat', '$nitem->mfrnr', '$nitem->mfrnr_name',".
                       "'$nitem->purch_price', '$nitem->purch_curr', ".
                       "$nitem->purch_prun, '$nitem->purch_puom', ".
                       "'$nitem->sales_price', '$nitem->sales_curr', $nitem->sales_prun, ".
                       "'$nitem->sales_puom', '$nitem->vbeln', '$nitem->posnr', '$nitem->kunnr', '$nitem->kunnr_name', ".
                       "'$nitem->shipto', '$nitem->shipto_name', '$nitem->ctv', '$nitem->ctv_name', ".
                       "'$nitem->stage', 0)";

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