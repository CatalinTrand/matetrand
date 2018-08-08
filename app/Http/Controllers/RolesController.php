<?php

namespace App\Http\Controllers;

use App\Materom\Roles;
use Illuminate\Support\Facades\Input;

class RolesController extends Controller
{
    public function insertGlobalData()
    {
        return Roles::insertGlobalData(
            Input::get("rfc_server"),
            Input::get("rfc_router")
        );
    }

    public function insertRoleData()
    {
        return Roles::insertRoleData(
            Input::get("role"),
            Input::get("rfc_sysnr"),
            Input::get("rfc_client"),
            Input::get("rfc_user"),
            Input::get("rfc_passwd")
        );
    }
}