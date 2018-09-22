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
                            <tr>
                                <th style="width: 1.2rem">NOF</th>
                                <th style="width: 1.2rem">Prio</th>
                                <th>ID Comanda</th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th>Status</th>
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
                                foreach($orders as $order){

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

                                    if($furnizor)
                                        $oid = "P" . $order->ebeln;
                                    else
                                        $oid = "S" . $order->vbeln;

                                    echo "<tr id='tr_$oid'><td>$nof</td><td>$prio</td><td>$comanda</td><td></td><td></td><td></td><td>$status</td></tr>";
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
                var split = _data.split('=');
                split.forEach(function (_ord) {
                    if( type == 'sales-order') {
                        var id = _ord.split('#')[0];
                        var lifnr = _ord.split('#')[1];
                        var lifnr_name = _ord.split('#')[2];
                        var ekgrp = _ord.split('#')[3];
                        var newRow = $("<tr>");
                        var cols = "";
                        cols += '<td></td>';
                        cols += '<td></td>';
                        cols += "<td><button style='margin-left:50px;' type='button' id='btn_P" + id + "' onclick=\"loadSub(\'" + id + "',\'purch-order\',this, \'\');\">+</button> " + id + "</td>";
                        cols += '<td>'+lifnr+'</td>';
                        cols += '<td>'+lifnr_name+'</td>';
                        cols += '<td>'+ekgrp+'</td>';
                        cols += "<td><image style='height: 1rem;' src='/images/status.png'></td>";
                        newRow.append(cols);
                        newRow.insertAfter($(_this).closest("tr"));
                        newRow.attr('id', "tr_P" + id);
                    } else if(type == 'purch-order'){
                            var ebeln2 = _ord.split('#')[0];
                            var id = _ord.split('#')[1];
                            var posnr = _ord.split('#')[2];
                            var idnlf = _ord.split('#')[3];
                            var newRow = $("<tr>");
                            var cols = "";
                            cols += '<td></td>';
                            cols += '<td></td>';
                            cols += "<td><button style='margin-left:100px;' type='button' id='btn_I" + ebeln2 + "_" + id + "' onclick=\"loadSub(\'" + ebeln2 + "',\'purch-item\',this, \'" + id + "');\">+</button> "+id+"</td>";
                            cols += '<td>'+posnr+'</td>';
                            cols += '<td>'+idnlf+'</td>';
                            cols += '<td></td>';
                            cols += "<td></td>";
                            newRow.append(cols);
                            newRow.insertAfter($(_this).closest("tr"));
                            newRow.attr('id', "tr_I" + ebeln2 + "_" + id);
                    } else if (type == 'purch-item') {
                        var ebeln3 = _ord.split('#')[0];
                        var ebelp3 = _ord.split('#')[1];
                        var col = _ord.split('#')[2];
                        var oldVal = _ord.split('#')[3];
                        var newVal = _ord.split('#')[4];
                        var modBy = _ord.split('#')[5];
                        var newRow = $("<tr>");
                        var cols = "";
                        cols += '<td></td>';
                        cols += '<td></td>';
                        cols += "<td><div style='margin-left:150px;'>"+col+"</div></td>";
                        cols += '<td>'+oldVal+'</td>';
                        cols += '<td>'+newVal+'</td>';
                        cols += '<td>'+modBy+'</td>';
                        cols += "<td></td>";
                        newRow.append(cols);
                        newRow.insertAfter($(_this).closest("tr"));
                        newRow.attr('id', "tr_C" + ebeln3 + "_" + ebelp3 + "_" + col + "-" + oldVal + "-" + newVal);
                    }
                });
                if( type == 'sales-order') {
                    var newRow = $("<tr>");
                    var cols = "";
                    cols += '<td></td>';
                    cols += '<td></td>';
                    cols += '<td><b style="margin-left: 50px">ID Comanda Vanzare</b></td>';
                    cols += '<td><b>ID Furnizor</b></td>';
                    cols += '<td><b>Nume Furnizor</b></td>';
                    cols += '<td><b>Grup Material</b></td>';
                    cols += '<td><b></b></td>';
                    newRow.append(cols);
                    newRow.insertAfter($(_this).closest("tr"));
                }
                if(type == 'purch-order'){
                    var newRow = $("<tr>");
                    var cols = "";
                    cols += '<td></td>';
                    cols += '<td></td>';
                    cols += '<td><b style="margin-left: 100px">ID Item</b></td>';
                    cols += '<td><b>Posnr</b></td>';
                    cols += '<td><b>IDNLF</b></td>';
                    cols += '<td><b></b></td>';
                    cols += '<td><b></b></td>';
                    newRow.append(cols);
                    newRow.insertAfter($(_this).closest("tr"));
                }
                if(type == 'purch-item'){
                    var newRow = $("<tr>");
                    var cols = "";
                    cols += '<td></td>';
                    cols += '<td></td>';
                    cols += '<td><b style="margin-left: 150px">CType</b></td>';
                    cols += '<td><b>Old Value</b></td>';
                    cols += '<td><b>New Value</b></td>';
                    cols += '<td><b>Modfied by</b></td>';
                    cols += '<td><b></b></td>';
                    newRow.append(cols);
                    newRow.insertAfter($(_this).closest("tr"));
                }
                _btn.innerHTML = '-';
                _btn.onclick = function(){ hideSub(item,type,_btn,ebelp); return false;};
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