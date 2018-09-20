<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use App\Materom\Webservice;
use App\Materom\SAP;

class WebserviceController extends Controller
{
    public function tryAuthAPIToken(){
        if (Auth::user() == null){
            if(Input::get("api_token") != null ){
                $token = Input::get("api_token");
                Auth::attempt(['api_token' => $token]);
            }
        }
    }

    public function rfcPing()
    {
        return Webservice::rfcPing(Input::get("rfc_router"),
                                   Input::get("rfc_server"),
                                   Input::get("rfc_sysnr"),
                                   Input::get("rfc_client"),
                                   Input::get("rfc_user"),
                                   Input::get("rfc_passwd")
            );
    }

    public function insertVendorID()
    {
        $this->tryAuthAPIToken(); if (Auth::user() == null) return "API authentication failed";
        return Webservice::insertVendorID(
            Input::get("user_id"),
            Input::get("wglif"),
            Input::get("mfrnr")
        );
    }

    public function changePassword() {
        $this->tryAuthAPIToken(); if (Auth::user() == null) return "API authentication failed";
        return Webservice::changePassword(Input::get("user_id"), Input::get("new_password"));
    }

    public function getOrderInfo() {
        $this->tryAuthAPIToken(); if (Auth::user() == null) return "API authentication failed";
        return Webservice::getOrderInfo(Input::get("order"), Input::get("type"));
    }

    public function getVendorUsers(){
        $this->tryAuthAPIToken(); if (Auth::user() == null) return "API authentication failed";
        return Webservice::getVendorUsers(Input::get("lifnr"));
    }

    public function sapActivateUser(){
        $this->tryAuthAPIToken(); if (Auth::user() == null) return "API authentication failed";
        return Webservice::sapActivateUser(Input::get("id"));
    }

    public function sapDeactivateUser() {
        $this->tryAuthAPIToken(); if (Auth::user() == null) return "API authentication failed";
        return Webservice::sapDeactivateUser(Input::get("id"));
    }

    public function sapCreateUser() {
        $this->tryAuthAPIToken(); if (Auth::user() == null) return "API authentication failed";
        return Webservice::sapCreateUser(
            Input::get("id"),
            Input::get("username"),
            'Furnizor',
            Input::get("email"),
            Input::get("language"),
            Input::get("lifnr"),
            Input::get("password")
        );
    }

    public function sapDeleteUser() {
        $this->tryAuthAPIToken(); if (Auth::user() == null) return "API authentication failed";
        return Webservice::sapDeleteUser(Input::get("id"));
    }

    public function sapResetPassword() {
        $this->tryAuthAPIToken(); if (Auth::user() == null) return null;
        return Webservice::changePassword(Input::get("id"), Input::get("password"));
    }

}
