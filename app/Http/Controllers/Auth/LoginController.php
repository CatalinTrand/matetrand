<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
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
    }

    protected function credentials(Request $request)
    {
        return array_merge($request->only($this->username(), 'password'),
            ['active' => $this->getIfActive($request->only($this->username()))]);
    }

    public function getIfActive($id){
        $user = DB::select("select * from users where id = '".$id['id']."'");
        if (!$user) return 0;
        if (strcmp($user[0]->active,'0') == 0) return 0;
        return 1;
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