<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

    public function orders()
    {
        return view('orders.orders');
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
