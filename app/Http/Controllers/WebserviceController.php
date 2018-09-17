<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use App\Materom\Webservice;
use App\Materom\SAP;

class WebserviceController extends Controller
{
    public function tryAuthAPIToken(){
        if(Auth::user() == null){
            if(Input::get("api_token") != null ){
                $token = Input::get("api_token");
                Auth::attempt(['api_token' => $token]);
            }
        }
    }

    public function rfcPing()
    {
        $this->tryAuthAPIToken();
        if(Auth::user() == null)
            return null;

        return Webservice::rfcPing(Input::get("rfc_router"),
                                   Input::get("rfc_server"),
                                   Input::get("rfc_sysnr"),
                                   Input::get("rfc_client"),
                                   Input::get("rfc_user"),
                                   Input::get("rfc_passwd")
            );
    }

    public function insertFollowupID()
    {
        $this->tryAuthAPIToken();
        if(Auth::user() == null)
            return null;

        return Webservice::insertFollowupID(
            Input::get("user_id"),
            Input::get("followup_user_id")
        );
    }

    public function insertRefferalID()
    {
        $this->tryAuthAPIToken();
        if(Auth::user() == null)
            return null;

        return Webservice::insertRefferalID(
            Input::get("user_id"),
            Input::get("refferal_id")
        );
    }

    public function insertVendorID()
    {
        $this->tryAuthAPIToken();
        if(Auth::user() == null)
            return null;

        return Webservice::insertVendorID(
            Input::get("user_id"),
            Input::get("wglif"),
            Input::get("mfrnr")
        );
    }

    public function changePassword(){
        $this->tryAuthAPIToken();
        if(Auth::user() == null)
            return null;

        return Webservice::changePassword(
            Input::get("user_id"),
            Input::get("new_password")
        );
    }

    public function getOrderInfo(){
        $this->tryAuthAPIToken();
        if(Auth::user() == null)
            return null;

        return Webservice::getOrderInfo(
            Input::get("order"),
            Input::get("type")
        );
    }

    public function getVendorUsers(){
        $this->tryAuthAPIToken();
        if(Auth::user() == null)
            return null;

        return Webservice::getVendorUsers(
            Input::get("lifnr")
        );
    }

}
