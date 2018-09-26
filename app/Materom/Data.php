<?php
/**
 * Created by PhpStorm.
 * User: Radu
 * Date: 26.09.2018
 * Time: 08:58
 */

namespace App\Materom;

use Illuminate\Support\Facades\DB;

class Data
{

    public static function getOrders($id, $mode) {
        $users = DB::select("select * from users where id ='$id'");
        if (count($users) == 0) return array();
        $user = $users[0];

        if ($user->role == "Administrator") {
            if ($mode) { // purchase orders \
                $porders =  DB::select("select * from porders order by ebeln");
                foreach($porders as $porder) $porder->vbeln = '';
                return $porders;
            } else {     // sales orders
                $result = array();
                $sorders = DB::select("select distinct vbeln from pitems order by vbeln");
                foreach($sorders AS $sorder) {
                    $porders = Data::getSalesOrderFlow($sorder->vbeln);
                    foreach($porders AS $porder) {
                        $orders = DB::select("select * from porders where ebeln = '$porder->ebeln' order by ebeln");
                        foreach($orders as $order) {
                            $order->vbeln = $sorder->vbeln;
                            $order->ebeln .= $porder->ebeln_id;

                            $ssorder = DB::select("select * from pitems where vbeln='$order->vbeln'")[0];
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

        if ($user->role == "CTV")
            if ($mode) { // purchase orders
                return DB::select("select * from porders where ctv = '$id' order by vbeln, ebeln");
            } else {

            }

        if ($user->role == "Referent")
            if ($mode) { // purchase orders
                return DB::select("select * from porders where ekgrp = '$user->ekgrp' order by vbeln, ebeln");
            } else { // sales orders

            }

        // Furnizor
        $sql = "select * from porders where id ='$id'";
        /*
        $sql = "select * from porders where lifnr = '$user->lifnr'";
        $brands = DB::select("select * from users_sel where id ='$id'");
        $xsql = "";
        foreach($brands as $brand) {
            $sel1 = "";
            if (!empty(trim($brand->wglif)))
                $sel1 = "wglif = '$brand->wglif'";
            if (!empty(trim($brand->mfrnr))) {
                if (!empty($sel1)) $sel1 .= " and ";
                $sel1 = "wglif = '$brand->wglif'";
            }
            if (empty($sel1)) continue;
            $sel1 = "(". $sel1 . ")";
            if (empty($zsql)) $xsql = $sel1;
            else $xsql .= ' or ' . $sel1;
        }
        if (!empty($xsql)) $sql .= " and (" . $xsql . ")";
        $sql .= " order by ebeln";
        */
        return DB::select($sql);
    }

    static public function getSalesOrderFlow($vbeln) {
        $porders = DB::select("select distinct ebeln from pitems where vbeln = '$vbeln' order by ebeln");
        $sql = "";
        foreach($porders as $porder) {
            if (!empty($sql)) $sql .= " or";
            $sql .= " ebeln = '$porder->ebeln'";
        }
        $sql = "select distinct vbeln, ebeln from pitems where" . $sql . " order by ebeln, vbeln";
        $sorders = DB::select($sql);
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
}