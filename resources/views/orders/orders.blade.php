@extends('layouts.app')

@section('content')
    @guest
        @php
            header("Location: /");
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
        if(isset($tmp) && !empty($tmp) && $filter_history == 2) $filter_time_val = $tmp;

        if (\Illuminate\Support\Facades\Auth::user()->role != 'CTV') {
            $filter_inquirements = 0;
            $inquirements_checked = "";
            unset($tmp);
            $tmp = \Illuminate\Support\Facades\Session::get("filter_inquirements");
            if (isset($tmp)) $filter_inquirements = intval($tmp);
            if ($filter_inquirements == 1) $inquirements_checked = "checked";
        } else {
            $filter_inquirements = "0";
            unset($tmp);
            $tmp = \Illuminate\Support\Facades\Session::get("filter_inquirements");
            $filter_inquirements_sel0 = "";
            $filter_inquirements_sel1 = "";
            $filter_inquirements_sel2 = "";
            if (isset($tmp)) {
                if ($tmp == "0") {
                    $filter_inquirements_sel0 = "selected";
                    $filter_inquirements = "0";
                } elseif ($tmp == "1") {
                    $filter_inquirements_sel1 = "selected";
                    $filter_inquirements = "1";
                } elseif ($tmp == "2")  {
                    $filter_inquirements_sel2 = "selected";
                    $filter_inquirements = "2";
                }
            }
        }

        unset($tmp);
        $tmp = \Illuminate\Support\Facades\Session::get("filter_backorders");
        if ($tmp != "1" && $tmp != "2") $tmp = "0";
        $filter_backorders = $tmp;
        $filter_backorders_0 = "";
        if ($tmp == "0") $filter_backorders_0 = "selected";
        $filter_backorders_1 = "";
        if ($tmp == "1") $filter_backorders_1 = "selected";
        $filter_backorders_2 = "";
        if ($tmp == "2") $filter_backorders_2 = "selected";

        unset($tmp);
        $tmp = \Illuminate\Support\Facades\Session::get("filter_eta_delayed");
        if ($tmp != "1" && $tmp != "2" && $tmp != "3") $tmp = "0";
        $filter_eta_delayed = $tmp;
        $filter_eta_delayed_0 = "";
        if ($tmp == "0") $filter_eta_delayed_0 = "selected";
        $filter_eta_delayed_1 = "";
        if ($tmp == "1") $filter_eta_delayed_1 = "selected";
        $filter_eta_delayed_2 = "";
        if ($tmp == "2") $filter_eta_delayed_2 = "selected";
        $filter_eta_delayed_3 = "";
        if ($tmp == "3") $filter_eta_delayed_3 = "selected";
        $filter_eta_delayed_date = "";
        unset($tmp);
        $tmp = \Illuminate\Support\Facades\Session::get("filter_eta_delayed_date");
        if(isset($tmp) && !empty($tmp) && $filter_history == 1) $filter_eta_delayed_date = $tmp;

        unset($tmp);
        $tmp = \Illuminate\Support\Facades\Session::get("filter_eta");
        if ($tmp != "1" && $tmp != "2") $tmp = "0";
        $filter_eta = $tmp;
        $filter_eta_0 = "";
        if ($tmp == "0") $filter_eta_0 = "selected";
        $filter_eta_1 = "";
        if ($tmp == "1") $filter_eta_1 = "selected";
        $filter_eta_2 = "";
        if ($tmp == "2") $filter_eta_2 = "selected";

        $filter_overdue = 0;
        $overdue_checked = "";
        unset($tmp);
        $tmp = \Illuminate\Support\Facades\Session::get("filter_overdue");
        if (isset($tmp)) $filter_overdue = intval($tmp);
        if ($filter_overdue == 1) $overdue_checked = "checked";
        $filter_overdue_low = "";
        unset($tmp);
        $tmp = \Illuminate\Support\Facades\Session::get("filter_overdue_low");
        if (isset($tmp)) $filter_overdue_low = intval($tmp);
        if ($filter_overdue_low <= 0) $filter_overdue_low = "";
        $filter_overdue_high = "";
        unset($tmp);
        $tmp = \Illuminate\Support\Facades\Session::get("filter_overdue_high");
        if (isset($tmp)) $filter_overdue_high = intval($tmp);
        if ($filter_overdue_high <= 0) $filter_overdue_high = "";

        unset($tmp);
        $tmp = \Illuminate\Support\Facades\Session::get("filter_goodsreceipt");
        if (($tmp != "1") && ($tmp != "2")) $tmp = "0";
        $filter_goodsreceipt = $tmp;
        $filter_goodsreceipt_0 = "";
        if ($tmp == "0") $filter_goodsreceipt_0 = "selected";
        $filter_goodsreceipt_1 = "";
        if ($tmp == "1") $filter_goodsreceipt_1 = "selected";
        $filter_goodsreceipt_2 = "";
        if ($tmp == "2") $filter_goodsreceipt_2 = "selected";

        $filter_deldate_low = "";
        $tmp = \Illuminate\Support\Facades\Session::get("filter_deldate_low");
        if(isset($tmp) && !empty($tmp) && $filter_history == 1) $filter_deldate_low = $tmp;
        $filter_deldate_high = "";
        $tmp = \Illuminate\Support\Facades\Session::get("filter_deldate_high");
        if(isset($tmp) && !empty($tmp) && $filter_history == 1) $filter_deldate_high = $tmp;

        $filter_etadate_low = "";
        $tmp = \Illuminate\Support\Facades\Session::get("filter_etadate_low");
        if(isset($tmp) && !empty($tmp) && $filter_history == 1) $filter_etadate_low = $tmp;
        $filter_etadate_high = "";
        $tmp = \Illuminate\Support\Facades\Session::get("filter_etadate_high");
        if(isset($tmp) && !empty($tmp) && $filter_history == 1) $filter_etadate_high = $tmp;

        $filter_vbeln = \Illuminate\Support\Facades\Session::get("filter_vbeln");
        if (!isset($filter_vbeln)) $filter_vbeln = "";

        $filter_klabc = \Illuminate\Support\Facades\Session::get("filter_klabc");
        if (!isset($filter_klabc) || empty($filter_klabc)) $filter_klabc = "*";
        $filter_klabc_all = "";
        $filter_klabc_none = "";
        $filter_klabc_a = ""; $filter_klabc_a_tooltip = __("Client clasificare A");
        $filter_klabc_b = ""; $filter_klabc_b_tooltip = __("Client clasificare B");
        $filter_klabc_c = ""; $filter_klabc_c_tooltip = __("Client clasificare C");
        $filter_klabc_d = ""; $filter_klabc_d_tooltip = __("Client clasificare D");
        $filter_klabc_n = ""; $filter_klabc_n_tooltip = __("Client clasificare N");
        if ($filter_klabc == "*") {
            // toate
            $filter_klabc_all = "selected";
        } elseif ($filter_klabc == "<>") {
            // empty
            $filter_klabc_none = "selected";
        }  elseif ($filter_klabc == "A") {
            $filter_klabc_a = "selected";
        }  elseif ($filter_klabc == "B") {
            $filter_klabc_b = "selected";
        }  elseif ($filter_klabc == "C") {
            $filter_klabc_c = "selected";
        }  elseif ($filter_klabc == "D") {
            $filter_klabc_d = "selected";
        }  elseif ($filter_klabc == "N") {
            $filter_klabc_n = "selected";
        }

        $filter_ebeln = \Illuminate\Support\Facades\Session::get("filter_ebeln");
        if (!isset($filter_ebeln)) $filter_ebeln = "";

        $filter_matnr = \Illuminate\Support\Facades\Session::get("filter_matnr");
        if (!isset($filter_matnr)) $filter_matnr = "";

        $filter_mtext = \Illuminate\Support\Facades\Session::get("filter_mtext");
        if (!isset($filter_mtext) || is_null($filter_mtext)) $filter_mtext = "";

        $filter_ekgrp = \Illuminate\Support\Facades\Session::get("filter_ekgrp");
        if (!isset($filter_ekgrp) || is_null($filter_ekgrp)) $filter_ekgrp = "";

        $filter_lifnr = \Illuminate\Support\Facades\Session::get("filter_lifnr");
        if (!isset($filter_lifnr) || is_null($filter_lifnr)) $filter_lifnr = "";

        $filter_lifnr_name = \Illuminate\Support\Facades\Session::get("filter_lifnr_name");
        if (!isset($filter_lifnr_name) || is_null($filter_lifnr_name)) $filter_lifnr_name = "";

        $filter_kunnr = \Illuminate\Support\Facades\Session::get("filter_kunnr");
        if (!isset($filter_kunnr) || is_null($filter_kunnr)) $filter_kunnr = "";

        $filter_kunnr_name = \Illuminate\Support\Facades\Session::get("filter_kunnr_name");
        if (!isset($filter_kunnr_name) || is_null($filter_kunnr_name)) $filter_kunnr_name = "";

        $filter_mfrnr_text = \Illuminate\Support\Facades\Session::get("filter_mfrnr_text");
        if (!isset($filter_mfrnr_text) || is_null($filter_mfrnr_text)) $filter_mfrnr_text = "";

        $filter_pnad_active = 0;

        $filter_pnad_status = 0;
        unset($tmp);
        $tmp = \Illuminate\Support\Facades\Session::get("filter_pnad_status");
        if (isset($tmp) && !is_null($tmp)) $filter_pnad_status = intval($tmp);
        if (($filter_pnad_status != "0") &&
            ($filter_pnad_status != "1") &&
            ($filter_pnad_status != "2"))
            $filter_pnad_status = 0;
        if ($filter_pnad_status != 0) $filter_pnad_active = 1;

        $filter_pnad_type = 0;
        unset($tmp);
        $tmp = \Illuminate\Support\Facades\Session::get("filter_pnad_type");
        if (isset($tmp) && !is_null($tmp)) $filter_pnad_type = intval($tmp);
        if (($filter_pnad_type != "0") &&
            ($filter_pnad_type != "1") &&
            ($filter_pnad_type != "2") &&
            ($filter_pnad_type != "3") &&
            ($filter_pnad_type != "4"))
            $filter_pnad_type = 0;
        if ($filter_pnad_type != 0) $filter_pnad_active = 1;

        $filter_pnad_mblnr = "";
        unset($tmp);
        $tmp = \Illuminate\Support\Facades\Session::get("filter_pnad_mblnr");
        if (isset($tmp) && !is_null($tmp)) $filter_pnad_mblnr = $tmp;
        if (!empty($filter_pnad_mblnr)) $filter_pnad_active = 1;

        $filter_mirror = 0;
        $mirror_checked = "";
        unset($tmp);
        $tmp = \Illuminate\Support\Facades\Session::get("filter_mirror");
        if (isset($tmp)) $filter_mirror = intval($tmp);
        if ($filter_mirror == 1) $mirror_checked = "checked";

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

        $suppliers =  App\Materom\Orders::getSupplierList();
        $supplier_list = "[]";
        if (($suppliers != null) && count($suppliers) > 0) {
            $supplier_list = "[";
            foreach($suppliers as $lifnr => $supplier) {
                $supplier_list .= "{lifnr:\"$lifnr\",lifnr_name:\"$supplier->lifnr_name\",orders:[";
                $order_list = "";
                foreach ($supplier->orders as $order)
                    $order_list .= "\"$order\",";
                $supplier_list .= substr($order_list, 0, -1) . "]},";
            }
            $supplier_list = substr($supplier_list, 0, -1);
            $supplier_list .= "]";
        }

    @endphp
    <div class="container-fluid">
        <input type="hidden" id="filter_history" value="{{$filter_history}}">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header" style="border-bottom-width: 0px;">
                        @if (\Illuminate\Support\Facades\Auth::user()->role == "Administrator" && \Illuminate\Support\Facades\Auth::user()->readonly != 1)
                            <a href="/roles">
                                <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;" class="card-line first">
                                    <image style='height: 2.2rem; margin-left: -1.5rem;' src='/images/icons8-administrative-tools-48.png'/>
                                    {{__("Roles")}}
                                </p>
                            </a>
                            <a href="/users">
                                <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;" class="card-line">
                                    <image style='height: 2.2rem; margin-left: -1.5rem;' src='/images/icons8-user-account-80.png'/>
                                    {{__("Users")}}
                                </p>
                            </a>
                            <a href="/messages">
                                <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;" class="card-line">
                                    <image style='height: 2.2rem; margin-left: -1.5rem;' src='/images/icons8-chat-80.png'/>
                                    {{__("Messages")}}{!!$message_svg!!}
                                </p>
                            </a>
                            <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;"
                               class="card-line selector">
                                <image style='height: 2.2rem; margin-left: -1.5rem;' src='/images/icons8-todo-list-96.png'/>
                                {{__("Orders")}}
                            </p>
                        @else
                            @if (\Illuminate\Support\Facades\Auth::user()->role == "CTV" && \Illuminate\Support\Facades\Auth::user()->ctvadmin == 1)
                                <a href="/users">
                                    <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;" class="card-line first">
                                        <image style='height: 2.2rem; margin-left: -1.5rem;' src='/images/icons8-user-account-80.png'/>
                                        {{__("Users")}}
                                    </p>
                                </a>
                                <a href="/messages">
                                    <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;" class="card-line">
                                        <image style='height: 2.2rem; margin-left: -1.5rem;' src='/images/icons8-chat-80.png'/>
                                        {{__('Messages')}}{!!$message_svg!!}
                                    </p>
                                </a>
                            @else
                                <a href="/messages">
                                    <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;" class="card-line first">
                                        <image style='height: 2.2rem; margin-left: -1.5rem;' src='/images/icons8-chat-80.png'/>
                                        {{__('Messages')}}{!!$message_svg!!}
                                    </p>
                                </a>
                            @endif
                            <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;" class="card-line selector">
                                <image style='height: 2.2rem; margin-left: -1.5rem;' src='/images/icons8-todo-list-96.png'/>
                                {{__("Orders")}}
                            </p>
                        @endif
                        @if(\Illuminate\Support\Facades\Auth::user()->role != "CTV")
                            <a href="/stats">
                                <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;" class="card-line">
                                    <image style='height: 2.2rem; margin-left: -1.5rem;' src='/images/icons8-area-chart-64.png'/>
                                    {{__("Statistics")}}
                                </p>
                            </a>
                        @endif
                    </div>
                    <div class="card-body" style="padding-bottom: 0px; padding-left: 0.5rem;">
                        <div style="{{$background_color}} border: 1px solid black; border-radius: 0.5rem; padding: 4px; height: 8.9rem;">
                            <form action="orders" method="post" onsubmit="orders_submit()">
                                {{csrf_field()}}
                                <div class="container" style="display: block; max-width: 100%;">
                                    <table style="border: none; width: 100%;">
                                        <colgroup>
                                            <col style="width:10rem;">
                                            <col style="width:16rem;">
                                            <col style="width:12rem;">
                                            <col style="width:18rem;">
                                            <col style="width:10rem;">
                                            <col style="width:14rem;">
                                            <col style="width:10rem;">
                                            <col style="width:25rem;">
                                            <col>
                                            <col style="width: 120px;">
                                        </colgroup>
                                        <tbody>
                                            <tr style="height: 1.5rem;">
                                                <td>
                                                    {{__('Show by')}}:
                                                </td>
                                                <td>
                                                    <select class="form-control-sm input-sm" style="height: 1.4rem; padding: 2px;"
                                                            name="groupOrdersBy" onchange="orders_submit();this.form.submit()">
                                                        <option value="1"{{$groupBySelPOa}}>{{__('Purchase orders (urgent & stock)')}}</option>
                                                        <option value="2"{{$groupBySelPOu}}>{{__('Purchase orders (urgent)')}}</option>
                                                        <option value="3"{{$groupBySelPOs}}>{{__('Purchase orders (stock)')}}</option>
                                                        <option value="4"{{$groupBySelSO}}>{{__('Sales orders')}}</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    {{__('Displayed orders')}}:
                                                </td>
                                                <td>
                                                    <select class="form-control-sm input-sm" style="height: 1.4rem; padding: 2px;"
                                                            name="filter_history" onchange="orders_submit();this.form.submit()">
                                                        <option value="1"{{$filter_history_curr}}>{{__("Unprocessed")}}</option>
                                                        <option value="2"{{$filter_history_arch}}>{{__("Processed")}}</option>
                                                    </select>

                                                    @if ((\Illuminate\Support\Facades\Auth::user()->role == "Referent" || \Illuminate\Support\Facades\Auth::user()->role == "Administrator") && ($filter_history_curr != ""))
                                                        <button title="Open PNAD filter dialog" type="button" id="show-pnad-filters"
                                                                @if ($filter_pnad_active == 1)
                                                                class="background-image-filters"
                                                                @endif
                                                                style="margin-left: 2px; height: 1.3rem; vertical-align: top;" id="pnad-filter-button"
                                                                onclick='showPNADFilters(event, this);return false;'/>
                                                                @if ($filter_pnad_active == 1)
                                                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                                                @endif
                                                                {{__('Filtre PNAD')}}
                                                        </button>
                                                        <input type="hidden" id="filter_pnad_status" name="filter_pnad_status" value="{{$filter_pnad_status}}">
                                                        <input type="hidden" id="filter_pnad_type" name="filter_pnad_type" value="{{$filter_pnad_type}}">
                                                        <input type="hidden" id="filter_pnad_mblnr" name="filter_pnad_mblnr" value="{{$filter_pnad_mblnr}}">
                                                    @endif
                                                </td>
                                                <td>
                                                    {{__("Purchase order")}}:
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control-sm input-sm"
                                                           style="width: 6rem; height: 1.4rem;" maxlength="10" name="filter_ebeln"
                                                           value="{{$filter_ebeln}}">&nbsp;&nbsp;
                                                </td>
                                                <td>
                                                    @if (\Illuminate\Support\Facades\Auth::user()->role != "Furnizor")
                                                        {{__("Sales order")}}:
                                                     @endif
                                                </td>
                                                <td>
                                                    @if (\Illuminate\Support\Facades\Auth::user()->role != "Furnizor")
                                                        <input type="text" class="form-control-sm input-sm"
                                                               style="width: 6rem; height: 1.4rem;" maxlength="10" name="filter_vbeln"
                                                               value="{{$filter_vbeln}}">&nbsp;&nbsp;
                                                        &nbsp;&nbsp;{{__("Classif")}}:
                                                        <select class="form-control-sm input-sm" style="height: 1.4rem; padding: 2px;"
                                                                name="filter_klabc" onchange="orders_submit();this.form.submit(); return false;">
                                                            <option value="*"{{$filter_klabc_all}}>{{__('All')}}</option>
                                                            <option value="<>"{{$filter_klabc_none}}>{{__('None')}}</option>
                                                            <option value="A"{{$filter_klabc_a}}>{{'A'}}</option>
                                                            <option value="B"{{$filter_klabc_b}}>{{'B'}}</option>
                                                            <option value="C"{{$filter_klabc_c}}>{{'C'}}</option>
                                                            <option value="D"{{$filter_klabc_d}}>{{'D'}}</option>
                                                            <option value="N"{{$filter_klabc_c}}>{{'N'}}</option>
                                                        </select>
                                                     @endif
                                                </td>
                                                <td>
                                                </td>
                                                <td>
                                                    @if (\Illuminate\Support\Facades\Auth::user()->role != "CTV")
                                                    <button title="Export purchase orders data to Excel" type="button" style="margin-left: 2px; height: 1.5rem;"
                                                            onclick="downloadXLSFile(1);return false;">{{__('XLS Export')}}</button>
                                                    @endif
                                                    @if (\Illuminate\Support\Facades\Auth::user()->id == "radu" && 1 == 2)
                                                        <button type="button" onclick="debug_job();return false;">d</button>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr style="height: 1.5rem;">
                                                <td>
                                                    {{__('Filter by status')}}:
                                                </td>
                                                <td>
                                                    <select class="form-control-sm input-sm" style="height: 1.4rem; padding: 2px; width: 6em;"
                                                            name="filter_status" onchange="orders_submit();this.form.submit(); return false;">
                                                        <option value="NA"{{$filter_status_selNA}}>{{__('All')}}</option>
                                                        <option value="AP"{{$filter_status_selAP}}>{{__('Approved')}}</option>
                                                        <option value="RE"{{$filter_status_selRE}}>{{__('Rejected')}}</option>
                                                    </select>
                                                    &nbsp;&nbsp;
                                                    @if (\Illuminate\Support\Facades\Auth::user()->role != "CTV")
                                                        <input type="checkbox" id="filter_inquirements" style="margin-bottom: 4px; align-self: center; vertical-align: middle; height: 1rem;" name="filter_inquirements" onchange="orders_submit();this.form.submit();" {{$inquirements_checked}}>
                                                        <label for="filter_inquirements" style="margin-top: 0; margin-bottom: 3px; align-self: center; vertical-align: middle;">{{__('Only inquirements')}}</label>
                                                    @else
                                                        <select class="form-control-sm input-sm" style="height: 1.4rem; padding: 2px;"
                                                                id="filter_inquirements" name="filter_inquirements" onchange="orders_submit();this.form.submit(); return false;">
                                                            <option value="0"{{$filter_inquirements_sel0}}>{{__('All (no filtering)')}}</option>
                                                            <option value="1"{{$filter_inquirements_sel1}}>{{__('Only inquirements')}}</option>
                                                            <option value="2"{{$filter_inquirements_sel2}}>{{__('Only notifications')}}</option>
                                                        </select>
                                                    @endif

                                                </td>
                                                @if ($filter_history == 2)
                                                    <td>
                                                        {{__('Documents archived since')}}:
                                                    </td>
                                                    <td>
                                                        <input type="text" id="time_search" class="form-control-sm"
                                                               style="height:1.4rem; width: 6rem;" name="time_search"
                                                               value="{{$filter_time_val}}"
                                                               onchange="orders_submit();this.form.submit();">
                                                    </td>
                                                @else
                                                    <td colspan="2">
                                                        <input type="checkbox" id="filter_overdue" name="filter_overdue" style="margin-bottom: 4px; align-self: center; vertical-align: middle; height: 1rem;" onchange="orders_submit();this.form.submit();" {{$overdue_checked}}>
                                                        <label for="filter_overdue" style="margin-top: 0; margin-bottom: 3px; align-self: center; vertical-align: middle;">{{__('Only overdue deliveries') . ' (' . \App\Materom\Orders::overdues() . ')'}}</label>&nbsp;
                                                        <input type="text" class="form-control-sm input-sm" onkeyup="this.value=this.value.replace(/[^\d]+/,'')"
                                                               style="width: 2.2rem; height: 1.4rem;" name="filter_overdue_low"
                                                               maxlength="2" value="{{$filter_overdue_low}}">&nbsp;-
                                                        <input type="text" class="form-control-sm input-sm" onkeyup="this.value=this.value.replace(/[^\d]+/,'')"
                                                               style="width: 2.2rem; height: 1.4rem;" name="filter_overdue_high"
                                                               maxlength="2" value="{{$filter_overdue_high}}">
                                                    </td>
                                                @endif
                                                <td>
                                                    {{__("Referent")}}:
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control-sm input-sm" title="{{__('Filtrare dupa codul referentului')}}"
                                                           style="width: 3rem; height: 1.4rem;" maxlength="3" name="filter_ekgrp"
                                                           value="{{$filter_ekgrp}}">
                                                </td>
                                                <td>
                                                    @if ((\Illuminate\Support\Facades\Auth::user()->role != "Furnizor") && (\Illuminate\Support\Facades\Auth::user()->role != "CTV"))
                                                        {{__("Supplier")}}:
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ((\Illuminate\Support\Facades\Auth::user()->role != "Furnizor") && (\Illuminate\Support\Facades\Auth::user()->role != "CTV"))
                                                        <input type="text" class="form-control-sm input-sm"  title="{{__('Filtrare dupa codul furnizorului')}}"
                                                               style="width: 6rem; height: 1.4rem;" maxlength="10" name="filter_lifnr"
                                                               value="{{$filter_lifnr}}">&nbsp;&nbsp;
                                                        <input type="text" class="form-control-sm input-sm"  title="{{__('Filtrare dupa numele furnizorului')}}"
                                                               style="width: 10rem; height: 1.4rem;" maxlength="20" name="filter_lifnr_name"
                                                               value="{{$filter_lifnr_name}}">
                                                    @endif
                                                </td>
                                                <td>
                                                </td>
                                                <td>
                                                    <button title="Mass change operations on order items" type="button" style="margin-left: 2px; height: 1.5rem;" id="mass-change-menu-button"
                                                    onclick='massChangeMenu(event, this);return false;'/>{{__('Mass changes')}}</button>
                                                </td>
                                            </tr>
                                            <tr style="height: 1.5rem;">
                                                <td colspan="1" style="padding-top: 0.4rem;">
                                                    {{__('Backorders')}}:
                                                </td>

                                                <td colspan="1" style="padding-top: 0.4rem;">
                                                    @if (\Illuminate\Support\Facades\Auth::user()->role != "Furnizor")
                                                        <input type="checkbox" id="filter_mirror" name="filter_mirror" style="display: none; margin-bottom: 4px; align-self: center; vertical-align: middle; height: 1rem;" onchange="orders_submit();this.form.submit();" {{$mirror_checked}}>
                                                        <label for="filter_mirror" style="display: none; margin-top: 0; margin-bottom: 3px; align-self: center; vertical-align: middle;">{{__('Only intercompany')}}</label>
                                                    @endif
                                                    <select class="form-control-sm input-sm" style="height: 1.4rem; padding: 2px;"
                                                            name="filter_backorders" onchange="orders_submit();this.form.submit(); return false;">
                                                        <option value="0"{{$filter_backorders_0}}>{{__('Nicio filtrare')}}</option>
                                                        <option value="1"{{$filter_backorders_1}}>{{__('Doar backorders')}}</option>
                                                        <option value="2"{{$filter_backorders_2}}>{{__('Fara backorders')}}</option>
                                                    </select>
                                                </td>

                                                <td colspan="1">
                                                    <label for="filter_goodsreceipt" style="margin-top: 0; margin-bottom: 3px; align-self: center; vertical-align: middle;">{{__('Deliveries')}}</label>
                                                </td>
                                                <td colspan="1">
                                                    <select id="filter_goodsreceipt" class="form-control-sm input-sm" style="height: 1.4rem; padding: 2px;"
                                                            name="filter_goodsreceipt" onchange="orders_submit();this.form.submit(); return false;">
                                                        <option value="0"{{$filter_goodsreceipt_0}}>{{__('Nicio filtrare')}}</option>
                                                        <option value="1"{{$filter_goodsreceipt_1}}>{{__('Doar cu intrare de bunuri')}}</option>
                                                        <option value="2"{{$filter_goodsreceipt_2}}>{{__('Fara intrare de bunuri')}}</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    {{__("Material")}}:
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control-sm input-sm"
                                                           style="width: 6rem; height: 1.4rem;" maxlength="15" name="filter_matnr"
                                                           value="{{$filter_matnr}}">&nbsp;&nbsp;
                                                </td>
                                                <td>
                                                    @if (\Illuminate\Support\Facades\Auth::user()->role != "Furnizor")
                                                        {{__("Customer")}}:
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (\Illuminate\Support\Facades\Auth::user()->role != "Furnizor")
                                                        <input type="text" class="form-control-sm input-sm"
                                                               style="width: 6rem; height: 1.4rem;" maxlength="10" name="filter_kunnr"
                                                               value="{{$filter_kunnr}}">&nbsp;&nbsp;
                                                        <input type="text" class="form-control-sm input-sm"
                                                               style="width: 10rem; height: 1.4rem;" maxlength="20" name="filter_kunnr_name"
                                                               value="{{$filter_kunnr_name}}">&nbsp;&nbsp;
                                                    @endif
                                                </td>
                                                <td>
                                                </td>
                                                <td>
                                                </td>
                                            </tr>
                                            <tr style="height: 1.5rem;">
                                                <td>
                                                    @if (($filter_history != 2) && ($filter_backorders != 2))
                                                        {{__('Delay dlv/ETA check')}}:
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (($filter_history != 2) && ($filter_backorders != 2))
                                                        <select class="form-control-sm input-sm" style="height: 1.4rem; padding: 2px;"
                                                                name="filter_eta_delayed" onchange="orders_submit();this.form.submit(); return false;">
                                                            <option value="0"{{$filter_eta_delayed_0}}>{{__('Nicio filtrare')}}</option>
                                                            <option value="1"{{$filter_eta_delayed_1}}>{{__('Fara mascate')}}</option>
                                                            <option value="2"{{$filter_eta_delayed_2}}>{{__('Mascate pana la')}}</option>
                                                            <option value="3"{{$filter_eta_delayed_3}}>{{__('Mascate dupa')}}</option>
                                                        </select>
                                                        @if ($filter_eta_delayed == 2 || $filter_eta_delayed == 3)
                                                            <input type="text" id="filter_eta_delayed_date" class="form-control-sm"
                                                                   style="height:1.4rem; width: 6rem;" name="filter_eta_delayed_date"
                                                                   value="{{$filter_eta_delayed_date}}">
                                                        @endif
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($filter_history != 2)
                                                        {{__('Delivery date')}}:
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($filter_history != 2)
                                                        <input type="text" id="filter_deldate_low" class="form-control-sm"
                                                               style="height:1.4rem; width: 6rem;" name="filter_deldate_low"
                                                               value="{{$filter_deldate_low}}">&nbsp;-
                                                        <input type="text" id="filter_deldate_high" class="form-control-sm"
                                                               style="height:1.4rem; width: 6rem;" name="filter_deldate_high"
                                                               value="{{$filter_deldate_high}}">
                                                    @endif
                                                </td>
                                                <td>
                                                    {{__("Material description")}}:
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control-sm input-sm"
                                                           style="width: 10rem; height: 1.4rem;" maxlength="20" name="filter_mtext"
                                                           value="{{$filter_mtext}}">&nbsp;&nbsp;
                                                </td>
                                                <td>
                                                </td>
                                                <td>
                                                </td>
                                                <td>
                                                </td>
                                                <td>
                                                    <button type="button" style="margin-left: 2px; height: 1.5rem; "
                                                            onclick="search_document();return false;">{{__('Search document')}}</button>
                                                </td>
                                            </tr>
                                            <tr style="height: 1.5rem;">
                                                <td>
                                                    @if ($filter_history != 2)
                                                        {{__('ETA')}}:
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($filter_history != 2)
                                                        <select class="form-control-sm input-sm" style="height: 1.4rem; padding: 2px;"
                                                                name="filter_eta" onchange="orders_submit();this.form.submit(); return false;">
                                                            <option value="0"{{$filter_eta_0}}>{{__('Nicio filtrare')}}</option>
                                                            <option value="1"{{$filter_eta_1}}>{{__('Fara ETA depasit')}}</option>
                                                            <option value="2"{{$filter_eta_2}}>{{__('Doar cu ETA depasit')}}</option>
                                                        </select>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($filter_history != 2)
                                                        {{__('ETA')}}:
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($filter_history != 2)
                                                        <input type="text" id="filter_etadate_low" class="form-control-sm"
                                                               style="height:1.4rem; width: 6rem;" name="filter_etadate_low"
                                                               value="{{$filter_etadate_low}}">&nbsp;-
                                                        <input type="text" id="filter_etadate_high" class="form-control-sm"
                                                               style="height:1.4rem; width: 6rem;" name="filter_etadate_high"
                                                               value="{{$filter_etadate_high}}">
                                                    @endif
                                                </td>
                                                <td>
                                                    {{__("Fabricant")}}:
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control-sm input-sm"
                                                           style="width: 10rem; height: 1.4rem;" maxlength="20" name="filter_mfrnr_text"
                                                           value="{{$filter_mfrnr_text}}">&nbsp;&nbsp;
                                                </td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td>
                                                    <button type="button" style="margin-left: 2px; height: 1.5rem; "
                                                            onclick="reset_filters();return false;">{{__('Reset')}}</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <input type="submit" style="position: absolute; left: -9999px; width: 1px; height: 1px;"
                                       tabindex="-1">
                            </form>
                        </div>
                    </div>
                    <div style="margin-left: -0.7rem; overflow: scroll;">
                        <div class="card-body orders-table-div" style="height: 65.5vh; padding-top: 0px; padding-right: 4px; width: 150%;">
                            <table style="table-layout: fixed;"
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
                                    <col style="width:1.6%;">

                                    <col style="width:2%;">
                                    <col style="width:4%;">
                                    <col style="width:3.5%;">
                                    <col style="width:4%;">
                                    <col style="width:4%;">
                                    <col style="width:2.8%;">
                                    <col style="width:2.8%;">
                                    <col style="width:2.8%;">
                                    <col style="width:2.8%;">
                                    <col style="width:3.0%;"> <!-- 31.5 -->

                                    <col style="width:3.0%;">
                                    <col style="width:3.7%;">
                                    <col style="width:3.7%;">
                                    <col style="width:1.8%;">
                                    <col style="width:2.9%;">
                                    <col style="width:2.4%;">
                                    <col style="width:2.8%;">
                                    <col style="width:2.8%;">
                                    <col style="width:2.8%;">
                                    <col style="width:2.8%;"> <!-- 28.2 -->

                                    <col style="width:3.0%;">
                                    <col style="width:3.0%;">
                                    <col style="width:3.0%;">
                                    <col style="width:3.0%;">
                                    <col style="width:3.0%;">
                                    <col style="width:3.0%;">
                                    <col style="width:3.0%;">
                                    <col style="width:3.0%;">

                                    <col style="width:3.0%;">
                                    <col style="width:2.5%;">
                                    <col style="width:2.5%;">
                                    <col style="width:2.5%;">
                                    <col style="width:2.5%;">
                                    <col style="width:3.5%;">
                                    <col style="width:3.5%;">
                                    <col style="width:3.5%;">
                                    <col style="width:3.5%;">
                                    <col style="width:4.5%;">
                                    <col style="width:4.5%;">
                                    <col style="width:10.5%;">

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
                                    <th colspan="1" class="td01">
                                        <image style='height: 1.5rem;' src='/images/icons8-abc-30.png' title='Customer classification'/>
                                    </th>
                                    @php
                                        if ($groupByPO != 4) {
                                            echo '<th class="td02" colspan="3">' . __('Purchase order') . '</th>';
                                            $th1 = __("Supplier");
                                            $th2 = ""; // "Nume";
                                            $th3 = __("Referent");
                                            $th4 = ""; // "Aprovizionare";
                                            $th5 = __("Data creare");
                                            $th6 = __("Moneda/Curs schimb");
                                            $th7a = __("Comandat");
                                            $th7b = __("Livrat");
                                            $th7c = __("Inca de livrat");
                                            $th7d = __("Facturat");
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
                                        echo "<th colspan=1>$th1</th>";
                                        echo "<th colspan=5>$th2</th>";
                                        echo "<th colspan=2>$th3</th>";
                                        echo "<th colspan=3>$th4</th>";
                                        echo "<th colspan=3>$th5</th>";
                                        echo "<th colspan=3>$th6</th>";
                                        echo "<th colspan=2>$th7a</th>";
                                        echo "<th colspan=2>$th7b</th>";
                                        echo "<th colspan=2>$th7c</th>";
                                        echo "<th colspan=2>$th7d</th>";
                                        for ($i = 0; $i < 12; $i++) echo "<th>&nbsp;</th>";
                                    } else {
                                        echo "<th colspan=2>$th1</th>";
                                        echo "<th colspan=5>$th2</th>";
                                        echo "<th colspan=2>$th3</th>";
                                        echo "<th colspan=5>$th4</th>";
                                        echo "<th colspan=2>$th5</th>";
                                        echo "<th colspan=5>$th6</th>";
                                        echo "<th>$th7</th>";
                                        for ($i = 0; $i < 15; $i++) echo "<th>&nbsp;</th>";
                                    }
                                    @endphp
                                </tr>
                                @php
                                    $line_counter = 1;

                                    foreach ($orders as $order) {

                                        $klabc_class = "";
                                        $customer_classif_icon = "";
                                        if (strtoupper(trim($order->klabc)) == 'A') {
                                            // $klabc_class = "klabc_a";
                                            $customer_classif_icon = "<image style='height: 1.2rem;' title='$filter_klabc_a_tooltip' src='/images/Letter-A-icon.png'>";
                                        }
                                        if (strtoupper(trim($order->klabc)) == 'B') {
                                            // $klabc_class = "klabc_b";
                                            $customer_classif_icon = "<image style='height: 1.2rem;' title='$filter_klabc_b_tooltip' src='/images/Letter-B-icon.png'>";
                                        }
                                        if (strtoupper(trim($order->klabc)) == 'C') {
                                            // $klabc_class = "klabc_c";
                                            $customer_classif_icon = "<image style='height: 1.2rem;' title='$filter_klabc_c_tooltip' src='/images/Letter-C-icon.png'>";
                                        }
                                        if (strtoupper(trim($order->klabc)) == 'D') {
                                            // $klabc_class = "klabc_d";
                                            $customer_classif_icon = "<image style='height: 1.2rem;' title='$filter_klabc_d_tooltip' src='/images/Letter-D-icon.png'>";
                                        }
                                        if (strtoupper(trim($order->klabc)) == 'N') {
                                            // $klabc_class = "klabc_n";
                                            $customer_classif_icon = "<image style='height: 1.2rem;' title='$filter_klabc_n_tooltip' src='/images/Letter-N-icon.png'>";
                                        }

                                        if ($groupByPO != 4) {
                                            $customer_classif_icon = "";
                                            $xebeln = $order->ebeln;
                                            if (substr($xebeln, 0, 1) == '+') $xebeln = "X" . substr($xebeln, 1, 9);
                                            $comanda = "<button type='button' id='butt_P$xebeln' style='width: 1.6rem; text-align: center;' onclick='getSubTree(this); return false;'>+</button> " . "<p onclick='re_filter(\"P\",\"$order->ebeln\")' style='display:inline' class='resetfilters $klabc_class'>" .
                                                \App\Materom\SAP::alpha_output($order->ebeln) . "</p>";
                                        } else {
                                            $buttname = $order->vbeln;
                                            if (strtoupper($buttname) == \App\Materom\Orders::stockorder) $buttname = __('Stock');
                                            elseif (strtoupper(trim($buttname)) == "SALESORDER") $buttname = __('Emergency');
                                            else $buttname = "<p onclick='re_filter(\"S\",\"$order->vbeln\")' style='display:inline' class='resetfilters $klabc_class'>" . \App\Materom\SAP::alpha_output($buttname) . "</p>";
                                            $comanda = "<button type='button' id='butt_S$order->vbeln' style='width: 1.6rem; text-align: center;' onclick='getSubTree(this); return false;'>+</button> $buttname";
                                        }

                                        $line_counter = $line_counter + 1;
                                        if ($line_counter == 2) $line_counter = 0;

                                        $button_accept = "";
                                        $button_reject = "";
                                        $button_inquire = "";

                                        if ($groupByPO != 4) {
                                            $oid = "P" . $order->ebeln;
                                            if (substr($order->ebeln, 0, 1) == '+') $oid = "PX" . substr($order->ebeln, 1, 9);
                                            $data = "<td class='td02' colspan=1>" . \App\Materom\SAP::alpha_output($order->lifnr) . "</td>" .
                                                    "<td class='td02' colspan=5>$order->lifnr_name</td>" .
                                                    "<td class='td02' colspan=1>$order->ekgrp</td>" .
                                                    "<td class='td02' colspan=4>$order->ekgrp_name</td>" .
                                                    "<td class='td02' colspan=3>$order->erdat_out</td>" .
                                                    "<td class='td02' colspan=1>$order->curr</td>" .
                                                    "<td class='td02' colspan=2>$order->fxrate</td>".
                                                    "<td class='td02' colspan=2>$order->qty_ordered</td>".
                                                    "<td class='td02' colspan=2>$order->qty_delivered</td>".
                                                    "<td class='td02' colspan=2>$order->qty_open</td>".
                                                    "<td class='td02' colspan=2>$order->qty_invoiced</td>";

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
                                                                 "onclick='acceptPOrder(this, 1);return false;'/>";
                                            if ($order->accept == 3)
                                                $button_accept = "<button type='button' class='order-button-accepted-keep' style='width: 1.5rem; height: 1.5rem; text-align: center;' " .
                                                                 "onclick='acceptPOrder(this, 3);return false;' title='". __("Keep proposal")."'/>";
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

                                            echo "<tr id='tr_$oid' style='$style' data-accept='$order->accept'>" .
                                                 "<td align='center' style='vertical-align: middle;'>".
                                                    "<input id='input_chk' type=\"checkbox\" name=\"$oid\" value=\"$oid\" onclick='boxCheck(this);'></td>" .
                                                 "<td class='td01'>$info_icon</td>" .
                                                 "<td class='td01'>$owner_icon</td>" .
                                                 "<td class='td01'>$changed_icon</td>" .
                                                 "<td class='td01'>$accepted_icon</td>" .
                                                 "<td class='td01'>$rejected_icon</td>" .
                                                 "<td class='td01'>$inquired_icon</td>" .
                                                 "<td class='td01' style='padding: 0;'>$button_accept</td>" .
                                                 "<td class='td01' style='padding: 0;'>$button_reject</td>" .
                                                 "<td class='td01' style='padding: 0;'>$button_inquire</td>" .
                                                 "<td class='td01' style='padding: 0;'>$customer_classif_icon</td>" .
                                                 "<td colspan='3' class='td02' class='first_color'>$comanda</td>" .
                                                 "$data<td colspan='12'></td></tr>";
                                        } else {
                                            $oid = "S" . $order->vbeln;
                                            if (\Illuminate\Support\Facades\Auth::user()->role != "Furnizor") {
                                                $ctv_class = "td02";
                                                $change_ctv = "";
                                                $change_ctv2 = "";
                                                if ($order->ctv_changeable != 0) {
                                                    $ctv_class = "td02h";
                                                    $change_ctv = "onclick=\"change_ctv(this, '$order->vbeln');return false;\"";
                                                    $change_ctv2 = "onclick=\"change_ctv(this.previousSibling, '$order->vbeln');return false;\"";
                                                }
                                                if ($order->ctv_man != 0) $ctv_class .= "_c";
                                                $data = "<td class='td02' colspan=2>" . \App\Materom\SAP::alpha_output($order->kunnr) . "</td>" .
                                                        "<td class='td02' colspan=5>$order->kunnr_name</td>" .
                                                        "<td class='td02' colspan=2>" . \App\Materom\SAP::alpha_output($order->shipto) . "</td>" .
                                                        "<td class='td02' colspan=5>$order->shipto_name</td>" .
                                                        "<td class='$ctv_class' $change_ctv colspan=2>$order->ctv</td>" .
                                                        "<td class='$ctv_class' $change_ctv2 colspan=5>$order->ctv_name</td>".
                                                        "<td class='td02' colspan=10></td>";
                                            } else {
                                                $data = "<td class='td02' colspan=2>&nbsp;</td>" .
                                                        "<td class='td02' colspan=5>&nbsp;</td>" .
                                                        "<td class='td02' colspan=2>&nbsp;</td>" .
                                                        "<td class='td02' colspan=5>&nbsp;</td>" .
                                                        "<td class='td02' colspan=2>&nbsp;</td>" .
                                                        "<td class='td02' colspan=9>&nbsp;</td>".
                                                        "<td class='td02' colspan=10></td>";
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
                                                 "<td>$customer_classif_icon</td>" .
                                                 "<td colspan='3' class='td02' class='first_color'>$comanda</td>" .
                                                 "$data<td colspan='7'></td></tr>";
                                        }
                                    }
                                @endphp
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($filter_history == 1)
        <ul class="order-tools-menu" id="order-tools-menu">
            <li><div id="order-tools-menu-archive" style="padding: 6px; font-weight: bold;"><span class="ui-icon ui-icon-disk"></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{__("Arhivare pozitie")}}</div></li>
            <li>-</li>
            <li><div id="order-tools-menu-rollback" style="padding: 6px; font-weight: bold;"><span class="ui-icon ui-icon-arrowreturnthick-1-w"></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{__("Rollback pozitie")}}</div></li>
        </ul>
    @elseif ($filter_history == 2)
        <ul class="order-tools-menu" id="order-tools-menu">
            <li><div id="order-tools-menu-unarchive" style="padding: 6px; font-weight: bold;"><span class="ui-icon ui-icon-disk"></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{__("Dezarhivare pozitie")}}</div></li>
        </ul>
    @endif

    <ul class="mass-change-menu" id="mass-change-menu">
        <li><div id="mass-change-menu-download" style="padding: 6px; font-weight: bold;"><span class="ui-icon ui-icon-circle-arrow-s"></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{__("Download position list data")}}</div></li>
        <li>-</li>
        <li><div id="mass-change-menu-upload" style="padding: 6px; font-weight: bold;"><span class="ui-icon ui-icon-circle-arrow-n"></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{__("Upload position list changes")}}</div></li>
    </ul>

    <ul class="delivery-date-menu" id="delivery-date-menu">
        <li><div id="delivery-date-menu-mark-delivered" style="padding: 6px; font-weight: bold;"><span class="ui-icon ui-icon-circle-check"></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{__("Mark item as fully delivered")}}</div></li>
        <!--
        <li>-</li>
        <li><div id="delivery-date-menu-mark-backorder" style="padding: 6px; font-weight: bold;"><span class="ui-icon ui-icon-circle-check"></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{__("Mark item as backorder")}}</div></li>
        -->
    </ul>

    <script>
        var supplierList = {!! $supplier_list !!};
        function debug_job() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajaxSetup({async: false});
            var df_data, df_status;
            $.post("webservice/debug_job",
                {
                    empty: null
                },
                function (data, status) {
                    df_data = data;
                    df_status = status;
                });
            jQuery.ajaxSetup({async: true});
        }

        function delete_filters(mode = 0) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajaxSetup({async: false});
            var df_data, df_status;
            $.post("webservice/deletefilters",
                {
                    mode: mode
                },
                function (data, status) {
                    df_data = data;
                    df_status = status;
                });
            jQuery.ajaxSetup({async: true});
        }

        function re_filter(type, order) {
            delete_filters(1);

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
            delete_filters(0);
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
            if (caller == 11) {
                $("#ar-immed-lifnr2").val(result_lifnr);
                $("#ar-immed-idnlf2").val(result_idnlf);
                $("#ar-immed-mtext2").val(result_mtext);
                $("#ar-immed-matnr2").val(result_matnr);
                $("#ar-immed-purch-price2").val(result_purch_price);
                $("#ar-immed-purch-curr2").val(result_purch_currency);
                ar_immed_purch_price2_check(null, $("#ar-immed-purch-price2")[0]);
            }
            if (caller == 2) {
                $("#aep-lifnr").val(result_lifnr);
                $("#aep-idnlf").val(result_idnlf);
                $("#aep-mtext").val(result_mtext);
                $("#aep-matnr").val(result_matnr);
                $("#aep-purch-price").val(result_purch_price);
                $("#aep-purch-curr").val(result_purch_currency);
            }
            if (caller == 12) {
                $("#aep-lifnr2").val(result_lifnr);
                $("#aep-idnlf2").val(result_idnlf);
                $("#aep-mtext2").val(result_mtext);
                $("#aep-matnr2").val(result_matnr);
                $("#aep-purch-price2").val(result_purch_price);
                $("#aep-purch-curr2").val(result_purch_currency);
                aep_purch_price2_check(null, $("#aep-purch-price2")[0]);
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
            if (caller == 11) {
                $("#ar-immed-lifnr2").val(result_lifnr);
                $("#ar-immed-idnlf2").val(result_idnlf);
                $("#ar-immed-mtext2").val(result_mtext);
                $("#ar-immed-matnr2").val(result_matnr);
                $("#ar-immed-purch-price2").val(result_purch_price);
                $("#ar-immed-purch-curr2").val(result_purch_currency);
                ar_immed_purch_price2_check(null, $("#ar-immed-purch-price2")[0]);
                $("#ar-immed-sales-price").val(result_sales_price);
                $("#ar-immed-sales-curr").val(result_sales_currency);
                ar_immed_sales_price2_check(null, $("#ar-immed-sales-price2")[0]);
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
            if (caller == 12) {
                $("#aep-lifnr2").val(result_lifnr);
                $("#aep-idnlf2").val(result_idnlf);
                $("#aep-mtext2").val(result_mtext);
                $("#aep-matnr2").val(result_matnr);
                $("#aep-purch-price2").val(result_purch_price);
                $("#aep-purch-curr2").val(result_purch_currency);
                $("#aep-sales-price2").val(result_sales_price);
                $("#aep-sales-curr2").val(result_sales_currency);
                aep_sales_price2_check(null, $("#aep-sales-price2")[0]);
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
            $("#filter_eta_delayed_date").datepicker({dateFormat: "yy-mm-dd"});
            $("#filter_deldate_low").datepicker({dateFormat: "yy-mm-dd"});
            $("#filter_deldate_high").datepicker({dateFormat: "yy-mm-dd"});
            $("#filter_etadate_low").datepicker({dateFormat: "yy-mm-dd"});
            $("#filter_etadate_high").datepicker({dateFormat: "yy-mm-dd"});
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

            return $("[name='" + id + "']").is(":checked");

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
            let parent_name = "P" + _this.name.substr(1, 10);
            let child_name = "I" + _this.name.substr(1, 10) + "_";
            if (_this.name.startsWith("I")) {
                if (!_this.checked) {
                    $("[name='" + parent_name + "']").prop("checked", false);
                } else {
                    let inputs = $("input[name^='" + child_name + "']");
                    let checked = 0;
                    for (let i = 0; i < inputs.length; i++) {
                        if (inputs[i].checked) checked++;
                    }
                    if (checked == inputs.length) $("[name='" + parent_name + "']").prop("checked", true);
                }
            }
            if (_this.name.startsWith("P")) {
                $("input[name^='" + child_name + "']").prop("checked", _this.checked);
            }
/*
            if (!isChecked(_this.name)) {
                addToChecked(_this.name);
            } else {
                removeFromChecked(_this.name);
                removeFromChecked(_this.name);
            }
*/
            refreshCheck();
        }

        function refreshCheck() {
            let inputs = $("input[id|='input_chk']");
//            for (let i = 0; i < inputs.length; i++) {
//                inputs[i].checked = isChecked(inputs[i].name);
//            }
            for (let i = 0; i < inputs.length; i++) {
                if (inputs[i].name.startsWith("P")) {
                    let p_order = inputs[i].name.substr(1, 10);
                    let p_accept = -2;
                    let parent = $("#tr_P" + p_order);
                    for (let j = 0; j < inputs.length; j++) {
                        if (!inputs[j].checked || !inputs[j].name.startsWith("I") || inputs[j].name.substr(1, 10) != p_order) continue;
                        let j_accept = $("#tr_" + inputs[j].name).attr("data-accept");
                        if (p_accept < 0) p_accept = j_accept;
                        else if (p_accept != j_accept) {
                            p_accept = -1;
                            break;
                        }
                    }
                    let p_td = $("#tr_P" + p_order + " td:eq(7)");
                    if (p_accept == -2) p_accept = parent.attr("data-accept");
                    if (p_accept <= 0) {
                        if (p_td.html() != "") p_td.html("").fadeIn(1000);
                        continue;
                    }
                    if (p_accept == 1) {
                        p_td.html("<button type='button' class='order-button-accepted' style='width: 1.5rem; height: 1.5rem; text-align: center;' " +
                            "onclick='acceptPOrder(this, 1);return false;'/>").fadeIn(1000);
                    } else if (p_accept == 2) {
                        p_td.html("<button type='button' class='order-button-accepted-changed' style='width: 1.5rem; height: 1.5rem; text-align: center;' " +
                            "onclick='acceptPOrder(this, 2);return false;' title='{{__("Send proposal to MATEROM")}}'/>").fadeIn(1000);
                    } else if (p_accept == 3) {
                        p_td.html("<button type='button' class='order-button-accepted-keep' style='width: 1.5rem; height: 1.5rem; text-align: center;' " +
                        "onclick='acceptPOrder(this, 3);return false;' title='{{__("Keep proposal")}}'/>").fadeIn(1000);
                    };
                }
            }
        }

        function boxMultiCheck(_this) {
            if (!_this.name.startsWith("I")) return;
            let currentrow = $("#tr_" + _this.name);
            let p_accept = currentrow.attr("data-accept");
            if (p_accept != 1 && p_accept != 2 && p_accept != 3) return;
            let p_checked = _this.checked;
            let inputs = $("input[id|='input_chk']");
            for (let i = 0; i < inputs.length; i++) {
                if (inputs[i].name.startsWith(_this.name.substr(0, 11)) &&
                    p_accept == $("#tr_" + inputs[i].name).attr("data-accept"))
                {
                    inputs[i].checked = p_checked;
                    if (p_checked) addToChecked(inputs[i].name);
                    else removeFromChecked(inputs[i].name);
                }
            }
            refreshCheck();
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
                alert(_data);
                return;
            }
            if (_status == "success") {
                if (reload) location.reload(true);
            } else alert('Error processing operation!');
        }

        function acceptItemList(ebeln, itemlist, acceptmode) {
            var _data, _status = "";
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajaxSetup({async: false});

            $.post("webservice/acceptitemlistchange",
                {
                    ebeln: ebeln,
                    itemlist: itemlist,
                    type: null,
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
            if (_status != "success") alert('Error processing operation!');
            else location.reload(true);
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
            if (thisbtn == undefined || thisbtn == null) return;
            let rowid = (currentrow = $(thisbtn).parent().parent()).attr('id').toUpperCase();
            let rowtype = rowid.substr(3, 1);
            let order = unescplus(rowid.substr(4, 10));
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
            cols += '<td class="first_color" style="' + so_style + '" colspan="12"></td>';
            cols += '<td colspan="3"><b>{{__("Purchase order")}}</b></td>';
            cols += '<td colspan="1"><b>{{__("Supplier")}}</b></td>';
            cols += '<td colspan="5"><b>&nbsp;</b></td>';
            cols += '<td colspan="2"><b>{{__("Referent")}}</b></td>';
            cols += '<td colspan="3"><b>&nbsp;</b></td>';
            cols += '<td colspan="3"><b>{{__("Data creare")}}</b></td>';
            cols += '<td colspan="3"><b>{{__("Moneda/Curs schimb")}}</b></td>';
            cols += '<td colspan="2"><b>{{__("Comandat")}}</b></td>';
            cols += '<td colspan="2"><b>{{__("Livrat")}}</b></td>';
            cols += '<td colspan="2"><b>{{__("Inca de livrat")}}</b></td>';
            cols += '<td colspan="2"><b>{{__("Facturat")}}</b></td>';
            cols += '<td colspan="11"></td>';
            newRow.append(cols).hide();
            $(currentrow).after(newRow);
            newRow.attr('style', "background-color:#FAEFCA; vertical-align: middle;");
            newRow.attr('id', "tr_HS" + order);

            // POs
            for (i = 0; i < _data.length; i++) {
                let prevrow = newRow;
                let porder = _data[i];

                let klabc_class = "";
                // if (porder.klabc.trim().toUpperCase() == 'A') klabc_class = "klabc_a";
                // if (porder.klabc.trim().toUpperCase() == 'B') klabc_class = "klabc_b";
                // if (porder.klabc.trim().toUpperCase() == 'C') klabc_class = "klabc_c";
                let customer_classif_icon = "";
                switch (porder.klabc) {
                    case "A":
                        customer_classif_icon = "<image style='height: 1.2rem;' title='{{$filter_klabc_a_tooltip}}' src='/images/Letter-A-icon.png'>";
                        break;
                    case "B":
                        customer_classif_icon = "<image style='height: 1.2rem;' title='{{$filter_klabc_b_tooltip}}' src='/images/Letter-B-icon.png'>";
                        break;
                    case "C":
                        customer_classif_icon = "<image style='height: 1.2rem;' title='{{$filter_klabc_c_tooltip}}' src='/images/Letter-C-icon.png'>";
                        break;
                    case "D":
                        customer_classif_icon = "<image style='height: 1.2rem;' title='{{$filter_klabc_d_tooltip}}' src='/images/Letter-D-icon.png'>";
                        break;
                    case "N":
                        customer_classif_icon = "<image style='height: 1.2rem;' title='{{$filter_klabc_n_tooltip}}' src='/images/Letter-N-icon.png'>";
                        break;
                }

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
                        "onclick='acceptPOrder(this, 1);return false;'/>";
                if (porder.accept == 2)
                    button_accept = "<button type='button' class='order-button-accepted-changed' style='width: 1.5rem; height: 1.5rem; text-align: center;' " +
                        "onclick='acceptPOrder(this, 2);return false;' title='{{__("Send proposal to MATEROM")}}'/>";
                if (porder.accept == 3)
                    button_accept = "<button type='button' class='order-button-accepted-keep' style='width: 1.5rem; height: 1.5rem; text-align: center;' " +
                        "onclick='acceptPOrder(this, 3);return false;' title='{{__("Keep proposal")}}'/>";
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
                customer_classif_icon = "";
                cols += '<td class="first_color td01" style="' + so_style + '; padding: 0;" colspan="1">' + customer_classif_icon + '</td>';
                cols += '<td class="first_color td01" style="' + so_style + '" colspan="1"></td>';
                cols += "<td colspan='3'><button type='button' id='butt_P" + escplus(porder.ebeln) + "' style='width: 1.6rem; text-align: center;' onclick=\"getSubTree(this);return false;\">+</button> " +
                    "<p onclick='re_filter(\"P\",\"" + porder.ebeln + "\")' style='display:inline' class='resetfilters " + klabc_class + "'>" + conv_exit_alpha_output(porder.ebeln) + "</p>"
                    + "</td>";
                cols += '<td class="td02" colspan="1">' + conv_exit_alpha_output(porder.lifnr) + '</td>';
                cols += '<td class="td02" colspan="5">' + porder.lifnr_name + '</td>';
                cols += '<td class="td02" colspan="1">' + porder.ekgrp + '</td>';
                cols += '<td class="td02" colspan="4">' + porder.ekgrp_name + '</td>';
                cols += '<td class="td02" colspan="3">' + porder.erdat_out + '</td>';
                cols += '<td class="td02" colspan="1">' + porder.curr + '</td>';
                cols += '<td class="td02" colspan="2">' + porder.fxrate + '</td>';
                cols += '<td class="td02" colspan="2">' + porder.qty_ordered + '</td>';
                cols += '<td class="td02" colspan="2">' + porder.qty_delivered + '</td>';
                cols += '<td class="td02" colspan="2">' + porder.qty_open + '</td>';
                cols += '<td class="td02" colspan="2">' + porder.qty_invoiced + '</td>';
                cols += '<td class="td02" colspan="11"></td>';
                newRow.append(cols).hide();
                $(prevrow).after(newRow);
                if (i % 2 == 0)
                    newRow.attr('style', "background-color:LightYellow; vertical-align: middle;");
                else
                    newRow.attr('style', "background-color:Wheat; vertical-align: middle;");
                newRow.attr('id', "tr_P" + escplus(porder.ebeln));
                newRow.attr('data-accept', "" + porder.accept);
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
                cols += '<td class="first_color" colspan="12" style="' + first_style + '"></td>';
                colsafter = 0;
            @else
                cols += '<td class="first_color" colspan="11" style="' + po_style + '"></td>';
                colsafter = 1;
            @endif
            cols += '<td style="' + po_style + '"></td>';
            cols += '<td class="td02" colspan="1"><b>{{__("Position")}}</b></td>';
            cols += '<td class="td02" colspan="1"><b>{{__("Plant")}}</b></td>';
            cols += '<td class="td02" colspan="1"><b>{{__("Fabricant")}}</b></td>';
            cols += '<td class="td02" colspan="3"><b>{{__("Material")}}</b></td>';
            cols += '<td class="td02" colspan="4"><b>{{__("Material description")}}</b></td>';
            cols += '<td class="td02" colspan="1" style="text-align: right;"><b>{{__("Quantity")}}</b></td>';
            cols += '<td class="td02" colspan="2" style="padding-left: 0.5rem;"><b>{{__("Delivery date")}}</b></td>';
            cols += '<td class="td02" colspan="1" style="padding-left: 0.1rem;"><b>{{__("Dly")}}</b></td>';
            cols += '<td class="td02" colspan="2" style="padding-left: 0.1rem;"><b>{{__("ETA date")}}</b></td>';
            cols += '<td class="td02" colspan="2" style="text-align: right;"><b>{{__("Purchase price")}}</b></td>';
            @if (\Illuminate\Support\Facades\Auth::user()->role != "Furnizor")
                let sales_price_hdr = '{{__("Sales price")}}';
                if (sorder == '{{\App\Materom\Orders::stockorder}}') sales_price_hdr = '';
                cols += '<td class="td02" colspan="2" style="text-align: right;"><b>' + sales_price_hdr + '</b></td>';
            @else
                cols += '<td class="td02" colspan="2"><b>&nbsp;</b></td>';
            @endif
            cols += '<td class="td02" colspan="2" style="text-align: left;"><b>{{__("Delivery")}}</b></td>';
            cols += '<td class="td02" colspan="2" style="text-align: left;"><b>{{__("Delivered on")}}</b></td>';
            cols += '<td class="td02" colspan="2" style="text-align: left;"><b>{{__("Inbound invoice")}}</b></td>';
            cols += '<td class="td02" colspan="2" style="text-align: left;"><b>{{__("Inbound invoice date")}}</b></td>';
            cols += '<td class="td02" colspan="2" style="text-align: left;"><b>{{__("Goods receipt date")}}</b></td>';
            cols += '<td class="td02" colspan="2" style="text-align: right;"><b>{{__("Goods receipt quantity")}}</b></td>';
            cols += '<td class="td02" colspan="1" style="text-align: right;"><b>{{__("Recept.")}}</b></td>';
            cols += '<td class="td02" colspan="1" style="text-align: right;"><b>{{__("Facturat")}}</b></td>';
            cols += '<td class="td02" colspan="1" style="text-align: right;"><b>{{__("Plus/Min")}}</b></td>';
            cols += '<td class="td02" colspan="1" style="text-align: right;"><b>{{__("Avariate")}}</b></td>';
            cols += '<td class="td02" colspan="3" style="text-align: left;"><b>{{__("Motiv/solutie")}}</b></td>';
            if (colsafter > 0)
                cols += '<td class="td02" colspan="' + colsafter + '"></td>';
            newRow.append(cols).hide();
            $(currentrow).after(newRow);
            newRow.attr('style', "background-color:YellowGreen; vertical-align: middle;");
            newRow.attr('id', "tr_HP" + escplus(order));

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
                        info_icon = "<image style='height: 1.5rem; margin: -2px;' src='/images/icons8-shipped-48.png' title='{{__("Completely delivered")}}'>";
                        break;
                    case 7:
                        info_icon = "<image style='height: 1.5rem; margin: -2px;' src='/images/icons8-partially-shipped-48.png' title='{{__("Partially delivered")}}'>";
                        break;
                    case 8:
                        info_icon = "<image style='height: 1.5rem; margin: -2px;' src='/images/icons8-drop-shipping-48.png' title='{{__("Inbound delivery confirmed")}}'>";
                        break;
                }
                if (pitem.backorder == 1)
                    info_icon = "<image style='height: 1.2rem;' src='/images/icons8-next-page-48.png' title='Backorder'>";
                if (pitem.pmfa.length > 0) {
                    @if (\Illuminate\Support\Facades\Auth::user()->role == "Furnizor")
                        switch (pitem.pmfa.substr(0, 1)) {
                            case "A":
                            case "B":
                                info_icon = "<image style='height: 1.2rem;' src='/images/ringing_bell_1.gif' " +
                                            "onclick='ack_bell(\"" + pitem.ebeln + "\", \"" + pitem.ebelp + "\", \"" + pitem.pmfa.substr(0, 1) + "\"); return false;' " +
                                            "title='{{__("Purchase item cancelled by Materom")}}'" +
                                    ">";
                                break;
                            case "F":
                                if (((pitem.pmfa_status & 4) == 0) && ((pitem.pmfa_status & 1) == 1))
                                    info_icon = "<image style='height: 1.2rem;' src='/images/ringing_bell_3.gif' " +
                                        "onclick='ack_bell(\"" + pitem.ebeln + "\", \"" + pitem.ebelp + "\", \"" + pitem.pmfa.substr(0, 1) + "\"); return false;' " +
                                        "title='{{__("Backorder acceptat de CTV, se va efectua o noua verificare de disponibilitate la data")}}" + " " + pitem.eta_delayed_date.substr(0, 10) + "'" +
                                        ">";
                                break;
                        }
                    @elseif (\Illuminate\Support\Facades\Auth::user()->role == "Referent")
                        switch (pitem.pmfa.substr(0, 1)) {
                            case "F":
                                if (((pitem.pmfa_status & 2) == 0) && ((pitem.pmfa_status & 1) == 1))
                                    info_icon = "<image style='height: 1.2rem;' src='/images/ringing_bell_3.gif' " +
                                        "onclick='ack_bell(\"" + pitem.ebeln + "\", \"" + pitem.ebelp + "\", \"" + pitem.pmfa.substr(0, 1) + "\"); return false;' " +
                                        "title='{{__("Backorder acceptat de CTV, se va efectua o noua verificare de disponibilitate la data")}}" + " " + pitem.eta_delayed_date.substr(0, 10) + "'" +
                                        ">";
                                break;
                        }
                    @elseif (\Illuminate\Support\Facades\Auth::user()->role == "CTV")
                        switch (pitem.pmfa.substr(0, 1)) {
                            case "C":
                                info_icon = "<image style='height: 1.2rem;' src='/images/ringing_bell_1.gif' " +
                                    "onclick='ack_bell(\"" + pitem.ebeln + "\", \"" + pitem.ebelp + "\", \"" + pitem.pmfa.substr(0, 1) + "\"); return false;' " +
                                    "title='{{__("Purchase item cancelled by supplier")}}'" +
                                    ">";
                                break;
                            case "D":
                                info_icon = "<image style='height: 1.2rem;' src='/images/ringing_bell_1.gif' " +
                                    "onclick='ack_bell(\"" + pitem.ebeln + "\", \"" + pitem.ebelp + "\", \"" + pitem.pmfa.substr(0, 1) + "\"); return false;' " +
                                    "title='{{__("ETA has changed")}}'" +
                                    ">";
                                break;
                            case "E":
                                info_icon = "<image style='height: 1.2rem;' src='/images/ringing_bell_1.gif' " +
                                    "onclick='ack_bell(\"" + pitem.ebeln + "\", \"" + pitem.ebelp + "\", \"" + pitem.pmfa.substr(0, 1) + "\"); return false;' " +
                                    "title='{{__("PNAD notification")}}'" +
                                    ">";
                                break;
                            case "F":
                                if ((pitem.pmfa_status & 1) == 0)
                                    info_icon = "<image style='height: 1.2rem;' src='/images/ringing_bell_3.gif' " +
                                        "onclick='ack_bell(\"" + pitem.ebeln + "\", \"" + pitem.ebelp + "\", \"" + pitem.pmfa.substr(0, 1) + "\"); return false;' " +
                                        "title='{{__("Backorder fara termen de livrare, se va efectua o noua verificare de disponibilitate la data")}}" + " " + pitem.eta_delayed_date.substr(0, 10) + "'" +
                                        ">";
                                break;
                        }
                    @endif
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
                        "onclick='acceptPItem(this);return false;' title='{{__("")}}'/>";
                if (pitem.accept == 2)
                    button_accept = "<button type='button' class='order-button-accepted-changed' style='width: 1.5rem; height: 1.5rem; text-align: center;' " +
                        "onclick='acceptPItem(this);return false;' title='{{__("Send proposal to MATEROM")}}'/>";
                if (pitem.accept == 3)
                    button_accept = "<button type='button' class='order-button-accepted-keep' style='width: 1.5rem; height: 1.5rem; text-align: center;' " +
                        "onclick='acceptPItem(this);return false;' title='{{__("Keep proposal")}}'/>";
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
                if (pitem.reject == 5)
                    button_reject = "<button type='button' class='order-button-rejected-cancel' style='width: 1.6rem; height: 1.5rem; text-align: center;' " +
                        "onclick='rejectPItem(this, 0, null);return false;' title='{{__("Reject proposal")}}'/>";
                let button_inquire = "";
                if (pitem.inquire == 1)
                    button_inquire = "<button type='button' class='order-button-request' style='width: 1.5rem; height: 1.5rem; text-align: center;' " +
                        "onclick='inquirePItem(this, \"" + pitem.pnad_status.trim() + "\");return false;'/>";
                let button_tools = "";
                if (pitem.tools == 1)
                    button_tools = "<button type='button' class='order-item-tools' style='width: 1.5rem; height: 1.5rem; text-align: center;' " +
                        "onclick='orderItemTools(event, this);return false;'/>";

                let customer_classif_icon = "";
                switch (pitem.klabc) {
                    case "A":
                        customer_classif_icon = "<image style='height: 1.2rem;' title='{{$filter_klabc_a_tooltip}}' src='/images/Letter-A-icon.png'>";
                        break;
                    case "B":
                        customer_classif_icon = "<image style='height: 1.2rem;' title='{{$filter_klabc_b_tooltip}}' src='/images/Letter-B-icon.png'>";
                        break;
                    case "C":
                        customer_classif_icon = "<image style='height: 1.2rem;' title='{{$filter_klabc_c_tooltip}}' src='/images/Letter-C-icon.png'>";
                        break;
                    case "D":
                        customer_classif_icon = "<image style='height: 1.2rem;' title='{{$filter_klabc_d_tooltip}}' src='/images/Letter-D-icon.png'>";
                        break;
                    case "N":
                        customer_classif_icon = "<image style='height: 1.2rem;' title='{{$filter_klabc_n_tooltip}}' src='/images/Letter-N-icon.png'>";
                        break;
                }

                var newRow = $("<tr>");
                var cols = "";
                cols += '<td colspan="1" align="center" style="vertical-align: middle;"><input id="input_chk" onclick="boxCheck(this);" ondblclick="boxMultiCheck(this);" type="checkbox" name="I' + pitem.ebeln + "_" + pitem.ebelp + '" value="I' + pitem.ebeln + "_" + pitem.ebelp + '"></td>';
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
                    cols += '<td class="first_color td01" colspan="1" style="' + first_style + '; padding: 0;">' + customer_classif_icon + '</td>';
                    cols += '<td class="first_color td01" colspan="1" style="' + first_style + '; padding: 0;">' + button_tools + '</td>';
                    cols += '<td class="coloured" style="' + po_style + '">' + pitem.posnr_out + '</td>';
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
                    cols += '<td class="first_color td01" colspan="1" style="' + po_style + '; padding: 0;">' + customer_classif_icon + '</td>';
                    cols += '<td class="coloured td01" colspan="1" style="' + po_style + '; padding: 0;">' + button_tools + '</td>';
                @endif
                cols += "<td colspan='1'><button type='button' style='width: 1.6rem; text-align: center;' onclick=\"getSubTree(this);return false;\">+</button><span id='span_item' style='padding-left: 0.2rem;' title='" + pitem.ebelp_title + "'>" + conv_exit_alpha_output(pitem.ebelp) + "</span></td>";
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
                    cols += '<td class="' + quantity_class + '" colspan="1" onclick="change_quantity(this, \'' + pitem.ebeln + '\', \'' + pitem.ebelp + '\');" style="text-align: right;">' + pitem.x_quantity + '</td>';
                } else {
                    let quantity_class = "td02";
                    if (pitem.quantity_changed == 1) quantity_class += "_c";
                    cols += '<td class="' + quantity_class + '" colspan="1" style="text-align: right;">' + pitem.x_quantity + '</td>';
                }

                let delivery_date_contextmenu = "";
                if (pitem.status == "A") delivery_date_contextmenu = " oncontextmenu=\"delivery_date_oncontenxtmenu(event, '" + pitem.ebeln + "', '" + pitem.ebelp + "', " + pitem.delivery_date_changeable.toString() + ");return false;\"";

                let delivery_date_blurry = "";
                @if (\Illuminate\Support\Facades\Auth::user()->role == "CTV")
                  if (pitem.eta_delayed_check) delivery_date_blurry = " text-hide";//" blurry-text";
                @endif

                if ((pitem.delivery_date_changeable == 1) || ((pitem.delivery_date_changeable == 2))) {
                    let delivery_date_class = "td02h";
                    if (pitem.delivery_date_changed == 1) delivery_date_class += "_c";
                    let change_delivery_date_func = "change_delivery_date";
                    if (pitem.delivery_date_changeable == 2) change_delivery_date_func = "change_delivery_date2";
                    delivery_date_class += delivery_date_blurry;
                    cols += '<td class="' + delivery_date_class + '" colspan="2" onclick="' + change_delivery_date_func + '(this, \'' + pitem.ebeln + '\', \'' + pitem.ebelp + '\', ' + pitem.backorder + ');" style="padding-left: 0.5rem;"' + delivery_date_contextmenu + '>' + pitem.x_delivery_date.split(' ')[0] + '</td>';
                } else {
                    let delivery_date_class = "td02";
                    if (pitem.delivery_date_changed == 1) delivery_date_class += "_c";
                    delivery_date_class += delivery_date_blurry;
                    cols += '<td class="' + delivery_date_class + '" colspan="2" style="padding-left: 0.5rem;"' + delivery_date_contextmenu + '>' + pitem.x_delivery_date.split(' ')[0] + '</td>';
                }

                let dodays = pitem.dodays;
                if (dodays > 99) dodays = ">99";
                cols += '<td class="td02" colspan="1" style="text-align: left;">' + dodays + '</td>';

                let eta_date_blurry = "";
                @if (\Illuminate\Support\Facades\Auth::user()->role == "CTV")
                    if (pitem.eta_delayed_check) eta_date_blurry = " text-hide";//" blurry-text";
                @endif

                if (pitem.eta_date_changeable != 0) {
                    let eta_date_class = "td02h";
                    if (pitem.eta_date_changed == 1) eta_date_class += "_c";
                    eta_date_class += eta_date_blurry;
                    cols += '<td class="' + eta_date_class + '" colspan="2" style="text-align: left; padding-left: 0.1rem;" onclick="change_eta_date(this, \'' + pitem.ebeln + '\', \'' + pitem.ebelp + '\');">' + pitem.etadt_out + '</td>';
                } else {
                    let eta_date_class = "td02";
                    if (pitem.eta_date_changed == 1) eta_date_class += "_c";
                    eta_date_class += eta_date_blurry;
                    cols += '<td class="' + eta_date_class + '" colspan="2" style="text-align: left; padding-left: 0.1rem;">' + pitem.etadt_out + '</td>';
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

                let inb_inv_date = "";
                if (pitem.inb_inv_date != null)
                    inb_inv_date = pitem.inb_inv_date.split(' ')[0];

                let inb_dlv_text = '';
                if (pitem.inb_dlv.trim().length != 0)
                    inb_dlv_text = conv_exit_alpha_output(pitem.inb_dlv) + '/' + conv_exit_alpha_output(pitem.inb_dlv_posnr);

                cols += '<td class="td02" colspan="2" style="text-align: left;">' + inb_dlv_text + '</td>';
                cols += '<td class="td02" colspan="2" style="text-align: left;">' + deldate + '</td>';
                cols += '<td class="td02" colspan="2" style="text-align: left;">' + pitem.inb_inv + '</td>';
                cols += '<td class="td02" colspan="2" style="text-align: left;">' + inb_inv_date + '</td>';
                cols += '<td class="td02" colspan="2" style="text-align: left;">' + grdate + '</td>';
                cols += '<td class="td02" colspan="2" style="text-align: right;">' + pitem.grqty + '</td>';
                cols += '<td class="td02" colspan="1" style="text-align: right;">' + pitem.qty_received + '</td>';
                cols += '<td class="td02" colspan="1" style="text-align: right;">' + pitem.qty_invoiced + '</td>';
                cols += '<td class="td02" colspan="1" style="text-align: right;">' + pitem.qty_diff + '</td>';
                cols += '<td class="td02" colspan="1" style="text-align: right;">' + pitem.qty_damaged + '</td>';
                cols += '<td class="td02" colspan="3" style="text-align: left;">' + pitem.qty_details + '</td>';


                @if ($groupByPO != 4)
                    cols += '<td colspan="1"></td>';
                @endif
                newRow.append(cols).hide();
                $(prevrow).after(newRow);
                if (i % 2 == 0)
                    newRow.attr('style', "background-color:#A0C0A0; vertical-align: middle;");
                else
                    newRow.attr('style', "background-color:#90D090; vertical-align: middle;");
                newRow.attr('id', "tr_I" + escplus(pitem.ebeln) + "_" + pitem.ebelp);
                newRow.attr('data-accept', "" + pitem.accept);
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
            cols += '<td class="first_color" colspan="11" style="' + first_style + '"></td>';
            @if ($groupByPO == 4)
                let colsafter = "10";
                cols += '<td class="first_color" colspan="1" style="' + first_style + '"></td>';
            @else
                let colsafter = "11";
            @endif
            cols += '<td class="coloured" style="' + last_style + '"></td>';
            cols += '<td style="' + po_style + '"></td>';
            cols += '<td class="td02" colspan="3"><b>{{__("Data")}}</b></td>';
            cols += '<td class="td02" colspan="6"><b>{{__("Utilizator")}}</b></td>';
            cols += '<td class="td02" colspan="8"><b>{{__("Ce s-a schimbat")}}</b></td>';
            cols += '<td class="td02" colspan="2"><b>{{__("Motiv")}}</b></td>';
            cols += '<td colspan=' + colsafter + '><b></b></td>';
            cols += '<td class="td02" colspan="8"><b></b></td>';
            newRow.append(cols).hide();
            $(currentrow).after(newRow);
            newRow.attr('style', "background-color:#ADD8E6; vertical-align: middle;");
            newRow.attr('id', "tr_HI" + escplus(order) + "_" + item);

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
                cols += '<td class="first_color" colspan="11" style="' + first_style + '"></td>';
                @if ($groupByPO == 4)
                    let colsreason = "12";
                    cols += '<td class="first_color" colspan="1" style="' + first_style + '"></td>';
                @else
                    let colsreason = "12";
                @endif
                cols += '<td class="coloured" style="' + last_style + '"></td>';
                cols += '<td style="' + pi_style + '"></td>';
                cols += '<td class="td02" colspan="3">' + pitemchg.cdate + '</td>';
                cols += '<td class="td02" colspan="2">' + pitemchg.cuser + '</td>';
                cols += '<td class="td02" colspan="4">' + pitemchg.cuser_name + '</td>';
                cols += '<td class="td02" colspan="8">' + pitemchg.text + '</td>';
                @if ($groupByPO != 4)
                    colsreason = (parseInt(colsreason) + 1).toString();
                @endif
                cols += '<td class="td02" colspan="' + colsreason + '">' + pitemchg.reason + '</td>';
                cols += '<td class="td02" colspan="8"></td>';
                newRow.append(cols).hide();
                $(prevrow).after(newRow);
                if (i % 2 == 0)
                    newRow.attr('style', "background-color:Azure; vertical-align: middle;");
                else
                    newRow.attr('style', "background-color:LightCyan; vertical-align: middle;");
                newRow.attr('id', "tr_C" + escplus(pitemchg.ebeln) + "_" + pitemchg.ebelp + "_" + i);
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

        function acceptPOrder(thisbtn, acceptmode) {
            var currentrow;
            let rowid = (currentrow = $(thisbtn).parent().parent()).attr('id').toUpperCase();
            let rowtype = rowid.substr(3, 1); // P
            let porder = unescplus(rowid.substr(4, 10));
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
                    history: $("filter_history").val(),
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
                let itemlist = [];
                for (let i = 0; i < _dataAP.length; i++) {
                    if (isChecked('I' + _dataAP[i].ebeln + "_"+_dataAP[i].ebelp)) {
                        // _unused_acceptItem(porder, _dataAP[i].ebelp, null, false);
                        itemlist.push(_dataAP[i].ebelp);
                    }
                }
                p_accept = currentrow.attr("data-accept");
                if (itemlist.length == 0 && p_accept != 0) {
                    for (let i = 0; i < _dataAP.length; i++) {
                        if ($("#tr_I" + escplus(_dataAP[i].ebeln) + "_"+_dataAP[i].ebelp).attr("data-accept") == p_accept)
                            itemlist.push(_dataAP[i].ebelp);
                    }
                }
                if (itemlist.length > 0) {
                    acceptItemList(porder, itemlist);
                    location.reload(true);
                }
            }
        }

        function inquirePOrder(thisbtn) {
            var currentrow;
            let rowid = (currentrow = $(thisbtn).parent().parent()).attr('id').toUpperCase();
            let rowtype = rowid.substr(3, 1); // P
            let porder = unescplus(rowid.substr(4, 10));
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
            let porder = unescplus(rowid.substr(4, 10));
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
            let porder = unescplus(rowid.substr(4, 10));
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
                let inputs = $("input[name^='I" + porder + "_']");
                let checked = 0;
                for (let i = 0; i < inputs.length; i++) {
                    if (inputs[i].checked) checked++;
                }
                for (let i = 0; i < _dataRP.length; i++) {
                    if (checked == 0 || isChecked('I' + _dataRP[i].ebeln + '_' + _dataRP[i].ebelp)) {
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
            let porder = unescplus(rowid.substr(4, 10));
            let item = rowid.substr(15, 5);
            if (category == "5") {
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
                accept_reject_dialog2(2, thisbtn, _dataIR, "Propunere pozitie noua", "Ati rejectat aceasta pozitie, propunei aici alte variante");
            } else {
                doRejectItem(porder, item, category, reason,
                    @if (\Illuminate\Support\Facades\Auth::user()->role == "Furnizor")
                      'R', 'R'
                    @else
                      'X', 'Z'
                    @endif
                );
            }
            location.reload(true);
        }

        function requestRejectPItem(thisbtn, mode) {
            var currentrow;
            let rowid = (currentrow = $(thisbtn).parent().parent()).attr('id').toUpperCase();
            let rowtype = rowid.substr(3, 1); // I
            let porder = unescplus(rowid.substr(4, 10));
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

        function inquirePItem(thisbtn, pnad_status) {
            var currentrow;
            let rowid = (currentrow = $(thisbtn).parent().parent()).attr('id').toUpperCase();
            let rowtype = rowid.substr(3, 1); // I
            let porder = unescplus(rowid.substr(4, 10));
            let item = rowid.substr(15, 5);

            @if (\Illuminate\Support\Facades\Auth::user()->role == "Furnizor")
                send_inquiry(porder, item, pnad_status);
            @else
                if (pnad_status == "X") {
                    swal({
                        title: "{{__('Aceasta pozitie a fost deja marcata ca fiind rezolvata')}}",
                        text: "{{__('Doriti sa marcati inapoi aceasta pozitie ca fiind NErezolvata?')}}",
                        icon: 'warning',
                        buttons: ["{{__('No')}}", "{{__('Da, marcheaza din nou ca nerezolvata')}}"],
                    }).then(function(result) {
                        if (result) {
                            send_inquiry(porder, item, pnad_status);
                        }
                    })
                }
                else
                    send_inquiry(porder, item, pnad_status);
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
            let porder = unescplus(rowid.substr(4, 10));
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
                    dangerMode: true,
                    buttons: {cancel: "Not yet",
                              no: "{{__('Do not agree')}}",
                              yes: "{{__('Agree with cancellation')}}"}
                }).then(function(result) {
                    if (result == "yes") {
                        doRejectItem(porder, item, 'G', '', 'X', 'Z');
                        location.reload(true);
                    } else
                    if (result == "no") {
                        _unused_acceptItem(porder, item, "F", true);
                    }
                })
            }
            @elseif (\Illuminate\Support\Facades\Auth::user()->role == "Referent")
            if (mode == 1) {
                accept_reject_dialog2(1, thisbtn, _dataIR, "Acceptare pozitie modificata", "Anumite campuri ale pozitiei au fost modificate - puteti accepta modificarile sau propune altele");
            }
            if (mode == 2) {
                accept_reject_dialog2(2, thisbtn, _dataIR, "Rejectare pozitie", "Furnizorul a rejectat aceasta pozitie - puteti propune alte variante");
            }
            if (mode == 3) {
                split_dialog('2', thisbtn, porder, item, "Selectie/Split item");
                // select_proposal(mode, thisbtn, _dataIR, "Selectie split", "Furnizorul a efectuat spargerea pozitiei in mai multe materiale, selectati o decizie");
            }
            @elseif (\Illuminate\Support\Facades\Auth::user()->role == "CTV")
                if (mode != 3)
                select_proposal(mode, thisbtn, _dataIR, "{{__('Selectie propunere')}}", "{{__('Furnizorul a cerut modificari ale conditiilor de aprovizionare - selectati una din propunerile referentului')}}");
                else
                select_proposal(mode, thisbtn, _dataIR, "Decizie split item", "Furnizorul a efectuat spargerea pozitiei in mai multe materiale, decideti daca acceptati");
            @elseif (\Illuminate\Support\Facades\Auth::user()->id == "radu" || \Illuminate\Support\Facades\Auth::user()->role == "Administrator")
            if (_dataIR.stage == 'R') {
                if (mode == 1) {
                    accept_reject_dialog2(1, thisbtn, _dataIR, "Acceptare pozitie modificata", "Anumite campuri ale pozitiei au fost modificate - puteti accepta modificarile sau propune altele");
                }
                if (mode == 2) {
                    accept_reject_dialog2(2, thisbtn, _dataIR, "Rejectare pozitie", "Furnizorul a rejectat aceasta pozitie - puteti propune alte variante");
                }
            } else if (_dataIR.stage == 'C') {
                select_proposal(mode, thisbtn, _dataIR, "{{__('Selectie propunere')}}", "{{__('Furnizorul a cerut modificari ale conditiilor de aprovizionare - selectati una din propunerile referentului')}}");
            }
            @elseif (\Illuminate\Support\Facades\Auth::user()->role == "Administrator")
            if (_dataIR.stage == 'R') {
                if (mode == 1) {
                    accept_reject_dialog2(1, thisbtn, _dataIR, "Acceptare pozitie modificata", "Anumite campuri ale pozitiei au fost modificate - puteti accepta modificarile sau propune altele");
                }
                if (mode == 2) {
                    accept_reject_dialog2(2, thisbtn, _dataIR, "Rejectare pozitie", "Furnizorul a rejectat aceasta pozitie - puteti propune alte variante");
                }
            } else if (_dataIR.stage == 'C') {
                select_proposal(mode, thisbtn, _dataIR, "{{__('Selectie propunere')}}", "{{__('Furnizorul a cerut modificari ale conditiilor de aprovizionare - selectati una din propunerile referentului')}}");
            }
            @endif
        }

        function doChangeItem(c_type, c_value, c_value_hlp, old_value, c_ebeln, c_ebelp, c_backorder) {
            var c_string = "";
            let comma_count = 0;
            let dot_count = 0;
            let v_backorder = 0;
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
                    v_backorder = c_backorder ? 1 : 0;
                    break;
                case 5:
                    c_string = "purch_price";
                    if ((comma_count + dot_count) > 1) return false;
                    if (comma_count == 1) c_value = c_value.replace(/,/g, '.');
                    if (!($.isNumeric(c_value)) || c_value.startsWith('-'))
                        return false;
                    break;
                case 6:
                    c_string = "etadt";
                    var d = new Date(c_value);
                    if (isNaN(d.valueOf()))
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
                    ebelp: c_ebelp,
                    backorder: v_backorder,
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

        function doChangeDeliveryDate(c_value, old_value, c_ebeln, c_ebelp, c_backorder, c_delay_check, c_delay_date) {
            let v_backorder = 0;
            let v_delay_check = 0;
            let v_delay_date = "";

            var d = new Date(c_value);
            if (c_value.trim() != "") {
                if (isNaN(d.valueOf())) return false;
            }
            v_backorder = c_backorder ? 1 : 0;
            v_delay_check = c_delay_check ? 1 : 0;
            if (c_delay_date.trim() != "") {
                d = new Date(c_delay_date);
                if (isNaN(d.valueOf())) return false;
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var _dataC, _statusC;
            jQuery.ajaxSetup({async: false});
            $.post("webservice/dochangedlvdate",
                {
                    value: c_value,
                    oldvalue: old_value,
                    ebeln: c_ebeln,
                    ebelp: c_ebelp,
                    backorder: v_backorder,
                    delay_check: v_delay_check,
                    delay_date: c_delay_date,
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

        function doChangeETADate(c_value, old_value, c_ebeln, c_ebelp, c_delay_check, c_delay_date) {
            let v_delay_check = c_delay_check ? 1 : 0;
            if (c_delay_date.trim() != "") {
                d = new Date(c_delay_date);
                if (isNaN(d.valueOf())) return false;
            }

            var d = new Date(c_value);
            if (c_value.trim() != "") {
                if (isNaN(d.valueOf())) return false;
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var _dataC, _statusC;
            jQuery.ajaxSetup({async: false});
            $.post("webservice/dochangedlvdate",
                {
                    value: c_value,
                    oldvalue: old_value,
                    ebeln: c_ebeln,
                    ebelp: c_ebelp,
                    backorder: "ETADT",
                    delay_check: v_delay_check,
                    delay_date: c_delay_date,
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

        var change_cell, change_type, type_string, change_ebeln, change_ebelp, change_vbeln, changeDialog, changeForm;

        $(function () {
            changeDialog = $("#change-dialog").dialog({
                autoOpen: false,
                height: 220,
                width: 420,
                modal: true,
                buttons: {
                    Change: function () {
                        let new_val = $("#new_chg_val").val().trim();
                        let delayed_date = $("#eta_delayed_date").val().trim();
                        if ((type_string == "LFDAT") || (new_val.length > 0)) {
                            if (type_string == "LFDAT") {
                                if ($("#eta_delayed_check").is(":checked") && delayed_date.length == 0) {
                                    alert("{{__('Enter a value for alert date')}}");
                                    return;
                                }
                                if (doChangeDeliveryDate(new_val, change_cell.innerHTML, change_ebeln, change_ebelp,
                                    $("#item_backorder").is(":checked"), $("#eta_delayed_check").is(":checked"),
                                    delayed_date)) {
                                    $("#new_chg_val").text("");
                                    $("#new_val_hlp").text("");
                                    $("#eta_delayed_check").prop("checked", false);
                                    $("#eta_delayed_date").text("");
                                    changeDialog.dialog("close");
                                    location.reload(true);
                                }
                            } else
                            if (type_string == "ETADT") {
                                if ($("#eta_delayed_check").is(":checked") && delayed_date.length == 0) {
                                    alert("{{__('Enter a value for alert date')}}");
                                    return;
                                }
                                if (doChangeETADate(new_val, change_cell.innerHTML, change_ebeln, change_ebelp,
                                    $("#eta_delayed_check").is(":checked"), delayed_date)) {
                                    $("#new_chg_val").text("");
                                    $("#new_val_hlp").text("");
                                    $("#eta_delayed_check").prop("checked", false);
                                    $("#eta_delayed_date").text("");
                                    changeDialog.dialog("close");
                                    location.reload(true);
                                }
                            } else
                            if (type_string != "CTV") {
                                if (doChangeItem(change_type, new_val, $("#new_val_hlp").text(),
                                    change_cell.innerHTML, change_ebeln, change_ebelp, $("#item_backorder").is(":checked"))) {
                                    $("#new_chg_val").text("");
                                    $("#new_val_hlp").text("");
                                    changeDialog.dialog("close");
                                    location.reload(true);
                                } else {
                                    alert("CTV-ul nu este definit");
                                }
                            }
                        } else {
                            alert("{{__('Enter a correct value')}}");
                        }
                    },
                    Cancel: function () {
                        changeDialog.dialog("close");
                    }
                },
                open: function() {
                    if (type_string != "LFDAT") {
                        $("#change-dialog").dialog("option", "height", 220);
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
            $("#backorder-row").hide();
            $("#eta-delayed-row").hide();
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
            $("#backorder-row").hide();
            $("#eta-delayed-row").hide();
            changeDialog.dialog("open");
        }

        function change_delivery_date2(cell, ebeln, ebelp, backorder) {
            swal({
                title: "{{__('Please confirm changing the delivery date for this item')}}",
                text: "{{__('You have issued earlier a confirmation for this item. Are you sure you want now to change its delivery date? ')}}",
                icon: 'warning',
                buttons: ["{{__('No')}}", "{{__('Ask MATEROM to check a new delivery date')}}"],
            }).then(function(result) {
                if (result) {
                    change_delivery_date(cell, ebeln, ebelp, backorder)
                }
            })
        }

        function change_delivery_date(cell, ebeln, ebelp, backorder) {

            change_cell = cell;
            change_type = 4;
            let old_value = cell.innerHTML;
            change_ebeln = ebeln;
            change_ebelp = ebelp;

            type_string = "LFDAT";
            $("#old_chg_val").text("Data de livrare existenta: " + old_value);
            $("#new_chg_val").val("");
            $("#new_val_txt").text("Introduceti noua data de livrare:");
            $("#new_val_hlp").text("");
            $("#change-dialog").dialog('option', 'title', 'Modificare data livrare pentru pozitia ' + ebelp);
            $("#backorder-row").show();
            $("#eta-delayed-row").show();
            $("#item_backorder").prop("checked", backorder != 0);
            $("#item_backorder").prop("disabled", backorder != 0);
            if (backorder != 0) {
                $("#eta_delayed_date").prop("enabled", 1);
                $("#eta_delayed_check").prop("enabled", 1);
                $("#eta_delayed_check").prop("checked", 1);
            } else {
                $("#eta_delayed_date").prop("enabled", 0);
                $("#eta_delayed_check").prop("enabled", 0);
                $("#eta_delayed_check").prop("checked", 0);
            }
            $("#change-dialog").dialog("option", "height", 250);
            $("#new_chg_val").datepicker({dateFormat: "yy-mm-dd"});
            $("#eta_delayed_date").datepicker({dateFormat: "yy-mm-dd"});
            changeDialog.dialog("open");
        }

        function change_ctv(cell, vbeln) {

            change_cell = cell;
            change_type = 1;
            let old_value = cell.innerHTML;
            change_vbeln = vbeln;
            type_string = "CTV";


            $("#old_chg_val").text("CTV-ul existent: " + old_value);
            $("#new_chg_val").val("");
            $("#new_val_txt").text("Introduceti noul CTV:");
            $("#new_val_hlp").text("");
            $("#change-dialog").dialog('option', 'title', 'Modificare CTV comanda ' + conv_exit_alpha_output(vbeln));
            $("#backorder-row").hide();
            $("#eta-delayed-row").hide();
            changeDialog.dialog("open");
        }

        function delivery_date_oncontenxtmenu(e, ebeln, ebelp, deldatechangeable) {
            $(".ui-tooltip").hide();
            $("#delivery-date-menu-mark-delivered").unbind("click");
            $("#delivery-date-menu-mark-delivered").click(function(){delivery_date_menu_mark_delivered(ebeln, ebelp)});
            /*
            $("#delivery-date-menu-mark-backorder").unbind("click");
            if (deldatechangeable != 0) {
                $("#delivery-date-menu-mark-backorder").removeClass("ui-state-disabled");
                $("#delivery-date-menu-mark-backorder").click(function(){delivery_date_menu_mark_backorder(ebeln, ebelp)});
            } else {
                $("#delivery-date-menu-mark-backorder").addClass("ui-state-disabled");
            }
            */
            e.preventDefault();
            e.stopPropagation();
            $("#delivery-date-menu").menu().toggle().position({
                my: "left top",
                at: "left+2px top+2px",
                of: e,
                collision: "none"}
            );
        }

        function delivery_date_menu_mark_delivered(ebeln, ebelp, dlvcompleted = true) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajaxSetup({async: false});
            var _data, _status;
            $.get("webservice/sap_poitem_dlvcompleted",
                {
                    ebeln: ebeln,
                    ebelp: ebelp,
                    dlvcompleted: dlvcompleted
                },
                function (data, status) {
                    _data = data;
                    _status = status;
                });
            jQuery.ajaxSetup({async: true});
            if (_status != "success") alert("An error occurred setting the delivery completed flag");
            else if(_data != null && _data.trim().length != 0) alert(_data);
            else location.reload();
        }

        function delivery_date_menu_mark_backorder(ebeln, ebelp) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajaxSetup({async: false});
            var _data, _status;
            $.get("webservice/sap_poitem_backorder",
                {
                    ebeln: ebeln,
                    ebelp: ebelp
                },
                function (data, status) {
                    _data = data;
                    _status = status;
                });
            jQuery.ajaxSetup({async: true});
            if (_status != "success") alert("An error occurred marking the position as a backorder");
            else if(_data != null && _data.trim().length != 0) alert(_data);
            else location.reload();
        }

        function change_eta_date(cell, ebeln, ebelp) {

            change_cell = cell;
            change_type = 6;
            let old_value = cell.innerHTML;
            change_ebeln = ebeln;
            change_ebelp = ebelp;

            type_string = "ETADT";
            $("#old_chg_val").text("Data estimata curenta: " + old_value);
            $("#new_chg_val").val("");
            $("#new_val_txt").text("Introduceti noua data estimata:");
            $("#new_val_hlp").text("");
            $("#change-dialog").dialog('option', 'title', 'Modificare data estimata pentru pozitia ' + ebelp);
            $("#backorder-row").hide();
            $("#eta-delayed-row").show();
            $("#new_chg_val").datepicker({dateFormat: "yy-mm-dd"});
            $("#eta_delayed_date").datepicker({dateFormat: "yy-mm-dd"});
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
            $("#backorder-row").hide();
            $("#eta-delayed-row").hide();
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
        <table style="border: none; padding: 0px;" width="100%">
            <tr>
                <td style="width: 15rem;">
                    <input id="new_chg_val" type="text" name="new_chg_val" style="width: 100%; height: 24px;"
                           class="form-control" value=""></td>
                <td style="text-align: left; width: 2rem;"><b style="text-align: left; margin-left: 4px;" id="new_val_hlp"></b></td>
                <td></td>
            </tr>
            <tr id="backorder-row">
                <td colspan="2" style="padding-top: 0.5em;">
                    <input type="checkbox" style="float: left; height: 24px;" id="item_backorder" name="item_backorder">
                    <label for="item_backorder" style="margin-left: 0.7em; margin-top: 0.5em;">{{ __('Backorder') }}</label>
                </td>
            </tr>
            <tr id="eta-delayed-row">
                <td>
                    <input type="checkbox" style="float: left; height: 24px;" id="eta_delayed_check" name="eta_delayed_check">
                    <label for="eta_delayed_check" style="margin-left: 0.7em; margin-top: 0.5em;">{{ __('Delay ETA checks until') }}</label>
                </td>
                <td style="width: 8rem;">
                    <input id="eta_delayed_date" type="text" name="eta_delayed_date" style="width: 100%; height: 24px;"
                           class="form-control" value="">
                </td>
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
                @if (\Illuminate\Support\Facades\Auth::user()->role == "Referent")
                    <option value="5">{{__("Propose new variant")}}</option>
                @endif
            </select>
        </div>
        <br>
        <div class="row">
            <label for="reject-reason"
                   class="col-md-2 col-form-label text-md-left">{{__("Explanations")}}</label>&nbsp;&nbsp;
            <textarea id="reject-reason" type="text" name="reject-reason" class="form-control col-md-9"
                      style="word-break: break-word; height: 4rem;" maxlength="255" value=""></textarea>
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
            $(function() {
                getSubTree(autoexplode_so_button[0]);
                autoexplode_so_button[0].scrollIntoView();
            });
        }
        @endif

        @if (isset($autoexplode_PO) && $autoexplode_PO != null)
            let autoexplode_po_button = $('#butt_P' + escplus('{{$autoexplode_PO}}'));
            if (autoexplode_po_button != null && autoexplode_po_button != undefined) {
                $(function() {
                    getSubTree(autoexplode_po_button[0]);
                    autoexplode_po_button[0].scrollIntoView();
                });
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
        @if ($filter_inquirements <> 0)
            Location.reload(true);
        @else
            $("#tr_I" + escplus(ebeln) + "_" + ebelp + " td:eq(1)").html("");
        @endif
    }

    function ack_bell(ebeln, ebelp, mode) {

        var _data, _status = "";

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajaxSetup({async: false});
        $.post("webservice/acknowledgebell",
            {
                ebeln: ebeln,
                ebelp: ebelp,
                mode: mode
            },
            function (data, status) {
                _data = data;
                _status = status;
            });
        jQuery.ajaxSetup({async: true});
        if (mode == "B" || mode == "C") {
            $("#tr_I" + escplus(ebeln) + "_" + ebelp).fadeOut();
        } else {
            $("#tr_I" + escplus(ebeln) + "_" + ebelp + " td:eq(1)").html("");
        }
    }

    function replyack2(ebeln) {
        var _data, _status = "";
    }

    function orderItemTools(e, _this) {
        var currentrow;
        let rowid = (currentrow = $(_this).parent().parent()).attr('id').toUpperCase();
        let rowtype = rowid.substr(3, 1); // I
        let porder = unescplus(rowid.substr(4, 10));
        let item = rowid.substr(15, 5);
        $("#order-tools-menu-archive").unbind("click");
        $("#order-tools-menu-archive").click(function(){item_tools_archive(porder, item, currentrow)});
        $("#order-tools-menu-unarchive").unbind("click");
        $("#order-tools-menu-unarchive").click(function(){item_tools_unarchive(porder, item, currentrow)});
        $("#order-tools-menu-rollback").unbind("click");
        $("#order-tools-menu-rollback").click(function(){item_tools_rollback(porder, item, currentrow)});
        e.stopPropagation();
        $("#order-tools-menu").menu().toggle().position({
            my: "left top",
            at: "right-8px top+8px",
            of: $(_this),
            collision: "fit flip"}
        );
    }

    function item_tools_archive(porder, item, currentrow) {
        swal({
            title: "{{__('Confirmation')}}",
            text: "{{__('Are you sure you want to archive this item now?')}}",
            icon: 'warning',
            buttons: ["{{__('No')}}", "{{__('Yes, archive it')}}"],
        }).then(function(result) {
            if (result) {
                jQuery.ajaxSetup({async: false});
                var _data, _status;
                $.get("webservice/archive_item",
                    {
                        porder: porder,
                        item: item
                    },
                    function (data, status) {
                        _data = data;
                        _status = status;
                    });
                jQuery.ajaxSetup({async: true});
                if (_status == null || _status == undefined)
                    _data = '{{__("An error occurred archiving this item")}}';
                if (_data != "OK") {
                    alert(_data);
                    return;
                }
                $(currentrow).fadeOut('slow', function(){$(currentrow).remove();});
            }
        })
    }

    function item_tools_unarchive(porder, item, currentrow) {
        swal({
            title: "{{__('Confirmation')}}",
            text: "{{__('Are you sure you want to unarchive this item now?')}}",
            icon: 'warning',
            buttons: ["{{__('No')}}", "{{__('Yes, unarchive it')}}"],
        }).then(function(result) {
            if (result) {
                jQuery.ajaxSetup({async: false});
                var _data, _status;
                $.get("webservice/unarchive_item",
                    {
                        porder: porder,
                        item: item
                    },
                    function (data, status) {
                        _data = data;
                        _status = status;
                    });
                jQuery.ajaxSetup({async: true});
                if (_status == null || _status == undefined)
                    _data = '{{__("An error occurred unarchiving this item")}}';
                if (_data != "OK") {
                    alert(_data);
                    return;
                }
                $(currentrow).fadeOut('slow', function(){$(currentrow).remove();});
            }
        })
    }

    function item_tools_rollback(porder, item, currentrow) {
        swal({
            title: "{{__('Confirmation')}}",
            text: "{{__('Are you sure you want to rollback this item now?')}}",
            icon: 'warning',
            buttons: ["{{__('No')}}", "{{__('Yes, roll it back')}}"],
        }).then(function(result) {
            if (result) {
                jQuery.ajaxSetup({async: false});
                var _data, _status;
                $.get("webservice/rollback_item",
                    {
                        porder: porder,
                        item: item
                    },
                    function (data, status) {
                        _data = data;
                        _status = status;
                    });
                jQuery.ajaxSetup({async: true});
                if (_status == null || _status == undefined)
                    _data = '{{__("An error occurred rolling back this item")}}';
                if (_data != "OK") {
                    alert(_data);
                    return;
                }
                location.reload();
            }
        })
    }

    function massChangeMenu(e, _this) {
        $(".ui-tooltip").hide();
        $("#mass-change-menu-download").click(function(){mass_change_download()});
        $("#mass-change-menu-upload").click(function(){mass_change_upload()});
        e.stopPropagation();
        $("#mass-change-menu").menu().toggle().position({
            my: "right top",
            at: "right-60px top+12px",
            of: $(_this),
            collision: "fit flip"}
        );
    }

    function orders_submit()
    {
        $(".orders-table-div").fadeOut(1000, function() {$("body").addClass("ajaxloading")});
    }

    function escplus(id) {
        if (id == undefined || id == null) return null;
        let _id = id;
        if (_id.substr(0, 1) == '+') _id = "X" + id.substr(1);
        return _id;
    }

    function unescplus(id) {
        if (id == undefined || id == null) return null;
        let _id = id;
        if (_id.substr(0, 1) == 'X') _id = "+" + id.substr(1);
        return _id;
    }

    $(document).on("click", function(e){
        $("#order-tools-menu").hide();
        $("#mass-change-menu").hide();
        $("#delivery-date-menu").hide();
    });

</script>

@include("orders.read_inforecords")
@include("orders.read_zpretrecords")
@include("orders.pnad_filters")
@include("orders.accept-reject")
@include("orders.accept-reject2")
@include("orders.split-item")
@include("orders.inquiries")
@include("orders.file-upload")
@include("orders.file-download")
@include("orders.search_document")

@endsection