<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use App\Materom\Webservice;
use App\Materom\SAP;

class WebserviceController extends Controller
{
    public function tryAuthAPIToken(){
        if (Auth::user() == null){
            if(Input::get("api_token") != null ){
                $token = Input::get("api_token");
                Auth::attempt(['api_token' => $token]);
            }
        }
    }

    public function rfcPing()
    {
        return Webservice::rfcPing(Input::get("rfc_router"),
                                   Input::get("rfc_server"),
                                   Input::get("rfc_sysnr"),
                                   Input::get("rfc_client"),
                                   Input::get("rfc_user"),
                                   Input::get("rfc_passwd")
            );
    }

    public function insertManufacturer()
    {
        $this->tryAuthAPIToken(); if (Auth::user() == null) return "API authentication failed";
        return Webservice::insertManufacturer(
            Input::get("user_id"),
            Input::get("mfrnr")
        );
    }

    public function acceptItemCHG(){
        return Webservice::acceptItemCHG(
            Input::get("ebeln"),
            Input::get("id"),
            Input::get("type")
        );
    }

    public function cancelItem(){
        return Webservice::cancelItem(
            Input::get("ebeln"),
            Input::get("id"),
            Input::get("type"),
            Input::get("category"),
            Input::get("reason")
        );
    }

    public function sendAck(){
        return Webservice::sendAck(
            Input::get("ebeln"),
            Input::get("ebelp"),
            Input::get("cdate")
        );
    }

    public function sortMessages(){
        return Webservice::sortMessages(
            Input::get("type")
        );
    }

    public function replyToMessage(){
        return Webservice::replyToMessage(
            Input::get("ebeln"),
            Input::get("ebelp"),
            Input::get("cdate"),
            Input::get("idnlf"),
            Input::get("lfdat"),
            Input::get("qty"),
            Input::get("purch_price"),
            Input::get("reason")
        );
    }

    public function changeItemStat(){
        return Webservice::changeItemStat(
            Input::get("column"),
            Input::get("value"),
            Input::get("valuehlp"),
            Input::get("oldvalue"),
            Input::get("ebeln"),
            Input::get("ebelp")
        );
    }

    public function getAllItems(){
        return Webservice::getAllItems(
            Input::get("history")
        );
    }

    function insertReferenceUser(){
        return Webservice::insertReferenceUser(
          Input::get("id"),
          Input::get("refid")
        );
    }

    public function changePassword() {
        $this->tryAuthAPIToken(); if (Auth::user() == null) return "API authentication failed";
        return Webservice::changePassword(Input::get("user_id"), Input::get("new_password"));
    }

    function getNrOfStatusChildren(){
        return Webservice::getNrOfStatusChildren(
            Input::get("id"),
            Input::get("status"),
            Input::get("type"),
            Input::get("history"),
            null
        );
    }

    public function getOrderInfo() {
        $this->tryAuthAPIToken(); if (Auth::user() == null) return "API authentication failed";
        return Webservice::getOrderInfo(
            Input::get("order"),
            Input::get("type"),
            Input::get("item"),
            Input::get("history"),
            Input::get("time_limit"),
            Input::get("filter_vbeln"),
            Input::get("filter_ebeln"),
            Input::get("filter_matnr"),
            Input::get("filter_mtext"),
            Input::get("filter_lifnr"),
            Input::get("filter_lifnr_name")
        );
    }

    public function getSubTree() {
        $this->tryAuthAPIToken(); if (Auth::user() == null) return "API authentication failed";
        return Webservice::getSubTree(
            Input::get("type"),
            Input::get("sorder"),
            Input::get("porder"),
            Input::get("item")
        );
    }

    public function getVendorUsers(){
        $this->tryAuthAPIToken(); if (Auth::user() == null) return "API authentication failed";
        return Webservice::getVendorUsers(Input::get("lifnr"));
    }

    public function getCTVUsers(){
        $this->tryAuthAPIToken(); if (Auth::user() == null) return "API authentication failed";
        return Webservice::getCTVUsers();
    }

    public function sapActivateUser(){
        $this->tryAuthAPIToken(); if (Auth::user() == null) return "API authentication failed";
        return Webservice::sapActivateUser(Input::get("id"));
    }

    public function sapDeactivateUser() {
        $this->tryAuthAPIToken(); if (Auth::user() == null) return "API authentication failed";
        return Webservice::sapDeactivateUser(Input::get("id"));
    }

    public function sapCreateUser() {
        $this->tryAuthAPIToken(); if (Auth::user() == null) return "API authentication failed";
        return Webservice::sapCreateUser(
            Input::get("id"),
            Input::get("username"),
            'Furnizor',
            Input::get("email"),
            Input::get("language"),
            Input::get("lifnr"),
            Input::get("password")
        );
    }

    public function sapDeleteUser() {
        $this->tryAuthAPIToken(); if (Auth::user() == null) return "API authentication failed";
        return Webservice::sapDeleteUser(Input::get("id"));
    }

    public function sapProcessPO() {
        $this->tryAuthAPIToken(); if (Auth::user() == null) return "API authentication failed";
        return Webservice::sapProcessPO(Input::get("ebeln"));
    }

}
