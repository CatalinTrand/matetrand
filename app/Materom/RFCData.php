<?php

namespace App\Materom;

use Illuminate\Support\Str;

class RFCData {

    public $rfc_router;
    public $rfc_server;
    public $as_host;
    public $rfc_sysnr;
    public $rfc_client;
    public $rfc_user;
    public $rfc_passwd;

    public function __construct($rfc_router, $rfc_server, $rfc_sysnr, $rfc_client, $rfc_user, $rfc_passwd) {
        $this->as_host = trim($rfc_router);
        if (Str::length($this->as_host) > 0) {
            if (!Str::startsWith($this->as_host, '/H/')) $this->as_host = '/H/' . $this->as_host;
            if (!Str::endsWith($this->as_host, '/H/')) $this->as_host = $this->as_host . '/H/';
            $this->as_host = $this->as_host . $rfc_server;
        } else $this->as_host = $rfc_server;
        $this->rfc_router = trim($rfc_router);
        $this->rfc_server = trim($rfc_server);
        $this->rfc_sysnr = trim($rfc_sysnr);
        $this->rfc_client = trim($rfc_client);
        $this->rfc_user = trim($rfc_user);
        $this->rfc_passwd = trim($rfc_passwd);
    }

    public function parameters() {
        return [
            'ashost' => $this->as_host,
            'sysnr'  => $this->rfc_sysnr,
            'client' => $this->rfc_client,
            'user'   => $this->rfc_user,
            'passwd' => $this->rfc_passwd
        ];

    }

    public function ping() {
//        $parameters = [
//            'ashost' => '/H/91.239.173.2/H/192.168.3.42',
//            'sysnr'  => '00',
//            'client' => '200',
//            'user' => 'cont-test',
//            'passwd' => 'PusulaC'
//        ];

        $parameters = [
            'ashost' => $this->as_host,
            'sysnr'  => $this->rfc_sysnr,
            'client' => $this->rfc_client,
            'user'   => $this->rfc_user,
            'passwd' => $this->rfc_passwd
        ];

        try {
            $connection = new \SAPNWRFC\Connection($parameters);
            $remoteFunction = $connection->getFunction('RFC_PING');
            $returnValue = $remoteFunction->invoke([]);
            $connection->close();
            return "Successfully connected to server using RFC version " . \SAPNWRFC\Connection::rfcVersion();
        } catch (\SAPNWRFC\Exception $e) {
            return $e->getErrorInfo();
        }
    }
}