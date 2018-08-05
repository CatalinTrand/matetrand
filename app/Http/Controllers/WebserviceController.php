<?php

namespace App\Http\Controllers;

use App\Materom\webservice\Webservice;

class WebserviceController extends Controller
{
    public function show($id ,$token)
    {
        if(Webservice::isValid($token))
            return Webservice::show($id);
    }
}
