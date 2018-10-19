@extends('layouts.app')

@section('content')
    @guest
        @php
            header("/");
            exit();
        @endphp
    @endguest
    @php

        $groupByPO = \Illuminate\Support\Facades\Session::get('groupOrdersBy');
        if (!isset($groupByPO)) $groupByPO = 1;

        if($groupByPO == 1) {
            $groupBySelPO = " selected";
            $groupBySelSO = "";
        } else {
            $groupBySelPO = "";
            $groupBySelSO = " selected";
        }

        $filter_status_type = 0;
        $filter_status_selAP = "";
        $filter_status_selRE = "";
        $filter_status_selNA = "";
        $tmp = \Illuminate\Support\Facades\Session::get('filter_status');
        if (isset($tmp)) {
            if ($tmp == "NA") {
                // toate
                $filter_status_type = 0;
                $filter_status_selNA = "selected";
            } elseif ($tmp == "AP") {
                // aprobat
                $filter_status_type = 2;
                $filter_status_selAP = "selected";
            } elseif ($tmp == "RE")  {
                // rejectat
                $filter_status_type = 3;
                $filter_status_selRE = "selected";
            }
        }

        $filter_history = 1;
        $filter_history_curr = " selected";
        $filter_history_arch = "";
        $tmp = \Illuminate\Support\Facades\Session::get('filter_history');
        if (isset($tmp)) {
            if (intval($tmp) != 2) {
                // noi
                $filter_history = 1;
                $filter_history_curr = " selected";
                $filter_history_arch = "";
            } else {
                // vechi
                $filter_history = 2;
                $filter_history_curr = "";
                $filter_history_arch = " selected";
            }
        }

        $filter_time_val = null;
        $tmp = \Illuminate\Support\Facades\Session::get("filter_archdate");
        if(isset($tmp) && $filter_history == 2) $filter_time_val = $tmp;

        $filter_vbeln = \Illuminate\Support\Facades\Session::get("filter_vbeln");
        if (!isset($filter_vbeln)) $filter_vbeln = "";

        $filter_ebeln = \Illuminate\Support\Facades\Session::get("filter_ebeln");
        if (!isset($filter_ebeln)) $filter_ebeln = "";

        $filter_matnr = \Illuminate\Support\Facades\Session::get("filter_matnr");
        if (!isset($filter_matnr)) $filter_matnr = "";

        $filter_mtext = \Illuminate\Support\Facades\Session::get("filter_mtext");
        if (!isset($filter_mtext)) $filter_mtext = "";

        $filter_lifnr = \Illuminate\Support\Facades\Session::get("filter_lifnr");
        if (!isset($filter_lifnr)) $filter_lifnr = "";

        $filter_lifnr_name = \Illuminate\Support\Facades\Session::get("filter_lifnr_name");
        if (!isset($filter_lifnr_name)) $filter_lifnr_name = "";

    @endphp
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header" style="border-bottom-width: 0px;">
                        @if (\Illuminate\Support\Facades\Auth::user()->role == "Administrator")
                            <a href="/roles"><p
                                style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;"
                                class="card-line first">
                                <image style='height: 2.2rem; margin-left: -1.5rem;' src='/images/icons8-administrative-tools-48.png'/>
                                {{__("Roles")}}
                                </p></a>
                            <a href="/users"><p
                                style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;"
                                class="card-line">
                                <image style='height: 2.2rem; margin-left: -1.5rem;' src='/images/icons8-user-account-80.png'/>
                                {{__("Users")}}
                                </p></a>
                            <a href="/messages"><p
                                style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;"
                                class="card-line">
                                <image style='height: 2.2rem; margin-left: -1.5rem;' src='/images/icons8-chat-80.png'/>
                                {{__("Messages")}}
                                </p></a>
                            <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;"
                               class="card-line selector">
                                <image style='height: 2.2rem; margin-left: -1.5rem;' src='/images/icons8-todo-list-96.png'/>
                                {{__("Orders")}}
                            </p>
                        @else
                            <a href="/messages"><p
                                style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;"
                                class="card-line first">Messages</p></a>
                            <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;"
                               class="card-line selector">
                                <image style='height: 2.2rem; margin-left: -1.5rem;' src='/images/icons8-todo-list-96.png'/>
                                {{__("Orders")}}
                            </p>
                        @endif
                    </div>

                    <div class="card-body" style="padding-bottom: 0px;">

                        <div style="border: 1px solid black; border-radius: 0.5rem; padding: 8px; height: 7.5rem;">
                            <form action="orders" method="post">
                                {{csrf_field()}}
                                <div class="container row" style="display: block; max-width: 100%;">
                                    Afisare dupa:&nbsp;
                                    <select class="form-control-sm input-sm" style="height: 1.6rem; padding: 2px;" name="groupOrdersBy" onchange="this.form.submit()">
                                        <option value="0"{{$groupBySelSO}}>Comenzi client</option>
                                        <option value="1"{{$groupBySelPO}}>Comenzi de aprovizionare</option>
                                    </select>
                                    Filtrare dupa status:
                                    <select class="form-control-sm input-sm" style="height: 1.6rem; padding: 2px;" name="filter_status" onchange="this.form.submit()">
                                        <option value="NA"{{$filter_status_selNA}}>{{__('Toate')}}</option>
                                        <option value="AP"{{$filter_status_selAP}}>{{__('Aprobate')}}</option>
                                        <option value="RE"{{$filter_status_selRE}}>{{__('Rejectate')}}</option>
                                    </select>
                                </div><br>
                                <div class="container row" style="display: block; max-width: 100%;">
                                    Filtrare dupa istoric:
                                    <select class="form-control-sm input-sm" style="height: 1.6rem; padding: 2px;" name="filter_history" onchange="this.form.submit()">
                                        <option value="1"{{$filter_history_curr}}>{{__("Neprocesate")}}</option>
                                        <option value="2"{{$filter_history_arch}}>{{__("Procesate")}}</option>
                                        @if ($filter_history == 2)
                                            &nbsp;&nbsp;&nbsp;Documente arhivate de la:
                                            <input type="date" id="time_search" name="time_search" value="{{$filter_time_val}}"
                                                   onchange="this.form.submit()">
                                        @endif
                                    </select>
                                </div><br>
                                <div class="container row" style="display: block; max-width: 100%;">
                                    @if (\Illuminate\Support\Facades\Auth::user()->role != "Furnizor")
                                        {{__("Sales order")}}:
                                        <input type="text" class="form-control-sm input-sm" style="width: 6rem; height: 1.4rem;" name="filter_vbeln" value="{{$filter_vbeln}}">&nbsp;&nbsp;
                                    @endif
                                    {{__("Purchase order")}}:
                                    <input type="text" class="form-control-sm input-sm" style="width: 6rem; height: 1.4rem;" name="filter_ebeln" value="{{$filter_ebeln}}">&nbsp;&nbsp;
                                    {{__("Material")}}:
                                    <input type="text" class="form-control-sm input-sm" style="width: 6rem; height: 1.4rem;" name="filter_matnr" value="{{$filter_matnr}}">&nbsp;&nbsp;
                                    {{__("Material description")}}:
                                    <input type="text" class="form-control-sm input-sm" style="width: 12rem; height: 1.4rem;" name="filter_mtext" value="{{$filter_mtext}}">&nbsp;&nbsp;
                                    @if (\Illuminate\Support\Facades\Auth::user()->role != "Furnizor")
                                        {{__("Supplier")}}:
                                        <input type="text" class="form-control-sm input-sm" style="width: 6rem; height: 1.4rem;" name="filter_lifnr" value="{{$filter_lifnr}}">&nbsp;&nbsp;
                                        {{__("Supplier name")}}:
                                        <input type="text" class="form-control-sm input-sm" style="width: 12rem; height: 1.4rem;" name="filter_lifnr_name" value="{{$filter_lifnr_name}}">&nbsp;&nbsp;
                                    @endif
                                </div>
                                <input type="submit" style="position: absolute; left: -9999px; width: 1px; height: 1px;" tabindex="-1">
                            </form>
                        </div>
                    </div>

                    <br>

                    <div class="card-body orders-table-div" style="height: 70vh; padding-top: 0rem;">

                        <table style="border: 2px solid black; table-layout: fixed;" class="orders-table basicTable table table-striped" id="orders_table">
                            <colgroup>
                                <col width="2%">
                                <col width="2%">
                                <col width="2%">
                                <col width="2%">
                                <col width="2%">
                                <col width="2%">
                                <col width="2%">
                                <col width="2%">
                                <col width="2%">
                                <col width="2%">
                                <col width="2%">
                                <col width="3%">
                                <col width="3%">
                                <col width="3%">
                                <col width="3%">
                                <col width="3%">
                                <col width="3%">
                                <col width="3%">
                                <col width="3%">
                                <col width="3%">
                                <col width="3%">
                                <col width="3%">
                                <col width="3%">
                                <col width="3%">
                                <col width="3%">
                                <col width="3%">
                                <col width="3%">
                                <col width="3%">
                                <col width="3%">
                                <col width="3%">
                                <col width="3%">
                                <col width="2%">
                                <col width="2%">
                                <col width="2%">
                                <col width="2%">
                                <col width="2%">
                                <col width="2%">
                                <col width="2%">
                                <col width="2%">
                                <col width="2%">
                            </colgroup>
                            <tr>
                                <th colspan="1" class="td01">
                                    <image style='height: 1.3rem;' src='/images/icons8-check-all-50.png'/>
                                </th>
                                <th colspan="1" class="td01">
                                    <image style='height: 1.3rem;' src='/images/icons8-info-50.png'/>
                                </th>
                                <th colspan="1" class="td01">
                                    <image style='height: 1.3rem;' src='/images/icons8-circled-right-50-1.png'/>
                                </th>
                                <th colspan="1" class="td01">
                                    <image style='height: 1.3rem;' src='/images/icons8-circled-thin-50.png'/>
                                </th>
                                <th colspan="1" class="td01">
                                    <image style='height: 1.3rem;' src='/images/icons8-checkmark-50-1.png'/>
                                </th>
                                <th colspan="1" class="td01">
                                    <image style='height: 1.3rem;' src='/images/icons8-delete-50-2.png'/>
                                </th>
                                <th colspan="1" class="td01">
                                    <image style='height: 1.3rem;' src='/images/icons8-qmark-50.png'/>
                                </th>
                                <th colspan="1" class="td01">
                                    <image style='height: 1.5rem;' src='/images/icons8-checkmark-50-3.png'/>
                                </th>
                                <th colspan="1" class="td01">
                                    <image style='height: 1.5rem;' src='/images/icons8-close-window-50.png'/>
                                </th>
                                <th colspan="1" class="td01">
                                    <image style='height: 1.5rem;' src='/images/icons8-greater-than-50-1.png'/>
                                </th>
                                @php
                                    if ($groupByPO == 1) {
                                        echo '<th class="td02" colspan="3">' . __('Comanda aprovizionare') . '</th>';
                                        $th1 = __("Supplier");
                                        $th2 = ""; // "Nume";
                                        $th3 = __("Referent");
                                        $th4 = ""; // "Aprovizionare";
                                        $th5 = __("Data creare");
                                        $th6 = __("Moneda");
                                        $th7 = __("Rata schimb");
                                    } else {
                                        echo '<th class="td02" colspan="3">' . __('Comanda client') . '</th>';
                                        if (\Illuminate\Support\Facades\Auth::user()->role != "Furnizor" ) {
                                            $th1 = __("Client");
                                            $th2 = ""; // "Nume";
                                            $th3 = __("Livrare la");
                                            $th4 = ""; // "Nume";
                                            $th5 = __("CTV");
                                            $th6 = __("Nume consilier");
                                            $th7 = "";
                                        } else {
                                            $th1 = "";
                                            $th2 = "";
                                            $th3 = "";
                                            $th4 = "";
                                            $th5 = "";
                                            $th6 = "";
                                            $th7 = "";
                                        }

                                    }
                                if ($groupByPO == 1) {
                                    echo "<th colspan=2>$th1</th>";
                                    echo "<th colspan=5>$th2</th>";
                                    echo "<th colspan=2>$th3</th>";
                                    echo "<th colspan=4>$th4</th>";
                                    echo "<th colspan=3>$th5</th>";
                                    echo "<th colspan=2>$th6</th>";
                                    echo "<th colspan=3>$th7</th>";
                                    for ($i = 0; $i < 6; $i++) echo "<th>&nbsp;</th>";
                                } else {
                                    echo "<th colspan=2>$th1</th>";
                                    echo "<th colspan=5>$th2</th>";
                                    echo "<th colspan=2>$th3</th>";
                                    echo "<th colspan=5>$th4</th>";
                                    echo "<th colspan=2>$th5</th>";
                                    echo "<th colspan=5>$th6</th>";
                                    echo "<th>$th7</th>";
                                    for ($i = 0; $i < 5; $i++) echo "<th>&nbsp;</th>";
                                }
                                @endphp
                            </tr>
                            @php
                                $orders = App\Materom\Orders::getOrderList();
                                $line_counter = 1;

                                foreach ($orders as $order) {

                                    if ($groupByPO == 1) {
                                        $comanda = "<button type='button' style='width: 1.6rem; text-align: center;' onclick='getSubTree(this); return false;'>+</button> ".
                                            \App\Materom\SAP::alpha_output($order->ebeln);
                                    } else {
                                        $buttname = $order->vbeln;
                                        if (strtoupper($buttname) == \App\Materom\Orders::stockorder) $buttname = __('Stock');
                                        elseif (strtoupper(trim($buttname)) == "SALESORDER") $buttname = __('Emergency');
                                        else $buttname = \App\Materom\SAP::alpha_output($buttname);
                                        $comanda = "<button type='button' style='width: 1.6rem; text-align: center;' onclick='getSubTree(this); return false;'>+</button> $buttname";
                                    }

                                    $line_counter = $line_counter + 1;
                                    if ($line_counter == 2) $line_counter = 0;

                                    $button_accept = "";
                                    $button_reject = "";
                                    $button_inquire = "";

                                    if ($groupByPO == 1) {
                                        $oid = "P" . $order->ebeln;
                                        $data = "<td class='td02' colspan=2>" . \App\Materom\SAP::alpha_output($order->lifnr) . "</td>" .
                                                "<td class='td02' colspan=5>$order->lifnr_name</td>" .
                                                "<td class='td02' colspan=1>$order->ekgrp</td>" .
                                                "<td class='td02' colspan=5>$order->ekgrp_name</td>" .
                                                "<td class='td02' colspan=3>$order->erdat</td>" .
                                                "<td class='td02' colspan=2>$order->curr</td>" .
                                                "<td class='td02' colspan=3>$order->fxrate</td>";

                                        switch ($order->info) {
                                            case 0:
                                                $info_icon = "";
                                                break;
                                            case 1:
                                                $info_icon = "<image style='height: 1.2rem;' src='/images/green_blink.gif'>";
                                                break;
                                            case 2:
                                                $info_icon = "<image style='height: 1.2rem;' src='/images/warning.png'>";
                                                break;
                                            case 3:
                                                $info_icon = "<image style='height: 1.2rem;' src='/images/critical.png'>";
                                                break;
                                            case 4:
                                                $info_icon = "<image style='height: 1.2rem;' src='/images/yellow_blink.png'>";
                                                break;
                                        }
                                        switch ($order->owner) {
                                            case 0:
                                                $owner_icon = "";
                                                break;
                                            case 1:
                                                $owner_icon = "<image style='height: 1.2rem;' src='/images/blueArrow.png'>";
                                                break;
                                            case 2:
                                                $owner_icon = "<image style='height: 1.2rem;' src='/images/yellowArrow.png'>";
                                                break;
                                            case 3:
                                                $owner_icon = "<image style='height: 1.2rem;' src='/images/purpleArrow.png'>";
                                                break;
                                        }

                                        $changed_icon = "";
                                        if ($order->changed != 0)
                                            $changed_icon = "<image style='height: 1.3rem;' src='/images/icons8-circled-thin-50.png'/>";
                                        $accepted_icon = "";
                                        if ($order->accepted == 1)
                                            $accepted_icon = "<image style='height: 1.3rem;' src='/images/icons8-checkmark-50-1.png'/>";
                                        elseif ($order->accepted == 2)
                                            $accepted_icon = "<image style='height: 1.3rem;' src='/images/icons8-checkmark-50-1.png'/>";
                                        $rejected_icon = "";
                                        if ($order->rejected == 1)
                                            $rejected_icon = "<image style='height: 1.3rem;' src='/images/icons8-delete-50-2.png'/>";
                                        elseif ($order->rejected == 2)
                                            $rejected_icon = "<image style='height: 1.3rem;' src='/images/icons8-delete-50-2.png'/>";
                                        $inquired_icon = "";
                                        if ($order->inquired == 1)
                                            $inquired_icon = "<image style='height: 1.3rem;' src='/images/icons8-qmark-50-green.png'/>";
                                        elseif ($order->inquired == 2)
                                            $inquired_icon = "<image style='height: 1.3rem;' src='/images/icons8-qmark-50-red.png'/>";
                                        elseif ($order->inquired == 3)
                                            $inquired_icon = "<image style='height: 1.3rem;' src='/images/icons8-qmark-50-blue.png'/>";

                                        if ($order->accept == 1)
                                            $button_accept = "<button type='button' class='order-button-accepted' style='width: 1.5rem; height: 1.5rem; text-align: center;'/>";
                                        if ($order->reject == 1)
                                            $button_reject = "<button type='button' class='order-button-rejected' style='width: 1.6rem; height: 1.5rem; text-align: center;'/>";
                                        if ($order->inquire == 1)
                                            $button_inquire = "<button type='button' class='order-button-request' style='width: 1.5rem; height: 1.5rem; text-align: center;'/>";

                                        if ($line_counter == 0)
                                            $style = "background-color:LightYellow;";
                                        else
                                            $style = "background-color:Wheat;";

                                        echo "<tr id='tr_$oid' style='$style'>" .
                                             "<td align='center' style='vertical-align: middle;'>".
                                                "<input id='input_chk' type=\"checkbox\" name=\"$oid\" value=\"$oid\" onclick='boxCheck(this);'></td>" .
                                             "<td class='td01'>$info_icon</td>" .
                                             "<td class='td01'>$owner_icon</td>" .
                                             "<td class='td01'>$changed_icon</td>" .
                                             "<td class='td01'>$accepted_icon</td>" .
                                             "<td class='td01'>$rejected_icon</td>" .
                                             "<td class='td01'>$inquired_icon</td>" .
                                             "<td  class='td01' style='padding: 0;'>$button_accept</td>" .
                                             "<td class='td01' style='padding: 0;'>$button_reject</td>" .
                                             "<td class='td01' style='padding: 0;'>$button_inquire</td>" .
                                             "<td colspan='3' class='td02' class='first_color'>$comanda</td>" .
                                             "$data<td colspan='6'></td></tr>";
                                    }else{
                                        $oid = "S" . $order->vbeln;
                                        if (\Illuminate\Support\Facades\Auth::user()->role != "Furnizor") {
                                            $data = "<td class='td02' colspan=2>" . \App\Materom\SAP::alpha_output($order->kunnr) . "</td>" .
                                                    "<td class='td02' colspan=5>$order->kunnr_name</td>" .
                                                    "<td class='td02' colspan=2>" . \App\Materom\SAP::alpha_output($order->shipto) . "</td>" .
                                                    "<td class='td02' colspan=5>$order->shipto_name</td>" .
                                                    "<td class='td02' colspan=2>$order->ctv</td>" .
                                                    "<td class='td02' colspan=5>$order->ctv_name</td>".
                                                    "<td></td>";
                                        } else {
                                            $data = "<td class='td02' colspan=2>&nbsp;</td>" .
                                                    "<td class='td02' colspan=5>&nbsp;</td>" .
                                                    "<td class='td02' colspan=2>&nbsp;</td>" .
                                                    "<td class='td02' colspan=5>&nbsp;</td>" .
                                                    "<td class='td02' colspan=2>&nbsp;</td>" .
                                                    "<td class='td02' colspan=5>&nbsp;</td>".
                                                    "<td></td>";
                                        }

                                        switch ($order->info) {
                                            case 0:
                                                $info_icon = "";
                                                break;
                                            case 1:
                                                $info_icon = "<image style='height: 1.2rem;' src='/images/green_blink.gif'>";
                                                break;
                                            case 2:
                                                $info_icon = "<image style='height: 1.2rem;' src='/images/warning.png'>";
                                                break;
                                            case 3:
                                                $info_icon = "<image style='height: 1.2rem;' src='/images/critical.png'>";
                                                break;
                                            case 4:
                                                $info_icon = "<image style='height: 1.2rem;' src='/images/yellow_blink.png'>";
                                                break;
                                        }
                                        switch ($order->owner) {
                                            case 0:
                                                $owner_icon = "";
                                                break;
                                            case 1:
                                                $owner_icon = "<image style='height: 1.2rem;' src='/images/blueArrow.png'>";
                                                break;
                                            case 2:
                                                $owner_icon = "<image style='height: 1.2rem;' src='/images/yellowArrow.png'>";
                                                break;
                                            case 3:
                                                $owner_icon = "<image style='height: 1.2rem;' src='/images/purpleArrow.png'>";
                                                break;
                                        }

                                        $changed_icon = "";
                                        if ($order->changed != 0)
                                            $changed_icon = "<image style='height: 1.3rem;' src='/images/icons8-circled-thin-50.png'/>";
                                        $accepted_icon = "";
                                        if ($order->accepted == 1)
                                            $changed_icon = "<image style='height: 1.3rem;' src='/images/icons8-checkmark-50-1.png'/>";
                                        elseif ($order->accepted == 2)
                                            $changed_icon = "<image style='height: 1.3rem;' src='/images/icons8-checkmark-50-1.png'/>";
                                        $rejected_icon = "";
                                        if ($order->rejected == 1)
                                            $rejected_icon = "<image style='height: 1.3rem;' src='/images/icons8-delete-50-2.png'/>";
                                        elseif ($order->rejected == 2)
                                            $rejected_icon = "<image style='height: 1.3rem;' src='/images/icons8-delete-50-2.png'/>";
                                        $inquired_icon = "";
                                        if ($order->inquired == 1)
                                            $imquired_icon = "<image style='height: 1.3rem;' src='/images/icons8-qmark-50-green.png'/>";
                                        elseif ($order->inquired == 2)
                                            $inquired_icon = "<image style='height: 1.3rem;' src='/images/icons8-qmark-50-red.png'/>";
                                        elseif ($order->inquired == 3)
                                            $inquired_icon = "<image style='height: 1.3rem;' src='/images/icons8-qmark-50-blue.png'/>";

                                        if ($order->accept == 1)
                                            $button_accept = "<button type='button' class='order-button-accepted' style='width: 1.5rem; height: 1.5rem; text-align: center;'/>";
                                        if ($order->reject == 1)
                                            $button_reject = "<button type='button' class='order-button-rejected' style='width: 1.6rem; height: 1.5rem; text-align: center;'/>";
                                        if ($order->inquire == 1)
                                            $button_inquire = "<button type='button' class='order-button-request' style='width: 1.5rem; height: 1.5rem; text-align: center;'/>";

                                        if ($line_counter == 0)
                                            $style = "background-color:white;";
                                        else
                                            $style = "background-color:WhiteSmoke;";

                                        echo "<tr id='tr_$oid' style='$style' class='td01'>" .
                                             "<td align='center' style='vertical-align: middle;'>" .
                                             "<input id='input_chk' type=\"checkbox\" name=\"$oid\" value=\"$oid\" onclick='boxCheck(this);'></td>" .
                                             "<td>$info_icon</td>" .
                                             "<td>$owner_icon</td>" .
                                             "<td colspan='7'></td>" .
                                             "<td colspan='3' class='td02' class='first_color'>$comanda</td>" .
                                             "$data<td colspan='5'></td></tr>";
                                    }
                                }
                            @endphp
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>

        @php
            echo 'let testvar = "' . (\Illuminate\Support\Facades\Auth::user()->role). '";';
        @endphp

        var checkedList = [];
        var unCheckedList = [];

        function parent(id) {
            if (id.startsWith('I')) {
                let res = id.substring(1);
                @if ($groupByPO == 0)
                    return $("input[name*='_" + res.split("_")[0] + "']")[0].name;
                @else
                    return $("input[name*='" + res.split("_")[0] + "']")[0].name;
                @endif
            } else if (id.startsWith('P')) {
                @if ($groupByPO == 1)
                    return null;
                @else
                    let res = id.substring(1);
                    return "S" + res.split("_")[0];
                @endif
            } else {
                return null;
            }
        }

        function isChecked(id) {

            if ($.inArray(id, checkedList) > -1)
                return true;
            if ($.inArray(id, unCheckedList) > -1)
                return false;

            @if ($groupByPO == 1)
                if (id.startsWith('S') || id.startsWith('P')) {
            @else
                if (id.startsWith('S')) {
            @endif
                return false;
            }

            return isChecked(parent(id));
        }

        function isChildOf(node, maybeParent) {

            if (parent(node) == null)
                return false;

            if (node == maybeParent)
                return true;

            if (parent(node) == maybeParent)
                return true;


            if (node.startsWith('I') && maybeParent.startsWith('S'))
                if (parent(parent(node)) == maybeParent)
                    return true;

            return false;
        }

        function addToChecked(id) {

            if ($.inArray(id, checkedList) <= -1) {
                checkedList.push(id);
            }
            if ($.inArray(id, unCheckedList) > -1)
                unCheckedList.splice($.inArray(id, unCheckedList), 1);

            for (let i = 0; i < unCheckedList.length; i++) {
                if (isChildOf(unCheckedList[i], id)) {
                    unCheckedList.splice(i, 1);
                    i--;
                }
            }
        }

        function removeFromChecked(id) {
            if ($.inArray(id, checkedList) > -1)
                checkedList.splice($.inArray(id, checkedList), 1);
            else if ($.inArray(id, unCheckedList) <= -1)
                unCheckedList.push(id);

            for (let i = 0; i < checkedList.length; i++) {
                if (isChildOf(checkedList[i], id)) {
                    checkedList.splice(i, 1);
                    i--;
                }
            }
        }

        function boxCheck(_this) {
            if (!isChecked(_this.name)) {
                addToChecked(_this.name);
            } else {
                removeFromChecked(_this.name);
                removeFromChecked(_this.name);
            }
            refreshCheck();
        }

        function refreshCheck() {
            let inputs = $("input[id|='input_chk']");
            for (let i = 0; i < inputs.length; i++) {
                inputs[i].checked = isChecked(inputs[i].name);
            }
        }

        function acceptItem(ebeln, id, type) {
            var _data, _status = "";
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajaxSetup({async: false});

            $.post("webservice/acceptItemCHG",
                {
                    ebeln: ebeln,
                    id: id,
                    type: type,
                },
                function (data, status) {
                    _data = data;
                    _status = status;
                });
            jQuery.ajaxSetup({async: true});
            if (_status == "success") {
                return;//todo
                //show new row
                if (_this.closest('tr').innerHTML.toString().includes(">-</button>")) {
                    var date = new Date();
                    var chdate = date.getFullYear() + "-" + date.getMonth() + "-" + date.getDay() + " " + date.getHours() + ":" + date.getMinutes() + ":" + date.getSeconds();
                    var cuser = "{{\Illuminate\Support\Facades\Auth::user()->id}}";
                    var cuser_name = "{{\Illuminate\Support\Facades\Auth::user()->username}}";
                    var ctext = "{{__("Acceptare")}}";
                    var creason = "";
                    if (chdate != null) {
                        var newRow = $("<tr>");
                        var cols = "";
                        var pi_style = "background-color:" + $(_this).css("background-color") + ";";
                        var color = $(_this).closest("tr").find(".coloured").css("background-color");
                        var last_style = "background-color:" + color;
                        var first_color = $(_this).closest("tr").find(".first_color").css("background-color");
                        var first_style = "background-color:" + first_color;
                        cols += '<td class="first_color" colspan="10" style="' + first_style + '"></td>';
                        @if ($groupByPO == 0)
                            let colsafter = "12";
                            cols += '<td class="first_color" colspan="1" style="' + first_style + '"></td>';
                        @else
                            let colsafter = "13";
                        @endif
                        cols += '<td class="coloured" style="' + last_style + '"></td>';
                        cols += '<td style="' + pi_style + '"></td>';
                        cols += "<td colspan='3'>" + chdate + "</td>";
                        cols += '<td colspan="4">' + cuser + ' ' + cuser_name + '</td>';
                        cols += '<td colspan="6">' + ctext + '</td>';
                        cols += '<td colspan="2">' + creason + '</td>';
                        cols += "<td colspan=" + colsafter + "></td>";
                        @if ($groupByPO == 1)
                            cols += '<td></td>';
                        @endif
                        cols += '<td colspan="4"></td>';
                        newRow.append(cols);
                        newRow.insertAfter($(_this).closest("tr"));
                        if (line_counter == 0)
                            newRow.attr('style', "background-color:Azure; vertical-align: middle;");
                        else
                            newRow.attr('style', "background-color:LightCyan; vertical-align: middle;");
                        alert("Accepted!");
                        newRow.attr('id', "tr_C" + ebeln3 + "_" + ebelp3 + "_" +
                            chdate.substr(0, 10) + "_" + chdate.substr(11, 8));
                    }
                }
            } else alert('Error processing operation!');
        }

        function rejectItem(ebeln, id, type, category, reason) {
            var _data, _status = "";
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajaxSetup({async: false});

            $.post("webservice/cancelItem",
                {
                    ebeln: ebeln,
                    id: id,
                    type: type,
                    category: category,
                    reason: reason
                },
                function (data, status) {
                    _data = data;
                    _status = status;
                });
            jQuery.ajaxSetup({async: true});
            if (_status == "success") {
                alert("Rejected!");
                return;//todo
                //show new row
                if (_this.closest('tr').innerHTML.toString().includes(">-</button>")) {
                    var date = new Date();
                    var chdate = date.getFullYear() + "-" + date.getMonth() + "-" + date.getDay() + " " + date.getHours() + ":" + date.getMinutes() + ":" + date.getSeconds();
                    var cuser = "{{\Illuminate\Support\Facades\Auth::user()->id}}";
                    var cuser_name = "{{\Illuminate\Support\Facades\Auth::user()->username}}";
                    var ctext = "{{__("Rejectare")}}";
                    var creason = "";
                    if (chdate != null) {
                        var newRow = $("<tr>");
                        var cols = "";
                        var pi_style = "background-color:" + $(_this).css("background-color") + ";";
                        var color = $(_this).closest("tr").find(".coloured").css("background-color");
                        var last_style = "background-color:" + color;
                        var first_color = $(_this).closest("tr").find(".first_color").css("background-color");
                        var first_style = "background-color:" + first_color;
                        cols += '<td class="first_color" colspan="10" style="' + first_style + '"></td>';
                        @if ($groupByPO == 0)
                            let colsafter = "12";
                            cols += '<td class="first_color" colspan="1" style="' + first_style + '"></td>';
                        @else
                            let colsafter = "12";
                        @endif
                        cols += '<td class="coloured" style="' + last_style + '"></td>';
                        cols += '<td style="' + pi_style + '"></td>';
                        cols += "<td colspan='3'>" + chdate + "</td>";
                        cols += '<td colspan="4">' + cuser + ' ' + cuser_name + '</td>';
                        cols += '<td colspan="6">' + ctext + '</td>';
                        cols += '<td colspan="2">' + creason + '</td>';
                        cols += "<td colspan=" + colsafter + "></td>";
                        @if ($groupByPO == 1)
                            cols += '<td></td>';
                        @endif
                        cols += '<td colspan="4"></td>';
                        newRow.append(cols);
                        newRow.insertAfter($(_this).closest("tr"));
                        if (line_counter == 0)
                            newRow.attr('style', "background-color:Azure; vertical-align: middle;");
                        else
                            newRow.attr('style', "background-color:LightCyan; vertical-align: middle;");
                        newRow.attr('id', "tr_C" + ebeln3 + "_" + ebelp3 + "_" +
                            chdate.substr(0, 10) + "_" + chdate.substr(11, 8));
                    }
                }
                alert("Rejected!");
            } else alert('Error processing operation!');
        }

        function accept(ebeln, id, type) {
            if (checkedList.length > 0) {
                //apply to all
                let f_history = {{$filter_history}};
                var _data2, _status2 = "";
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                jQuery.ajaxSetup({async: false});

                $.post("webservice/getAllItems",
                    {
                        history: f_history
                    },
                    function (data, status) {
                        _data2 = data;
                        _status2 = status;
                    });
                jQuery.ajaxSetup({async: true});
                if (_status2 == "success") {
                    var split = _data2.split('=');
                    split.forEach(function (_ord) {
                        let _ebeln = _ord.split('#')[0];
                        let _id = _ord.split('#')[1];
                        if(isChecked("I" + _ebeln + "_" + _id))
                            acceptItem(_ebeln,_id,'item-purch');
                    });
                }
            } else {
                //apply individually
                acceptItem(ebeln, id, 'item-purch');
            }
        }

        function reject(ebeln, id, type, category, reason) {
            if (checkedList.length > 0) {
                //apply to all
                //apply to all
                let f_history = {{$filter_history}};
                var _data2, _status2 = "";
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                jQuery.ajaxSetup({async: false});

                $.post("webservice/getAllItems",
                    {
                        history: f_history
                    },
                    function (data, status) {
                        _data2 = data;
                        _status2 = status;
                    });
                jQuery.ajaxSetup({async: true});
                if (_status2 == "success") {
                    var split = _data2.split('=');
                    split.forEach(function (_ord) {
                        let _ebeln = _ord.split('#')[0];
                        let _id = _ord.split('#')[1];
                        if(isChecked("I" + _ebeln + "_" + _id))
                            rejectItem(_ebeln,_id,'item-purch',category,reason);
                    });
                    return true;
                }
            } else {
                //apply individually
                rejectItem(ebeln, id, 'item-purch', category,reason);
                return true;
            }
            return false;
        }

        function hasNoChildrenWithStatus(id, status, type) {

            var _data, _status;

            let f_history = {{$filter_history}};

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajaxSetup({async: false});

            $.post("webservice/getNrOfStatusChildren",
                {
                    id: id,
                    status: status,
                    type: type,
                    history: f_history
                },
                function (data, status) {
                    _data = data;
                    _status = status;
                });
            jQuery.ajaxSetup({async: true});
            if (_status == "success") {

                if (_data == 1)
                    return false;
            }
            return true;
        }

        function getSubTree(thisbtn) {
            var currentrow;
            let rowid = (currentrow = $(thisbtn).parent().parent()).attr('id').toUpperCase();
            let rowtype = rowid.substr(3, 1);
            let order = rowid.substr(4, 10);
            let sorder = "";
            let porder = "";
            let item = "";
            if (rowtype == 'S') sorder = order;
            else if (rowtype == "I") {
                porder = order;
                item = rowid.substr(15, 5);
            }
            else if (rowtype == 'P') {
                porder = order;
                @if ($groupByPO == 0)
                    let prevRow = $(currentrow).prev();
                    while (prevRow.attr("id").substr(0, 4) != "tr_S") prevRow = $(prevRow).prev();
                    sorder = $(prevRow).attr("id").substr(4, 10);
                @endif
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajaxSetup({async: false});
            var _data, _status;
            $.get("webservice/getsubtree",
                {
                    type: rowtype,
                    sorder: sorder,
                    porder: porder,
                    item: item
                },
                function (data, status) {
                    _data = data;
                    _status = status;
                }, "json");
            jQuery.ajaxSetup({async: true});
            if (_status != "success") return;
            if (_data.length > 0) {
                if (rowtype == 'S') getSOSubTree(currentrow, sorder, _data);
                else if (rowtype == 'P') getPOSubTree(currentrow, sorder, porder, _data);
                else if (rowtype == 'I') getPOItemSubTree(currentrow, porder, item, _data);
            }
            thisbtn.innerHTML = '-';
            thisbtn.onclick = function () {
                hideSubTree(this);
                return false;
            };
            return false;
        }

        function getSOSubTree(currentrow, order, _data)
        {
            // PO header
            var newRow = $("<tr>");
            var cols = "";
            var so_style = "background-color:" + $(currentrow).css("background-color") + ";";
            cols += '<td class="first_color" style="' + so_style + '" colspan="11"></td>';
            cols += '<td colspan="3"><b>{{__("Comanda aprovizionare")}}</b></td>';
            cols += '<td colspan="2"><b>{{__("Supplier")}}</b></td>';
            cols += '<td colspan="5"><b>&nbsp;</b></td>';
            cols += '<td colspan="2"><b>{{__("Referent")}}</b></td>';
            cols += '<td colspan="4"><b>&nbsp;</b></td>';
            cols += '<td colspan="3"><b>{{__("Data creare")}}</b></td>';
            cols += '<td colspan="2"><b>{{__("Moneda")}}</b></td>';
            cols += '<td colspan="3"><b>{{__("Rata de schimb")}}</b></td>';
            cols += '<td colspan="5"><b></b></td>';
            newRow.append(cols).hide();
            $(currentrow).after(newRow);
            newRow.attr('style', "background-color:#FAEFCA; vertical-align: middle;");
            newRow.attr('id', "tr_HS" + order);

            // POs
            for (i = 0; i < _data.length; i++) {
                let prevrow = newRow;
                let porder = _data[i];

                let info_icon = "";
                switch (porder.info) {
                    case 0:
                        info_icon = "";
                        break;
                    case 1:
                        info_icon = "<image style='height: 1.2rem;' src='/images/green_blink.gif'>";
                        break;
                    case 2:
                        info_icon = "<image style='height: 1.2rem;' src='/images/warning.png'>";
                        break;
                    case 3:
                        info_icon = "<image style='height: 1.2rem;' src='/images/critical.png'>";
                        break;
                    case 4:
                        info_icon = "<image style='height: 1.2rem;' src='/images/yellow_blink.png'>";
                        break;
                }
                let owner_icon = "";
                switch (porder.owner) {
                    case 0:
                        owner_icon = "";
                        break;
                    case 1:
                        owner_icon = "<image style='height: 1.2rem;' src='/images/blueArrow.png'>";
                        break;
                    case 2:
                        owner_icon = "<image style='height: 1.2rem;' src='/images/yellowArrow.png'>";
                        break;
                    case 3:
                        owner_icon = "<image style='height: 1.2rem;' src='/images/purpleArrow.png'>";
                        break;
                }

                let changed_icon = "";
                if (porder.changed != 0)
                    changed_icon = "<image style='height: 1.3rem;' src='/images/icons8-circled-thin-50.png'/>";

                let accepted_icon = "";
                if (porder.accepted == 1)
                    accepted_icon = "<image style='height: 1.3rem;' src='/images/icons8-checkmark-50-1.png'/>";
                else
                    if (porder.accepted == 2)
                        accepted_icon = "<image style='height: 1.3rem;' src='/images/icons8-checkmark-50-1.png'/>";

                let rejected_icon = "";
                if (porder.rejected == 1)
                    rejected_icon = "<image style='height: 1.3rem;' src='/images/icons8-delete-50-2.png'/>";
                else
                    if (porder.rejected == 2)
                        rejected_icon = "<image style='height: 1.3rem;' src='/images/icons8-delete-50-2.png'/>";

                let inquired_icon = "";
                if (porder.inquired == 1)
                    inquired_icon = "<image style='height: 1.3rem;' src='/images/icons8-qmark-50-green.png'/>";
                else
                    if (porder.inquired == 2)
                        inquired_icon = "<image style='height: 1.3rem;' src='/images/icons8-qmark-50-red.png'/>";
                    else
                        if (porder.inquired == 3)
                            inquired_icon = "<image style='height: 1.3rem;' src='/images/icons8-qmark-50-blue.png'/>";

                let button_accept = "";
                if (porder.accept == 1)
                    button_accept = "<button type='button' class='order-button-accepted' style='width: 1.5rem; height: 1.5rem; text-align: center;' " +
                                     "onclick='acceptOrder(this);return false;'/>";
                let button_reject = "";
                if (porder.reject == 1)
                    button_reject = "<button type='button' class='order-button-rejected' style='width: 1.6rem; height: 1.5rem; text-align: center;' " +
                                    "onclick='rejectOrder(this);return false;'/>";
                let button_inquire = "";
                if (porder.inquire == 1)
                    button_inquire = "<button type='button' class='order-button-request' style='width: 1.5rem; height: 1.5rem; text-align: center;' " +
                                     "onclick='inquireOrder(this);return false;'/>";

                var newRow = $("<tr>");
                var cols = "";
                cols += '<td colspan="1" align="center" style="vertical-align: middle;"><input id="input_chk" onclick="boxCheck(this);" type="checkbox" name="P' + order + "_" + porder.ebeln + '" value="P' + order + "_" + porder.ebeln + '"></td>';
                var so_style = "background-color:" + $(currentrow).css("background-color") + ";";
                cols += '<td class="first_color td01" style="' + so_style + '" colspan="1">' + info_icon + '</td>';
                cols += '<td class="first_color td01" style="' + so_style + '" colspan="1">' + owner_icon + '</td>';
                cols += '<td class="first_color td01" style="' + so_style + '" colspan="1">' + changed_icon + '</td>';
                cols += '<td class="first_color td01" style="' + so_style + '" colspan="1">' + accepted_icon + '</td>';
                cols += '<td class="first_color td01" style="' + so_style + '" colspan="1">' + rejected_icon + '</td>';
                cols += '<td class="first_color td01" style="' + so_style + '" colspan="1">' + inquired_icon + '</td>';
                cols += '<td class="first_color td01" style="' + so_style + '; padding: 0;" colspan="1">' + button_accept + '</td>';
                cols += '<td class="first_color td01" style="' + so_style + '; padding: 0;" colspan="1">' + button_reject + '</td>';
                cols += '<td class="first_color td01" style="' + so_style + '; padding: 0;" colspan="1">' + button_inquire + '</td>';
                cols += '<td class="first_color td01" style="' + so_style + '" colspan="1"></td>';
                cols += "<td colspan='3'><button type='button' style='width: 1.6rem; text-align: center;' onclick=\"getSubTree(this);return false;\">+</button> " + conv_exit_alpha_output(porder.ebeln) + "</td>";
                cols += '<td class="td02" colspan="2">' + conv_exit_alpha_output(porder.lifnr) + '</td>';
                cols += '<td class="td02" colspan="5">' + porder.lifnr_name + '</td>';
                cols += '<td class="td02" colspan="1">' + porder.ekgrp + '</td>';
                cols += '<td class="td02" colspan="5">' + porder.ekgrp_name + '</td>';
                cols += '<td class="td02" colspan="3">' + porder.erdat + '</td>';
                cols += '<td class="td02" colspan="2">' + porder.curr + '</td>';
                cols += '<td class="td02" colspan="3">' + porder.fxrate + '</td>';
                cols += '<td class="td02" colspan="5"></td>';
                newRow.append(cols).hide();
                $(prevrow).after(newRow);
                if (i%2 == 0)
                    newRow.attr('style', "background-color:LightYellow; vertical-align: middle;");
                else
                    newRow.attr('style', "background-color:Wheat; vertical-align: middle;");
                newRow.attr('id', "tr_P" + porder.ebeln);
            }
        }

        function getPOSubTree(currentrow, sorder, order, _data)
        {
            // PO item header
            var newRow = $("<tr>");
            var cols = "";
            var po_style = "background-color:" + $(currentrow).css("background-color") + ";";
            var first_color = $(currentrow).find(".first_color").css("background-color");
            var first_style = "background-color:" + first_color;
            @if ($groupByPO == 0)
                cols += '<td class="first_color" colspan="11" style="' + first_style + '"></td>';
            colsafter = 4;
            @else
                cols += '<td class="first_color" colspan="10" style="' + po_style + '"></td>';
                colsafter = 5;
            @endif
            cols += '<td style="' + po_style + '"></td>';
            cols += '<td class="td02" colspan="2"><b>{{__("Pozitie")}}</b></td>';
            cols += '<td class="td02" colspan="3"><b>{{__("Material")}}</b></td>';
            cols += '<td class="td02" colspan="5"><b>{{__("Descriere material")}}</b></td>';
            cols += '<td class="td02" colspan="3" style="text-align: right;"><b>{{__("Cantitate")}}</b></td>';
            cols += '<td class="td02" colspan="3" style="padding-left: 0.5rem;"><b>{{__("Data livrare")}}</b></td>';
            cols += '<td class="td02" colspan="4" style="text-align: right;"><b>{{__("Pret achizitie")}}</b></td>';
            @if (\Illuminate\Support\Facades\Auth::user()->role != "Furnizor")
                let sales_price_hdr = '{{__("Pret vanzare")}}';
                if (sorder == '{{\App\Materom\Orders::stockorder}}') sales_price_hdr = '';
                cols += '<td class="td02" colspan="4" style="text-align: right;"><b>' + sales_price_hdr + '</b></td>';
            @else
                cols += '<td class="td02" colspan="4"><b>&nbsp;</b></td>';
            @endif
            cols += '<td class="td02" colspan="' + colsafter + '"></td>';
            newRow.append(cols).hide();
            $(currentrow).after(newRow);
            newRow.attr('style', "background-color:YellowGreen; vertical-align: middle;");
            newRow.attr('id', "tr_HP" + order);

            // PO Items
            for (i = 0; i < _data.length; i++) {
                let prevrow = newRow;
                let pitem = _data[i];

                let info_icon = "";
                switch (pitem.info) {
                    case 0:
                        info_icon = "";
                        break;
                    case 1:
                        info_icon = "<image style='height: 1.2rem;' src='/images/green_blink.gif'>";
                        break;
                    case 2:
                        info_icon = "<image style='height: 1.2rem;' src='/images/warning.png'>";
                        break;
                    case 3:
                        info_icon = "<image style='height: 1.2rem;' src='/images/critical.png'>";
                        break;
                    case 4:
                        info_icon = "<image style='height: 1.2rem;' src='/images/yellow_blink.png'>";
                        break;
                }
                let owner_icon = "";
                switch (pitem.owner) {
                    case 0:
                        owner_icon = "";
                        break;
                    case 1:
                        owner_icon = "<image style='height: 1.2rem;' src='/images/blueArrow.png'>";
                        break;
                    case 2:
                        owner_icon = "<image style='height: 1.2rem;' src='/images/yellowArrow.png'>";
                        break;
                    case 3:
                        owner_icon = "<image style='height: 1.2rem;' src='/images/purpleArrow.png'>";
                        break;
                }

                let changed_icon = "";
                if (pitem.changed != 0)
                    changed_icon = "<image style='height: 1.3rem;' src='/images/icons8-circled-thin-50.png'/>";

                let accepted_icon = "";
                if (pitem.accepted == 1)
                    accepted_icon = "<image style='height: 1.3rem;' src='/images/icons8-checkmark-50-1.png'/>";
                else if (pitem.accepted == 2)
                    accepted_icon = "<image style='height: 1.3rem;' src='/images/icons8-checkmark-50-1.png'/>";

                let rejected_icon = "";
                if (pitem.rejected == 1)
                    rejected_icon = "<image style='height: 1.3rem;' src='/images/icons8-delete-50-2.png'/>";
                else if (pitem.rejected == 2)
                    rejected_icon = "<image style='height: 1.3rem;' src='/images/icons8-delete-50-2.png'/>";

                let inquired_icon = "";
                if (pitem.inquired == 1)
                    inquired_icon = "<image style='height: 1.3rem;' src='/images/icons8-qmark-50-green.png'/>";
                else if (pitem.inquired == 2)
                    inquired_icon = "<image style='height: 1.3rem;' src='/images/icons8-qmark-50-red.png'/>";
                else if (pitem.inquired == 3)
                    inquired_icon = "<image style='height: 1.3rem;' src='/images/icons8-qmark-50-blue.png'/>";

                let button_accept = "";
                if (pitem.accept == 1)
                    button_accept = "<button type='button' class='order-button-accepted' style='width: 1.5rem; height: 1.5rem; text-align: center;' " +
                        "onclick='acceptItem(this);return false;'/>";
                let button_reject = "";
                if (pitem.reject == 1)
                    button_reject = "<button type='button' class='order-button-rejected' style='width: 1.6rem; height: 1.5rem; text-align: center;' " +
                        "onclick='rejectItem(this);return false;'/>";
                let button_inquire = "";
                if (pitem.inquire == 1)
                    button_inquire = "<button type='button' class='order-button-request' style='width: 1.5rem; height: 1.5rem; text-align: center;' " +
                        "onclick='inquireItem(this);return false;'/>";

                var newRow = $("<tr>");
                var cols = "";
                cols += '<td colspan="1" align="center" style="vertical-align: middle;"><input id="input_chk" onclick="boxCheck(this);" type="checkbox" name="I' + pitem.ebeln + "_" + pitem.ebelp + '" value="I' + pitem.ebeln + "_" + pitem.ebelp + '"></td>';
                var po_style = "background-color:" + $(currentrow).css("background-color") + ";";
                var first_color = $(currentrow).find(".first_color").css("background-color");
                var first_style = "background-color:" + first_color;

                @if ($groupByPO == 0)
                    cols += '<td class="first_color td01" colspan="1" style="' + first_style + '">' + info_icon + '</td>';
                    cols += '<td class="first_color td01" colspan="1" style="' + first_style + '">' + owner_icon + '</td>';
                    cols += '<td class="first_color td01" colspan="1" style="' + first_style + '">' + changed_icon + '</td>';
                    cols += '<td class="first_color td01" colspan="1" style="' + first_style + '">' + accepted_icon + '</td>';
                    cols += '<td class="first_color td01" colspan="1" style="' + first_style + '">' + rejected_icon + '</td>';
                    cols += '<td class="first_color td01" colspan="1" style="' + first_style + '">' + inquired_icon + '</td>';
                    cols += '<td class="first_color td01" colspan="1" style="' + first_style + '; padding: 0;">' + button_accept + '</td>';
                    cols += '<td class="first_color td01" colspan="1" style="' + first_style + '; padding: 0;">' + button_reject + '</td>';
                    cols += '<td class="first_color td01" colspan="1" style="' + first_style + '; padding: 0;">' + button_inquire + '</td>';
                    cols += '<td class="first_color td01" colspan="1" style="' + first_style + '"></td>';
                @else
                    cols += '<td class="first_color td01" colspan="1" style="' + po_style + '">' + info_icon + '</td>';
                    cols += '<td class="first_color td01" colspan="1" style="' + first_style + '">' + owner_icon + '</td>';
                    cols += '<td class="first_color td01" colspan="1" style="' + po_style + '">' + changed_icon + '</td>';
                    cols += '<td class="first_color td01" colspan="1" style="' + po_style + '">' + accepted_icon + '</td>';
                    cols += '<td class="first_color td01" colspan="1" style="' + po_style + '">' + rejected_icon + '</td>';
                    cols += '<td class="first_color td01" colspan="1" style="' + po_style + '">' + inquired_icon + '</td>';
                    cols += '<td class="first_color td01" colspan="1" style="' + po_style + '; padding: 0;">' + button_accept + '</td>';
                    cols += '<td class="first_color td01" colspan="1" style="' + po_style + '; padding: 0;">' + button_reject + '</td>';
                    cols += '<td class="first_color td01" colspan="1" style="' + po_style + '; padding: 0;">' + button_inquire + '</td>';
                @endif
                    cols += '<td class="coloured" style="' + po_style + '"></td>';
                cols += "<td colspan='2'><button type='button' style='width: 1.6rem; text-align: center;' onclick=\"getSubTree(this);return false;\">+</button> " + conv_exit_alpha_output(pitem.ebelp) + "</td>";

                if (pitem.matnr_changeable == 1) {
                    let matnr_class = "td02h";
                    if (pitem.matnr_changed == 1) matnr_class += "_c";
                    cols += '<td class="' + matnr_class + '" colspan="3" onclick="change_matnr(this, \'' + pitem.ebeln + '\', \'' + pitem.ebelp + '\');return false;">' + pitem.idnlf + '</td>';
                    cols += '<td class="' + matnr_class + '" colspan="5" onclick="change_matnr(this.previousSibling, \'' + pitem.ebeln + '\', \'' + pitem.ebelp + '\');return false;">' + pitem.mtext + '</td>';
                } else {
                    let matnr_class = "td02";
                    if (pitem.matnr_changed == 1) matnr_class += "_c";
                    cols += '<td class="' + matnr_class + '" colspan="3">' + pitem.idnlf + '</td>';
                    cols += '<td class="' + matnr_class + '" colspan="5">' + pitem.mtext + '</td>';
                }

                if (pitem.quantity_changeable == 1) {
                    let quantity_class = "td02h";
                    if (pitem.quantity_changed == 1) quantity_class += "_c";
                    cols += '<td class="' + quantity_class + '" colspan="3" onclick="change_quantity(this, \'' + pitem.ebeln + '\', \'' + pitem.ebelp + '\');" style="text-align: right;">' + pitem.x_quantity + '</td>';
                } else {
                    let quantity_class = "td02";
                    if (pitem.quantity_changed == 1) quantity_class += "_c";
                    cols += '<td class="' + quantity_class + '" colspan="3" style="text-align: right;">' + pitem.x_quantity + '</td>';
                }

                if (pitem.delivery_date_changeable == 1) {
                    let delivery_date_class = "td02h";
                    if (pitem.delivery_date_changed == 1) delivery_date_class += "_c";
                    cols += '<td class="' + delivery_date_class + '" colspan="3" onclick="change_delivery_date(this, \'' + pitem.ebeln + '\', \'' + pitem.ebelp + '\');" style="padding-left: 0.5rem;">' + pitem.x_delivery_date + '</td>';
                } else {
                    let delivery_date_class = "td02";
                    if (pitem.delivery_date_changed == 1) delivery_date_class += "_c";
                    cols += '<td class="' + delivery_date_class + '" colspan="3" style="padding-left: 0.5rem;">' + pitem.x_delivery_date + '</td>';
                }

                if (pitem.price_changeable == 1) {
                    let price_class = "td02h";
                    if (pitem.price_changed == 1) price_class += "_c";
                    cols += '<td class="' + price_class + '" colspan="4" onclick="change_purchase_price(this, \'' + pitem.ebeln + '\', \'' + pitem.ebelp + '\');" style="text-align: right;">' + pitem.x_purchase_price + '</td>';
                } else {
                    let price_class = "td02";
                    if (pitem.price_changed == 1) price_class += "_c";
                    cols += '<td class="' + price_class + '" colspan="4" style="text-align: right;">' + pitem.x_purchase_price + '</td>';
                }

                cols += '<td class="td02" colspan="4" style="text-align: right;">' + pitem.x_sales_price + '</td>';
                @if ($groupByPO == 1)
                    cols += '<td colspan="5"></td>';
                @else
                    cols += '<td colspan="4"></td>';
                @endif
                newRow.append(cols).hide();
                $(prevrow).after(newRow);
                if (i % 2 == 0)
                    newRow.attr('style', "background-color:#A0C0A0; vertical-align: middle;");
                else
                    newRow.attr('style', "background-color:#90D090; vertical-align: middle;");
                newRow.attr('id', "tr_I" + pitem.ebeln + "_" + pitem.ebelp);
            }
        }

        function getPOItemSubTree(currentrow, order, item, _data)
        {
            // PO item changes header
            var newRow = $("<tr>");
            var cols = "";
            var po_style = "background-color:" + $(currentrow).css("background-color") + ";";
            var color = $(currentrow).closest("tr").find(".coloured").css("background-color");
            var last_style = "background-color:" + color;
            var first_color = $(currentrow).closest("tr").find(".first_color").css("background-color");
            var first_style = "background-color:" + first_color;
            cols += '<td class="first_color" colspan="10" style="' + first_style + '"></td>';
            @if ($groupByPO == 0)
                let colsafter = "8";
                cols += '<td class="first_color" colspan="1" style="' + first_style + '"></td>';
            @else
                let colsafter = "9";
            @endif
            cols += '<td class="coloured" style="' + last_style + '"></td>';
            cols += '<td style="' + po_style + '"></td>';
            cols += '<td class="td02" colspan="3"><b>{{__("Data")}}</b></td>';
            cols += '<td class="td02" colspan="6"><b>{{__("Utilizator")}}</b></td>';
            cols += '<td class="td02" colspan="8"><b>{{__("Ce s-a schimbat")}}</b></td>';
            cols += '<td class="td02" colspan="2"><b>{{__("Motiv")}}</b></td>';
            cols += '<td colspan=' + colsafter + '><b></b></td>';
            newRow.append(cols).hide();
            $(currentrow).after(newRow);
            newRow.attr('style', "background-color:#ADD8E6; vertical-align: middle;");
            newRow.attr('id', "tr_HI" + order + "_" + item);

            // PO Item changes
            for (i = 0; i < _data.length; i++) {
                let prevrow = newRow;
                let pitemchg = _data[i];

                newRow = $("<tr>");
                let cols = "";
                let pi_style = "background-color:" + $(currentrow).css("background-color") + ";";
                let color = $(currentrow).closest("tr").find(".coloured").css("background-color");
                let last_style = "background-color:" + color;
                let first_color = $(currentrow).closest("tr").find(".first_color").css("background-color");
                let first_style = "background-color:" + first_color;
                cols += '<td class="first_color" colspan="10" style="' + first_style + '"></td>';
                @if ($groupByPO == 0)
                    let colsreason = "10";
                    cols += '<td class="first_color" colspan="1" style="' + first_style + '"></td>';
                @else
                  let colsreason = "11";
                @endif
                cols += '<td class="coloured" style="' + last_style + '"></td>';
                cols += '<td style="' + pi_style + '"></td>';
                cols += '<td class="td02" colspan="3">' + pitemchg.cdate + '</td>';
                cols += '<td class="td02" colspan="2">' + pitemchg.cuser + '</td>';
                cols += '<td class="td02" colspan="4">' + pitemchg.cuser_name + '</td>';
                cols += '<td class="td02" colspan="8">' + pitemchg.text + '</td>';
                @if ($groupByPO == 1)
                    colsreason = colsreason + 1;
                @endif
                cols += '<td class="td02" colspan="' + colsreason + '">' + pitemchg.reason + '</td>';
                newRow.append(cols).hide();
                $(prevrow).after(newRow);
                if (i % 2 == 0)
                    newRow.attr('style', "background-color:Azure; vertical-align: middle;");
                else
                    newRow.attr('style', "background-color:LightCyan; vertical-align: middle;");
                newRow.attr('id', "tr_C" + pitemchg.ebeln + "_" + pitemchg.ebelp + "_" + i);
            }
        }

        function hideSubTree(thisbtn)
        {
            var currentrow, nextrow;
            let rowid = (currentrow = $(thisbtn).parent().parent()).attr('id').substr(0, 4);
            while (((nextrow = $(currentrow).next()) != null) &&
                   (nextrow !== undefined) &&
                   (nextrow.length > 0)) {
                let crtid = nextrow.attr('id').substr(0, 4);
                if (crtid == rowid) break;
                if (rowid == "tr_P")
                    if (crtid == "tr_S") break;
                if (rowid == "tr_I")
                    if ((crtid == "tr_S") || (crtid == "tr_P")) break;
                if (rowid == "tr_C")
                    if ((crtid == "tr_S") || (crtid == "tr_P") || (crtid == "tr_I")) break;
                nextrow.remove();
            }
            thisbtn.innerHTML = '+';
            thisbtn.onclick = function () {
                getSubTree(this);
                return false;
            }
        }

        function loadSub(item, type, _btn, ebelp) {

            let f_history = {{$filter_history}};

            var _data, _status;
            var _this;
            if (type == "sales-order") _this = document.getElementById("tr_S" + item);
            if (type == "purch-order") _this = document.getElementById("tr_P" + item);
            if (type == "purch-item") _this = document.getElementById("tr_I" + item + "_" + ebelp);

            let time_limit = $("#time_search").val();
            let filter_vbeln = $("#filter_vbeln").val();
            let filter_ebeln = $("#filter_ebeln").val();
            let filter_matnr = $("#filter_matnr").val();
            let filter_mtext = $("#filter_mtext").val();
            let filter_lifnr = $("#filter_lifnr").val();
            let filter_lifnr_name = $("#filter_lifnr_name").val();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajaxSetup({async: false});

            $.post("webservice/getOrderInfo",
                {
                    order: item,
                    type: type,
                    item: ebelp,
                    history: f_history,
                    time_limit: time_limit,
                    filter_vbeln: filter_vbeln,
                    filter_ebeln: filter_ebeln,
                    filter_matnr: filter_matnr,
                    filter_mtext: filter_mtext,
                    filter_lifnr: filter_lifnr,
                    filter_lifnr_name: filter_lifnr_name
                },
                function (data, status) {
                    _data = data;
                    _status = status;
                });
            jQuery.ajaxSetup({async: true});
            if (_status == "success") {
                if (_data.length > 0) {
                    var split = _data.split('=');
                    var line_counter = 1;

                    let filter_status = {{$filter_status_type}};

                    split.forEach(function (_ord) {
                        line_counter = line_counter + 1;
                        if (line_counter == 2) line_counter = 0;
                        if (type == 'sales-order') {
                            var id = _ord.split('#')[0];
                            var lifnr = _ord.split('#')[1];
                            var lifnr_name = _ord.split('#')[2];
                            var ekgrp = _ord.split('#')[3];
                            var vbeln = _ord.split('#')[4];
                            var ekgrp_name = _ord.split('#')[5];
                            var erdat = _ord.split('#')[6];
                            var curr = _ord.split('#')[7];
                            var fxrate = _ord.split('#')[8];
                            var gravity = _ord.split('#')[9];
                            var owner = _ord.split('#')[10];
                            var stage = _ord.split('#')[11];
                            var image_info = "";

                            if (filter_vbeln != null && filter_vbeln.length > 0) {
                                if (filter_vbeln.indexOf("*") != -1) {
                                    if (vbeln.indexOf(filter_vbeln.replace(/\*/g, "")) == -1)
                                        return;
                                } else {
                                    if (vbeln != filter_vbeln)
                                        return;
                                }
                            };
                            if (filter_ebeln != null && filter_ebeln.length > 0) {
                                if (filter_ebeln.indexOf("*") != -1) {
                                    if (ebeln.indexOf(filter_ebeln.replace(/\*/g, "")) == -1)
                                        return;
                                } else {
                                    if (ebeln != filter_ebeln)
                                        return;
                                }
                            };
                            if (filter_lifnr != null && filter_lifnr.length > 0) {
                                if (filter_lifnr.indexOf("*") != -1) {
                                    if (lifnr.indexOf(filter_lifnr.replace(/\*/g, "")) == -1)
                                        return;
                                } else {
                                    if (lifnr != filter_lifnr)
                                        return;
                                }
                            };
                            if (filter_lifnr_name != null && filter_lifnr_name.length > 0) {
                                if (filter_lifnr_name.indexOf("*") != -1) {
                                    if (lifnr_name.indexOf(filter_lifnr_name.replace(/\*/g, "")) == -1)
                                        return;
                                } else {
                                    if (lifnr_name != filter_lifnr_name)
                                        return;
                                }
                            };


                            if (filter_status == "1" && stage != 0 && stage < 10)
                                return;

                            if ((filter_status == "2" || filter_status == "3") && hasNoChildrenWithStatus(id, filter_status, 1))
                                return;

                            if (gravity == 1)
                                image_info = "<image style='height: 1.2rem;' src='/images/warning.png'>";
                            if (gravity == 2)
                                image_info = "<image style='height: 1.2rem;' src='/images/critical.png'>";
                            var image_owner = "";
                            if (owner == 1)
                                image_owner = "<image style='height: 1.2rem;' src='/images/yellowArrow.png'>";
                            if (owner == 2)
                                image_owner = "<image style='height: 1.2rem;' src='/images/blueArrow.png'>";
                            if (owner == 3)
                                image_owner = "<image style='height: 1.2rem;' src='/images/purpleArrow.png'>";

                            var blue_circle = "";
                            if (stage >= 10)
                                blue_circle = "<image style='height: 1.3rem;' src='/images/icons8-circled-thin-50.png'/>";

                            var newRow = $("<tr>");
                            var cols = "";
                            cols += '<td colspan="1" align="center" style="vertical-align: middle;"><input id="input_chk" onclick="boxCheck(this);" type="checkbox" name="P' + vbeln + "_" + id + '" value="P' + vbeln + "_" + id + '"></td>';
                            var so_style = "background-color:" + $(_this).css("background-color") + ";";
                            let buttonok = owner < 2 ? "" : "<button type='button' class='order-button-accepted' style='width: 1.5rem; height: 1.5rem;'/>";
                            let buttoncancel = owner < 2 ? "" : "<button type='button' class='order-button-rejected' style='width: 1.6rem; height: 1.5rem;'/>";
                            let buttonrequest = owner < 2 ? "" : "<button type='button' class='order-button-request' style='width: 1.5rem; height: 1.5rem;'/>";

                            if(f_history == 2)
                                buttonok = buttoncancel = buttonrequest = "";

                            if(stage%10 > 0)
                                buttonok = buttoncancel = "";

                            cols += '<td class="first_color td01" style="' + so_style + '" colspan="1">' + image_info + '</td>';
                            cols += '<td class="first_color td01" style="' + so_style + '" colspan="1">' + image_owner + '</td>';
                            cols += '<td class="first_color td01" style="' + so_style + '" colspan="1">' + blue_circle + '</td>';
                            cols += '<td class="first_color td01" style="' + so_style + '" colspan="1"></td>';
                            cols += '<td class="first_color td01" style="' + so_style + '" colspan="1"></td>';
                            cols += '<td class="first_color td01" style="' + so_style + '" colspan="1">6</td>';
                            cols += '<td class="first_color td01" style="' + so_style + '; padding: 0;" colspan="1">' + buttonok + '</td>';
                            cols += '<td class="first_color td01" style="' + so_style + '; padding: 0;" colspan="1">' + buttoncancel + '</td>';
                            cols += '<td class="first_color td01" style="' + so_style + '; padding: 0;" colspan="1">' + buttonrequest + '</td>';
                            cols += '<td class="first_color td01" style="' + so_style + '" colspan="1"></td>';
                            cols += "<td colspan='3'><button type='button' style='width: 1.6rem; text-align: center;' id='btn_P" + id + "_" + vbeln + "' onclick=\"loadSub(\'" + id + "_" + vbeln + "',\'purch-order\',this, \'" + vbeln + "\');\">+</button> " + id.substr(0, 10) + "</td>";
                            cols += '<td class="td02" colspan="2">' + conv_exit_alpha_output(lifnr) + '</td>';
                            cols += '<td class="td02" colspan="5">' + lifnr_name + '</td>';
                            cols += '<td class="td02" colspan="1">' + ekgrp + '</td>';
                            cols += '<td class="td02" colspan="5">' + ekgrp_name + '</td>';
                            cols += '<td class="td02" colspan="3">' + erdat + '</td>';
                            cols += '<td class="td02" colspan="2">' + curr + '</td>';
                            cols += '<td class="td02" colspan="3">' + fxrate + '</td>';
                            cols += '<td class="td02" colspan="5"></td>';
                            newRow.append(cols).hide();
                            newRow.insertAfter($(_this).closest("tr")).fadeIn(250);
                            if (line_counter == 0)
                                newRow.attr('style', "background-color:LightYellow; vertical-align: middle;");
                            else
                                newRow.attr('style', "background-color:Wheat; vertical-align: middle;");
                            newRow.attr('id', "tr_P" + id + "_" + vbeln);
                        } else if (type == 'purch-order') {
                            var ebeln2 = _ord.split('#')[0];
                            var id = _ord.split('#')[1];
                            var posnr = _ord.split('#')[2];
                            var idnlf = _ord.split('#')[3];
                            var mtext = _ord.split('#')[4];
                            var owner2 = _ord.split('#')[5];
                            var stage = _ord.split('#')[6];
                            var quantity = _ord.split('#')[7];
                            var deldate = _ord.split('#')[8];
                            var pur_price = _ord.split('#')[9];
                            var sal_price = _ord.split('#')[10];
                            var newRow = $("<tr>");
                            var cols = "";
                            cols += '<td colspan="1" align="center" style="vertical-align: middle;"><input id="input_chk" onclick="boxCheck(this);" type="checkbox" name="I' + ebeln2 + "_" + id + '" value="I' + ebeln2 + "_" + id + '"></td>';
                            var po_style = "background-color:" + $(_this).css("background-color") + ";";
                            var first_color = $(_this).find(".first_color").css("background-color");
                            var first_style = "background-color:" + first_color;
                            let buttonok = owner2 < 2 ? "" : "<button type='button' onclick='accept(\"" + ebeln2 + "\", \"" + id + "\", \"item\");' class='order-button-accepted' style='width: 1.5rem; height: 1.5rem;'/>";
                            let buttoncancel = owner2 < 2 ? "" : "<button type='button' onclick='reject_init(\"" + ebeln2 + "\",  \"" + id + "\");' class='order-button-rejected' style='width: 1.6rem; height: 1.5rem;'/>";
                            let buttonrequest = owner2 < 2 ? "" : "<button type='button' class='order-button-request' style='width: 1.5rem; height: 1.5rem;'/>";

                            if(f_history == 2)
                                buttonok = buttoncancel = buttonrequest = "";

                            if(stage%10 > 0)
                                buttonok = buttoncancel = "";

                            var image_owner = "";
                            if (owner2 == 1)
                                image_owner = "<image style='height: 1.2rem;' src='/images/yellowArrow.png'>";
                            if (owner2 == 2)
                                image_owner = "<image style='height: 1.2rem;' src='/images/blueArrow.png'>";
                            if (owner2 == 3)
                                image_owner = "<image style='height: 1.2rem;' src='/images/purpleArrow.png'>";

                            var blue_circle = "";
                            var green_tick = "";
                            var red_cross = "";

                            if (stage >= 10)
                                blue_circle = "<image style='height: 1.3rem;' src='/images/icons8-circled-thin-50.png'/>";

                            if ((stage == 1) || (stage == 11))
                                green_tick = "<image style='height: 1.3rem;' src='/images/icons8-checkmark-50-1.png'/>";

                            if ((stage == 2) || (stage == 12))
                                red_cross = "<image style='height: 1.3rem;' src='/images/icons8-delete-50-2.png'/>";

                            let filter_status = {{$filter_status_type}};

                            if (filter_status != "0") {
                                if (filter_status == "2" && stage != 1 && stage != 11)
                                    return;
                                if (filter_status == "3" && stage != 2 && stage != 12)
                                    return;
                            }

                            @if ($groupByPO == 0)
                                cols += '<td class="first_color td01" colspan="1" style="' + first_style + '"></td>';
                                cols += '<td class="first_color td01" colspan="1" style="' + first_style + '">' + image_owner + '</td>';
                                cols += '<td class="first_color td01" colspan="1" style="' + first_style + '">' + blue_circle + '</td>';
                                cols += '<td class="first_color td01" colspan="1" style="' + first_style + '">' + green_tick + '</td>';
                                cols += '<td class="first_color td01" colspan="1" style="' + first_style + '">' + red_cross + '</td>';
                                cols += '<td class="first_color td01" colspan="1" style="' + first_style + '">6</td>';
                                cols += '<td class="first_color td01" colspan="1" style="' + first_style + '; padding: 0;">' + buttonok + '</td>';
                                cols += '<td class="first_color td01" colspan="1" style="' + first_style + '; padding: 0;">' + buttoncancel + '</td>';
                                cols += '<td class="first_color td01" colspan="1" style="' + first_style + '; padding: 0;">' + buttonrequest + '</td>';
                                cols += '<td class="first_color td01" colspan="1" style="' + first_style + '"></td>';
                            @else
                                cols += '<td class="first_color td01" colspan="1" style="' + po_style + '"></td>';
                                cols += '<td class="first_color td01" colspan="1" style="' + first_style + '">' + image_owner + '</td>';
                                cols += '<td class="first_color td01" colspan="1" style="' + po_style + '">' + blue_circle + '</td>';
                                cols += '<td class="first_color td01" colspan="1" style="' + po_style + '">' + green_tick + '</td>';
                                cols += '<td class="first_color td01" colspan="1" style="' + po_style + '">' + red_cross + '</td>';
                                cols += '<td class="first_color td01" colspan="1" style="' + po_style + '">6</td>';
                                cols += '<td class="first_color td01" colspan="1" style="' + po_style + '; padding: 0;">' + buttonok + '</td>';
                                cols += '<td class="first_color td01" colspan="1" style="' + po_style + '; padding: 0;">' + buttoncancel + '</td>';
                                cols += '<td class="first_color td01" colspan="1" style="' + po_style + '; padding: 0;">' + buttonrequest + '</td>';
                            @endif
                            cols += '<td class="coloured" style="' + po_style + '"></td>';
                            cols += "<td colspan='2'><button type='button' style='width: 1.6rem; text-align: center;' id='btn_I" + ebeln2 + "_" + id + "' onclick=\"loadSub(\'" + ebeln2 + "',\'purch-item\',this, \'" + id + "');\">+</button> " + id + "</td>";
                            cols += '<td class="td02h" colspan="2" onclick="change_matnr(this, \'' + ebeln2 + '\', \'' + id + '\');">' + idnlf + '</td>';
                            cols += '<td class="td02h" colspan="5" onclick="change_matnr(this.previousSibling, \'' + ebeln2 + '\', \'' + id + '\');">' + mtext + '</td>';
                            cols += '<td class="td02h" colspan="3" onclick="change_quantity(this, \'' + ebeln2 + '\', \'' + id + '\');">' + quantity + '</td>';
                            cols += '<td class="td02h" colspan="3" onclick="change_delivery_date(this, \'' + ebeln2 + '\', \'' + id + '\');">' + deldate.substr(0, 10) + '</td>';
                            cols += '<td class="td02h" colspan="4" onclick="change_purchase_price(this, \'' + ebeln2 + '\', \'' + id + '\');">' + pur_price + '</td>';
                            cols += '<td class="td02" colspan="4">' + sal_price + '</td>';
                            cols += '<td colspan="5"></td>';
                            @if ($groupByPO == 1)
                                cols += '<td colspan="1"></td>';
                            @endif
                            newRow.append(cols).hide();
                            newRow.insertAfter($(_this).closest("tr")).fadeIn(250);
                            if (line_counter == 0)
                                newRow.attr('style', "background-color:#A0C0A0; vertical-align: middle;");
                            else
                                newRow.attr('style', "background-color:#90D090; vertical-align: middle;");
                            newRow.attr('id', "tr_I" + ebeln2 + "_" + id);
                        } else if (type == 'purch-item') {
                            var ebeln3 = _ord.split('#')[0];
                            var ebelp3 = _ord.split('#')[1];
                            var chdate = _ord.split('#')[2];
                            var cuser = _ord.split('#')[3];
                            var cuser_name = _ord.split('#')[4];
                            var ctext = _ord.split('#')[5];
                            var creason = _ord.split('#')[6];
                            if (chdate != null) {
                                var newRow = $("<tr>");
                                var cols = "";
                                var pi_style = "background-color:" + $(_this).css("background-color") + ";";
                                var color = $(_this).closest("tr").find(".coloured").css("background-color");
                                var last_style = "background-color:" + color;
                                var first_color = $(_this).closest("tr").find(".first_color").css("background-color");
                                var first_style = "background-color:" + first_color;
                                cols += '<td class="first_color" colspan="10" style="' + first_style + '"></td>';
                                @if ($groupByPO == 0)
                                    let colsreason = "10";
                                    cols += '<td class="first_color" colspan="1" style="' + first_style + '"></td>';
                                @else
                                    let colsreason = "11";
                                @endif
                                cols += '<td class="coloured" style="' + last_style + '"></td>';
                                cols += '<td style="' + pi_style + '"></td>';
                                cols += '<td class="td02" colspan="3">' + chdate + '</td>';
                                cols += '<td class="td02" colspan="2">' + cuser + '</td>';
                                cols += '<td class="td02" colspan="4">' + cuser_name + '</td>';
                                cols += '<td class="td02" colspan="8">' + ctext + '</td>';
                                @if ($groupByPO == 1)
                                    colsreason = colsreason + 1;
                                @endif
                                cols += '<td class="td02" colspan="' + colsreason + '">' + creason + '</td>';
                                newRow.append(cols);
                                newRow.insertAfter($(_this).closest("tr"));
                                if (line_counter == 0)
                                    newRow.attr('style', "background-color:Azure; vertical-align: middle;");
                                else
                                    newRow.attr('style', "background-color:LightCyan; vertical-align: middle;");
                                newRow.attr('id', "tr_C" + ebeln3 + "_" + ebelp3 + "_" +
                                    chdate.substr(0, 10) + "_" + chdate.substr(11, 8));
                            }
                        }
                    });
                    if (type == 'sales-order') {
                        var newRow = $("<tr>");
                        var cols = "";
                        var so_style = "background-color:" + $(_this).css("background-color") + ";";
                        cols += '<td class="first_color" style="' + so_style + '" colspan="11"></td>';
                        cols += '<td colspan="3"><b>Comanda aprovizionare</b></td>';
                        cols += '<td colspan="2"><b>Furnizor</b></td>';
                        cols += '<td colspan="5"><b>&nbsp;</b></td>';
                        cols += '<td colspan="2"><b>Referent</b></td>';
                        cols += '<td colspan="4"><b>&nbsp;</b></td>';
                        cols += '<td colspan="3"><b>Data creare</b></td>';
                        cols += '<td colspan="2"><b>Moneda</b></td>';
                        cols += '<td colspan="3"><b>Rata de schimb</b></td>';
                        cols += '<td colspan="5"><b></b></td>';
                        newRow.append(cols).hide();
                        newRow.insertAfter($(_this).closest("tr")).fadeIn(250);
                        newRow.attr('style', "background-color:#FAEFCA; vertical-align: middle;");
                    }
                    if (type == 'purch-order') {
                        var newRow = $("<tr>");
                        var cols = "";
                        var po_style = "background-color:" + $(_this).css("background-color") + ";";
                        var first_color = $(_this).find(".first_color").css("background-color");
                        var first_style = "background-color:" + first_color;
                        @if ($groupByPO == 0)
                            cols += '<td class="first_color" colspan="11" style="' + first_style + '"></td>';
                            colsafter = 5;
                        @else
                            cols += '<td class="first_color" colspan="10" style="' + po_style + '"></td>';
                            colsafter = 6;
                        @endif
                        cols += '<td style="' + po_style + '"></td>';
                        cols += '<td class="td02" colspan="2"><b>Pozitie</b></td>';
                        cols += '<td class="td02" colspan="2"><b>Material</b></td>';
                        cols += '<td class="td02" colspan="5"><b>Descriere material</b></td>';
                        cols += '<td class="td02" colspan="3"><b>Cantitate</b></td>';
                        cols += '<td class="td02" colspan="3"><b>Data livrare</b></td>';
                        cols += '<td class="td02" colspan="4"><b>Pret achizitie</b></td>';
                        let sal_price_colname = "";
                        @php
                            if (\Illuminate\Support\Facades\Auth::user()->role != "Furnizor")
                            echo 'sal_price_colname = "Pret vanzare";';
                        @endphp
                        cols += '<td class="td02" colspan="4"><b>' + sal_price_colname + '</b></td>';
                        cols += '<td class="td02" colspan="' + colsafter + '"></td>';
                        newRow.append(cols).hide();
                        newRow.insertAfter($(_this).closest("tr")).fadeIn(250);
                        newRow.attr('style', "background-color:YellowGreen; vertical-align: middle;");
                    }
                    if (type == 'purch-item') {
                        var newRow = $("<tr>");
                        var cols = "";
                        var po_style = "background-color:" + $(_this).css("background-color") + ";";
                        var color = $(_this).closest("tr").find(".coloured").css("background-color");
                        var last_style = "background-color:" + color;
                        var first_color = $(_this).closest("tr").find(".first_color").css("background-color");
                        var first_style = "background-color:" + first_color;
                        cols += '<td class="first_color" colspan="10" style="' + first_style + '"></td>';
                        @if ($groupByPO == 0)
                            let colsafter = "8";
                            cols += '<td class="first_color" colspan="1" style="' + first_style + '"></td>';
                        @else
                            let colsafter = "9";
                        @endif
                        cols += '<td class="coloured" style="' + last_style + '"></td>';
                        cols += '<td style="' + po_style + '"></td>';
                        cols += '<td class="td02" colspan="3"><b>Data</b></td>';
                        cols += '<td class="td02" colspan="6"><b>Utilizator</b></td>';
                        cols += '<td class="td02" colspan="8"><b>Ce s-a schimbat</b></td>';
                        cols += '<td class="td02" colspan="2"><b>Motiv</b></td>';
                        cols += '<td colspan=' + colsafter + '><b></b></td>';
                        newRow.append(cols).hide();
                        newRow.insertAfter($(_this).closest("tr")).fadeIn(250);
                        newRow.attr('style', "background-color:#ADD8E6; vertical-align: middle;");
                    }
                }
                _btn.innerHTML = '-';
                _btn.onclick = function () {
                    hideSub(item, type, _btn, ebelp);
                    return false;
                };
                refreshCheck();
            } else {
                alert('Error processing operation!');
            }
        }

        function hideSub(item, type, _btn, ebelp) {
            var table = document.getElementById('orders_table');
            var tr_id = "";
            if (type == "sales-order") tr_id = "tr_S" + item;
            if (type == "purch-order") tr_id = "tr_P" + item;
            if (type == "purch-item") tr_id = "tr_I" + item + "_" + ebelp;

            var started = false;
            for (i = 0; i < table.rows.length; i++) {
                if (started) {
                    if (table.rows[i].innerHTML.includes(type) || table.rows[i].innerHTML.includes('sales-order'))
                        break;
                    if (type == "purch-item")
                        if (table.rows[i].innerHTML.includes(type) || table.rows[i].innerHTML.includes('purch-order'))
                            break;
                    table.deleteRow(i);
                    i--;
                }
                if (table.rows[i].id == tr_id) started = true;
            }
            _btn.innerHTML = '+';
            _btn.onclick = function () {
                loadSub(item, type, _btn, ebelp);
                return false;
            };
        }

        function conv_exit_alpha_output(input) {
            output = input;
            if (/^\d+$/.test(output)) {
                output = output.replace(/^0+/, '');
                if (output.length == 0) output = input;
            }
            return output;
        }

        function changeItemStat(c_type, c_value, c_value_hlp, old_value, c_ebeln, c_ebelp) {
            var c_string = "";
            switch (c_type) {
                case 1:
                    c_string = "idnlf";
                    break;
                case 3:
                    c_string = "qty";
                    break;
                case 4:
                    c_string = "lfdat";
                    break;
                case 5:
                    c_string = "purch_price";
                    break;
            }


            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var _dataC, _statusC;
            jQuery.ajaxSetup({async: false});
            $.post("webservice/changeItemStat",
                {
                    column: c_string,
                    value: c_value,
                    valuehlp: c_value_hlp,
                    oldvalue: old_value,
                    ebeln: c_ebeln,
                    ebelp: c_ebelp
                },
                function (data, status) {
                    _dataC = data;
                    _statusC = status;
                });
            jQuery.ajaxSetup({async: true});
            if (_statusC == "success") {
                return true;
            }
            return false;
        }

        var change_cell, change_type, change_value, type_string, change_ebeln, change_ebelp, changeDialog, changeForm;

        $(function () {
            changeDialog = $("#change-dialog").dialog({
                autoOpen: false,
                height: 200,
                width: 400,
                modal: true,
                buttons: {
                    Change: function (){
                        if(changeItemStat(change_type, $("#new_chg_val").val(), $("#new_val_hlp").text(),
                            change_cell.innerHTML,change_ebeln,change_ebelp)) {
                            hideSub(change_ebeln,'purch-item',$("#I"+change_ebeln+"_"+change_ebelp),change_ebelp);
                            change_cell.innerHTML = ($("#new_chg_val").val() + " " + $("#new_val_hlp").text()).trim();
                            $("#new_chg_val").text("");
                            $("#new_val_hlp").text("");
                            changeDialog.dialog("close");
                        }
                    },
                    Cancel: function () {
                        changeDialog.dialog("close");
                    }
                },
                close: function () {
                    changeForm[0].reset();
                },
                position: {
                    my: "right",
                    at: "center",
                    of: change_cell // $("#orders_table")
                }
            });

            changeForm = changeDialog.find("form").on("submit", function (event) {
                event.preventDefault();
            });
            $(changeDialog).keydown(function (event) {
                if (event.keyCode == $.ui.keyCode.ENTER) {
                    $(this).parent()
                        .find("button:eq(0)").trigger("click");
                    return false;
                }
            });
        });

        function change_matnr(cell, ebeln, ebelp) {

            change_cell = cell;
            change_type = 1;
            let old_value = cell.innerHTML;
            change_ebeln = ebeln;
            change_ebelp = ebelp;
            type_string = "IDNLF";


            $("#old_chg_val").text("Codul existent: " + old_value);
            $("#new_val_txt").text("Introduceti noul cod:");
            $("#new_val_hlp").text("");
            $("#change-dialog").dialog('option', 'title', 'Modificare cod material pozitia ' + ebelp);
            changeDialog.dialog("open");
        }

        function change_quantity(cell, ebeln, ebelp) {

            change_cell = cell;
            change_type = 3;
            let old_value = cell.innerHTML;
            let values = old_value.split(" ");
            change_ebeln = ebeln;
            change_ebelp = ebelp;

            type_string = "QTY";
            $("#old_chg_val").text("Cantitatea existenta: " + old_value);
            $("#new_val_txt").text("Introduceti noua cantitate:");
            $("#new_val_hlp").text(values[1]);
            $("#change-dialog").dialog('option', 'title', 'Modificare cantitate pozitia ' + ebelp);
            changeDialog.dialog("open");
        }

        function change_delivery_date(cell, ebeln, ebelp) {

            change_cell = cell;
            change_type = 4;
            let old_value = cell.innerHTML;
            change_ebeln = ebeln;
            change_ebelp = ebelp;

            type_string = "LFDAT";
            $("#old_chg_val").text("Data de livrare existenta: " + old_value);
            $("#new_val_txt").text("Introduceti noua data de livrare:");
            $("#new_val_hlp").text("");
            $("#change-dialog").dialog('option', 'title', 'Modificare data livrare pentru pozitia ' + ebelp);
            changeDialog.dialog("open");
        }

        function change_purchase_price(cell, ebeln, ebelp) {

            change_cell = cell;
            change_type = 5;
            let old_value = cell.innerHTML;
            let values = old_value.split(" ");
            change_ebeln = ebeln;
            change_ebelp = ebelp;

            type_string = "PURCH_PRICE";
            $("#old_chg_val").text("Pretul existent: " + old_value);
            $("#new_val_txt").text("Introduceti noul pret de achizitie:");
            $("#new_val_hlp").text(values[1]);
            $("#change-dialog").dialog('option', 'title', 'Modificare pret achizitie pentru pozitia ' + ebelp);
            changeDialog.dialog("open");
        }

    </script>

    <div id="change-dialog" title="Modificare pozitie" >
        <form>
            <br>
            <div class="form-group container" align="left">
                <b id="old_chg_val"></b><br><br>
                <i id="new_val_txt"></i>
                <br><br>
                <table style="border: none; padding: 0px;" width="80%"><tr>
                <td><input id="new_chg_val" type="text" name="new_chg_val" size="20"
                       class="form-control col-md-8" value=""></td>
                <td style="text-align: left;" width="4rem"><b style="text-align: left;  margin-left: -5rem;" id="new_val_hlp"></b></td>
                </tr></table>
            </div>
        </form>
    </div>

    <div id="init-rejection-dialog" title="Rejectare pozitie" >
        <form>
            <br>
            <div class="form-group container-fluid" align="middle">
                <select id="reject-category" name="reject-category" onchange="rejectCategoryChange(this);return false;">
                    <option value="1" selected>Diverse</option>
                    <option value="2">Altele</option>
                </select>
                <br>
                <label for="reject-reason" class="col-md-4 col-form-label text-md-left">Reason:</label>
                <input id="reject-reason" type="text" name="reject-reason" size="20"
                       class="form-control col-md-12" value="">
            </div>

            <i id="new_rej_msg" style="color: red"></i>
        </form>
    </div>

    <script>
        function rejectCategoryChange(_this){
            if(_this.value == 2)
                $("#reject-reason").attr('required', '');
            else
                $("#reject-reason").attr('required', '');
        }
    </script>

    <script>

        var rejectDialog, rejectForm, _ebelp, _id;
        $(function () {
            rejectDialog = $("#init-rejection-dialog").dialog({
                autoOpen: false,
                height: 200,
                width: 400,
                modal: true,
                buttons: {
                    Add: function (){
                        if(!($("#reject-category").val() == 2 && $("#reject-reason").val().length == 0 ))
                            if(reject(_ebelp,_id,'item',$("#reject-category").val(),$("#reject-reason").val()))
                                rejectDialog.dialog("close");
                    },
                    Cancel: function () {
                        rejectDialog.dialog("close");
                    }
                },
                close: function () {
                    rejectForm[0].reset();
                },
                position: {
                    my: "center",
                    at: "center",
                    of: $("#orders_table")
                }
            });
            $("#reject-category").on('input', function () {
                if ($("#new_rej_msg").text() != "") $("#new_rej_msg").text("");
            });
            rejectForm = rejectDialog.find("form").on("submit", function (event) {
                event.preventDefault();
            });
        });

        function reject_init(ebelp,id) {
            $("#new_rej_msg").text("");
            $("#init-rejection-dialog").dialog('option', 'title', 'Formular de rejectare');
            _ebelp = ebelp;
            _id = id;
            rejectDialog.dialog("open");
        }
    </script>
@endsection