@extends('layouts.app')

@section('content')
    @guest
        @php
            header("/");
            exit();
        @endphp
    @endguest
    @php
        if(isset($_GET['del'])){
            $id_del = $_GET['del'];
            DB::delete("delete from messages where id = '$id_del'");
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
                                    <image style='height: 2.2rem; margin-left: -1.5rem;'
                                           src='/images/icons8-administrative-tools-48.png'/>
                                    {{__("Roles")}}
                                </p>
                            </a>
                            <a href="/users">
                                <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;"
                                   class="card-line">
                                    <image style='height: 2.2rem; margin-left: -1.5rem;'
                                           src='/images/icons8-user-account-80.png'/>
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
                                    <image style='height: 2.2rem; margin-left: -1.5rem;'
                                           src='/images/icons8-todo-list-96.png'/>
                                    {{__("Orders")}}
                                </p>
                            </a>
                        @else
                            <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;"
                               class="card-line first selector">Messages</p>
                            <a href="/orders"><p
                                        style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;"
                                        class="card-line">Comenzi</p></a>
                        @endif
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        <br><br>
                        <table id="messages_table" class="orders-table basicTable table table-striped">
                            <tr>
                                <th>EBELN</th>
                                <th>EBELP</th>
                                <th>Sales Order</th>
                                <th>CDATE</th>
                                <th>CUSER & CNAME</th>
                                <th>Acknowledge</th>
                                <th>Reply</th>
                                <th>Text mesaj</th>
                            </tr>
                            @php

                            $messages = App\Materom\Orders::getMessageList();

                            foreach ($messages as $message){
                                $item = DB::select("select * from pitems where ebeln = '$message->ebeln' and ebelp = '$message->ebelp'")[0];
                                $tablerow = "<tr><td>$message->ebeln</td>
                                                 <td>$message->ebelp</td>
                                                 <td>$message->vbeln</td>
                                                 <td>$message->cdate</td>
                                                 <td>$message->cuser ( $message->cuser_name )</td>
                                                 <td><button onclick=\"ack('$message->ebeln','$message->ebelp','$message->cdate');return false;\"><image style='height:1.5rem;width:1.5rem' src='/images/icons8-checkmark-50-3.png'></button></td>
                                                 <td><button onclick=\"replyMsg('$message->ebeln','$message->ebelp','$message->cdate','$item->idnlf','$item->purch_price','$item->qty','$item->lfdat'); return false;\"><image style='height:1.5rem;width:1.5rem' src='/images/reply_arrow1600.png'></button></td>
                                                 <td>$message->text</td></tr>";

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

    <div id="init-reply-dialog" title="Raspuns la mesaj" >
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
                    Change: function (){
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        jQuery.ajaxSetup({async: false});

                        $.post("webservice/replyMsg",
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
                        replyDialog.dialog("close");
                    }
                },
                close: function () {
                    rejectForm[0].reset();
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

        function replyMsg(ebeln,ebelp,cdate,idnlf,purch_price,qty,lfdat) {
            $("#new_reply_msg").text("");
            $("#idnlf").val(idnlf);
            $("#purch_price").val(purch_price);
            $("#qty").val(qty);
            $("#lfdat").val(lfdat);
            $("#init-reply-dialog").dialog('option', 'title', 'Formular de raspuns la item '+ ebelp);
            _ebeln = ebeln;
            _ebelp = ebelp;
            _cdate = cdate;
            replyDialog.dialog("open");
        }
    </script>
@endsection