<?php

namespace App\Http\Controllers;

use App\Materom\Mailservice;
use App\Materom\Orders;
use App\Materom\SAP\MasterData;
use App\Materom\System;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Materom\Webservice;
use App\Materom\SAP;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

use Maatwebsite\Excel\Facades\Excel;

class WebserviceController extends Controller
{
    public function tryAuthAPIToken()
    {
        if (Auth::user() == null) {
            if (Input::get("api_token") != null) {
                $token = Input::get("api_token");
                Auth::attempt(['api_token' => $token]);
            }
        }
        if (Auth::user() != null && System::$system != Auth::user()->sap_system) {
            System::init(Auth::user()->sap_system);
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
        $this->tryAuthAPIToken();
        if (Auth::user() == null) return "API authentication failed";
        return Webservice::insertManufacturer(
            Input::get("user_id"),
            Input::get("mfrnr")
        );
    }

    public function acceptItemChange()
    {
        return Webservice::acceptItemChange(
            Input::get("ebeln"),
            Input::get("id"),
            Input::get("type")
        );
    }

    public function cancelItem()
    {
        return Webservice::cancelItem(
            Input::get("ebeln"),
            Input::get("item"),
            Input::get("category"),
            Input::get("reason"),
            Input::get("new_status"),
            Input::get("new_stage")
        );
    }

    public function createPurchReq()
    {
        return Webservice::createPurchReq(
            Input::get("lifnr"),
            Input::get("idnlf"),
            Input::get("mtext"),
            Input::get("matnr"),
            Input::get("qty"),
            Input::get("unit"),
            Input::get("price"),
            Input::get("curr"),
            Input::get("deldate"),
            Input::get("infnr")
        );
    }

    public function sendAck()
    {
        return Webservice::sendAck(
            Input::get("ebeln"),
            Input::get("ebelp"),
            Input::get("cdate")
        );
    }

    public function deletefilters()
    {
//        Session::forget('filter_status');
//        Session::forget('filter_history');
//        Session::forget("filter_archdate");
//        Session::forget("filter_inquirements");
//        Session::forget("filter_overdue");
        Session::forget("filter_vbeln");
        Session::forget("filter_ebeln");
        Session::forget("filter_matnr");
        Session::forget("filter_mtext");
        Session::forget("filter_lifnr");
        Session::forget("filter_lifnr_name");
        Session::forget("autoexplode_PO");
        Session::forget("autoexplode_SO");
        Orders::fillCache();
    }

    public function refilter()
    {
        if (Input::get("type") == 'S') {
            Session::put("filter_vbeln", Input::get("order"));
            // Session::put("groupOrdersBy", 0);
        } else {
            Session::put("filter_ebeln", Input::get("order"));
            // Session::put("groupOrdersBy", 1);
        }
        Orders::fillCache();
    }

    public function sortMessages()
    {
        return Webservice::sortMessages(
            Input::get("type")
        );
    }

    public function itemsOfOrder()
    {
        return Webservice::itemsOfOrder(
            Input::get("type"),
            Input::get("order"),
            Input::get("history"),
            Input::get("vbeln")
        );
    }

    public function replyMessage()
    {
        return Webservice::replyMessage(
            Input::get("ebeln"),
            Input::get("ebelp"),
            Input::get("message"),
            Input::get("to")
        );
    }

    public function doChangeItem()
    {
        return Webservice::doChangeItem(
            Input::get("column"),
            Input::get("value"),
            Input::get("valuehlp"),
            Input::get("oldvalue"),
            Input::get("ebeln"),
            Input::get("ebelp")
        );
    }

    public function readPOItem()
    {
        return Webservice::readPOItem(
            Input::get("order"),
            Input::get("item")
        );
    }

    public function readLifnrName()
    {
        return MasterData::getLifnrName(SAP::alpha_input(Input::get("lifnr")));
    }

    public function readProposals()
    {
        return Webservice::readProposals(
            Input::get("type"),
            Input::get("ebeln"),
            Input::get("ebelp")
        );
    }

    public function processProposal()
    {
        return Webservice::processProposal(json_decode(Input::get("proposal")));
    }

    public function acceptProposal()
    {
        return Webservice::acceptProposal(
            Input::get("ebeln"),
            Input::get("ebelp"),
            Input::get("cdate"),
            Input::get("pos")
        );
    }

    public function rejectProposal()
    {
        return Webservice::rejectProposal(
            Input::get("ebeln"),
            Input::get("ebelp"),
            Input::get("cdate")
        );
    }

    public function processSplit()
    {
        return Webservice::processSplit(json_decode(Input::get("split")));
    }

    public function acceptSplit()
    {
        return Webservice::acceptSplit(
            Input::get("ebeln"),
            Input::get("ebelp"),
            Input::get("cdate")
        );
    }

    public function rejectSplit()
    {
        return Webservice::rejectSplit(
            Input::get("ebeln"),
            Input::get("ebelp"),
            Input::get("cdate")
        );
    }

    public function sendInquiry()
    {
        return Webservice::sendInquiry(
            Input::get("ebeln"),
            Input::get("ebelp"),
            Input::get("text"),
            Input::get("to")
        );
    }

    function insertReferenceUser()
    {
        return Webservice::insertReferenceUser(
            Input::get("id"),
            Input::get("refid")
        );
    }

    function insertAgent()
    {
        return Webservice::insertAgent(
            Input::get("userid"),
            Input::get("agent")
        );
    }

    function insertCustomer()
    {
        return Webservice::insertCustomer(
            Input::get("userid"),
            Input::get("kunnr")
        );
    }

    public function downloadOrdersXLS()
    {

        $orders = Orders::getOrderList(1);

        $itemsArray = [];

        //Excel header
        if (Auth::user()->role == "Furnizor") {
            array_push($itemsArray, [
                __("Purchase order"),
                __("Item"),
                __("Vendor mat."),
                __("Description"),
                __("Fabricant"),
                __("Quantity"), '',
                __("Price"), '',
                __("Delivery date"),
                __("Delivered quantity"),
                __("Goods receipt date"),
            ]);
        } else {
            array_push($itemsArray, [
                __("Purchase order"),
                __("Item"),
                __("Vendor mat."),
                __("Description"),
                __("Supplier"),
                __("Supplier name"),
                __("Refferal"),
                __("Refferal Name"),
                __("Fabricant"),
                __("Quantity"), '',
                __("Price"), '',
                __("Delivery date"),
                __("Delivered quantity"),
                __("Goods receipt date"),
            ]);
        }

        //            array_push($itemsArray,DB::getSchemaBuilder()->getColumnListing("pitems"));

        //Contents
        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                if (Auth::user()->role == "Furnizor") {
                    array_push($itemsArray, [
                        SAP::alpha_output($item->ebeln),
                        SAP::alpha_output($item->ebelp),
                        $item->idnlf,
                        $item->mtext,
                        ucfirst(strtolower(MasterData::getLifnrName($item->mfrnr))),
                        $item->qty,
                        $item->qty_uom,
                        $item->purch_price,
                        $item->purch_curr,
                        substr($item->lfdat, 0, 10),
                        explode(" ", $item->delqty)[0],
                        ($item->grdate == null ? "" : substr($item->grdate, 0, 10))
                    ]);
                } else {
                    array_push($itemsArray, [
                        SAP::alpha_output($item->ebeln),
                        SAP::alpha_output($item->ebelp),
                        $item->idnlf,
                        $item->mtext,
                        SAP::alpha_output($order->lifnr),
                        MasterData::getLifnrName($order->lifnr),
                        $order->ekgrp,
                        MasterData::getEkgrpName($order->ekgrp),
                        ucfirst(strtolower(MasterData::getLifnrName($item->mfrnr))),
                        $item->qty,
                        $item->qty_uom,
                        $item->purch_price,
                        $item->purch_curr,
                        substr($item->lfdat, 0, 10),
                        explode(" ", $item->delqty)[0],
                        ($item->grdate == null ? "" : substr($item->grdate, 0, 10))
                    ]);
                }
            }
        }

        //Build excel
        Excel::create(__("Materom purchase orders ") . substr(now(), 0, 10),
            function ($excel) use ($itemsArray) {
                $excel->setTitle('Items');
                $excel->setCreator(Auth::user()->id)->setCompany('Materom');
                $excel->setDescription('items file');

                $excel->sheet(__("Purchase orders"), function ($sheet) use ($itemsArray) {
                    $sheet->fromArray($itemsArray, null, 'A1', false, false);
                });
            })->download('xlsx');
        return null;
    }

    public function changePassword()
    {
        $this->tryAuthAPIToken();
        if (Auth::user() == null) return "API authentication failed";
        return Webservice::changePassword(Input::get("user_id"), Input::get("new_password"));
    }

    public function impersonateAsUser()
    {
        $this->tryAuthAPIToken();
        if (Auth::user() == null) return "API authentication failed";
        if (Auth::user()->role == "Administrator") {
            Auth::loginUsingId(Input::get("id"));

            System::init(Auth::user()->sap_system);
            Session::put('locale', strtolower(Auth::user()->lang));
            Session::put('materomdbcache', Orders::newCacheToken());
            Session::put("groupOrdersBy", 1);
            if (Auth::user()->role == "CTV") {
                $id = Auth::user()->id;
                DB::delete("delete from " . System::$table_user_agent_clients . " where id = '$id'");
                $clients = SAP::getAgentClients($id);
                if (!empty($clients)) {
                    $sql = "";
                    foreach ($clients as $client) {
                        $sql .= ",('$id','$client->client','$client->agent')";
                    }
                    $sql = substr($sql, 1);
                    DB::insert("insert into " . System::$table_user_agent_clients . " (id, kunnr, agent) values " . $sql);
                }
                $customers = DB::select("select * from " . System::$table_users_cli . " where id = '$id'");
                if (!empty($customers)) {
                    foreach ($customers as $customer) {
                        $client = $customer->kunnr;
                        if (!DB::table(System::$table_user_agent_clients)->where([["id", "=", $id], ["kunnr", "=", $client]])->exists())
                            DB::insert("insert into " . System::$table_user_agent_clients . " (id, kunnr) values ('$id','$client')");
                    }
                }
                Session::put("groupOrdersBy", 4);
            }
            if (Auth::user()->role == "Administrator") Session::put("filter_ebeln", "NONE");
            Orders::fillCache();
        }

    }

    public function getSubTree()
    {
        $this->tryAuthAPIToken();
        if (Auth::user() == null) return "API authentication failed";
        return Webservice::getSubTree(
            Input::get("type"),
            Input::get("sorder"),
            Input::get("porder"),
            Input::get("item")
        );
    }

    public function getVendorUsers()
    {
        $this->tryAuthAPIToken();
        if (Auth::user() == null) return "API authentication failed";
        return Webservice::getVendorUsers(Input::get("lifnr"));
    }

    public function getCTVUsers()
    {
        $this->tryAuthAPIToken();
        if (Auth::user() == null) return "API authentication failed";
        return Webservice::getCTVUsers();
    }

    public function sapActivateUser()
    {
        $this->tryAuthAPIToken();
        if (Auth::user() == null) return "API authentication failed";
        return Webservice::sapActivateUser(Input::get("id"));
    }

    public function sapDeactivateUser()
    {
        $this->tryAuthAPIToken();
        if (Auth::user() == null) return "API authentication failed";
        return Webservice::sapDeactivateUser(Input::get("id"));
    }

    public function sapCreateUser()
    {
        $this->tryAuthAPIToken();
        if (Auth::user() == null) return "API authentication failed";
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

    public function sapDeleteUser()
    {
        $this->tryAuthAPIToken();
        if (Auth::user() == null) return "API authentication failed";
        return Webservice::sapDeleteUser(Input::get("id"));
    }

    public function sapProcessPO()
    {
        $this->tryAuthAPIToken();
        if (Auth::user() == null) return "API authentication failed";
        return Webservice::sapProcessPO(Input::get("ebeln"));
    }

    public function readInforecords()
    {
        $this->tryAuthAPIToken();
        if (Auth::user() == null) return "API authentication failed";
        return SAP::readInforecords(
            Input::get("lifnr"),
            Input::get("lifnr_name"),
            Input::get("idnlf"),
            Input::get("mtext"),
            Input::get("matnr")
        );
    }

    public function readZPRETrecords()
    {
        $this->tryAuthAPIToken();
        if (Auth::user() == null) return "API authentication failed";
        return SAP::readZPRETrecords(
            Input::get("lifnr"),
            Input::get("lifnr_name"),
            Input::get("idnlf"),
            Input::get("mtext"),
            Input::get("matnr")
        );
    }

    public function getSalesMargin()
    {
        $this->tryAuthAPIToken();
        if (Auth::user() == null) return "API authentication failed";
        return MasterData::getSalesMargin(
            Input::get("lifnr"),
            Input::get("mfrnr"),
            Input::get("wglif")
        );
    }

    public function getMessageHistory()
    {
        $this->tryAuthAPIToken();
        if (Auth::user() == null) return "API authentication failed";
        $item = DB::table(System::$table_pitems)->where(["ebeln" => Input::get("ebeln"),
            "ebelp" => Input::get("ebelp")])->first();
        if (is_null($item)) return "";
        return Mailservice::orderHistoryByItem(Auth::user(), $item, "100%");
    }

    public function getFXRate()
    {
        $this->tryAuthAPIToken();
        if (Auth::user() == null) return "API authentication failed";
        return MasterData::getFXRate(Input::get("curr"));
    }

    public function archiveItem()
    {
        $this->tryAuthAPIToken();
        if (Auth::user() == null) return "API authentication failed";
        return Webservice::archiveItem(Input::get("porder"), Input::get("item"));
    }

    public function unarchiveItem()
    {
        $this->tryAuthAPIToken();
        if (Auth::user() == null) return "API authentication failed";
        return Webservice::unarchiveItem(Input::get("porder"), Input::get("item"));
    }

    public function rollbackItem()
    {
        $this->tryAuthAPIToken();
        if (Auth::user() == null) return "API authentication failed";
        return Webservice::rollbackItem(Input::get("porder"), Input::get("item"));
    }


}