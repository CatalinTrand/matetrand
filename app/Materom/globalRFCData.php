<?php
/**
 * Created by PhpStorm.
 * User: Catalin
 * Date: 08/08/2018
 * Time: 16:56
 */

namespace App\Materom;


class globalRFCData
{

    public $rfc_role;
    public $rfc_sysnr;
    public $rfc_client;
    public $rfc_user;
    public $rfc_passwd;

    public function __construct()
    {
        $this->rfc_server = "";
        $this->rfc_router = "";
    }
}