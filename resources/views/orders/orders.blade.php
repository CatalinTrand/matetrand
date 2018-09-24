@extends('layouts.app')

@section('content')
    @guest
        @php
                header("/");
                exit();
        @endphp
    @endguest
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
                        <table class="orders-table basicTable table table-striped" id="orders_table">
                            <colgroup>
                                <col width="3%"><col width="3%">
                                <col width="3%"><col width="3%">
                                <col width="3%"><col width="3%">
                                <col width="3%"><col width="3%">
                                <col width="3%"><col width="3%">
                                <col width="3%"><col width="3%">
                                <col width="3%"><col width="3%">
                                <col width="3%"><col width="3%">
                                <col width="3%"><col width="3%">
                                <col width="3%"><col width="3%">
                                <col width="3%"><col width="3%">
                                <col width="3%"><col width="3%">
                                <col width="3%"><col width="3%">
                                <col width="3%"><col width="3%">
                                <col width="3%"><col width="3%">
                                <col width="3%"><col width="3%">
                                <col width="3%"><col width="3%">
                                <col width="3%"><col width="3%">
                                <col width="3%"><col width="3%">
                                <col width="3%"><col width="3%">
                            </colgroup>
                            <tr>
                                <th style="width: 1.2rem" colspan="1">NOF</th>
                                <th style="width: 1.2rem" colspan="1">Prio</th>
                                <th colspan="4">ID Comanda</th>
                                <th colspan="22"></th>
                                <th colspan="1"></th>
                                <th colspan="1"></th>
                                <th colspan="1"></th>
                                <th colspan="1"></th>
                                <th colspan="1"></th>
                                <th colspan="1">Status</th>
                            </tr>
                            @php
                                use Illuminate\Support\Facades\DB;
                                $id = \Illuminate\Support\Facades\Auth::user()->id;

                                $orders = \App\User::getOrders($id);
                                if(strcmp( (\Illuminate\Support\Facades\Auth::user()->role), "Furnizor" ) == 0){
                                    $furnizor = true;
                                } else {
                                    $furnizor = false;
                                }

                                $seen = "";
                                $line_counter = 1;
                                foreach ($orders as $order) {

                                    if($furnizor){
                                        if(strchr($seen,$order->ebeln) == null)
                                            $seen.= " $order->ebeln";
                                        else
                                            continue;

                                        $comanda = "<button type='button' id='btn_P$order->ebeln' onclick='loadSub(\"$order->ebeln\",\"purch-order\",this, \"\"); return false;'>+</button> $order->ebeln";
                                    } else {
                                        if(strchr($seen,$order->vbeln) == null)
                                            $seen.= " $order->vbeln";
                                        else
                                            continue;

                                        $comanda = "<button type='button' id='btn_S$order->vbeln' onclick='loadSub(\"$order->vbeln\",\"sales-order\",this, \"\"); return false;'>+</button> $order->vbeln";
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

                                    $interval_wtime = $now - $wtime;
                                    if($interval_wtime > 0){
                                        $interval_ctime = $now - $ctime;
                                        if($interval_ctime > 0){
                                            $prio = "<image style='height: 1rem;' src='/images/critical.png'>";
                                        } else {
                                            $prio = "<image style='height: 1rem;' src='/images/warning.png'>";
                                        }
                                    } else $prio = "None";

                                    $status = "<image style='height: 1rem;' src='/images/status.png'>"; //TODO

                                    if($furnizor){
                                        $oid = "P" . $order->ebeln;
                                        if ($line_counter == 0)
                                            $style = "background-color:LightYellow;";
                                        else
                                            $style = "background-color:Wheat;";
                                    }else{
                                        $oid = "S" . $order->vbeln;
                                        if ($line_counter == 0)
                                            $style = "background-color:white;";
                                        else
                                            $style = "background-color:WhiteSmoke;";
                                    }

                                    echo "<tr id='tr_$oid' style='$style' colspan='1'><td colspan='1'>$nof</td><td>$prio</td><td colspan='4' class='first_color'>$comanda</td><td colspan='22'></td><td></td><td></td><td></td><td></td><td></td><td>$status</td></tr>";
                                }
                            @endphp
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function loadSub(item, type, _btn, ebelp) {
            var _data, _status;
            var _this;
            if (type == "sales-order") _this = document.getElementById("tr_S" + item);
            if (type == "purch-order") _this = document.getElementById("tr_P" + item);
            if (type == "purch-item")  _this = document.getElementById("tr_I" + item + "_" + ebelp);

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
            if (_status == "success" ) {
                if(_data.length > 0) {
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
                            var newRow = $("<tr>");
                            var cols = "";
                            var so_style = "background-color:" + $(_this).css("background-color") + ";";
                            cols += '<td class="first_color" style="' + so_style + '" colspan="3"></td>';
                            cols += "<td colspan='4'><button type='button' id='btn_P" + id + "' onclick=\"loadSub(\'" + id + "',\'purch-order\',this, \'\');\">+</button> " + id + "</td>";
                            cols += '<td colspan="1"></td>';
                            cols += '<td colspan="2">' + lifnr + '</td>';
                            cols += '<td colspan="2">' + lifnr_name + '</td>';
                            cols += '<td colspan="1">' + ekgrp + '</td>';
                            cols += '<td colspan="20"></td>';
                            cols += "<td colspan='1'><image style='height: 1rem;' src='/images/status.png'></td>";
                            newRow.append(cols);
                            newRow.insertAfter($(_this).closest("tr")).fadeIn(500);
                            if (line_counter == 0)
                                newRow.attr('style', "background-color:LightYellow;");
                            else
                                newRow.attr('style', "background-color:Wheat;");
                            newRow.attr('id', "tr_P" + id);
                        } else if (type == 'purch-order') {
                            var ebeln2 = _ord.split('#')[0];
                            var id = _ord.split('#')[1];
                            var posnr = _ord.split('#')[2];
                            var idnlf = _ord.split('#')[3];
                            var newRow = $("<tr>");
                            var cols = "";
                            var po_style = "background-color:" + $(_this).css("background-color") + ";";
                            var first_color = $(_this).closest("tr").find(".first_color").css("background-color");
                            var first_style = "background-color:" + first_color;
                            cols += '<td class="first_color" colspan="3" style="'+first_style+'"></td>';
                            cols += '<td class="coloured" style="' + po_style + '"></td>';
                            cols += "<td colspan='2'><button type='button' id='btn_I" + ebeln2 + "_" + id + "' onclick=\"loadSub(\'" + ebeln2 + "',\'purch-item\',this, \'" + id + "');\">+</button> " + id + "</td>";
                            cols += '<td>' + posnr + '</td>';
                            cols += '<td>' + idnlf + '</td>';
                            cols += '<td></td>';
                            cols += '<td colspan="24"></td>';
                            cols += "<td></td>";
                            newRow.append(cols);
                            newRow.insertAfter($(_this).closest("tr"));
                            if (line_counter == 0)
                                newRow.attr('style', "background-color:MediumAquaMarine;");
                            else
                                newRow.attr('style', "background-color:MediumTurquoise;");
                            newRow.attr('id', "tr_I" + ebeln2 + "_" + id);
                        } else if (type == 'purch-item') {
                            var ebeln3 = _ord.split('#')[0];
                            var ebelp3 = _ord.split('#')[1];
                            var chdate = _ord.split('#')[2];
                            var oldVal = _ord.split('#')[3];
                            var newVal = _ord.split('#')[4];
                            var modBy = _ord.split('#')[5];
                            if (chdate != null && modBy != null) {
                                var newRow = $("<tr>");
                                var cols = "";
                                var pi_style = "background-color:" + $(_this).css("background-color") + ";";
                                var color = $(_this).closest("tr").find(".coloured").css("background-color");
                                var last_style = "background-color:" + color;
                                var first_color = $(_this).closest("tr").find(".first_color").css("background-color");
                                var first_style = "background-color:" + first_color;
                                cols += '<td class="first_color" colspan="3" style="'+first_style+'"></td>';
                                cols += '<td class="coloured" style="' + last_style + '"></td>';
                                cols += '<td style="' + pi_style + '"></td>';
                                cols += "<td colspan='4'>" + chdate + "</td>";
                                cols += '<td colspan="2">' + oldVal + '</td>';
                                cols += '<td colspan="2">' + newVal + '</td>';
                                cols += '<td colspan="2">' + modBy + '</td>';
                                cols += "<td colspan='20'></td>";
                                newRow.append(cols);
                                newRow.insertAfter($(_this).closest("tr"));
                                if (line_counter == 0)
                                    newRow.attr('style', "background-color:Azure");
                                else
                                    newRow.attr('style', "background-color:LightCyan");
                                newRow.attr('id', "tr_C" + ebeln3 + "_" + ebelp3 + "_" +
                                    chdate.substr(0, 10) + "_" + chdate.substr(11, 8));
                            }
                        }
                    });
                    if (type == 'sales-order') {
                        var newRow = $("<tr>");
                        var cols = "";
                        var so_style = "background-color:" + $(_this).css("background-color") + ";";
                        cols += '<td class="first_color" style="' + so_style + '" colspan="3"></td>';
                        cols += '<td colspan="4"><b>Comanda aprovizionare</b></td>';
                        cols += '<td colspan="1"></td>';
                        cols += '<td colspan="2"><b>ID Furnizor</b></td>';
                        cols += '<td colspan="2"><b>Nume Furnizor</b></td>';
                        cols += '<td colspan="1"><b>Grup Material</b></td>';
                        cols += '<td colspan="21"><b></b></td>';
                        newRow.append(cols);
                        newRow.insertAfter($(_this).closest("tr"));
                        newRow.attr('style', "background-color:#FAEFCA");
                    }
                    if (type == 'purch-order') {
                        var newRow = $("<tr>");
                        var cols = "";
                        var po_style = "background-color:" + $(_this).css("background-color") + ";";
                        var color = $(_this).closest("tr").find(".first_color").css("background-color");
                        var last_style = "background-color:" + color;
                        cols += '<td class="first_color" colspan="3" style="'+last_style+'"></td>';
                        cols += '<td style="' + po_style + '"></td>';
                        cols += '<td colspan="2"><b>Pozitie</b></td>';
                        cols += '<td><b>Posnr</b></td>';
                        cols += '<td><b>IDNLF</b></td>';
                        cols += '<td colspan="24"></td>';
                        cols += '<td><b></b></td>';
                        cols += '<td><b></b></td>';
                        newRow.append(cols);
                        newRow.insertAfter($(_this).closest("tr"));
                        newRow.attr('style', "background-color:#57D7BB");
                    }
                    if (type == 'purch-item') {
                        var newRow = $("<tr>");
                        var cols = "";
                        var po_style = "background-color:" + $(_this).css("background-color") + ";";
                        var color = $(_this).closest("tr").find(".coloured").css("background-color");
                        var last_style = "background-color:" + color;
                        var first_color = $(_this).closest("tr").find(".first_color").css("background-color");
                        var first_style = "background-color:" + first_color;
                        cols += '<td class="first_color" colspan="3" style="'+first_style+'"></td>';
                        cols += '<td class="coloured" style="' + last_style +'"></td>';
                        cols += '<td style="' + po_style + '"></td>';
                        cols += '<td colspan="4"><b>Change date</b></td>';
                        cols += '<td colspan="2"><b>Old Value</b></td>';
                        cols += '<td colspan="2"><b>New Value</b></td>';;
                        cols += '<td colspan="2"><b>Modfied by</b></td>';
                        cols += '<td colspan="20"><b></b></td>';
                        newRow.append(cols);
                        newRow.insertAfter($(_this).closest("tr"));
                        newRow.attr('style', "background-color:#CFECF3");
                    }
                }
                    _btn.innerHTML = '-';
                    _btn.onclick = function () {
                        hideSub(item, type, _btn, ebelp);
                        return false;
                    };

            } else {
                alert('Error processing operation!');
            }
        }

        function hideSub(item,type,_btn,ebelp){
            var table = document.getElementById('orders_table');
            var tr_id = "";
            if (type == "sales-order") tr_id = "tr_S" + item;
            if (type == "purch-order") tr_id = "tr_P" + item;
            if (type == "purch-item")  tr_id = "tr_I" + item + "_" + ebelp;

            var started = false;
            for (i = 0; i < table.rows.length; i++) {
                if(started){
                    if(table.rows[i].innerHTML.includes(type) || table.rows[i].innerHTML.includes('sales-order'))
                        break;
                    if (type == "purch-item")
                        if(table.rows[i].innerHTML.includes(type) || table.rows[i].innerHTML.includes('purch-order'))
                            break;
                    table.deleteRow(i);
                    i--;
                }
                if(table.rows[i].id == tr_id ) started = true;
            }
            _btn.innerHTML = '+';
            _btn.onclick = function(){ loadSub(item,type,_btn, ebelp); return false;};
        }
    </script>
@endsection