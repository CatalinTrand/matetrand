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

    public $rfc_router;
    public $rfc_server;
    public $rfc_sysnr;
    public $rfc_client;

    public function __construct()
    {
        $this->rfc_router = "";
        $this->rfc_server = "";
        $this->rfc_sysnr = "";
        $this->rfc_client = "";
    }
}