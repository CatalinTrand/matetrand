<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use App\Materom\Webservice;

class WebserviceController extends Controller
{
    public function rfcPing()
    {
        return Webservice::rfcPing(Input::get("rfc_router"),
                                   Input::get("rfc_server"),
                                   Input::get("rfc_sysnr"),
                                   Input::get("rfc_client"),
                                   Input::get("rfc_user"),
                                   Input::get("rfc_password")
            );
    }

    public function insertFollowupID()
    {
        return Webservice::insertFollowupID(
            Input::get("user_id"),
            Input::get("followup_user_id")
        );
    }

    public function insertRefferalID()
    {
        return Webservice::insertRefferalID(
            Input::get("user_id"),
            Input::get("refferal_id")
        );
    }

    public function insertVendorID()
    {
        return Webservice::insertVendorID(
            Input::get("user_id"),
            Input::get("lifnr"),
            Input::get("matkl"),
            Input::get("mfrnr")
        );
    }

    public function changePassword(){
        return Webservice::changePassword(
            Input::get("user_id"),
            Input::get("new_password")
        );
    }

}
