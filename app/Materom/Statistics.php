<?php
/**
 * Created by PhpStorm.
 * User: Radu
 * Date: 15.04.2019
 * Time: 08:43
 */

namespace App\Materom;


use App\Materom\SAP\MasterData;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Statistics
{

    static public function getStatData($type, $lifnr, $sdate, $interval, $ekgrp, $otype)
    {
        $result = "";
        if ($type == "A")
            $result = json_encode(self::getOrderStatistics($lifnr, $sdate, $interval, $ekgrp, $otype));
        return $result;
    }

    static public function getStatEkgrpOfLifnr($type, $lifnr, $sdate, $interval)
    {
        $result = "";
        if ($type == "A")
            $result = json_encode(self::getEkgrpListOfLifnr($lifnr, $sdate, $interval));
        return $result;
    }

    static public function getOrderStatistics($lifnr, $sdate, $interval, $ekgrp, $otype)
    {
        $result = new \stdClass();
        $result->title = __("Delayed vs. open number of purchase orders/items");
        $cdate = new Carbon($sdate);
        $sdate = new Carbon($sdate);
        switch ($interval) {
            case "A":
                $cdate->subDays(7);
                break;
            case "B":
                $cdate->subDays(14);
                break;
            case "C":
                $cdate->subMonth(1);
                break;
        }
        $result->data = new \stdClass();
        $result->data->labels = [];
        $result->data->datasets = [];

        $dataset1 = new \stdClass();
        $dataset1->label = __("Number of open items");
        $dataset1->stack = "Items";
        $dataset1->data = [];
        $dataset1->backgroundColor = 'rgba(75, 192, 192, 0.2)';
        $dataset1->borderColor = 'rgba(75, 192, 192, 1)';
        $dataset1->borderWidth = 1;

        $dataset2 = new \stdClass();
        $dataset2->label = __("Number of delayed items");
        $dataset2->stack = "Items";
        $dataset2->data = [];
        $dataset2->backgroundColor = 'rgba(255, 102, 255, 0.2)';
        $dataset2->borderColor = 'rgba(255, 102, 255, 1)';
        $dataset2->borderWidth = 1;

        $dataset3 = new \stdClass();
        $dataset3->label = __("Number of open orders");
        $dataset3->stack = "Orders";
        $dataset3->data = [];
        $dataset3->backgroundColor = 'rgba(127, 206, 86, 0.2)';
        $dataset3->borderColor = 'rgba(127, 206, 86, 1)';
        $dataset3->borderWidth = 1;

        $dataset4 = new \stdClass();
        $dataset4->label = __("Number of delayed orders");
        $dataset4->stack = "Orders";
        $dataset4->data = [];
        $dataset4->backgroundColor = 'rgba(255, 32, 96, 0.2)';
        $dataset4->borderColor = 'rgba(255, 32, 96, 1)';
        $dataset4->borderWidth = 1;

        while ($cdate <= $sdate) {
            array_push($result->data->labels, substr($cdate, 0, 10));
            $rec = null;
            $date0 = new Carbon("" . $cdate);
            $date0->hour = 0;
            $date0->minute = 0;
            $date0->second = 0;
            $date1 = new Carbon("" . $cdate);
            $date1->hour = 23;
            $date1->minute = 59;
            $date1->second = 59;
            $where = "lifnr = '$lifnr' and date >= '$date0' and date <= '$date1'";
            if ($ekgrp <> "***") $where .= " and ekgrp = '$ekgrp'";
            if ($otype <> "A") $where .= " and otype = '$otype'";
            $recs = DB::select("select sum(cnt_total_orders) as cnt_total_orders, ".
                                             "sum(cnt_total_items) as cnt_total_items, ".
                                             "sum(cnt_delayed_orders) as cnt_delayed_orders, ".
                                             "sum(cnt_delayed_items) as cnt_delayed_items from "
                . System::$table_stat_orders . " where " .$where);
            if ($recs != null) $rec = $recs[0];
            if ($rec == null ||
                ($rec->cnt_total_items == null && $rec->cnt_total_orders == null &&
                 $rec->cnt_delayed_items == null && $rec->cnt_delayed_orders == null)) {
                array_push($dataset1->data, 0);
                array_push($dataset2->data, 0);
                array_push($dataset3->data, 0);
                array_push($dataset4->data, 0);
            } else {
                array_push($dataset1->data, $rec->cnt_total_items - $rec->cnt_delayed_items);
                array_push($dataset2->data, $rec->cnt_delayed_items);
                array_push($dataset3->data, $rec->cnt_total_orders - $rec->cnt_delayed_orders);
                array_push($dataset4->data, $rec->cnt_delayed_orders);
            }
            $cdate->addDay();
        }
        array_push($result->data->datasets, $dataset1);
        array_push($result->data->datasets, $dataset2);
        array_push($result->data->datasets, $dataset3);
        array_push($result->data->datasets, $dataset4);
        return $result;
    }

    static public function getEkgrpListOfLifnr($lifnr, $sdate, $interval)
    {
        $date_from = new Carbon($sdate);
        $date_to = new Carbon($sdate);
        switch ($interval) {
            case "A":
                $date_from->subDays(7);
                break;
            case "B":
                $date_from->subDays(14);
                break;
            case "C":
                $date_from->subMonth(1);
                break;
        }
        $date_from->hour = 0;
        $date_from->minute = 0;
        $date_from->second = 0;
        $date_to->hour = 23;
        $date_to->minute = 59;
        $date_to->second = 59;
        $ekgrps = DB::select("select distinct ekgrp from ". System::$table_stat_orders . " where " .
                             " lifnr = '$lifnr' and date >= '$date_from' and date <= '$date_to'");
        foreach($ekgrps as $ekgrp) $ekgrp->name = MasterData::getEkgrpName($ekgrp->ekgrp);
        return $ekgrps;
    }

}