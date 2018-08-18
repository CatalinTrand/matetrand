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
                            <a href="/roles"><p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;" class="card-line first">Roles</p></a>
                            <a href="/users"><p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;" class="card-line">Users</p></a>
                            <a href="/messages"><p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;" class="card-line">Messages</p></a>
                            <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;" class="card-line selector">Comenzi</p>
                        @else
                            <a href="/messages"><p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;" class="card-line first">Messages</p></a>
                            <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;" class="card-line selector">Comenzi</p>
                        @endif
                    </div>

                    <div class="card-body">
                        <table class="orders-table">
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

                                    $now = date('Y-m-d H:i:s');
                                    $wtime = strtotime($order->wtime);
                                    $ctime = strtotime($order->ctime);

                                    $interval_wtime = abs($now - $wtime);
                                    if($interval_wtime > 0){
                                        $interval_ctime = abs($now - $ctime);
                                        if($interval_ctime > 0){
                                            $prio = "<image src='/images/critical.png'>";
                                        } else {
                                            $prio = "<image src='/images/warning.png'>";
                                        }
                                    } else $prio = "";

                                    $status = "<image src='/images/status.png'>"; //TODO

                                    $purchase_orders = array();
                                    $links = DB::select("select ebeln from porders where id = '$id' and vbeln = '$order->vbeln'");
                                    foreach ($links as $link){
                                        $purchase_order = DB::select("select * from pitems where ebeln = '$link->ebeln'");
                                        array_push($purchase_orders,$purchase_order);
                                    }
                                    /*
                                    purchase-orders -> purchase-order-1 -> item-1
                                                                        -> item-2
                                                    -> purchase-order-2 -> item-1
                                                                        -> item-2
                                    */

                                    $comanda = "<div id='$$order->vbeln'>";
                                    
                                    $comanda.= "</div>";

                                    echo "<tr><td>$nof</td><td>$prio</td><td>$comanda</td><td>$status</td></tr>";
                                }
                            @endphp
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection