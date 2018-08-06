<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use App\Materom\Webservice\Webservice;

class WebserviceController extends Controller
{
    public function show()
    {
        return Auth::user()->username;
        if(Webservice::isTokenValid(Input::get("wstoken")))
            return Webservice::show(Input::get("userid"));
        else
            return null;
    }
}
