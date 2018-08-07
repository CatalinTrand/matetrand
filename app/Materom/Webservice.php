<?php

namespace App\Materom;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Webservice {

    static public function rfcPing($rfc_router, $rfc_server, $rfc_sysnr,
                                   $rfc_client, $rfc_user, $rfc_password) {
        return (new RFCData())->ping($rfc_router, $rfc_server, $rfc_sysnr,
                                     $rfc_client, $rfc_user, $rfc_password);
    }
}