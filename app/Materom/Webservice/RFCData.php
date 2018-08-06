<?php

namespace App\Materom\Webservice;

class RFCData {

    public $rfc_role;
    public $rfc_router;
    public $rfc_server;
    public $rfc_sysnr;
    public $rfc_client;
    public $rfc_user;
    public $rfc_passwd;

    public function __construct() {
        $this->rfc_role = "";
        $this->rfc_router = "";
        $this->rfc_server = "";
        $this->rfc_sysnr = "";
        $this->rfc_client = "";
        $this->rfc_passwd = "";
    }

}