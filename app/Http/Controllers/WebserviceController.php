<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use App\Materom\Webservice\Webservice;

class WebserviceController extends Controller
{
    public function show()
    {
        if(Webservice::isTokenValid(Input::get("token")))
            return Webservice::show(Input::get("userid"));
        else
            return null;
    }
}
