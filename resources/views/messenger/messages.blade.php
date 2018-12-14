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
            $sorting = "cdate";

        $sort_color_ebeln = "";
        $sort_color_cdate = "";
        $sort_color_cuser = "";

        if($sorting == "ebeln")
            $sort_color_ebeln = ' style="background-color:#99ffcc"';
        if ($sorting == "cdate")
            $sort_color_cdate = ' style="background-color:#99ffcc"';
        if ($sorting == "cuser")
            $sort_color_cuser = ' style="background-color:#99ffcc"';

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
                                {{__("Messages")}}{!!$message_svg!!}
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
                                {{__("Messages")}}{!!$message_svg!!}
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
                        <div style="border: 1px solid black; border-radius: 0.5rem; padding: 8px; height: 5rem;">
                            <form action="/messages" method="post">
                                {{csrf_field()}}
                                <div class="container row" style="display: block; max-width: 100%;">
                                    {{__('Displayed messages')}}:
                                    <select class="form-control-sm input-sm" style="height: 1.6rem; padding: 2px;" name="filter_history" onchange="this.form.submit()">
                                        <option value="1"{{$filter_history_curr}}>{{__("Unprocessed")}}</option>
                                        <option value="2"{{$filter_history_arch}}>{{__("Processed")}}</option>
                                    </select>
                                    @if ($filter_history == 2)
                                        &nbsp;{{__('Documents archived since')}}:
                                        <input type="text" id="time_search" name="time_search" value="{{$filter_time_val}}"
                                               onchange="this.form.submit()">
                                    @endif
                                    <br><br>
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
                                <th colspan="3" class="td02h"
                                    onclick="sortBy('ebeln'); return false;" {!! $sort_color_ebeln !!}>{{__('Purchase order')}}
                                </th>
                                <th colspan="2">{{__('Item')}}</th>
                                <th></th>
                                <th colspan="3">{{__('Sales order')}}</th>
                                <th colspan="4" class="td02h"
                                    onclick="sortBy('cdate'); return false;" {!! $sort_color_cdate !!}>{{__('Sent on')}}
                                </th>
                                <th></th>
                                <th colspan="4" class="td02h"
                                    onclick="sortBy('cuser'); return false;" {!! $sort_color_cuser !!}>{{__('Sent by')}}
                                </th>
                                <th></th>
                                <th colspan="1"></th>
                                <th colspan="2"></th>
                                <th colspan="20">{{__('Message text')}}</th>
                            </tr>
                            @php

                                $messages = App\Materom\Orders::getMessageList($sorting);

                                foreach ($messages as $message){
                                    $button_ack = $filter_history == 2 ? "" : "<button onclick=\"replyack('$message->ebeln','$message->ebelp','$message->cdate');return false;\"><image style='height:1.5rem;width:1.5rem' src='/images/icons8-checkmark-50-3.png'></button>";
                                    $button_reply = $filter_history == 2 ? "" : "<button onclick=\"replyMsg('$message->ebeln','$message->ebelp','$message->cdate'); return false;\"><image style='height:1.5rem;width:1.5rem' src='/images/reply_arrow1600.png'></button>";
                                    $vbeln = $message->vbeln;
                                    if ($vbeln == \App\Materom\Orders::stockorder) $vbeln = __("Stock");
                                    elseif (\Illuminate\Support\Facades\Auth::user()->role == "Furnizor") $vbeln = __("Emergency");
                                    $tablerow = "<tr><td colspan='3' $sort_color_ebeln>$message->ebeln</td>
                                                     <td colspan='2'>" . App\Materom\SAP::alpha_output($message->ebelp) . "</td>
                                                     <td></td>
                                                     <td colspan='3'>$vbeln</td>
                                                     <td colspan='4' $sort_color_cdate>$message->cdate</td>
                                                     <td></td>
                                                     <td colspan='2' $sort_color_cuser>$message->cuser</td>
                                                     <td colspan='2' $sort_color_cuser>$message->cuser_name</td>
                                                     <td></td>
                                                     <td colspan='1'>$button_ack</td>
                                                     <td colspan='1'>$button_reply</td>
                                                     <td colspan='1'></td>
                                                     <td colspan='20'>$message->reason</td></tr>";


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
        $( function() {
            $( "#time_search" ).datepicker();
        } );
    </script>

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
        function replyack(ebeln, ebelp, cdate) {

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
                if (cdate != null) location.reload(true);
            }
        }
    </script>

    <div id="init-reply-dialog" title="{{__('Reply to message')}}">
        <form>
            @if (\Illuminate\Support\Facades\Auth::user()->role == "Referent")
                <div class="form-group row" style="width: 80%">
                    {{__("Recipient")}}:<br>
                    <select class="form-control-sm input-sm" style="height: 1.6rem; padding: 2px;" id="reply_recipient">
                        <option value="F" selected>{{__("Vendor")}}</option>
                        <option value="C">{{__("CTV")}}</option>
                    </select>
                </div>
            @endif
            <br>
            <div class="form-group container-fluid" align="middle">
                <label for="message" class="col-md-12 col-form-label text-md-left">{{__('Message')}}:</label>
                <input id="message" type="text" name="message" size="20"
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
                height: 200,
                width: 400,
                modal: true,
                buttons: {
                    Send: function () {
                        var _to = '';
                        @if (\Illuminate\Support\Facades\Auth::user()->role == "Furnizor")
                            _to = 'R';
                        @elseif (\Illuminate\Support\Facades\Auth::user()->role == "CTV")
                            _to = 'R';
                        @elseif (\Illuminate\Support\Facades\Auth::user()->role == "Referent")
                            _to = $('#reply_recipient').val();
                        @endif
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        jQuery.ajaxSetup({async: false});
                        $.post("webservice/replymessage",
                            {
                                message: $("#message").val(),
                                ebeln: _ebeln,
                                ebelp: _ebelp,
                                to: _to
                            },
                            function (data, status) {
                                _data = data;
                                _status = status;
                                alert(_data);
                            });
                        jQuery.ajaxSetup({async: true});

                        if (_status == "success") {
                            alert('Mesaj trimis!');
                            location.reload(true);
                        } else {
                            alert('Eroare la trimitere!');
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

        function replyMsg(ebeln, ebelp, cdate) {
            replyto_inquiry(ebeln, ebelp, cdate);
            return;
            /*
            $("#new_reply_msg").text("");
            $("#init-reply-dialog").dialog('option', 'title', 'Formular de raspuns la item ' + ebelp);
            _ebeln = ebeln;
            _ebelp = ebelp;
            _cdate = cdate;
            replyDialog.dialog("open");
            */
        }
    </script>

    @include("orders.inquiries")

@endsection