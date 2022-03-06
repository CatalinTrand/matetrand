<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/users';

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
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'id' => 'required|string|max:20|unique:users',
            'role' => 'required|string|max:50',
            'username' => 'required|string|max:50',
            'email' => 'required|string|email|max:100',
            'password' => 'required|string|min:6|confirmed',
            'lang' => 'required|string|max:2|min:2'
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $ekgrp = $data['ekgrp'];
        if (is_null($ekgrp) || !isset($ekgrp)) $ekgrp = "";
        $lifnr = $data['lifnr'];
        if (is_null($lifnr) || !isset($lifnr)) $lifnr = "";
        if (ctype_digit($lifnr)) $lifnr = str_pad($lifnr, 10, "0", STR_PAD_LEFT);
        $sap_system = $data['sap_system'];
        if ($sap_system == null) $sap_system = "";
        $sap_system = trim($sap_system);
        if ("X".$sap_system == "X200") $sap_system = "";
        $readonly = 0;
        if (isset($data['readonly']) && strtoupper($data['readonly']) == "ON") $readonly = 1;
        $pnad = 0;
        if (isset($data['pnad']) && strtoupper($data['pnad']) == "ON" && $data['role'] == "Referent") $pnad = 1;
        $none = 1;
        if (!isset($data['none']) || strtoupper($data['none']) != "ON") $none = 0;
        $mirror_user1 = "";
        if (isset($data['mirror_user1'])) $mirror_user1 = $data['mirror_user1'];
        if ($mirror_user1 == null) $mirror_user1 = "";
        $mirror_user1 = trim($mirror_user1);
        $ctvadmin = 0;
        if (isset($data['ctvadmin']) && strtoupper($data['ctvadmin']) == "ON") $ctvadmin = 1;
        $rgroup = "";
        if (isset($data['rgroup'])) $rgroup = trim($data['rgroup']);

        return User::create([
            'id' => $data['id'],
            'role' => $data['role'],
            'username' => $data['username'],
            'lang' => $data['lang'],
            'ekgrp' => $ekgrp,
            'lifnr' => $lifnr,
            'email' => $data['email'],
            'sap_system' => $sap_system,
            'readonly'   => $readonly,
            'pnad'   => $pnad,
            'none'   => $none,
            'mirror_user1'  => $mirror_user1,
            'ctvadmin'   => $ctvadmin,
            'rgroup'   => $rgroup,
            'password' => Hash::make($data['password']),
            'created_at' => Carbon::now()->getTimestamp()
        ]);
    }

    public function sendFailedRegisterResponse()
    {
        \Session::put("alert-danger", "Failed to register user. Please correct data and retry.");
        return redirect()->back();
    }
}
