@extends('layouts.app')

@section('content')
    @guest
        @php
            header("/");
            exit();
        @endphp
    @endguest
    @php

        $sorting = \Illuminate\Support\Facades\Session::get('message-sorting');
        if(!isset($sorting))
            $sorting = "none";

        $sort_color_ebeln = "";
        $sort_color_cdate = "";
        $sort_color_cuser = "";

        if($sorting == "ebeln")
            $sort_color_ebeln = " style='background-color:#99ffcc'";
        if ($sorting == "cdate")
            $sort_color_cdate = " style='background-color:#99ffcc'";
        if ($sorting == "cuser")
            $sort_color_cuser = " style='background-color:#99ffcc'";

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
                        @if(strcmp( (\Illuminate\Support\Facades\Auth::user()->role), "Administrator" ) == 0)
                            <a href="/roles">
                                <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;"
                                   class="card-line first">
                                    <image style='height: 2.2rem; margin-left: -1.5rem;' src='/images/icons8-administrative-tools-48.png'/>
                                    {{__("Roles")}}
                                </p>
                            </a>
                            <a href="/users">
                                <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;"
                                   class="card-line">
                                    <image style='height: 2.2rem; margin-left: -1.5rem;' src='/images/icons8-user-account-80.png'/>
                                    {{__("Users")}}
                                </p>
                            </a>
                            <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;"
                               class="card-line selector">
                                <image style='height: 2.2rem; margin-left: -1.5rem;' src='/images/icons8-chat-80.png'/>
                                {{__("Messages")}}
                            </p>
                            <a href="/orders">
                                <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;"
                                   class="card-line">
                                    <image style='height: 2.2rem; margin-left: -1.5rem;' src='/images/icons8-todo-list-96.png'/>
                                    {{__("Orders")}}
                                </p>
                            </a>
                        @else
                            <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;"
                               class="card-line first selector">
                                <image style='height: 2.2rem; margin-left: -1.5rem;' src='/images/icons8-chat-80.png'/>
                                {{__("Messages")}}
                            </p>
                            <a href="/orders"><p
                                        style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;"
                                        class="card-line">
                                    <image style='height: 2.2rem; margin-left: -1.5rem;' src='/images/icons8-todo-list-96.png'/>
                                    {{__("Orders")}}
                                </p></a>
                        @endif
                    </div>

                    <div class="card-body" style="padding-bottom: 0px;">
                        <div style="border: 1px solid black; border-radius: 0.5rem; padding: 8px; height: 2.5rem;">
                            <form action="/messages" method="post">
                                {{csrf_field()}}
                                <div class="container row" style="display: block; max-width: 100%;">
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
                                        <input type="text" class="form-control-sm input-sm" style="width: 6rem; height: 1.4rem;" name="filter_lifnr" value="{{$filter_lifnr}}">&nbsp;&nbsp;
                                        {{__("Supplier name")}}:
                                        <input type="text" class="form-control-sm input-sm" style="width: 12rem; height: 1.4rem;" name="filter_lifnr_name" value="{{$filter_lifnr_name}}">&nbsp;&nbsp;
                                    @endif
                                </div>
                                <input type="submit" style="position: absolute; left: -9999px; width: 1px; height: 1px;"
                                       tabindex="-1">
                            </form>
                        </div>
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        <br><br>
                        <table id="messages_table" class="orders-table basicTable table table-striped" style="margin-top: -24px">
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
                                <th colspan="2" class="td02h"
                                    onclick="sortBy('ebeln'); return false;" {{$sort_color_ebeln}}>EBELN
                                </th>
                                <th></th>
                                <th colspan="2">EBELP</th>
                                <th></th>
                                <th colspan="2">Sales Order</th>
                                <th></th>
                                <th colspan="3" class="td02h"
                                    onclick="sortBy('cdate'); return false;" {{$sort_color_cdate}}>CDATE
                                </th>
                                <th></th>
                                <th colspan="4" class="td02h"
                                    onclick="sortBy('cuser'); return false;" {{$sort_color_cuser}}>CUSER & CNAME
                                </th>
                                <th></th>
                                <th colspan="1">Acknowledge</th>
                                <th colspan="1">Reply</th>
                                <th colspan="20">Text mesaj</th>
                            </tr>
                            @php

                                $messages = App\Materom\Orders::getMessageList($sorting);

                                foreach ($messages as $message){
                                    $tablerow = "<tr><td colspan='2' $sort_color_ebeln>$message->ebeln</td>
                                                     <td></td>
                                                     <td colspan='2'>$message->ebelp</td>
                                                     <td></td>
                                                     <td colspan='2'>$message->vbeln</td>
                                                     <td></td>
                                                     <td colspan='3' $sort_color_cdate>$message->cdate</td>
                                                     <td></td>
                                                     <td colspan='2' $sort_color_cuser>$message->cuser</td>
                                                     <td colspan='2' $sort_color_cuser>$message->cuser_name</td>
                                                     <td></td>
                                                     <td colspan='1'><button onclick=\"ack('$message->ebeln','$message->ebelp','$message->cdate');return false;\"><image style='height:1.5rem;width:1.5rem' src='/images/icons8-checkmark-50-3.png'></button></td>
                                                     <td colspan='1'><button onclick=\"replyMsg('$message->ebeln','$message->ebelp','$message->cdate','','','',''); return false;\"><image style='height:1.5rem;width:1.5rem' src='/images/reply_arrow1600.png'></button></td>
                                                     <td colspan='20'>$message->text</td></tr>";

                                    echo $tablerow;
                                }
                            @endphp
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function sortBy(type) {
            var _datA, _statuS = "";

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajaxSetup({async: false});

            $.post("webservice/sortmessages",
                {
                    type: type
                },
                function (data, status) {
                    _datA = data;
                    _statuS = status;
                });

            jQuery.ajaxSetup({async: true});
            if (_statuS == "success") {
                location.reload(true);
            }


        }
    </script>

    <script>
        function ack(ebeln, ebelp, cdate) {

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
                    cdate: cdate
                },
                function (data, status) {
                    _data = data;
                    _status = status;
                });
            jQuery.ajaxSetup({async: true});
            if (_status == "success") {
                location.reload(true);
            }
        }
    </script>

    <div id="init-reply-dialog" title="Raspuns la mesaj">
        <form>
            <br>
            <div class="form-group container-fluid" align="middle">
                <label for="idnlf" class="col-md-4 col-form-label text-md-left">IDLNF:</label>
                <input id="idnlf" type="text" name="idnlf" size="20"
                       class="form-control col-md-12" value="">
                <label for="purch_price" class="col-md-4 col-form-label text-md-left">Pret:</label>
                <input id="purch_price" type="text" name="purch_price" size="20"
                       class="form-control col-md-12" value="">
                <label for="qty" class="col-md-4 col-form-label text-md-left">Cantitate
                    :</label>
                <input id="qty" type="text" name="qty" size="20"
                       class="form-control col-md-12" value="">
                <label for="lfdat" class="col-md-4 col-form-label text-md-left">Data limita:</label>
                <input id="lfdat" type="text" name="lfdat" size="20"
                       class="form-control col-md-12" value="">
                <label for="reason" class="col-md-4 col-form-label text-md-left">Motiv:</label>
                <input id="reason" type="text" name="reason" size="20"
                       class="form-control col-md-12" value="">
            </div>
            <i id="new_reply_msg" style="color: red"></i>
        </form>
    </div>

    <script>

        var replyDialog, replyForm, _ebeln, _ebelp, _cdate, _data, _status;
        $(function () {
            replyDialog = $("#init-reply-dialog").dialog({
                autoOpen: false,
                height: 450,
                width: 400,
                modal: true,
                buttons: {
                    Change: function () {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        jQuery.ajaxSetup({async: false});

                        $.post("webservice/replymessage",
                            {
                                ebeln: _ebeln,
                                ebelp: _ebelp,
                                cdate: _cdate,
                                idnlf: $("#idnlf").val(),
                                purch_price: $("#purch_price").val(),
                                qty: $("#qty").val(),
                                lfdat: $("#lfdat").val(),
                                reason: $("#reason").val()
                            },
                            function (data, status) {
                                _data = data;
                                _status = status;
                            });
                        jQuery.ajaxSetup({async: true});

                        if (_status == "success") {
                            location.reload(true);
                        }

                    },
                    Cancel: function () {
                        //replyDialog.dialog("close");
                        location.reload(true);
                    }
                },
                close: function () {
                    //rejectForm[0].reset();
                    location.reload(true);
                },
                position: {
                    my: "center",
                    at: "center",
                    of: $("#messages_table")
                }
            });
            replyForm = replyDialog.find("form").on("submit", function (event) {
                event.preventDefault();
            });
        });

        function replyMsg(ebeln, ebelp, cdate, idnlf, purch_price, qty, lfdat) {
            $("#new_reply_msg").text("");
            $("#idnlf").val(idnlf);
            $("#purch_price").val(purch_price);
            $("#qty").val(qty);
            $("#lfdat").val(lfdat);
            $("#reason").val("");
            $("#init-reply-dialog").dialog('option', 'title', 'Formular de raspuns la item ' + ebelp);
            _ebeln = ebeln;
            _ebelp = ebelp;
            _cdate = cdate;
            replyDialog.dialog("open");
        }
    </script>
@endsection