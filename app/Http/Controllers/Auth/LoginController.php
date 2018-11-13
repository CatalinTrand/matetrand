<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Materom\Orders;
use App\Materom\SAP;
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
        Session::put('locale', strtolower(Auth::user()->lang));
        Session::put('materomdbcache', Orders::newCacheToken());
        if (Auth::user()->role == "CTV") {
            $id = Auth::user()->id;
            DB::delete("delete from user_agent_clients where id = '$id'");
            $clients = SAP::getAgentClients($id);
            foreach ($clients as $client) {
                DB::insert("insert into user_agent_clients (id, kunnr) values ('$id','$client')");
            }
            $customers = DB::select("select * from users_cli where id = '$id'");
            if (!empty($customers)) {
                foreach ($customers as $customer) {
                    $client = $customer->kunnr;
                    if (!DB::table("user_agent_clients")->where([["id", "=", $id], ["kunnr", "=", $client]])->exists())
                        DB::insert("insert into user_agent_clients (id, kunnr) values ('$id','$client')");
                }
            }
        }
        Orders::fillCache();
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