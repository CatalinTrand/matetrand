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

                    <div class="card-body">
                        <table class="orders-table basicTable table table-striped" id="orders_table">
                            <tr>
                                <th>NOF</th>
                                <th>Prioritate</th>
                                <th>Comanda</th>
                                <th>Status</th>
                            </tr>
                            @php
                                use Illuminate\Support\Facades\DB;
                                $id = \Illuminate\Support\Facades\Auth::user()->id;
                                $orders = DB::select("select * from porders where id = '$id'");
                                $seen = "";
                                foreach($orders as $order){
                                    if(strchr($seen,$order->vbeln) == null)
                                        $seen.= " $order->vbeln";
                                    else
                                        continue;

                                    if($order->nof)
                                        $nof = "<image src='/images/nof.png'>";
                                    else
                                        $nof = "";

                                    $now = strtotime(date('Y-m-d H:i:s'));
                                    $wtime = strtotime($order->wtime);
                                    $ctime = strtotime($order->ctime);

                                    $interval_wtime = $now - $wtime;
                                    if($interval_wtime > 0){
                                        $interval_ctime = $now - $ctime;
                                        if($interval_ctime > 0){
                                            $prio = "<image src='/images/critical.png'>";
                                        } else {
                                            $prio = "<image src='/images/warning.png'>";
                                        }
                                    } else $prio = "None";

                                    $status = "<image src='/images/status.png'>"; //TODO

                                    $comanda = "<button type='button' id='btn_$order->vbeln' onclick='loadSub(\"$order->vbeln\",\"sales-order\",this); return false;'>+</button> $order->vbeln";

                                    echo "<tr id='tr_$order->vbeln'><td>$nof</td><td>$prio</td><td>$comanda</td><td>$status</td></tr>";
                                }
                            @endphp
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function loadSub(item, type, _btn) {
            var _data, _status;
            var _this = document.getElementById("tr_"+item);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajaxSetup({async: false});

            $.post("webservice/getOrderInfo",
                {
                    order: item,
                    type: type
                },
                function (data, status) {
                    _data = data;
                    _status = status;
                });
            jQuery.ajaxSetup({async: true});
            if (_status == "success" ) {
                var split = _data.split('=');
                split.forEach(function (_ord) {
                    switch (type) {
                        case 'sales-order':
                            var newRow = $("<tr>");
                            var cols = "";
                            cols += '<td></td>';
                            cols += '<td></td>';
                            cols += "<td id='tr_"+_ord+"' ><button style='margin-left:50px;' type='button' id='btn_" + _ord + "' onclick=\"loadSub(\'"+ _ord +"',\'purch-order\',this);\">+</button> "+ _ord+"</td>";
                            cols += "<td><image src='/images/status.png'></td>";
                            newRow.append(cols);
                            newRow.insertAfter($(_this).closest("tr"));
                            newRow.attr('id', "tr_"+_ord);
                            break;
                        case 'purch-order':
                            var newRow = $("<tr>");
                            var cols = "";
                            cols += '<td></td>';
                            cols += '<td></td>';
                            cols += "<td><div style='margin-left: 100px'>"+_ord+"</div></td>";
                            cols += "<td><image src='/images/status.png'></td>";
                            newRow.append(cols);
                            newRow.insertAfter($(_this).closest("tr"));
                            break;
                    }
                });
                _btn.innerHTML = '-';
                _btn.onclick = function(){ hideSub(item,type,_btn); return false;};
            } else {
                alert('Error processing operation!');
            }
        }
        function hideSub(item,type,_btn){
            var table = document.getElementById('orders_table');
            var started = false;
            for (i = 0; i < table.rows.length; i++) {
                if(started){
                    if(table.rows[i].innerHTML.includes(type) || table.rows[i].innerHTML.includes('sales-order'))
                        break;
                    table.deleteRow(i);
                    i--;
                }
                if(table.rows[i].id == 'tr_'+item )
                    started = true;
            }
            _btn.innerHTML = '+';
            _btn.onclick = function(){ loadSub(item,type,_btn); return false;};
        }
    </script>
@endsection