<?php

namespace App\Http\Controllers;

use Materom\Webservice\Webservice;

class WebserviceController extends Controller
{
    public function show($id ,$wstoken)
    {
        if(Webservice::isTokenValid($wstoken))
            return Webservice::show($id);
        else
            return null;
    }
}
