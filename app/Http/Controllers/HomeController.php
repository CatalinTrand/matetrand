<?php

namespace App\Http\Controllers;

use App\Materom\Orders;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @throws \Exception
     */

    public function index()
    {
        if(isset($_GET['del'])){
            $id_del = $_GET['del'];
            $user = User::all()->find($id_del);
            if($user != null)
                $user->delete();
        }

        return view('users.users');
    }

    public function editUser()
    {

        //vendor::delete
        if(isset($_GET['mfrnrDEL'])){
            $id = $_GET['id'];
            $mfrnr = $_GET['mfrnrDEL'];
            DB::delete("delete from ". \App\Materom\System::$table_users_sel ." where id = '$id' and mfrnr = '$mfrnr'");
            unset($_GET['mfrnrDEL']);
        }

        //refferal delete
        if(isset($_GET['refidDEL'])){
            $id = $_GET['id'];
            $refID = $_GET['refidDEL'];
            DB::delete("delete from users_ref where id = '$id' and refid = '$refID'");
            unset($_GET['refidDEL']);
        }

        //agent delete
        if(isset($_GET['agentDEL'])){
            $id = $_GET['id'];
            $agentDEL = $_GET['agentDEL'];
            DB::delete("delete from ". \App\Materom\System::$table_users_agent ." where id = '$id' and agent = '$agentDEL'");
            unset($_GET['agentDEL']);
        }

        return view('users.editUser');
    }

    public function save_edit()
    {
        return view('users.editUser');
    }

    public function orders_get()
    {
        return view('orders.orders');
    }

    public function stats_get()
    {
        return view('stats.stats');
    }

    public function orders_post()
    {
        Session::put("groupOrdersBy", Input::get("groupOrdersBy"));
        Session::put("filter_status", Input::get("filter_status"));
        Session::put("filter_history", Input::get("filter_history"));
        Session::put("filter_archdate", Input::get("time_search"));
        Session::put("filter_vbeln", Input::get("filter_vbeln"));
        Session::put("filter_klabc", Input::get("filter_klabc"));
        Session::put("filter_ebeln", Input::get("filter_ebeln"));
        Session::put("filter_matnr", Input::get("filter_matnr"));
        Session::put("filter_mtext", Input::get("filter_mtext"));
        Session::put("filter_ekgrp", Input::get("filter_ekgrp"));
        Session::put("filter_lifnr", Input::get("filter_lifnr"));
        Session::put("filter_lifnr_name", Input::get("filter_lifnr_name"));
        Session::put("filter_kunnr", Input::get("filter_kunnr"));
        Session::put("filter_kunnr_name", Input::get("filter_kunnr_name"));
        Session::put("filter_mfrnr_text", Input::get("filter_mfrnr_text"));

        Session::put("filter_inquirements", "0");
        $tmp = strtoupper(Input::get("filter_inquirements"));
        if (\Illuminate\Support\Facades\Auth::user()->role != 'CTV') {
            if ($tmp == "ON") $tmp = "1"; else $tmp = "0";
        } else {
            if ($tmp != "0" && $tmp != "1" && $tmp != "2") $tmp = "0";
        }
        Session::put("filter_inquirements", $tmp);

        Session::put("filter_backorders", "0");
        $tmp = trim(Input::get("filter_backorders"));
        if ($tmp != "1" && $tmp != 2) $tmp = "0";
        Session::put("filter_backorders", $tmp);

        Session::put("filter_eta_delayed", "0");
        $tmp = trim(Input::get("filter_eta_delayed"));
        if ($tmp != "1" && $tmp != "2" && $tmp != "3") $tmp = "0";
        Session::put("filter_eta_delayed", $tmp);

        Session::put("filter_eta_delayed_date", "");
        unset($tmp);
        $tmp = Input::get("filter_eta_delayed_date");
        if (isset($tmp) && $tmp != null) Session::put("filter_eta_delayed_date", $tmp);

        Session::put("filter_eta", "0");
        $tmp = trim(Input::get("filter_eta"));
        if ($tmp != "1" && $tmp != 2) $tmp = "0";
        Session::put("filter_eta", $tmp);

        Session::put("filter_overdue", "0");
        $tmp = Input::get("filter_overdue");
        if (strtoupper($tmp) == "ON" ) Session::put("filter_overdue", "1");
        Session::put("filter_overdue_low", "");
        unset($tmp);
        $tmp = Input::get("filter_overdue_low");
        if (isset($tmp) && $tmp != null) {
            $tmp = intval($tmp);
            if ($tmp <= 0) $tmp = "";
            Session::put("filter_overdue_low", $tmp);
        }
        Session::put("filter_overdue_high", "");
        unset($tmp);
        $tmp = Input::get("filter_overdue_high");
        if (isset($tmp) && $tmp != null) {
            $tmp = intval($tmp);
            if ($tmp <= 0) $tmp = "";
            Session::put("filter_overdue_high", $tmp);
        }

        Session::put("filter_goodsreceipt", "0");
        $tmp = Input::get("filter_goodsreceipt");
        if (($tmp != "1") && ($tmp != "2")) $tmp = "0";
        Session::put("filter_goodsreceipt", $tmp);

        Session::put("filter_deldate_low", "");
        unset($tmp);
        $tmp = Input::get("filter_deldate_low");
        if (isset($tmp) && $tmp != null) Session::put("filter_deldate_low", $tmp);
        Session::put("filter_deldate_high", "");
        unset($tmp);
        $tmp = Input::get("filter_deldate_high");
        if (isset($tmp) && $tmp != null) Session::put("filter_deldate_high", $tmp);

        Session::put("filter_etadate_low", "");
        unset($tmp);
        $tmp = Input::get("filter_etadate_low");
        if (isset($tmp) && $tmp != null) Session::put("filter_etadate_low", $tmp);
        Session::put("filter_etadate_high", "");
        unset($tmp);
        $tmp = Input::get("filter_etadate_high");
        if (isset($tmp) && $tmp != null) Session::put("filter_etadate_high", $tmp);

        $tmp = Input::get("filter_pnad_status");
        if (($tmp != "1") && ($tmp != "2")) $tmp = "0";
        Session::put("filter_pnad_status", $tmp);

        $tmp = Input::get("filter_pnad_type");
        if (($tmp != "1") && ($tmp != "2") && ($tmp != "3") && ($tmp != "4")) $tmp = "0";
        Session::put("filter_pnad_type", $tmp);

        Session::put("filter_pnad_mblnr", Input::get("filter_pnad_mblnr"));

        Session::put("filter_mirror", "0");
        $tmp = Input::get("filter_mirror");
        if (strtoupper($tmp) == "ON" ) Session::put("filter_mirror", "1");

        Orders::fillCache();
        return redirect()->back();
    }

    public function messages_get()
    {
        return view('messenger.messages');
    }

    public function messages_post()
    {
        Session::put("filter_vbeln", Input::get("filter_vbeln"));
        Session::put("filter_klabc", Input::get("filter_klabc"));
        Session::put("filter_ebeln", Input::get("filter_ebeln"));
        Session::put("filter_matnr", Input::get("filter_matnr"));
        Session::put("filter_mtext", Input::get("filter_mtext"));
        Session::put("filter_ekgrp", Input::get("filter_ekgrp"));
        Session::put("filter_lifnr", Input::get("filter_lifnr"));
        Session::put("filter_lifnr_name", Input::get("filter_lifnr_name"));
        Session::put("filter_kunnr", Input::get("filter_kunnr"));
        Session::put("filter_kunnr_name", Input::get("filter_kunnr_name"));
        Session::put("filter_mfrnr_text", Input::get("filter_mfrnr_text"));
        Session::put("filter_history", Input::get("filter_history"));
        Session::put("filter_archdate", Input::get("time_search"));
        Orders::fillCache();
        return redirect()->back();
    }

    public function roles()
    {
        return view('roles.roles');
    }

    public function save_roles()
    {
        return view('roles.roles');
    }
}
