<?php

namespace App\Http\Controllers;

use App\Materom\Orders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
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
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('users.users');
    }

    public function editUser()
    {
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

    public function orders_post()
    {
        Session::put("groupOrdersBy", Input::get("groupOrdersBy"));
        Session::put("filter_status", Input::get("filter_status"));
        Session::put("filter_history", Input::get("filter_history"));
        Session::put("filter_archdate", Input::get("time_search"));
        Session::put("filter_vbeln", Input::get("filter_vbeln"));
        Session::put("filter_ebeln", Input::get("filter_ebeln"));
        Session::put("filter_matnr", Input::get("filter_matnr"));
        Session::put("filter_mtext", Input::get("filter_mtext"));
        Session::put("filter_lifnr", Input::get("filter_lifnr"));
        Session::put("filter_lifnr_name", Input::get("filter_lifnr_name"));
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
