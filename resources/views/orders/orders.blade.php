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

        if ($groupByPO == 2) {
            $groupBySelPOa = "";
            $groupBySelPOu = " selected";
            $groupBySelPOs = "";
            $groupBySelSO = "";
        } elseif ($groupByPO == 3) {
            $groupBySelPOa = "";
            $groupBySelPOu = "";
            $groupBySelPOs = " selected";
            $groupBySelSO = "";
        } elseif ($groupByPO == 4) {
            $groupBySelPOa = "";
            $groupBySelPOu = "";
            $groupBySelPOs = "";
            $groupBySelSO = " selected";
        } else {
            $groupByPO = 1;
            $groupBySelPOa = " selected";
            $groupBySelPOu = "";
            $groupBySelPOs = "";
            $groupBySelSO = "";
        }

        $filter_status_selAP = "";
        $filter_status_selRE = "";
        $filter_status_selNA = "";
        $tmp = \Illuminate\Support\Facades\Session::get('filter_status');
        if (isset($tmp)) {
            if ($tmp == "NA") {
                // toate
                $filter_status_selNA = "selected";
            } elseif ($tmp == "AP") {
                // aprobat
                $filter_status_selAP = "selected";
            } elseif ($tmp == "RE")  {
                // rejectat
                $filter_status_selRE = "selected";
            }
        }

        $filter_history = 1;
        $filter_history_curr = " selected";
        $filter_history_arch = "";
        unset($tmp);
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

        $filter_inquirements = 0;
        $inquirements_checked = "";
        unset($tmp);
        $tmp = \Illuminate\Support\Facades\Session::get("filter_inquirements");
        if (isset($tmp)) $filter_inquirements = intval($tmp);
        if ($filter_inquirements == 1) $inquirements_checked = "checked";

        $filter_overdue = 0;
        $overdue_checked = "";
        unset($tmp);
        $tmp = \Illuminate\Support\Facades\Session::get("filter_overdue");
        if (isset($tmp)) $filter_overdue = intval($tmp);
        if ($filter_overdue == 1) $overdue_checked = "checked";

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

        $autoexplode_PO = \Illuminate\Support\Facades\Session::get("autoexplode_PO");
        $autoexplode_SO = null;
        if ($groupByPO == 4) {
            $autoexplode_SO = \Illuminate\Support\Facades\Session::get("autoexplode_SO");
        } else \Illuminate\Support\Facades\Session::forget("autoexplode_SO");

        $background_color = "";
        if (\Illuminate\Support\Facades\Auth::user()->role == 'Furnizor') $background_color = "background-color: lightyellow;";
        elseif (\Illuminate\Support\Facades\Auth::user()->role == 'Referent') $background_color = "background-color: lightgreen;";
        elseif (\Illuminate\Support\Facades\Auth::user()->role == 'CTV') $background_color = "background-color: lightgray;";

        $orders = App\Materom\Orders::getOrderList();
        $message_count = App\Materom\Orders::unreadMessageCount();
        $message_svg = "";
        if ($message_count > 0) {
            if ($message_count > 99) $message_count = '>99';
            else $message_count = "&nbsp;" . $message_count;
            $message_svg = '&nbsp;<svg style="vertical-align: middle;" width="41" height="38">
                  <g>
                  <rect x="2" y="2" rx="8" ry="8" width="35" height="32" style="fill:red;stroke:black;stroke-width:2;opacity:0.99" />
                  <text x="5" y="23" font-family="Arial" font-size="16" fill="white">' . $message_count . '</text>
                  </g>
                </svg>';
        }

    @endphp
    <div class="container-fluid">
        <input type="hidden" id="filter_history" value="{{$filter_history}}">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header" style="border-bottom-width: 0px;">
                        @if (\Illuminate\Support\Facades\Auth::user()->role == "Administrator")
                            <a href="/roles">
                                <p
                                        style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;"
                                        class="card-line first">
                                    <image style='height: 2.2rem; margin-left: -1.5rem;'
                                           src='/images/icons8-administrative-tools-48.png'/>
                                    {{__("Roles")}}
                                </p>
                            </a>
                            <a href="/users">
                                <p
                                        style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;"
                                        class="card-line">
                                    <image style='height: 2.2rem; margin-left: -1.5rem;'
                                           src='/images/icons8-user-account-80.png'/>
                                    {{__("Users")}}
                                </p>
                            </a>
                            <a href="/messages">
                                <p
                                        style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;"
                                        class="card-line">
                                    <image style='height: 2.2rem; margin-left: -1.5rem;'
                                           src='/images/icons8-chat-80.png'/>
                                    {{__("Messages")}}{!!$message_svg!!}
                                </p>
                            </a>
                            <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;"
                               class="card-line selector">
                                <image style='height: 2.2rem; margin-left: -1.5rem;'
                                       src='/images/icons8-todo-list-96.png'/>
                                {{__("Orders")}}
                            </p>
                        @else
                            <a href="/messages">
                                <p
                                        style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;"
                                        class="card-line first">
                                    <image style='height: 2.2rem; margin-left: -1.5rem;'
                                           src='/images/icons8-chat-80.png'/>
                                    {{__('Messages')}}{!!$message_svg!!}
                                </p>
                            </a>
                            <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;"
                               class="card-line selector">
                                <image style='height: 2.2rem; margin-left: -1.5rem;'
                                       src='/images/icons8-todo-list-96.png'/>
                                {{__("Orders")}}
                            </p>
                        @endif
                    </div>
                    <div class="card-body" style="padding-bottom: 0px;">
                        <div style="{{$background_color}} border: 1px solid black; border-radius: 0.5rem; padding: 4px; height: 8.2rem;">
                            <form action="orders" method="post">
                                {{csrf_field()}}
                                <div class="container row" style="display: block; max-width: 100%;">
                                    <table style="border: none; width: 100%;">
                                    <tr>
                                    <td>
                                    {{__('Show by')}}:
                                    <select class="form-control-sm input-sm" style="height: 1.6rem; padding: 2px;"
                                            name="groupOrdersBy" onchange="this.form.submit()">
                                        <option value="1"{{$groupBySelPOa}}>{{__('Purchase orders (urgent & stock)')}}</option>
                                        <option value="2"{{$groupBySelPOu}}>{{__('Purchase orders (urgent)')}}</option>
                                        <option value="3"{{$groupBySelPOs}}>{{__('Purchase orders (stock)')}}</option>
                                        <option value="4"{{$groupBySelSO}}>{{__('Sales orders')}}</option>
                                    </select>
                                    {{__('Filter by status')}}:
                                    <select class="form-control-sm input-sm" style="height: 1.6rem; padding: 2px;"
                                            name="filter_status" onchange="this.form.submit(); return false;">
                                        <option value="NA"{{$filter_status_selNA}}>{{__('All')}}</option>
                                        <option value="AP"{{$filter_status_selAP}}>{{__('Approved')}}</option>
                                        <option value="RE"{{$filter_status_selRE}}>{{__('Rejected')}}</option>
                                    </select>
                                    <input type="checkbox" id="filter_inquirements" name="filter_inquirements" style="margin-left: 8px; padding: 2px;" onchange="this.form.submit();" {{$inquirements_checked}}>
                                    <label for="filter_inquirements" class="col-form-label text-md-left">{{__('Only inquirements')}}</label>&nbsp;&nbsp;
                                    </td>
                                    <td width="100px" style="text-align: right;">
                                        <button title="Download the xls report for shown orders" type="button" style="margin-left: 2px; height: 1.5rem;"
                                                onclick="download_orders_xls();return false;">{{__('XLS Report')}}</button>
                                    </td>
                                    </tr>
                                    </table>
                                </div>
                                <br>
                                <div class="container row" style="display: block; max-width: 100%;">
                                    {{__('Displayed orders')}}:
                                    <select class="form-control-sm input-sm" style="height: 1.6rem; padding: 2px;"
                                            name="filter_history" onchange="this.form.submit()">
                                        <option value="1"{{$filter_history_curr}}>{{__("Unprocessed")}}</option>
                                        <option value="2"{{$filter_history_arch}}>{{__("Processed")}}</option>
                                    </select>
                                    @if ($filter_history == 2)
                                        &nbsp;{{__('Documents archived since')}}:
                                        <input type="text" id="time_search" class="form-control-sm"
                                               style="height:1.6rem; width: 6rem;" name="time_search"
                                               value="{{$filter_time_val}}"
                                               onchange="this.form.submit()">
                                    @else
                                        <input type="checkbox" id="filter_overdue" name="filter_overdue" style="margin-left: 8px; padding: 2px;" onchange="this.form.submit();" {{$overdue_checked}}>
                                        <label for="filter_overdue" class="col-form-label text-md-left">{{__('Only overdue deliveries') . ' (' . \App\Materom\Orders::overdues() . ')'}}</label>&nbsp;&nbsp;
                                    @endif
                                </div>
                                <br>
                                <div class="container row" style="display: block; max-width: 100%;">
                                    <table style="border: none; width: 100%;">
                                    <tr>
                                    <td>
                                    @if (\Illuminate\Support\Facades\Auth::user()->role != "Furnizor")
                                        {{__("Sales order")}}:
                                        <input type="text" class="form-control-sm input-sm"
                                               style="width: 6rem; height: 1.4rem;" name="filter_vbeln"
                                               value="{{$filter_vbeln}}">&nbsp;&nbsp;
                                    @endif
                                    {{__("Purchase order")}}:
                                    <input type="text" class="form-control-sm input-sm"
                                           style="width: 6rem; height: 1.4rem;" name="filter_ebeln"
                                           value="{{$filter_ebeln}}">&nbsp;&nbsp;
                                    {{__("Material")}}:
                                    <input type="text" class="form-control-sm input-sm"
                                           style="width: 6rem; height: 1.4rem;" name="filter_matnr"
                                           value="{{$filter_matnr}}">&nbsp;&nbsp;
                                    {{__("Material description")}}:
                                    <input type="text" class="form-control-sm input-sm"
                                           style="width: 12rem; height: 1.4rem;" name="filter_mtext"
                                           value="{{$filter_mtext}}">&nbsp;&nbsp;
                                    @if (\Illuminate\Support\Facades\Auth::user()->role != "Furnizor")
                                        {{__("Supplier")}}:
                                        <input type="text" class="form-control-sm input-sm"
                                               style="width: 6rem; height: 1.4rem;" name="filter_lifnr"
                                               value="{{$filter_lifnr}}">&nbsp;&nbsp;
                                        {{__("Supplier name")}}:
                                        <input type="text" class="form-control-sm input-sm"
                                               style="width: 12rem; height: 1.4rem;" name="filter_lifnr_name"
                                               value="{{$filter_lifnr_name}}">&nbsp;&nbsp;
                                    @endif
                                    </td>
                                    <td width="100px" style="text-align: right;">
                                        <button type="button" style="margin-left: 2px; height: 1.5rem; "
                                            onclick="reset_filters();return false;">{{__('Reset')}}</button>
                                    </td>
                                    </tr>
                                    </table>
                                </div>

                                <input type="submit" style="position: absolute; left: -9999px; width: 1px; height: 1px;"
                                       tabindex="-1">
                            </form>
                        </div>
                    </div>

                    <br>

                    <div class="card-body orders-table-div" style="height: 69vh; padding-top: 0rem;">
                        <table style="border: 2px solid black; table-layout: fixed;"
                               class="orders-table basicTable table table-striped" id="orders_table">
                            <colgroup>
                                <col style="width:1.6%;">
                                <col style="width:1.6%;">
                                <col style="width:1.6%;">
                                <col style="width:1.6%;">
                                <col style="width:1.6%;">
                                <col style="width:1.6%;">
                                <col style="width:1.6%;">
                                <col style="width:1.6%;">
                                <col style="width:1.6%;">
                                <col style="width:1.6%;">
                                <col style="width:2%;">
                                <col style="width:4%;">
                                <col width="4%">
                                <col width="4%">
                                <col width="4%">
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
                                <col width="3%">
                                <col width="3%">
                                <col width="3%">
                                <col width="3%">
                                <col width="3%">
                                <col width="2%">
                                <col width="3%">
                                <col width="2%">
                                <col width="3%">
                                <col width="2%">
                                <col width="1%">
                                <col width="3%">
                                <col width="3%">
                            </colgroup>
                            <tr>
                                <th colspan="1" class="td01">
                                    <image style='height: 1.3rem;' src='/images/icons8-check-all-50.png' title='Select/unselect items to perform repetitively the same operation'/>
                                </th>
                                <th colspan="1" class="td01">
                                    <image style='height: 1.3rem;' src='/images/icons8-info-50.png' title='Information: new arrival, response required, processing time'/>
                                </th>
                                <th colspan="1" class="td01">
                                    <image style='height: 1.3rem;' src='/images/icons8-circled-right-50-1.png' title='Your responsability to take action'/>
                                </th>
                                <th colspan="1" class="td01">
                                    <image style='height: 1.3rem;' src='/images/icons8-unchecked-checkbox-50-3.png' title='Order/item has been changed'/>
                                </th>
                                <th colspan="1" class="td01">
                                    <image style='height: 1.3rem;' src='/images/icons8-checkmark-50-1.png' title='Order/item has been approved'/>
                                </th>
                                <th colspan="1" class="td01">
                                    <image style='height: 1.3rem;' src='/images/icons8-delete-50-2.png' title='Order/item has been rejected'/>
                                </th>
                                <th colspan="1" class="td01">
                                    <image style='height: 1.3rem;' src='/images/icons8-qmark-50.png' title='Inquirement for this order/item'/>
                                </th>
                                <th colspan="1" class="td01">
                                    <image style='height: 1.5rem;' src='/images/icons8-checkmark-50-3.png' title='Perform approval of order/item'/>
                                </th>
                                <th colspan="1" class="td01">
                                    <image style='height: 1.5rem;' src='/images/icons8-close-window-50.png' title='Reject order/item'/>
                                </th>
                                <th colspan="1" class="td01">
                                    <image style='height: 1.5rem;' src='/images/icons8-greater-than-50-1.png' title='Send a message to a person in the flow'/>
                                </th>
                                @php
                                    if ($groupByPO != 4) {
                                        echo '<th class="td02" colspan="3">' . __('Purchase order') . '</th>';
                                        $th1 = __("Supplier");
                                        $th2 = ""; // "Nume";
                                        $th3 = __("Referent");
                                        $th4 = ""; // "Aprovizionare";
                                        $th5 = __("Data creare");
                                        $th6 = __("Moneda");
                                        $th7 = __("Rata schimb");
                                        $th8 = __("Data cda.");
                                    } else {
                                        echo '<th class="td02" colspan="3">' . __('Sales order') . '</th>';
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
                                if ($groupByPO != 4) {
                                    echo "<th colspan=2>$th1</th>";
                                    echo "<th colspan=5>$th2</th>";
                                    echo "<th colspan=2>$th3</th>";
                                    echo "<th colspan=4>$th4</th>";
                                    echo "<th colspan=3>$th5</th>";
                                    echo "<th colspan=2>$th6</th>";
                                    echo "<th colspan=2>$th7</th>";
                                    echo "<th colspan=2>$th8</th>";
                                    for ($i = 0; $i < 4; $i++) echo "<th>&nbsp;</th>";
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
                                $line_counter = 1;

                                foreach ($orders as $order) {

                                    if ($groupByPO != 4) {
                                        $comanda = "<button type='button' id='butt_P$order->ebeln' style='width: 1.6rem; text-align: center;' onclick='getSubTree(this); return false;'>+</button> " . "<p onclick='re_filter(\"P\",\"$order->ebeln\")' style='display:inline' class='resetfilters'>" .
                                            \App\Materom\SAP::alpha_output($order->ebeln) . "</p>";
                                    } else {
                                        $buttname = $order->vbeln;
                                        if (strtoupper($buttname) == \App\Materom\Orders::stockorder) $buttname = __('Stock');
                                        elseif (strtoupper(trim($buttname)) == "SALESORDER") $buttname = __('Emergency');
                                        else $buttname = "<p onclick='re_filter(\"S\",\"$order->vbeln\")' style='display:inline' class='resetfilters'>" . \App\Materom\SAP::alpha_output($buttname) . "</p>";
                                        $comanda = "<button type='button' id='butt_S$order->vbeln' style='width: 1.6rem; text-align: center;' onclick='getSubTree(this); return false;'>+</button> $buttname";
                                    }

                                    $line_counter = $line_counter + 1;
                                    if ($line_counter == 2) $line_counter = 0;

                                    $button_accept = "";
                                    $button_reject = "";
                                    $button_inquire = "";

                                    if ($groupByPO != 4) {
                                        $oid = "P" . $order->ebeln;
                                        $data = "<td class='td02' colspan=2>" . \App\Materom\SAP::alpha_output($order->lifnr) . "</td>" .
                                                "<td class='td02' colspan=5>$order->lifnr_name</td>" .
                                                "<td class='td02' colspan=1>$order->ekgrp</td>" .
                                                "<td class='td02' colspan=5>$order->ekgrp_name</td>" .
                                                "<td class='td02' colspan=3>$order->erdat_out</td>" .
                                                "<td class='td02' colspan=2>$order->curr</td>" .
                                                "<td class='td02' colspan=2>$order->fxrate</td>".
                                                "<td class='td02' colspan=2>$order->bedat_out</td>";

                                        switch ($order->info) {
                                            case 0:
                                                $info_icon = "";
                                                break;
                                            case 1:
                                                $info_icon = "";
                                                break;
                                            case 2:
                                                $info_icon = "<image style='height: 1.2rem;' src='/images/warning.png'>";
                                                break;
                                            case 3:
                                                $info_icon = "<image style='height: 1.2rem;' src='/images/critical.png'>";
                                                break;
                                            case 4:
                                                $info_icon = "<image style='height: 1.2rem;' src='/images/green_blink.gif' onclick='replyack2(\"$order->ebeln\"); return false;'>";
                                                $info_icon = "";
                                                break;
                                            case 5:
                                                $info_icon = "<image style='height: 1.2rem;' src='/images/yellow_blink.gif'>";
                                                $info_icon = "";
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
                                                $owner_icon = "<image style='height: 1.2rem;' src='/images/purpleArrow.png'>";
                                                break;
                                        }

                                        $changed_icon = "";
                                        if ($order->changed != 0)
                                            $changed_icon = "<image style='height: 1.3rem;' src='/images/icons8-unchecked-checkbox-50-3.png'/>";
                                        $accepted_icon = "";
                                        if ($order->accepted == 1)
                                            $accepted_icon = "<image style='height: 1.3rem;' src='/images/icons8-checkmark-50-1.png'/>";
                                        elseif ($order->accepted == 2)
                                            $accepted_icon = "<image style='height: 1.3rem;' src='/images/icons8-checkmark-yellow-64.png'/>";
                                        $rejected_icon = "";
                                        if ($order->rejected == 1)
                                            $rejected_icon = "<image style='height: 1.3rem;' src='/images/icons8-delete-50-2.png'/>";
                                        elseif ($order->rejected == 2)
                                            $rejected_icon = "<image style='height: 1.3rem;' src='/images/icons8-cancel-yellow-48.png'/>";

                                        $inq_onclick = "";
                                        if ($order->inq_reply == 1) $inq_onclick = "onclick='inquireReply(this, $order->inquired);return false;' class='cursorpointer'";
                                        $inquired_icon = "";
                                        if ($order->inquired == 1)
                                            $inquired_icon = "<image style='height: 1.3rem;' src='/images/icons8-qmark-50-green.png' $inq_onclick/>";
                                        elseif ($order->inquired == 2)
                                            $inquired_icon = "<image style='height: 1.3rem;' src='/images/icons8-qmark-50-red.png' $inq_onclick/>";
                                        elseif ($order->inquired == 3)
                                            $inquired_icon = "<image style='height: 1.3rem;' src='/images/icons8-qmark-50-yellow.png' $inq_onclick/>";

                                        if ($order->accept == 1)
                                            $button_accept = "<button type='button' class='order-button-accepted' style='width: 1.5rem; height: 1.5rem; text-align: center;' " .
                                                             "onclick='acceptPOrder(this);return false;'/>";
                                        if ($order->reject == 1)
                                            $button_reject = "<button type='button' class='order-button-rejected' style='width: 1.6rem; height: 1.5rem; text-align: center;' " .
                                                             "onclick='rejectPOrder(this, 0, null);return false;'/>";
                                        if ($order->inquire == 1)
                                            $button_inquire = "<button type='button' class='order-button-request' style='width: 1.5rem; height: 1.5rem; text-align: center;' " .
                                                              "onclick='inquirePOrder(this);return false;'/>";

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
                                    } else {
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
                                                $info_icon = "";
                                                break;
                                            case 2:
                                                $info_icon = "<image style='height: 1.2rem;' src='/images/warning.png'>";
                                                break;
                                            case 3:
                                                $info_icon = "<image style='height: 1.2rem;' src='/images/critical.png'>";
                                                break;
                                            case 4:
                                                $info_icon = "<image style='height: 1.2rem;' src='/images/green_blink.gif'>";
                                                $info_icon = "";
                                                break;
                                            case 5:
                                                $info_icon = "<image style='height: 1.2rem;' src='/images/yellow_blink.gif'>";
                                                $info_icon = "";
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
                                                $owner_icon = "<image style='height: 1.2rem;' src='/images/purpleArrow.png'>";
                                                break;
                                        }

                                        $changed_icon = "";
                                        if ($order->changed != 0)
                                            $changed_icon = "<image style='height: 1.3rem;' src='/images/icons8-unchecked-checkbox-50-3.png'/>";
                                        $accepted_icon = "";
                                        if ($order->accepted == 1)
                                            $changed_icon = "<image style='height: 1.3rem;' src='/images/icons8-checkmark-50-1.png'/>";
                                        elseif ($order->accepted == 2)
                                            $changed_icon = "<image style='height: 1.3rem;' src='/images/icons8-checkmark-yellow-64.png'/>";
                                        $rejected_icon = "";
                                        if ($order->rejected == 1)
                                            $rejected_icon = "<image style='height: 1.3rem;' src='/images/icons8-delete-50-2.png'/>";
                                        elseif ($order->rejected == 2)
                                            $rejected_icon = "<image style='height: 1.3rem;' src='/images/icons8-cancel-yellow-48.png'/>";
                                        $inq_onclick = "";
                                        if ($order->inq_reply == 1) $inq_onclick = "onclick='inquireReply(this, $order->inquired);return false;' class='cursorpointer'";
                                        $inquired_icon = "";
                                        if ($order->inquired == 1)
                                            $imquired_icon = "<image style='height: 1.3rem;' src='/images/icons8-qmark-50-green.png' $inq_onclick/>";
                                        elseif ($order->inquired == 2)
                                            $inquired_icon = "<image style='height: 1.3rem;' src='/images/icons8-qmark-50-red.png' $inq_onclick/>";
                                        elseif ($order->inquired == 3)
                                            $inquired_icon = "<image style='height: 1.3rem;' src='/images/icons8-qmark-50-yellow.png' $inq_onclick/>";

                                        if ($order->accept == 1)
                                            $button_accept = "<button type='button' class='order-button-accepted' style='width: 1.5rem; height: 1.5rem; text-align: center;' " .
                                                             "onclick='acceptSOrder(this);return false;' />";
                                        if ($order->reject == 1)
                                            $button_reject = "<button type='button' class='order-button-rejected' style='width: 1.6rem; height: 1.5rem; text-align: center;' " .
                                                             "onclick='rejectSOrder(this, 0, null);return false;' />";
                                        if ($order->inquire == 1)
                                            $button_inquire = "<button type='button' class='order-button-request' style='width: 1.5rem; height: 1.5rem; text-align: center;' " .
                                                              "onclick='inquireSOrder(this);return false;' />";

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
        function delete_filters() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajaxSetup({async: false});
            var df_data, df_status;
            $.post("webservice/deletefilters",
                {
                    empty: null
                },
                function (data, status) {
                    df_data = data;
                    df_status = status;
                });
            jQuery.ajaxSetup({async: true});
        }

        function re_filter(type, order) {
            delete_filters();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            order = conv_exit_alpha_output(order);
            jQuery.ajaxSetup({async: false});
            var rf_data, rf_status;
            $.post("webservice/refilter",
                {
                    type: type,
                    order: order
                },
                function (data, status) {
                    rf_data = data;
                    rf_status = status;
                });
            jQuery.ajaxSetup({async: true});
            location.reload();
        }

        function reset_filters() {
            delete_filters();
            location.reload();
        }

        function download_orders_xls() {
            location.replace("webservice/downloadordersxls");
            return;
        }

        function onselect_Inforecord(caller, result_infnr, result_lifnr, result_lifnr_name, result_idnlf, result_mtext, result_matnr, result_purch_price, result_purch_currency, result_sales_price, result_sales_currency) {
            if (caller == 1) {
                $("#ar-immed-lifnr").val(result_lifnr);
                $("#ar-immed-idnlf").val(result_idnlf);
                $("#ar-immed-mtext").val(result_mtext);
                $("#ar-immed-matnr").val(result_matnr);
                $("#ar-immed-purch-price").val(result_purch_price);
                $("#ar-immed-purch-curr").val(result_purch_currency);
            }
            if (caller == 2) {
                $("#aep-lifnr").val(result_lifnr);
                $("#aep-idnlf").val(result_idnlf);
                $("#aep-mtext").val(result_mtext);
                $("#aep-matnr").val(result_matnr);
                $("#aep-purch-price").val(result_purch_price);
                $("#aep-purch-curr").val(result_purch_currency);
            }
            if (caller == 3) {
                $("#aes-lifnr").val(result_lifnr);
                $("#aes-idnlf").val(result_idnlf);
                $("#aes-mtext").val(result_mtext);
                $("#aes-matnr").val(result_matnr);
                $("#aes-purch-price").val(result_purch_price);
                $("#aes-purch-curr").val(result_purch_currency);
            }
        }

        function onselect_zpretrecord(caller, result_infnr, result_lifnr, result_lifnr_name, result_idnlf, result_mtext, result_matnr,
                                      result_purch_price, result_purch_currency, result_sales_price, result_sales_currency) {
            if (result_matnr == null || result_matnr.trim().length == 0) result_matnr = "PA01";
            if (caller == 1) {
                $("#ar-immed-lifnr").val(result_lifnr);
                $("#ar-immed-idnlf").val(result_idnlf);
                $("#ar-immed-mtext").val(result_mtext);
                $("#ar-immed-matnr").val(result_matnr);
                $("#ar-immed-purch-price").val(result_purch_price);
                $("#ar-immed-purch-curr").val(result_purch_currency);
                $("#ar-immed-sales-price").val(result_sales_price);
                $("#ar-immed-sales-curr").val(result_sales_currency);
            }
            if (caller == 2) {
                $("#aep-lifnr").val(result_lifnr);
                $("#aep-idnlf").val(result_idnlf);
                $("#aep-mtext").val(result_mtext);
                $("#aep-matnr").val(result_matnr);
                $("#aep-purch-price").val(result_purch_price);
                $("#aep-purch-curr").val(result_purch_currency);
                $("#aep-sales-price").val(result_sales_price);
                $("#aep-sales-curr").val(result_sales_currency);
            }
            if (caller == 3) {
                $("#aes-lifnr").val(result_lifnr);
                $("#aes-idnlf").val(result_idnlf);
                $("#aes-mtext").val(result_mtext);
                $("#aes-matnr").val(result_matnr);
                $("#aes-purch-price").val(result_purch_price);
                $("#aes-purch-curr").val(result_purch_currency);
                $("#aes-sales-price").val(result_sales_price);
                $("#aes-sales-curr").val(result_sales_currency);
            }
        }

        $(function () {
            $("#time_search").datepicker({dateFormat: "yy-mm-dd"});
        });

        var checkedList = [];
        var unCheckedList = [];

        function parent(id) {
            if (id.startsWith('I')) {
                let res = id.substring(1);
                @if ($groupByPO == 4)
                    return $("input[name*='_" + res.split("_")[0] + "']")[0].name;
                @else
                    return $("input[name*='" + res.split("_")[0] + "']")[0].name;
                @endif
            } else if (id.startsWith('P')) {
                @if ($groupByPO != 4)
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

            @if ($groupByPO != 4)
            if (id.startsWith('S') || id.startsWith('P'))
                @else
                if (id.startsWith('S')) @endif
                {
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

        function _unused_acceptItem(ebeln, id, type, reload) {
            var _data, _status = "";
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajaxSetup({async: false});

            $.post("webservice/acceptitemchange",
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
            if (_data != null && _data != undefined && _data.trim().length != 0) {
                alert(_2data);
                return;
            }
            if (_status == "success") {
                if (reload) location.reload(true);
            } else alert('Error processing operation!');
        }

        function doRejectItem(ebeln, item, category, reason, new_status, new_stage) {
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
                    item: item,
                    category: category,
                    reason: reason,
                    new_status: new_status,
                    new_stage: new_stage,
                },
                function (data, status) {
                    _data = data;
                    _status = status;
                });
            jQuery.ajaxSetup({async: true});
            if (_data != null && _data != undefined && _data.trim().length != 0) {
                alert(_data);
                return;
            }
            return _status == "success";
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
                @if ($groupByPO == 4)
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
            refreshCheck();
            return false;
        }

        function getSOSubTree(currentrow, order, _data) {
            // PO header
            var newRow = $("<tr>");
            var cols = "";
            var so_style = "background-color:" + $(currentrow).css("background-color") + ";";
            cols += '<td class="first_color" style="' + so_style + '" colspan="11"></td>';
            cols += '<td colspan="3"><b>{{__("Purchase order")}}</b></td>';
            cols += '<td colspan="2"><b>{{__("Supplier")}}</b></td>';
            cols += '<td colspan="5"><b>&nbsp;</b></td>';
            cols += '<td colspan="2"><b>{{__("Referent")}}</b></td>';
            cols += '<td colspan="4"><b>&nbsp;</b></td>';
            cols += '<td colspan="3"><b>{{__("Data creare")}}</b></td>';
            cols += '<td colspan="2"><b>{{__("Moneda")}}</b></td>';
            cols += '<td colspan="3"><b>{{__("Rata de schimb")}}</b></td>';
            cols += '<td colspan="3"><b>{{__("Data cda.")}}</b></td>';
            cols += '<td colspan="2"><b></b></td>';
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
                        info_icon = "";
                        break;
                    case 2:
                        info_icon = "<image style='height: 1.2rem;' src='/images/warning.png'>";
                        break;
                    case 3:
                        info_icon = "<image style='height: 1.2rem;' src='/images/critical.png'>";
                        break;
                    case 4:
                        info_icon = "<image style='height: 1.2rem;' src='/images/green_blink.gif' onclick='replyack2(\"' + porder.ebeln + '\"); return false;'>";
                        info_icon = "";
                        break;
                    case 5:
                        info_icon = "<image style='height: 1.2rem;' src='/images/yellow_blink.gif'>";
                        info_icon = "";
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
                        owner_icon = "<image style='height: 1.2rem;' src='/images/purpleArrow.png'>";
                        break;
                }

                let changed_icon = "";
                if (porder.changed != 0)
                    changed_icon = "<image style='height: 1.3rem;' src='/images/icons8-unchecked-checkbox-50-3.png'/>";

                let accepted_icon = "";
                if (porder.accepted == 1)
                    accepted_icon = "<image style='height: 1.3rem;' src='/images/icons8-checkmark-50-1.png'/>";
                else if (porder.accepted == 2)
                    accepted_icon = "<image style='height: 1.3rem;' src='/images/icons8-checkmark-yellow-64.png'/>";

                let rejected_icon = "";
                if (porder.rejected == 1)
                    rejected_icon = "<image style='height: 1.3rem;' src='/images/icons8-delete-50-2.png'/>";
                else if (porder.rejected == 2)
                    rejected_icon = "<image style='height: 1.3rem;' src='/images/icons8-cancel-yellow-48.png'/>";


                let inq_onclick = "";
                if (porder.inq_reply == 1) inq_onclick = "onclick='inquireReply(this, " + porder.inquired + ");return false;' class='cursorpointer'";
                let inquired_icon = "";
                if (porder.inquired == 1)
                    inquired_icon = "<image style='height: 1.3rem;' src='/images/icons8-qmark-50-green.png' " + inq_onclick + "/>";
                else if (porder.inquired == 2)
                    inquired_icon = "<image style='height: 1.3rem;' src='/images/icons8-qmark-50-red.png' " + inq_onclick + "/>";
                else if (porder.inquired == 3)
                    inquired_icon = "<image style='height: 1.3rem;' src='/images/icons8-qmark-50-yellow.png' " + inq_onclick + "/>";

                let button_accept = "";
                if (porder.accept == 1)
                    button_accept = "<button type='button' class='order-button-accepted' style='width: 1.5rem; height: 1.5rem; text-align: center;' " +
                        "onclick='acceptPOrder(this);return false;'/>";
                let button_reject = "";
                if (porder.reject == 1)
                    button_reject = "<button type='button' class='order-button-rejected' style='width: 1.6rem; height: 1.5rem; text-align: center;' " +
                        "onclick='rejectPOrder(this, 0, null);return false;'/>";
                let button_inquire = "";
                if (porder.inquire == 1)
                    button_inquire = "<button type='button' class='order-button-request' style='width: 1.5rem; height: 1.5rem; text-align: center;' " +
                        "onclick='inquirePOrder(this);return false;'/>";

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
                cols += "<td colspan='3'><button type='button' id='butt_P" + porder.ebeln + "' style='width: 1.6rem; text-align: center;' onclick=\"getSubTree(this);return false;\">+</button> " +
                    "<p onclick='re_filter(\"P\",\"" + porder.ebeln + "\")' style='display:inline' class='resetfilters'>" + conv_exit_alpha_output(porder.ebeln) + "</p>"
                    + "</td>";
                cols += '<td class="td02" colspan="2">' + conv_exit_alpha_output(porder.lifnr) + '</td>';
                cols += '<td class="td02" colspan="5">' + porder.lifnr_name + '</td>';
                cols += '<td class="td02" colspan="1">' + porder.ekgrp + '</td>';
                cols += '<td class="td02" colspan="5">' + porder.ekgrp_name + '</td>';
                cols += '<td class="td02" colspan="3">' + porder.erdat_out + '</td>';
                cols += '<td class="td02" colspan="2">' + porder.curr + '</td>';
                cols += '<td class="td02" colspan="3">' + porder.fxrate + '</td>';
                cols += '<td class="td02" colspan="3">' + porder.bedat_out + '</td>';
                cols += '<td class="td02" colspan="2"></td>';
                newRow.append(cols).hide();
                $(prevrow).after(newRow);
                if (i % 2 == 0)
                    newRow.attr('style', "background-color:LightYellow; vertical-align: middle;");
                else
                    newRow.attr('style', "background-color:Wheat; vertical-align: middle;");
                newRow.attr('id', "tr_P" + porder.ebeln);
            }
        }

        function getPOSubTree(currentrow, sorder, order, _data) {
            // PO item header
            var newRow = $("<tr>");
            var cols = "";
            var po_style = "background-color:" + $(currentrow).css("background-color") + ";";
            var first_color = $(currentrow).find(".first_color").css("background-color");
            var first_style = "background-color:" + first_color;
            @if ($groupByPO == 4)
                cols += '<td class="first_color" colspan="11" style="' + first_style + '"></td>';
            colsafter = 0;
            @else
                cols += '<td class="first_color" colspan="10" style="' + po_style + '"></td>';
            colsafter = 1;
            @endif
            cols += '<td style="' + po_style + '"></td>';
            cols += '<td class="td02" colspan="1"><b>{{__("Position")}}</b></td>';
            cols += '<td class="td02" colspan="1"><b>{{__("Plant")}}</b></td>';
            cols += '<td class="td02" colspan="1"><b>{{__("Fabricant")}}</b></td>';
            cols += '<td class="td02" colspan="3"><b>{{__("Material")}}</b></td>';
            cols += '<td class="td02" colspan="4"><b>{{__("Material description")}}</b></td>';
            cols += '<td class="td02" colspan="2" style="text-align: right;"><b>{{__("Quantity")}}</b></td>';
            cols += '<td class="td02" colspan="2" style="padding-left: 0.5rem;"><b>{{__("Delivery date")}}</b></td>';
            cols += '<td class="td02" colspan="2" style="text-align: right;"><b>{{__("Purchase price")}}</b></td>';
            @if (\Illuminate\Support\Facades\Auth::user()->role != "Furnizor")
            let sales_price_hdr = '{{__("Sales price")}}';
            if (sorder == '{{\App\Materom\Orders::stockorder}}') sales_price_hdr = '';
            cols += '<td class="td02" colspan="2" style="text-align: right;"><b>' + sales_price_hdr + '</b></td>';
            @else
                cols += '<td class="td02" colspan="2"><b>&nbsp;</b></td>';
            @endif
                cols += '<td class="td02" colspan="2" style="text-align: left;"><b>{{__("Delivered on")}}</b></td>';
            cols += '<td class="td02" colspan="2" style="text-align: right;"><b>{{__("Delivered quantity")}}</b></td>';
            cols += '<td class="td02" colspan="3" style="text-align: left;"><b>{{__("Goods receipt date")}}</b></td>';
            cols += '<td class="td02" colspan="3" style="text-align: right;"><b>{{__("Goods receipt quantity")}}</b></td>';
            if (colsafter > 0)
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
                        info_icon = "";
                        break;
                    case 2:
                        info_icon = "<image style='height: 1.2rem;' src='/images/warning.png'>";
                        break;
                    case 3:
                        info_icon = "<image style='height: 1.2rem;' src='/images/critical.png'>";
                        break;
                    case 4:
                        info_icon = "<image style='height: 1.2rem;' src='/images/green_blink.gif' onclick='replyack(\"" + pitem.ebeln + "\", \"" + pitem.ebelp + "\"); return false;'>";
                        break;
                    case 5:
                        info_icon = "<image style='height: 1.2rem;' src='/images/yellow_blink.gif' onclick='replyack(\"" + pitem.ebeln + "\", \"" + pitem.ebelp + "\"); return false;'>";
                        break;
                    case 6:
                        info_icon = "<image style='height: 1.2rem;' src='/images/icons8-shipped-48.png'>";
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
                        owner_icon = "<image style='height: 1.2rem;' src='/images/purpleArrow.png'>";
                        break;
                }

                let changed_icon = "";
                if (pitem.changed != 0)
                    changed_icon = "<image style='height: 1.3rem;' src='/images/icons8-unchecked-checkbox-50-3.png'/>";

                let accepted_icon = "";
                if (pitem.accepted == 1)
                    accepted_icon = "<image style='height: 1.3rem;' src='/images/icons8-checkmark-50-1.png'/>";
                else if (pitem.accepted == 2)
                    accepted_icon = "<image style='height: 1.3rem;' src='/images/icons8-checkmark-yellow-64.png'/>";

                let rejected_icon = "";
                if (pitem.rejected == 1)
                    rejected_icon = "<image style='height: 1.3rem;' src='/images/icons8-delete-50-2.png'/>";
                else if (pitem.rejected == 2)
                    rejected_icon = "<image style='height: 1.3rem;' src='/images/icons8-cancel-yellow-48.png'/>";

                let inq_onclick = "";
                if (pitem.inq_reply == 1) inq_onclick = "onclick='inquireReply(this, " + pitem.inquired + ");return false;' class='cursorpointer'";
                let inquired_icon = "";
                if (pitem.inquired == 1)
                    inquired_icon = "<image style='height: 1.3rem;' src='/images/icons8-qmark-50-green.png' " + inq_onclick + "/>";
                else if ((pitem.inquired == 2) || (pitem.inquired == 4))
                    inquired_icon = "<image style='height: 1.3rem;' src='/images/icons8-qmark-50-red.png' " + inq_onclick + "/>";
                else if (pitem.inquired == 3)
                    inquired_icon = "<image style='height: 1.3rem;' src='/images/icons8-qmark-50-yellow.png' " + inq_onclick + "/>";

                let button_accept = "";
                if (pitem.accept == 1)
                    button_accept = "<button type='button' class='order-button-accepted' style='width: 1.5rem; height: 1.5rem; text-align: center;' " +
                        "onclick='acceptPItem(this);return false;'/>";
                let button_reject = "";
                if (pitem.reject == 1)
                    button_reject = "<button type='button' class='order-button-rejected' style='width: 1.6rem; height: 1.5rem; text-align: center;' " +
                        "onclick='rejectPItem(this, 0, null);return false;'/>";
                if (pitem.reject == 2)
                    button_reject = "<button type='button' class='order-button-rejected' style='width: 1.6rem; height: 1.5rem; text-align: center;' " +
                        "onclick='rejectPItem(this, 0, \"\");return false;'/>";
                if (pitem.reject == 3)
                    button_reject = "<button type='button' class='order-button-rejected4' style='width: 1.6rem; height: 1.5rem; text-align: center;' " +
                        "onclick='requestRejectPItem(this, 3);return false;'/>";
                if (pitem.reject == 4)
                    button_reject = "<button type='button' class='order-button-rejected4' style='width: 1.6rem; height: 1.5rem; text-align: center;' " +
                        "onclick='requestRejectPItem(this, 4);return false;'/>";
                let button_inquire = "";
                if (pitem.inquire == 1)
                    button_inquire = "<button type='button' class='order-button-request' style='width: 1.5rem; height: 1.5rem; text-align: center;' " +
                        "onclick='inquirePItem(this);return false;'/>";

                var newRow = $("<tr>");
                var cols = "";
                cols += '<td colspan="1" align="center" style="vertical-align: middle;"><input id="input_chk" onclick="boxCheck(this);" type="checkbox" name="I' + pitem.ebeln + "_" + pitem.ebelp + '" value="I' + pitem.ebeln + "_" + pitem.ebelp + '"></td>';
                var po_style = "background-color:" + $(currentrow).css("background-color") + ";";
                var first_color = $(currentrow).find(".first_color").css("background-color");
                var first_style = "background-color:" + first_color;

                @if ($groupByPO == 4)
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
                    cols += '<td class="first_color td01" colspan="1" style="' + po_style + '">' + owner_icon + '</td>';
                    cols += '<td class="first_color td01" colspan="1" style="' + po_style + '">' + changed_icon + '</td>';
                    cols += '<td class="first_color td01" colspan="1" style="' + po_style + '">' + accepted_icon + '</td>';
                    cols += '<td class="first_color td01" colspan="1" style="' + po_style + '">' + rejected_icon + '</td>';
                    cols += '<td class="first_color td01" colspan="1" style="' + po_style + '">' + inquired_icon + '</td>';
                    cols += '<td class="first_color td01" colspan="1" style="' + po_style + '; padding: 0;">' + button_accept + '</td>';
                    cols += '<td class="first_color td01" colspan="1" style="' + po_style + '; padding: 0;">' + button_reject + '</td>';
                    cols += '<td class="first_color td01" colspan="1" style="' + po_style + '; padding: 0;">' + button_inquire + '</td>';
                @endif
                cols += '<td class="coloured" style="' + po_style + '">' + pitem.posnr_out + '</td>';
                cols += "<td colspan='1'><button type='button' style='width: 1.6rem; text-align: center;' onclick=\"getSubTree(this);return false;\">+</button><span id='span_item' style='padding-left: 0.2rem;'>" + conv_exit_alpha_output(pitem.ebelp) + "</span></td>";
                cols += '<td class="td02" colspan="1" style="text-align: left;">' + pitem.werks + '</td>';
                cols += '<td class="td02" colspan="1" style="text-align: left;">' + conv_exit_alpha_output(pitem.mfrnr) + '</td>';

                if (pitem.matnr_changeable == 1) {
                    let matnr_class = "td02h";
                    if (pitem.matnr_changed == 1) matnr_class += "_c";
                    cols += '<td class="' + matnr_class + '" colspan="3" onclick="change_matnr(this, \'' + pitem.ebeln + '\', \'' + pitem.ebelp + '\');return false;">' + pitem.idnlf + '</td>';
                    cols += '<td class="' + matnr_class + '" colspan="4" onclick="change_matnr(this.previousSibling, \'' + pitem.ebeln + '\', \'' + pitem.ebelp + '\');return false;">' + pitem.mtext + '</td>';
                } else {
                    let matnr_class = "td02";
                    if (pitem.matnr_changed == 1) matnr_class += "_c";
                    cols += '<td class="' + matnr_class + '" colspan="3">' + pitem.idnlf + '</td>';
                    cols += '<td class="' + matnr_class + '" colspan="4">' + pitem.mtext + '</td>';
                }

                if (pitem.quantity_changeable == 1) {
                    let quantity_class = "td02h";
                    if (pitem.quantity_changed == 1) quantity_class += "_c";
                    cols += '<td class="' + quantity_class + '" colspan="2" onclick="change_quantity(this, \'' + pitem.ebeln + '\', \'' + pitem.ebelp + '\');" style="text-align: right;">' + pitem.x_quantity + '</td>';
                } else {
                    let quantity_class = "td02";
                    if (pitem.quantity_changed == 1) quantity_class += "_c";
                    cols += '<td class="' + quantity_class + '" colspan="2" style="text-align: right;">' + pitem.x_quantity + '</td>';
                }

                if ((pitem.delivery_date_changeable == 1) || ((pitem.delivery_date_changeable == 2))) {
                    let delivery_date_class = "td02h";
                    if (pitem.delivery_date_changed == 1) delivery_date_class += "_c";
                    let change_delivery_date_func = "change_delivery_date";
                    if (pitem.delivery_date_changeable == 2) change_delivery_date_func = "change_delivery_date2";
                    cols += '<td class="' + delivery_date_class + '" colspan="2" onclick="' + change_delivery_date_func + '(this, \'' + pitem.ebeln + '\', \'' + pitem.ebelp + '\');" style="padding-left: 0.5rem;">' + pitem.x_delivery_date.split(' ')[0] + '</td>';
                } else {
                    let delivery_date_class = "td02";
                    if (pitem.delivery_date_changed == 1) delivery_date_class += "_c";
                    cols += '<td class="' + delivery_date_class + '" colspan="2" style="padding-left: 0.5rem;">' + pitem.x_delivery_date.split(' ')[0] + '</td>';
                }

                if ((pitem.price_changeable == 1) || ((pitem.price_changeable == 2))) {
                    let price_class = "td02h";
                    if (pitem.price_changed == 1) price_class += "_c";
                    let change_purchase_price_func = "change_purchase_price";
                    if (pitem.price_changeable == 2) change_purchase_price_func = "change_purchase_price2";
                    cols += '<td class="' + price_class + '" colspan="2" onclick="' + change_purchase_price_func + '(this, \'' + pitem.ebeln + '\', \'' + pitem.ebelp + '\');" style="text-align: right;">' + pitem.x_purchase_price + '</td>';
                } else {
                    let price_class = "td02";
                    if (pitem.price_changed == 1) price_class += "_c";
                    cols += '<td class="' + price_class + '" colspan="2" style="text-align: right;">' + pitem.x_purchase_price + '</td>';
                }

                cols += '<td class="td02" colspan="2" style="text-align: right;">' + pitem.x_sales_price + '</td>';

                let deldate = "";
                if (pitem.deldate != null)
                    deldate = pitem.deldate.split(' ')[0];

                let grdate = "";
                if (pitem.grdate != null)
                    grdate = pitem.grdate.split(' ')[0];

                cols += '<td class="td02" colspan="2" style="text-align: left;">' + deldate + '</td>';
                cols += '<td class="td02" colspan="2" style="text-align: right;">' + pitem.delqty + '</td>';
                cols += '<td class="td02" colspan="3" style="text-align: left;">' + grdate + '</td>';
                cols += '<td class="td02" colspan="3" style="text-align: right;">' + pitem.grqty + '</td>';


                @if ($groupByPO != 4)
                    cols += '<td colspan="1"></td>';
                @endif
                newRow.append(cols).hide();
                $(prevrow).after(newRow);
                if (i % 2 == 0)
                    newRow.attr('style', "background-color:#A0C0A0; vertical-align: middle;");
                else
                    newRow.attr('style', "background-color:#90D090; vertical-align: middle;");
                newRow.attr('id', "tr_I" + pitem.ebeln + "_" + pitem.ebelp);
                if (pitem.position_splittable == '1') {
                    let __inqSpan = $(newRow).find("#span_item").first();
                    __inqSpan.mouseover(function() {
                        $(this).css("color", "blue");
                        $(this).addClass("cursorpointer");
                    }).mouseout(function() {
                        $(this).css("color", "black");
                        $(this).removeClass("cursorpointer");
                    });
                    __inqSpan.click(function () {
                        split_dialog('1', __inqSpan, pitem.ebeln, pitem.ebelp, "Split item");
                    });
                }
            }
        }

        function getPOItemSubTree(currentrow, order, item, _data) {
            // PO item changes header
            var newRow = $("<tr>");
            var cols = "";
            var po_style = "background-color:" + $(currentrow).css("background-color") + ";";
            var color = $(currentrow).closest("tr").find(".coloured").css("background-color");
            var last_style = "background-color:" + color;
            var first_color = $(currentrow).closest("tr").find(".first_color").css("background-color");
            var first_style = "background-color:" + first_color;
            cols += '<td class="first_color" colspan="10" style="' + first_style + '"></td>';
            @if ($groupByPO == 4)
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
                @if ($groupByPO == 4)
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
                @if ($groupByPO != 4)
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

        function hideSubTree(thisbtn) {
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

        function conv_exit_alpha_output(input) {
            output = input;
            if (/^\d+$/.test(output)) {
                output = output.replace(/^0+/, '');
                if (output.length == 0) output = input;
            }
            return output;
        }

        function acceptPOrder(thisbtn) {
            var currentrow;
            let rowid = (currentrow = $(thisbtn).parent().parent()).attr('id').toUpperCase();
            let rowtype = rowid.substr(3, 1); // P
            let porder = rowid.substr(4, 10);
            let sorder = "";
            @if ($groupByPO == 4)
            let prevRow = $(currentrow).prev();
            while (prevRow.attr("id").substr(0, 4) != "tr_S") prevRow = $(prevRow).prev();
            sorder = $(prevRow).attr("id").substr(4, 10);
            @endif

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajaxSetup({async: false});
            var _dataAP, _statusAP;
            $.get("webservice/itemsOfOrder",
                {
                    type: rowtype,
                    order: porder,
                    history: $("filter_hhistory").val(),
                    @if($groupByPO == 4)
                        vbeln: sorder
                    @else
                        vbeln: null
                    @endif
                },
                function (data, status) {
                    _dataAP = data;
                    _statusAP = status;
                }, "json");
            jQuery.ajaxSetup({async: true});
            if (_statusAP != "success") return;
            if (_dataAP.length > 0) {
                for (let i = 0; i < _dataAP.length; i++) {
                    if (isChecked('I' + _dataAP[i].ebeln + "_"+_dataAP[i].ebelp)) {
                        _unused_acceptItem(porder, _dataAP[i].ebelp, null, false);
                    }
                }
                location.reload(true);
            }
        }

        function inquirePOrder(thisbtn) {
            var currentrow;
            let rowid = (currentrow = $(thisbtn).parent().parent()).attr('id').toUpperCase();
            let rowtype = rowid.substr(3, 1); // P
            let porder = rowid.substr(4, 10);
            @if ($groupByPO == 4)
            let prevRow = $(currentrow).prev();
            while (prevRow.attr("id").substr(0, 4) != "tr_S") prevRow = $(prevRow).prev();
            sorder = $(prevRow).attr("id").substr(4, 10);
            @endif
        }

        function acceptPItem(thisbtn) {
            var currentrow;
            let rowid = (currentrow = $(thisbtn).parent().parent()).attr('id').toUpperCase();
            let rowtype = rowid.substr(3, 1); // I
            let porder = rowid.substr(4, 10);
            let item = rowid.substr(15, 5);
            _unused_acceptItem(porder, item, null, true);
            location.reload(true);
        }

        function rejectSOrder(thisbtn, category, reason) {
            if (category == 0 || reason == null) {
                reject_init("S", thisbtn, "{{__('Reject sales order')}}");
                return;
            }

            var currentrow;
            let rowid = (currentrow = $(thisbtn).parent().parent()).attr('id').toUpperCase();
            let rowtype = rowid.substr(3, 1); // S
            let sorder = rowid.substr(4, 10);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajaxSetup({async: false});
            var _dataRS, _statusRS;
            $.get("webservice/itemsOfOrder",
                {
                    type: rowtype,
                    order: sorder,
                    history: $("filter_history").val()
                },
                function (data, status) {
                    _dataRS = data;
                    _statusRS = status;
                }, "json");
            jQuery.ajaxSetup({async: true});
            if (_statusRS != "success") return;
            if (_dataRS.length > 0) {
                for (let i = 0; i < _dataRS.length; i++) {
                    if (isChecked('I' + _dataRS[i].ebeln +"_" + _dataRS[i].ebelp)) {
                        doRejectItem(porder, _dataRS[i].ebelp, category, reason, 'X', 'Z');
                    }
                }
                location.reload(true);
            }
        }

        function rejectPOrder(thisbtn, category, reason) {
            if (category == 0 || reason == null) {
                reject_init("P", thisbtn, "{{__('Reject purchase order')}}");
                return;
            }
            var currentrow;
            let rowid = (currentrow = $(thisbtn).parent().parent()).attr('id').toUpperCase();
            let rowtype = rowid.substr(3, 1); // P
            let porder = rowid.substr(4, 10);
            @if ($groupByPO == 4)
            let prevRow = $(currentrow).prev();
            while (prevRow.attr("id").substr(0, 4) != "tr_S") prevRow = $(prevRow).prev();
            sorder = $(prevRow).attr("id").substr(4, 10);
            @endif
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajaxSetup({async: false});
            var _dataRP, _statusRP;
            $.get("webservice/itemsOfOrder",
                {
                    type: rowtype,
                    order: porder,
                    history: $("filter_history").val(),
                    @if($groupByPO == 4)
                    vbeln: sorder
                    @else
                    vbeln: null
                    @endif
                },
                function (data, status) {
                    _dataRP = data;
                    _statusRP = status;
                }, "json");
            jQuery.ajaxSetup({async: true});
            if (_statusRP != "success") return;
            if (_dataRP.length > 0) {
                for (let i = 0; i < _dataRP.length; i++) {
                    if (isChecked('I' + _dataRP[i].ebeln +'_' + _dataRP[i].ebelp)) {
                        doRejectItem(porder, _dataRP[i].ebelp, category, reason,
                                @if (\Illuminate\Support\Facades\Auth::user()->role == "Furnizor")
                                    'R', 'R'
                        @else
                            'X', 'Z'
                        @endif
                    );
                    }
                }
                location.reload(true);
            }
        }

        function rejectPItem(thisbtn, category, reason) {
            if (category == 0 && reason == null) {
                reject_init("I", thisbtn, "{{__('Reject item')}}");
                return;
            }
            var currentrow;
            let rowid = (currentrow = $(thisbtn).parent().parent()).attr('id').toUpperCase();
            let rowtype = rowid.substr(3, 1); // I
            let porder = rowid.substr(4, 10);
            let item = rowid.substr(15, 5);
            doRejectItem(porder, item, category, reason,
            @if (\Illuminate\Support\Facades\Auth::user()->role == "Furnizor")
                'R', 'R'
            @else
                'X', 'Z'
            @endif
            );
            location.reload(true);
        }

        function requestRejectPItem(thisbtn, mode) {
            var currentrow;
            let rowid = (currentrow = $(thisbtn).parent().parent()).attr('id').toUpperCase();
            let rowtype = rowid.substr(3, 1); // I
            let porder = rowid.substr(4, 10);
            let item = rowid.substr(15, 5);
            if (mode == 3) {
                swal({
                    title: "{{__('Please confirm you want to cancel this item')}}",
                    text: "{{__('You have issued earlier a confirmation for this item. Are you sure you want now to cancel it?')}}",
                    icon: 'warning',
                    buttons: ["{{__('No')}}", "{{__('This item will not be delivered')}}"],
                }).then(function(result) {
                    if (result) {
                        doRejectItem(porder, item, 'F', '', 'R', 'R');
                        location.reload(true);
                    }
                })
            } else if (mode == 4) {
                swal({
                    title: "{{__('Please confirm you want to request cancellation of this item')}}",
                    text: "{{__('The vendor has issued earlier a confirmation for this item. Are you sure you want now to request the vendor to cancel it?')}}",
                    icon: "warning",
                    buttons: ["{{__('No')}}", "{{__('Ask vendor to cancel this item')}}"],
                }).then(function(result) {
                    if (result) {
                        doRejectItem(porder, item, 'G', '', 'R', 'F');
                        location.reload(true);
                    }
                })

            }
        }

        function inquirePItem(thisbtn) {
            var currentrow;
            let rowid = (currentrow = $(thisbtn).parent().parent()).attr('id').toUpperCase();
            let rowtype = rowid.substr(3, 1); // I
            let porder = rowid.substr(4, 10);
            let item = rowid.substr(15, 5);

            @if (\Illuminate\Support\Facades\Auth::user()->role == "Furnizor")
                send_inquiry(porder, item);
            @else
                send_inquiry(porder, item);
            @endif
        }

        function acceptSOrder(thisbtn) {
            var currentrow;
            let rowid = (currentrow = $(thisbtn).parent().parent()).attr('id').toUpperCase();
            let rowtype = rowid.substr(3, 1); // S
            let sorder = rowid.substr(4, 10);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajaxSetup({async: false});
            var _dataAS, _statusAS;
            $.get("webservice/itemsOfOrder",
                {
                    type: rowtype,
                    order: sorder,
                    history: $("filter_history").val()
                },
                function (data, status) {
                    _dataAS = data;
                    _statusAS = status;
                }, "json");
            jQuery.ajaxSetup({async: true});
            if (_statusAS != "success") return;
            if (_dataAS.length > 0) {
                for (let i = 0; i < _dataAS.length; i++) {
                    if (isChecked('I' + _dataAS[i].ebeln + "_" +_dataAS[i].ebelp)) {
                        _unused_acceptItem(porder, _dataAS[i].ebelp, null, false);
                    }
                }
                location.reload(true);
            }
        }

        function inquireSOrder(thisbtn) {
            var currentrow;
            let rowid = (currentrow = $(thisbtn).parent().parent()).attr('id').toUpperCase();
            let rowtype = rowid.substr(3, 1); // S
            let sorder = rowid.substr(4, 10);
        }

        function inquireReply(thisbtn, mode) {
            var currentrow;
            let rowid = (currentrow = $(thisbtn).parent().parent()).attr('id').toUpperCase();
            let rowtype = rowid.substr(3, 1); // P sau I
            let porder = rowid.substr(4, 10);
            let item = rowid.substr(15, 5);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajaxSetup({async: false});
            var _dataIR, _statusIR;
            $.get("webservice/readpitem",
                {
                    order: porder,
                    item: item
                },
                function (data, status) {
                    _dataIR = data;
                    _statusIR = status;
                }, "json");
            jQuery.ajaxSetup({async: true});
            if (_statusIR != "success") return;

            @if (\Illuminate\Support\Facades\Auth::user()->role == "Furnizor")
            if (mode == 4) {
                swal({
                    title: "{{__('MATEROM has requested cancellation of this item')}}",
                    text: "{{__('This item was previously confirmed by you, but MATEROM asks you now to cancel it. Do you agree?')}}",
                    icon: 'warning',
                    buttons: ["{{__('No')}}", "{{__('Agree with cancellation')}}"]
                }).then(function(result) {
                    if (result) {
                        doRejectItem(porder, item, 'G', '', 'X', 'Z');
                        location.reload();
                    } else {
                        _unused_acceptItem(porder, item, "F", true);
                    }
                })
            }
            @elseif (\Illuminate\Support\Facades\Auth::user()->role == "Referent")
            if (mode == 1) {
                accept_reject_dialog(1, thisbtn, _dataIR, "Acceptare pozitie modificata", "Anumite campuri ale pozitiei au fost modificate - puteti accepta modificarile sau propune altele");
            }
            if (mode == 2) {
                accept_reject_dialog(2, thisbtn, _dataIR, "Rejectare pozitie", "Furnizorul a rejectat aceasta pozitie - puteti propune alte variante");
            }
            if (mode == 3) {
                split_dialog('2', thisbtn, porder, item, "Selectie/Split item");
                // select_proposal(mode, thisbtn, _dataIR, "Selectie split", "Furnizorul a efectuat spargerea pozitiei in mai multe materiale, selectati o decizie");
            }
            @elseif (\Illuminate\Support\Facades\Auth::user()->role == "CTV")
                if (mode != 3)
                select_proposal(mode, thisbtn, _dataIR, "Selectie propunere", "Furnizorul a cerut modificari ale conditiilor de aprovizionare - selectati una din propunerile referentului");
                else
                select_proposal(mode, thisbtn, _dataIR, "Decizie split item", "Furnizorul a efectuat spargerea pozitiei in mai multe materiale, decideti daca acceptati");
            @elseif (\Illuminate\Support\Facades\Auth::user()->role == "Administrator")
            if (_dataIR.stage == 'R') {
                if (mode == 1) {
                    accept_reject_dialog(1, thisbtn, _dataIR, "Acceptare pozitie modificata", "Anumite campuri ale pozitiei au fost modificate - puteti accepta modificarile sau propune altele");
                }
                if (mode == 2) {
                    accept_reject_dialog(2, thisbtn, _dataIR, "Rejectare pozitie", "Furnizorul a rejectat aceasta pozitie - puteti propune alte variante");
                }
            } else if (_dataIR.stage == 'C') {
                select_proposal(mode, thisbtn, _dataIR, "Selectie propunere", "Furnizorul a cerut modificari ale conditiilor de aprovizionare - selectati una din propunerile referentului");
            }
            @endif
        }

        function doChangeItem(c_type, c_value, c_value_hlp, old_value, c_ebeln, c_ebelp) {
            var c_string = "";
            let comma_count = 0;
            let dot_count = 0;
            if (c_value.indexOf(",") >= 0) comma_count = 1;
            if (c_value.indexOf(".") >= 0) dot_count = 1;
            switch (c_type) {
                case 1:
                    c_string = "idnlf";
                    break;
                case 3:
                    c_string = "qty";
                    if ((comma_count + dot_count) > 1) return false;
                    if (!(Math.floor(c_value) == c_value && $.isNumeric(c_value)) || c_value.startsWith('-'))
                        return false;
                    break;
                case 4:
                    c_string = "lfdat";
                    var d = new Date(c_value);
                    if (isNaN(d.valueOf()))
                        return false;
                    break;
                case 5:
                    c_string = "purch_price";
                    if ((comma_count + dot_count) > 1) return false;
                    if (comma_count == 1) c_value = c_value.replace(/,/g, '.');
                    if (!($.isNumeric(c_value)) || c_value.startsWith('-'))
                        return false;
                    break;
            }


            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var _dataC, _statusC;
            jQuery.ajaxSetup({async: false});
            $.post("webservice/dochangeitem",
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

        var change_cell, change_type, type_string, change_ebeln, change_ebelp, changeDialog, changeForm;

        $(function () {
            changeDialog = $("#change-dialog").dialog({
                autoOpen: false,
                height: 200,
                width: 400,
                modal: true,
                buttons: {
                    Change: function () {
                        let new_val = $("#new_chg_val").val().trim();
                        if (new_val.length > 0) {
                            if (doChangeItem(change_type, new_val, $("#new_val_hlp").text(),
                                change_cell.innerHTML, change_ebeln, change_ebelp)) {
                                change_cell.innerHTML = ($("#new_chg_val").val() + " " + $("#new_val_hlp").text()).trim();
                                $("#new_chg_val").text("");
                                $("#new_val_hlp").text("");
                                changeDialog.dialog("close");
                                location.reload();
                            }
                        } else {
                            alert("{{__('Enter a correct value')}}");
                        }
                    },
                    Cancel: function () {
                        changeDialog.dialog("close");
                    }
                },
                close: function () {
                    $("#new_chg_val").datepicker("destroy");
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
            $("#new_chg_val").val("");
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
            $("#new_chg_val").val("");
            $("#new_val_txt").text("Introduceti noua cantitate:");
            $("#new_val_hlp").text(values[1]);
            $("#change-dialog").dialog('option', 'title', 'Modificare cantitate pozitia ' + ebelp);
            changeDialog.dialog("open");
        }

        function change_delivery_date2(cell, ebeln, ebelp) {
            swal({
                title: "{{__('Please confirm changing the delivery date for this item')}}",
                text: "{{__('You have issued earlier a confirmation for this item. Are you sure you want now to change its delivery date? ')}}",
                icon: 'warning',
                buttons: ["{{__('No')}}", "{{__('Ask MATEROM to check a new delivery date')}}"],
            }).then(function(result) {
                if (result) {
                    change_delivery_date(cell, ebeln, ebelp)
                }
            })
        }

        function change_delivery_date(cell, ebeln, ebelp) {

            change_cell = cell;
            change_type = 4;
            let old_value = cell.innerHTML;
            change_ebeln = ebeln;
            change_ebelp = ebelp;

            type_string = "LFDAT";
            $("#old_chg_val").text("Data de livrare existenta: " + old_value);
            $("#new_chg_val").val("");
            $("#new_chg_val").datepicker({dateFormat: "yy-mm-dd"});
            $("#new_val_txt").text("Introduceti noua data de livrare:");
            $("#new_val_hlp").text("");
            $("#change-dialog").dialog('option', 'title', 'Modificare data livrare pentru pozitia ' + ebelp);
            changeDialog.dialog("open");
        }

        function change_purchase_price2(cell, ebeln, ebelp) {
            swal({
                title: "{{__('Please confirm changing the purchase price for this item')}}",
                text: "{{__('You have issued earlier a confirmation for this item. Are you sure you want now to change its purchase price? ')}}",
                icon: 'warning',
                buttons: ["{{__('No')}}", "{{__('Ask MATEROM to check a new purchase price')}}"],
            }).then(function(result) {
                if (result) {
                    change_purchase_price(cell, ebeln, ebelp)
                }
            })
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
            $("#new_chg_val").val("");
            $("#new_val_txt").text("Introduceti noul pret de achizitie:");
            $("#new_val_hlp").text(values[1]);
            $("#change-dialog").dialog('option', 'title', 'Modificare pret achizitie pentru pozitia ' + ebelp);
            changeDialog.dialog("open");
        }

    </script>

    <div id="change-dialog" title="Modificare pozitie">
        <form>
            <br>
            <div class="form-group container" align="left">
                <b id="old_chg_val"></b><br><br>
                <i id="new_val_txt"></i>
                <br><br>
                <table style="border: none; padding: 0px;" width="80%">
                    <tr>
                        <td><input id="new_chg_val" type="text" name="new_chg_val" size="20"
                                   class="form-control col-md-8" value=""></td>
                        <td style="text-align: left;" width="4rem"><b style="text-align: left;  margin-left: -5rem;"
                                                                      id="new_val_hlp"></b></td>
                    </tr>
                </table>
            </div>
        </form>
    </div>

    <div id="init-rejection-dialog" title="{{__("Reject item")}}">
        <form>
            <br>
            <div class="form-group container" align="left">
                <div class="row">
                    <label for="reject-category" class="col-md-2 col-form-label text-md-left">{{__("Reason")}}</label>&nbsp;&nbsp;
                    <select id="reject-category" name="reject-category" class="form-control col-md-9"
                            onchange="rejectCategoryChange(this);return false;">
                        <option value="1" selected>{{__("Reason 1")}}</option>
                        <option value="2">{{__("Reason 2")}}</option>
                        <option value="3">{{__("Miscellaneous")}}</option>
                        <option value="4">{{__("Other")}}</option>
                    </select>
                </div>
                <br>
                <div class="row">
                    <label for="reject-reason"
                           class="col-md-2 col-form-label text-md-left">{{__("Explanations")}}</label>&nbsp;&nbsp;
                    <textarea id="reject-reason" type="text" name="reject-reason" class="form-control col-md-9"
                              style="word-break: break-word; height: 4rem;" maxlength="100" value=""></textarea>
                </div>
            </div>

            <i id="new_rej_msg" style="color: red"></i>
        </form>
    </div>

    <script>
        var rejectDialog, rejectForm, _reject_type, _reject_this;

        function rejectCategoryChange(_this) {
            if (_this.value == 4)
                $("#reject-reason").attr('required', 'true');
            else
                $("#reject-reason").removeAttr('required');
        }

        $(function () {
            rejectDialog = $("#init-rejection-dialog").dialog({
                autoOpen: false,
                height: 320,
                width: 480,
                modal: true,
                buttons: {
                    {{__("Reject")}}:
                    function() {
                        if (!($("#reject-category").val() == 4 && $("#reject-reason").val().length == 0)) {
                            switch (_reject_type) {
                                case "S":
                                    rejectSOrder(_reject_this, $("#reject-category").val(), $("#reject-reason").val());
                                    break;
                                case "P":
                                    rejectPOrder(_reject_this, $("#reject-category").val(), $("#reject-reason").val());
                                    break;
                                case "I":
                                    rejectPItem(_reject_this, $("#reject-category").val(), $("#reject-reason").val());
                                    break;
                            }
                            rejectDialog.dialog("close");
                        }
                    },

                    {{__("Cancel")}}:
                    function() {
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

            @if (isset($autoexplode_SO) && $autoexplode_SO != null)
            let autoexplode_so_button = $('#butt_S{{$autoexplode_SO}}');
            if (autoexplode_so_button != null && autoexplode_so_button != undefined) {
                getSubTree(autoexplode_so_button[0]);
                autoexplode_so_button[0].scrollIntoView();
            }
            @endif

            @if (isset($autoexplode_PO) && $autoexplode_PO != null)
                let autoexplode_po_button = $('#butt_P{{$autoexplode_PO}}');
                if (autoexplode_po_button != null && autoexplode_po_button != undefined) {
                    getSubTree(autoexplode_po_button[0]);
                    autoexplode_po_button[0].scrollIntoView();
                }
            @endif

            @php
                \Illuminate\Support\Facades\Session::forget("autoexplode_PO");
                \Illuminate\Support\Facades\Session::forget("autoexplode_SO");
            @endphp
        });

        function reject_init(type, this0, title) {
            $("#new_rej_msg").text("");
            $("#reject-reason").val("");
            $("#init-rejection-dialog").dialog('option', 'title', title);
            _reject_type = type;
            _reject_this = this0;
            rejectDialog.dialog("open");
        }

        function readLifnrName(lifnr) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajaxSetup({async: false});
            var _dataLN, _statusLN;
            $.get("webservice/readlifnrname",
                {
                    lifnr: lifnr
                },
                function (data, status) {
                    _dataLN = data;
                    _statusLN = status;
                });
            jQuery.ajaxSetup({async: true});
            if (_statusLN != "success") return;
            return _dataLN;
        }

        function replyack(ebeln, ebelp) {

            var _data, _status = "";

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajaxSetup({async: false});
            $.post("webservice/sendAck",
                {
                    ebeln: ebeln,
                    ebelp: ebelp,
                    cdate: null
                },
                function (data, status) {
                    _data = data;
                    _status = status;
                });
            jQuery.ajaxSetup({async: true});
            $("#tr_I" + ebeln + "_" + ebelp + " td:eq(1)").html("");
        }

        function replyack2(ebeln) {
            var _data, _status = "";
        }

    </script>

    @include("orders.read_inforecords")
    @include("orders.read_zpretrecords")
    @include("orders.updaterow")
    @include("orders.accept-reject")
    @include("orders.split-item")
    @include("orders.inquiries")

@endsection