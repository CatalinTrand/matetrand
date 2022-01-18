<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Materom\ExcelData;
use App\Materom\Orders;
use App\Materom\SAP;
use App\Materom\System;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/orders';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        System::init(Auth::user()->sap_system);
        Session::put('locale', strtolower(Auth::user()->lang));
        Session::put('materomdbcache', Orders::newCacheToken());
        Session::put("groupOrdersBy", 1);
        if (Auth::user()->role == "CTV") {
            $id = Auth::user()->id;
            DB::delete("delete from ". System::$table_user_agent_clients ." where id = '$id'");
            $clients = SAP::getAgentClients($id);
            if (!empty($clients)) {
                $sql = "";
                foreach ($clients as $client) {
                    $sql .= ",('$id','$client->client','$client->agent')";
                }
                $sql = substr($sql, 1);
                DB::insert("insert into ". System::$table_user_agent_clients ." (id, kunnr, agent) values " . $sql);
            }
            $customers = DB::select("select * from ". System::$table_users_cli ." where id = '$id'");
            if (!empty($customers)) {
                foreach ($customers as $customer) {
                    $client = $customer->kunnr;
                    if (!DB::table(System::$table_user_agent_clients)->where([["id", "=", $id], ["kunnr", "=", $client]])->exists())
                        DB::insert("insert into ". System::$table_user_agent_clients ." (id, kunnr) values ('$id','$client')");
                }
            }
            if ($id == DB::table(System::$table_roles)->where("rfc_role", "CTV")->value("user1")) {
                // fallback CTV - get all non-assigned customers
                $customers = DB::select("select distinct kunnr from " . System::$table_pitems .
                    " where not exists (select * from " . System::$table_user_agent_clients .
                                        " where " . System::$table_user_agent_clients .".kunnr = ".System::$table_pitems.".kunnr)");
                if (!empty($customers)) {
                    foreach ($customers as $customer) {
                        $client = $customer->kunnr;
                        if (!empty(trim($client)) && !DB::table(System::$table_user_agent_clients)->where([["id", "=", $id], ["kunnr", "=", $client]])->exists())
                            DB::insert("insert into ". System::$table_user_agent_clients ." (id, kunnr) values ('$id','$client')");
                    }
                }
            }
            Session::put("groupOrdersBy", 4);
        }
        if (Auth::user()->role == "Administrator" || Auth::user()->readonly == 1 || Auth::user()->none == 1)
            Session::put("filter_ebeln", "NONE");
        if (Auth::user()->role == "Referent" && Auth::user()->readonly == 1 && Auth::user()->pnad == 1)
            Session::put("filter_pnad_status", "1");
        Orders::fillCache();
        ExcelData::setDefaultXLSFields("xls01");
    }

    protected function validateLogin(Request $request)
    {
        if($request->only('api_token')) {
            $this->validate($request, [
                'api_token' => 'required|string',
            ]);
        }
        else {
            $this->validate($request, [
                $this->username() => 'required|string',
                'password' => 'required|string',
            ]);
        }
    }

    protected function credentials(Request $request)
    {
        if($request->only('api_token')){
            return array_merge($request->only('api_token'));
        }

        return array_merge($request->only($this->username(), 'password'),
            ['active' => '1']);
    }

    public function sendFailedLoginResponse()
    {
        \Session::put("alert-danger", "User or password is incorrect. Please correct and retry.");
        return redirect()->back();
    }

    public function username()
    {
        return 'id';
    }
}