@extends('layouts.app')

@section('content')
    @guest
        @php
            header("/");
            exit();
        @endphp
    @endguest
    @php
        use Illuminate\Support\Facades\DB;

        if(strcmp( (\Illuminate\Support\Facades\Auth::user()->role), "Furnizor" ) == 0){
            $furnizor = true;
        } else {
            if(isset($_POST['all']) && strcmp($_POST['all'],"true") == 0)
                $furnizor = false;
            else if (isset($_POST['all']))
                $furnizor = true;
            else
                $furnizor = false;
        }

        if($furnizor){
            $selF = " selected";
            $selAll = "";
        } else {
            $selF = "";
            $selAll = " selected";
        }

    @endphp
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header" style="border-bottom-width: 0px;">
                        @if(strcmp( (\Illuminate\Support\Facades\Auth::user()->role), "Administrator" ) == 0)
                            <a href="/roles"><p
                                        style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;"
                                        class="card-line first">Roles</p></a>
                            <a href="/users"><p
                                        style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;"
                                        class="card-line">Users</p></a>
                            <a href="/messages"><p
                                        style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;"
                                        class="card-line">Messages</p></a>
                            <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;"
                               class="card-line selector">Comenzi</p>
                        @else
                            <a href="/messages"><p
                                        style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;"
                                        class="card-line first">Messages</p></a>
                            <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;"
                               class="card-line selector">Comenzi</p>
                        @endif
                    </div>

                    <div class="card-body orders-table-div">

                        @if(strcmp( (\Illuminate\Support\Facades\Auth::user()->role), "Furnizor" ) != 0)
                            <form action="orders" method="post">
                                Afisare dupa:
                                <select name="all" onchange="this.form.submit()">
                                    <option value="true"{{$selAll}}>comenzi de vanzare</option>
                                    <option value="false"{{$selF}}>comenzi de aprovizionare</option>
                                </select>
                                {{csrf_field()}}
                            </form>
                        @endif

                        <table class="orders-table basicTable table table-striped" id="orders_table">
                            <colgroup>
                                <col width="1%">
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
                                <col width="3%">
                                <col width="3%">
                                <col width="3%">
                                <col width="3%">
                                <col width="3%">
                                <col width="3%">
                                <col width="3%">
                                <col width="3%">
                                <col width="5%">
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
                                    <image style='height: 1.3rem;' src='/images/icons8-unchecked-checkbox-50-3.png'/>
                                </th>
                                <th colspan="1" class="td01">
                                    <image style='height: 1.5rem;' src='/images/icons8-checkmark-50-3.png'/>
                                </th>
                                <th colspan="1" class="td01">
                                    <image style='height: 1.5rem;' src='/images/icons8-close-window-50.png'/>
                                </th>
                                <th colspan="1" class="td01">
                                    <image style='height: 1.5rem;' src='/images/icons8-error-50.png'/>
                                </th>
                                @php
                                    if($furnizor){
                                        echo '<th class="td02" colspan="3">Comanda aprovizionare</th>';
                                        $th1 = "LIFNR";
                                        $th2 = "LIFNR_NAME";
                                        $th3 = "EKGRP";
                                        $th4 = "EKGRP_NAME";
                                        $th5 = "ERDAT";
                                        $th6 = "CURR";
                                        $th7 = "FXRATE";
                                        } else {
                                        echo '<th class="td02" colspan="3">Comanda vanzare</th>';
                                        $th1 = "KUNNR";
                                        $th2 = "KUNNR_NAME";
                                        $th3 = "SHIPTO";
                                        $th4 = "SHIPTO_NAME";
                                        $th5 = "CTV";
                                        $th6 = "CTV_NAME";
                                        $th7 = "";
                                        }
                                @endphp
                                <th>{{$th1}}</th>
                                <th>{{$th2}}</th>
                                <th>{{$th3}}</th>
                                <th>{{$th4}}</th>
                                <th>{{$th5}}</th>
                                <th>{{$th6}}</th>
                                <th>{{$th7}}</th>
                                @php for ($i = 0; $i < 21; $i++) echo "<th>&nbsp;</th>"; @endphp
                            </tr>
                            @php
                                $id = \Illuminate\Support\Facades\Auth::user()->id;
                                $uname = \Illuminate\Support\Facades\Auth::user()->username;

                                $orders = \App\Materom\Data::getOrders($id, $furnizor);

                                echo "<input type=\"hidden\" id=\"set-furnizor\" value=\"$furnizor\">";
                                echo "<input type=\"hidden\" id=\"user_id\" value=\"$id\">";
                                echo "<input type=\"hidden\" id=\"user_name\" value=\"$uname\">";

                                $seen = "";
                                $line_counter = 1;
                                foreach ($orders as $order) {

                                    if($furnizor){
                                        if(strchr($seen,$order->ebeln) == null)
                                            $seen.= " $order->ebeln";
                                        else
                                            continue;
                                        $viewebeln = substr($order->ebeln, 0, 10);
                                        $comanda = "<button type='button' id='btn_P$order->ebeln' onclick='loadSub(\"$order->ebeln\",\"purch-order\",this, \"$order->vbeln\"); return false;'>+</button> $viewebeln";
                                    } else {
                                        $lvbeln = $order->vbeln;
                                        if(strchr($seen,$lvbeln) == null)
                                            $seen.= " $lvbeln";
                                        else
                                            continue;

                                        $comanda = "<button type='button' id='btn_S$lvbeln' onclick='loadSub(\"$order->vbeln\",\"sales-order\",this, \"\"); return false;'>+</button> $order->vbeln";
                                    }

                                    $line_counter = $line_counter + 1;
                                    if ($line_counter == 2) $line_counter = 0;

                                    if($order->nof)
                                        $nof = "<image style='height: 1rem;' src='/images/nof.png'>";
                                    else
                                        $nof = "";

                                    $now = strtotime(date('Y-m-d H:i:s'));
                                    $wtime = strtotime($order->wtime);
                                    $ctime = strtotime($order->ctime);

                                    $status = "<image style='height: 1rem;' src='/images/status.png'>"; //TODO
                                    $buttonok = "<button type='button' class='order-button-accepted' style='width: 1.5rem; height: 1.5rem;'/>";
                                    $buttoncancel = "<button type='button' class='order-button-rejected' style='width: 1.6rem; height: 1.5rem;'/>";
                                    $buttonrequest = "<button type='button' class='order-button-request' style='width: 1.5rem; height: 1.5rem;'/>";

                                    if($furnizor){
                                        $oid = "P" . $order->ebeln;
                                        $data = "<td>$order->lifnr</td><td>$order->lifnr_name</td><td>$order->ekgrp</td><td>$order->ekgrp_name</td><td>$order->erdat</td><td>$order->curr</td><td>$order->fxrate</td>";
                                        switch (\App\Materom\Webservice::getGravity($order, "purch-order")){
                                            case 0:
                                                $info = "";
                                            break;
                                            case 1:
                                                $info = "<image style='height: 1.2rem;' src='/images/warning.png'>";
                                            break;
                                            case 2:
                                                $info = "<image style='height: 1.2rem;' src='/images/critical.png'>";
                                            break;
                                        }
                                        switch (\App\Materom\Webservice::getOwner($order,"purch-order")){
                                            case 0:
                                                $owner = "";
                                            break;
                                            case 1:
                                                $owner = "<image style='height: 1.2rem;' src='/images/yellowArrow.png'>";
                                            break;
                                            case 2:
                                                $owner = "<image style='height: 1.2rem;' src='/images/blueArrow.png'>";
                                            break;
                                        }
                                        if ($line_counter == 0)
                                            $style = "background-color:LightYellow;";
                                        else
                                            $style = "background-color:Wheat;";
                                        echo "<tr id='tr_$oid' style='$style' class='td01' colspan='1'><td align='center' style='vertical-align: middle;'><input id='input_chk' type=\"checkbox\" name=\"$oid\" value=\"$oid\" onclick='boxCheck(this);'></td><td>$info</td><td>$owner</td><td></td><td></td><td></td><td>6</td><td style='padding: 0;'>$buttonok</td><td style='padding: 0;'>$buttoncancel</td><td style='padding: 0;'>$buttonrequest</td><td colspan='3' class='first_color'>$comanda</td>$data<td colspan='20'></td></tr>";
                                    }else{
                                        $oid = "S" . $order->vbeln;
                                        $data = "<td>$order->kunnr</td><td>$order->kunnr_name</td><td>$order->shipto</td><td>$order->shipto_name</td><td>$order->ctv</td><td>$order->ctv_name</td><td></td>";
                                        switch (\App\Materom\Webservice::getGravity($order, "sales-order")){
                                            case 0:
                                                $info = "";
                                            break;
                                            case 1:
                                                $info = "<image style='height: 1.2rem;' src='/images/warning.png'>";
                                            break;
                                            case 2:
                                                $info = "<image style='height: 1.2rem;' src='/images/critical.png'>";
                                            break;
                                        }
                                        switch (\App\Materom\Webservice::getOwner($order,"sales-order")){
                                            case 0:
                                                $owner = "";
                                            break;
                                            case 1:
                                                $owner = "<image style='height: 1.2rem;' src='/images/yellowArrow.png'>";
                                            break;
                                            case 2:
                                                $owner = "<image style='height: 1.2rem;' src='/images/blueArrow.png'>";
                                            break;
                                        }
                                        if ($line_counter == 0)
                                            $style = "background-color:white;";
                                        else
                                            $style = "background-color:WhiteSmoke;";
                                        echo "<tr id='tr_$oid' style='$style' class='td01' colspan='1'><td align='center' style='vertical-align: middle;'><input id='input_chk' type=\"checkbox\" name=\"$oid\" value=\"$oid\" onclick='boxCheck(this);'></td><td>$info</td><td>$owner</td><td colspan='7'></td><td colspan='3' class='first_color'>$comanda</td>$data<td colspan='21'></td></tr>";
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

        var checkedList = [];
        var unCheckedList = [];

        function parent(id) {
            if (id.startsWith('I')) {
                let res = id.substring(1);
                if (($("#set-furnizor").val() == ""))
                    return $("input[name*='_" + res.split("_")[0] + "']")[0].name;
                else
                    return $("input[name*='" + res.split("_")[0] + "']")[0].name;
            } else if (id.startsWith('P')) {

                if (($("#set-furnizor").val() == "1"))
                    return null;

                let res = id.substring(1);
                return "S" + res.split("_")[0];
            } else {
                return null;
            }
        }

        function isChecked(id) {

            if ($.inArray(id, checkedList) > -1)
                return true;
            if ($.inArray(id, unCheckedList) > -1)
                return false;

            if (id.startsWith('S') || (($("#set-furnizor").val() != "") && id.startsWith('P'))) {
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

        function accept(_this,id,type) {
            var _data,_status = "";
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajaxSetup({async: false});

            $.post("webservice/acceptItemCHG",
                {
                    id: id,
                    type: type,
                },
                function (data, status) {
                    _data = data;
                    _status = status;
                });
            jQuery.ajaxSetup({async: true});
            if (_status == "success") {
                switch (type) {
                    case 'item':
                        //show new row
                        if(_this.closest('tr').innerHTML.toString().includes(">-</button>")) {
                            var date = new Date();
                            var chdate = date.getFullYear() + "-" + date.getMonth() + "-" + date.getDay() + " " + date.getHours() + ":" + date.getMinutes() + ":" + date.getSeconds();
                            var cuser = $("#user_id").val();
                            var cuser_name = $("#user_name").val();
                            var ctext = "Acceptare";
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
                                let colsafter = "12";
                                if ($("#set-furnizor").val() == "")
                                    cols += '<td class="first_color" colspan="1" style="' + first_style + '"></td>';
                                else colsafter = "13";
                                cols += '<td class="coloured" style="' + last_style + '"></td>';
                                cols += '<td style="' + pi_style + '"></td>';
                                cols += "<td colspan='3'>" + chdate + "</td>";
                                cols += '<td colspan="4">' + cuser + ' ' + cuser_name + '</td>';
                                cols += '<td colspan="6">' + ctext + '</td>';
                                cols += '<td colspan="2">' + creason + '</td>';
                                cols += "<td colspan=" + colsafter + "></td>";
                                if ($("#set-furnizor").val() != "")
                                    cols += '<td></td>';
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
                        } else alert("Accepted!");
                        break;
                    case 'purch-order':
                        //TODO - apply to all children items
                        break;
                    case 'sales-order':
                        //TODO - apply to all children items
                        break;
                }
            } else alert('Error processing operation!');
        }

        function reject(_this,id,type){
            var _data,_status = "";
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajaxSetup({async: false});

            $.post("webservice/cancelItem",
                {
                    id: id,
                    type: type,
                },
                function (data, status) {
                    _data = data;
                    _status = status;
                });
            jQuery.ajaxSetup({async: true});
            if (_status == "success") {
                switch (type) {
                    case 'item':
                        //show new row
                        if(_this.closest('tr').innerHTML.toString().includes(">-</button>")) {
                            var date = new Date();
                            var chdate = date.getFullYear() + "-" + date.getMonth() + "-" + date.getDay() + " " + date.getHours() + ":" + date.getMinutes() + ":" + date.getSeconds();
                            var cuser = $("#user_id").val();
                            var cuser_name = $("#user_name").val();
                            var ctext = "Rejectare";
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
                                let colsafter = "12";
                                if ($("#set-furnizor").val() == "")
                                    cols += '<td class="first_color" colspan="1" style="' + first_style + '"></td>';
                                else colsafter = "13";
                                cols += '<td class="coloured" style="' + last_style + '"></td>';
                                cols += '<td style="' + pi_style + '"></td>';
                                cols += "<td colspan='3'>" + chdate + "</td>";
                                cols += '<td colspan="4">' + cuser + ' ' + cuser_name + '</td>';
                                cols += '<td colspan="6">' + ctext + '</td>';
                                cols += '<td colspan="2">' + creason + '</td>';
                                cols += "<td colspan=" + colsafter + "></td>";
                                if ($("#set-furnizor").val() != "")
                                    cols += '<td></td>';
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
                        } else alert("Rejected!");
                        break;
                    case 'purch-order':
                        //TODO - apply to all children items
                        break;
                    case 'sales-order':
                        //TODO - apply to all children items
                        break;
                }
            } else alert('Error processing operation!');
        }

        function loadSub(item, type, _btn, ebelp) {
            var _data, _status;
            var _this;
            if (type == "sales-order") _this = document.getElementById("tr_S" + item);
            if (type == "purch-order") _this = document.getElementById("tr_P" + item);
            if (type == "purch-item") _this = document.getElementById("tr_I" + item + "_" + ebelp);

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
                    item: ebelp
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
                            var image_info = "";
                            if(gravity == 1)
                                image_info = "<image style='height: 1.2rem;' src='/images/warning.png'>";
                            if(gravity == 2)
                                image_info = "<image style='height: 1.2rem;' src='/images/critical.png'>";
                            var image_owner = "";
                            if(owner == 1)
                                image_owner = "<image style='height: 1.2rem;' src='/images/yellowArrow.png'>";
                            if(owner == 2)
                                image_owner = "<image style='height: 1.2rem;' src='/images/blueArrow.png'>";
                            var newRow = $("<tr>");
                            var cols = "";
                            cols += '<td colspan="1" align="center" style="vertical-align: middle;"><input id="input_chk" onclick="boxCheck(this);" type="checkbox" name="P' + vbeln + "_" + id + '" value="P' + vbeln + "_" + id + '"></td>';
                            var so_style = "background-color:" + $(_this).css("background-color") + ";";
                            let buttonok = "<button type='button' class='order-button-accepted' style='width: 1.5rem; height: 1.5rem;'/>";
                            let buttoncancel = "<button type='button' class='order-button-rejected' style='width: 1.6rem; height: 1.5rem;'/>";
                            let buttonrequest = "<button type='button' class='order-button-request' style='width: 1.5rem; height: 1.5rem;'/>";
                            cols += '<td class="first_color td01" style="' + so_style + '" colspan="1">'+ image_info +'</td>';
                            cols += '<td class="first_color td01" style="' + so_style + '" colspan="1">'+image_owner+'</td>';
                            cols += '<td class="first_color td01" style="' + so_style + '" colspan="1"></td>';
                            cols += '<td class="first_color td01" style="' + so_style + '" colspan="1"></td>';
                            cols += '<td class="first_color td01" style="' + so_style + '" colspan="1"></td>';
                            cols += '<td class="first_color td01" style="' + so_style + '" colspan="1">6</td>';
                            cols += '<td class="first_color td01" style="' + so_style + '; padding: 0;" colspan="1">' + buttonok + '</td>';
                            cols += '<td class="first_color td01" style="' + so_style + '; padding: 0;" colspan="1">' + buttoncancel + '</td>';
                            cols += '<td class="first_color td01" style="' + so_style + '; padding: 0;" colspan="1">' + buttonrequest + '</td>';
                            cols += '<td class="first_color td01" style="' + so_style + '" colspan="1"></td>';
                            cols += "<td colspan='3'><button type='button' id='btn_P" + id + "' onclick=\"loadSub(\'" + id + "',\'purch-order\',this, \'" + vbeln + "\');\">+</button> " + id.substr(0, 10) + "</td>";
                            cols += '<td colspan="1">' + lifnr + '</td>';
                            cols += '<td colspan="1">' + lifnr_name + '</td>';
                            cols += '<td colspan="1">' + ekgrp + '</td>';
                            cols += '<td colspan="1">' + ekgrp_name + '</td>';
                            cols += '<td colspan="1">' + erdat + '</td>';
                            cols += '<td colspan="1">' + curr + '</td>';
                            cols += '<td colspan="1">' + fxrate + '</td>';
                            cols += '<td colspan="20"></td>';
                            newRow.append(cols).hide();
                            newRow.insertAfter($(_this).closest("tr")).fadeIn(250);
                            if (line_counter == 0)
                                newRow.attr('style', "background-color:LightYellow; vertical-align: middle;");
                            else
                                newRow.attr('style', "background-color:Wheat; vertical-align: middle;");
                            newRow.attr('id', "tr_P" + id);
                        } else if (type == 'purch-order') {
                            var ebeln2 = _ord.split('#')[0];
                            var id = _ord.split('#')[1];
                            var posnr = _ord.split('#')[2];
                            var idnlf = _ord.split('#')[3];
                            var owner2 = _ord.split('#')[4];
                            var stage = _ord.split('#')[5];
                            var newRow = $("<tr>");
                            var cols = "";
                            cols += '<td colspan="1" align="center" style="vertical-align: middle;"><input id="input_chk" onclick="boxCheck(this);" type="checkbox" name="I' + ebeln2 + "_" + id + '" value="I' + ebeln2 + "_" + id + '"></td>';
                            var po_style = "background-color:" + $(_this).css("background-color") + ";";
                            var first_color = $(_this).find(".first_color").css("background-color");
                            var first_style = "background-color:" + first_color;
                            let buttonok = "<button type='button' onclick='accept(this,\""+id+"\",\"item\");' class='order-button-accepted' style='width: 1.5rem; height: 1.5rem;'/>";
                            let buttoncancel = "<button type='button' onclick='reject(this,\""+id+"\",\"item\");' class='order-button-rejected' style='width: 1.6rem; height: 1.5rem;'/>";
                            let buttonrequest = "<button type='button' class='order-button-request' style='width: 1.5rem; height: 1.5rem;'/>";
                            var image_owner = "";
                            if(owner2 == 1)
                                image_owner = "<image style='height: 1.2rem;' src='/images/yellowArrow.png'>";
                            if(owner2 == 2)
                                image_owner = "<image style='height: 1.2rem;' src='/images/blueArrow.png'>";

                            var blue_circle = "";
                            var green_tick = "";
                            var red_cross = "";

                            if(stage == 0)
                                blue_circle = "<image style='height: 1.3rem;' src='/images/icons8-circled-thin-50.png'/>";

                            if(stage == 1)
                                green_tick = "<image style='height: 1.3rem;' src='/images/icons8-checkmark-50-1.png'/>";

                            if(stage == 2)
                                red_cross = "<image style='height: 1.3rem;' src='/images/icons8-delete-50-2.png'/>";

                            if ($("#set-furnizor").val() == "") {
                                cols += '<td class="first_color td01" colspan="1" style="' + first_style + '"></td>';
                                cols += '<td class="first_color td01" colspan="1" style="' + first_style + '">'+image_owner+'</td>';
                                cols += '<td class="first_color td01" colspan="1" style="' + first_style + '">'+ blue_circle +'</td>';
                                cols += '<td class="first_color td01" colspan="1" style="' + first_style + '">'+ green_tick +'</td>';
                                cols += '<td class="first_color td01" colspan="1" style="' + first_style + '">'+ red_cross +'</td>';
                                cols += '<td class="first_color td01" colspan="1" style="' + first_style + '">6</td>';
                                cols += '<td class="first_color td01" colspan="1" style="' + first_style + '; padding: 0;">' + buttonok + '</td>';
                                cols += '<td class="first_color td01" colspan="1" style="' + first_style + '; padding: 0;">' + buttoncancel + '</td>';
                                cols += '<td class="first_color td01" colspan="1" style="' + first_style + '; padding: 0;">' + buttonrequest + '</td>';
                                cols += '<td class="first_color td01" colspan="1" style="' + first_style + '"></td>';
                            } else {
                                cols += '<td class="first_color td01" colspan="1" style="' + po_style + '"></td>';
                                cols += '<td class="first_color td01" colspan="1" style="' + first_style + '">'+ image_owner +'</td>';
                                cols += '<td class="first_color td01" colspan="1" style="' + po_style + '">'+ blue_circle +'</td>';
                                cols += '<td class="first_color td01" colspan="1" style="' + po_style + '">'+ green_tick +'</td>';
                                cols += '<td class="first_color td01" colspan="1" style="' + po_style + '">'+ red_cross +'</td>';
                                cols += '<td class="first_color td01" colspan="1" style="' + po_style + '">6</td>';
                                cols += '<td class="first_color td01" colspan="1" style="' + po_style + '; padding: 0;">' + buttonok + '</td>';
                                cols += '<td class="first_color td01" colspan="1" style="' + po_style + '; padding: 0;">' + buttoncancel + '</td>';
                                cols += '<td class="first_color td01" colspan="1" style="' + po_style + '; padding: 0;">' + buttonrequest + '</td>';
                            }
                            cols += '<td class="coloured" style="' + po_style + '"></td>';
                            cols += "<td colspan='2'><button type='button' id='btn_I" + ebeln2 + "_" + id + "' onclick=\"loadSub(\'" + ebeln2 + "',\'purch-item\',this, \'" + id + "');\">+</button> " + id + "</td>";
                            cols += '<td>' + posnr + '</td>';
                            cols += '<td>' + idnlf + '</td>';
                            cols += '<td colspan="25"></td>';
                            if ($("#set-furnizor").val() != "")
                                cols += '<td colspan="1"></td>';
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
                                let colsafter = "12";
                                if ($("#set-furnizor").val() == "")
                                    cols += '<td class="first_color" colspan="1" style="' + first_style + '"></td>';
                                else colsafter = "13";
                                cols += '<td class="coloured" style="' + last_style + '"></td>';
                                cols += '<td style="' + pi_style + '"></td>';
                                cols += "<td colspan='3'>" + chdate + "</td>";
                                cols += '<td colspan="4">' + cuser + ' ' + cuser_name + '</td>';
                                cols += '<td colspan="6">' + ctext + '</td>';
                                cols += '<td colspan="2">' + creason + '</td>';
                                cols += "<td colspan=" + colsafter + "></td>";
                                if ($("#set-furnizor").val() != "")
                                    cols += '<td></td>';
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
                    });
                    if (type == 'sales-order') {
                        var newRow = $("<tr>");
                        var cols = "";
                        var so_style = "background-color:" + $(_this).css("background-color") + ";";
                        cols += '<td class="first_color" style="' + so_style + '" colspan="11"></td>';
                        cols += '<td colspan="3"><b>Comanda aprovizionare</b></td>';
                        cols += '<td colspan="1"><b>LIFNR</b></td>';
                        cols += '<td colspan="1"><b>LIFNR_NAME</b></td>';
                        cols += '<td colspan="1"><b>EKGRP</b></td>';
                        cols += '<td colspan="1"><b>EKGRP_NAME</b></td>';
                        cols += '<td colspan="1"><b>ERDAT</b></td>';
                        cols += '<td colspan="1"><b>CURR</b></td>';
                        cols += '<td colspan="1"><b>FXRATE</b></td>';
                        cols += '<td colspan="20"><b></b></td>';
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
                        if ($("#set-furnizor").val() == "") {
                            cols += '<td class="first_color" colspan="11" style="' + first_style + '"></td>';
                        } else {
                            cols += '<td class="first_color" colspan="10" style="' + po_style + '"></td>';
                        }
                        cols += '<td style="' + po_style + '"></td>';
                        cols += '<td colspan="2"><b>Pozitie</b></td>';
                        cols += '<td><b>Posnr</b></td>';
                        cols += '<td><b>IDNLF</b></td>';
                        cols += '<td colspan="25"></td>';
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
                        var colsafter = "13";
                        if ($("#set-furnizor").val() == "")
                            cols += '<td class="first_color" colspan="1" style="' + first_style + '"></td>';
                        else colsafter = "14";
                        cols += '<td class="coloured" style="' + last_style + '"></td>';
                        cols += '<td style="' + po_style + '"></td>';
                        cols += '<td colspan="3"><b>Data</b></td>';
                        cols += '<td colspan="4"><b>Utilizator</b></td>';
                        cols += '<td colspan="6"><b>Ce s-a schimbat</b></td>';
                        cols += '<td colspan="2"><b>Motiv</b></td>';
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
    </script>
@endsection