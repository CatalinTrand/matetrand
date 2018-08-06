<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use App\Materom\Webservice;

class WebserviceController extends Controller
{
    public function show()
    {
        return Webservice::show();
    }
}
