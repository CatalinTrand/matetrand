<?php

namespace App\Materom;

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

    public function ping() {
        $parameters = [
            'ashost' => '/H/91.239.173.2/H/192.168.3.42',
            'sysnr'  => '00',
            'client' => '200',
            'user' => 'cont-test',
            'passwd' => 'PusulaC'
        ];

        try {
            $connection = new \SAPNWRFC\Connection($parameters);
            $remoteFunction = $connection->getFunction('RFC_PING');
            $returnValue = $remoteFunction->invoke([]);
            $connection->close();
            return "Success " . \SAPNWRFC\Connection::rfcVersion();
        } catch (\SAPNWRFC\Exception $e) {
            return $e->getErrorInfo();
        }
    }
}