<div id="new-inquiry-dialog" title="Send message">
    <form>
        @if (\Illuminate\Support\Facades\Auth::user()->role == "Referent")
            <div id="new_inquiry_dest" class="form-group" style="width: 95%; margin-left: 0.5rem;">
                <div class="row" style="width: 100%; height: 1.75rem;">
                    <label for="inquiry_recipient" id="label_inquiry_recipient"
                           class="col-md-2 col-form-label text-md-left">{{__("Recipient")}}</label>
                    <select class="form-control-sm input-sm" style="height: 1.5rem; margin-top: 3px; padding: 2px;" id="inquiry_recipient"
                        @if (\Illuminate\Support\Facades\Auth::user()->pnad != 1)
                        onchange="inquiry_recipient_changed();">
                        <option value="F" selected>{{__("Vendor")}}</option>
                        <option value="C">{{__("CTV")}}</option>
                        @else
                            disabled>
                            <option value="P">{{__("PNAD")}}</option>
                        @endif
                    </select>
                </div>
            </div>
        @else
            <br>
        @endif
        <div id="new_inquiry_div" class="form-group" style="width: 95%; margin-left: 0.5rem;">
            <div class="row" style="width: 100%;">
                <textarea id="new_inquiry" type="text" name="new_inquiry" class="form-control"
                          style="margin-left: 0.8rem; word-break: break-word; height: 4rem;" maxlength="255" value=""></textarea>
                <i id="new_inquiry_msg" style="color: red"></i>
            </div>
        </div>
        <div id="new_pnad_div" class="form-group" style="width: 95%; margin-left: 0.5rem;">
            <div class="row" style="width: 100%; height: 1.75rem;">
                <label for="inquiry_pnad_details" id="label_inquiry_pnad_details"
                       class="col-md-2 col-form-label text-md-left">{{__("Motiv/Doc.")}}</label>
                <input id="inquiry_pnad_details" style="width: 75%; height: 1.5rem;" type="text" name="inquiry_pnad_details"
                       class="form-control-sm input-sm" maxlength="120" value="">
            </div>
        </div>
    </form>
</div>


<script>

    var inqPorder, inqPitem, inqCDate, newInquiryDialog, newInquiryForm, inqPnadStatus;
    var inquiryData, inquiryStatus;
    $(function () {
        newInquiryDialog = $("#new-inquiry-dialog").dialog({
            autoOpen: false,
            height: 220,
            width: 560,
            modal: true,
            buttons: {
                @if (\Illuminate\Support\Facades\Auth::user()->role == "Referent")
                Solved:{
                    text: '{{__("Mark as solved")}}',
                    class: "leftInquiryDialogButton",
                    click: function () {
                        let _text = $("#inquiry_pnad_details").val().trim();
                        if (_text == undefined || _text == null || _text.length == 0) return;
                        $.ajaxSetup({
                            headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        jQuery.ajaxSetup({async: false});
                        let pnad_status = "S" + inqPnadStatus;
                        $.post("webservice/sendinquiry",
                            {
                                ebeln: inqPorder,
                                ebelp: inqPitem,
                                text: _text,
                                pnad_status: pnad_status,
                                to: "p"
                            },
                            function (data, status) {
                                inquiryData = data;
                                inquiryStatus = status;
                            });
                        jQuery.ajaxSetup({async: true});
                        newInquiryDialog.dialog("close");
                        location.reload();
                    }
                },
                @endif
                Send: {
                    text: '{{__("Send")}}',
                    class: "inquirySendDialogButton",
                    click: function () {
                        var _to = "";
                        var _text = $("#new_inquiry").val();
                        @if (\Illuminate\Support\Facades\Auth::user()->role == "Furnizor")
                            _to = 'R';
                        @elseif (\Illuminate\Support\Facades\Auth::user()->role == "CTV")
                            _to = 'R';
                        @elseif (\Illuminate\Support\Facades\Auth::user()->role == "Referent")
                            _to = $('#inquiry_recipient').val();
                            if (_to == "P") {
                                _text = $("#inquiry_pnad_details").val().trim();
                                if (_text.length == 0) return;
                            }
                        @endif
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        let pnad_status = "O" + inqPnadStatus;
                        jQuery.ajaxSetup({async: false});
                        $.post("webservice/sendinquiry",
                            {
                                ebeln: inqPorder,
                                ebelp: inqPitem,
                                text: _text,
                                pnad_status: pnad_status,
                                to: _to
                            },
                            function (data, status) {
                                inquiryData = data;
                                inquiryStatus = status;
                            });
                        jQuery.ajaxSetup({async: true});
                        if (inqCDate != null && _to != "P") replyack(inqPorder, inqPitem, inqCDate);
                        newInquiryDialog.dialog("close");
                        location.reload();
                    }
                },
                Cancel: function () {
                    newInquiryDialog.dialog("close");
                }
            },
            open: function() {
                var _odata, _ostatus;
                inquiry_recipient_changed();
                $("#inquiry_pnad_details").val("");
                $(".leftInquiryDialogButton").text('{{__("Mark as solved")}}')
                if (inqPnadStatus == "X") $(".leftInquiryDialogButton").text('{{__("Mark as unsolved")}}');
            },
            close: function () {
                newInquiryForm[0].reset();
            },
            position: {
                my: 'right top-160px',
                at: 'center middle',
                of: window
            }
        });
        $("#new_inquiry").on('input', function () {
            if ($("#new_inquiry_msg").text() != "") $("#new_inquiry_msg").text("")
        });
        newInquiryForm = newInquiryDialog.find("form").on("submit", function (event) {
            event.preventDefault();
        });
    });

    function send_inquiry(porder, pitem, pnad_status) {
        $("#new_inquiry_msg").text("");
        $("#new-inquiry-dialog").dialog('option', 'title', 'Trimitere mesaj');
        if (pnad_status != "N") $("#new-inquiry-dialog").dialog('option', 'title', 'Exceptie');
        inqPorder = porder;
        inqPitem = pitem;
        inqCDate = null;
        inqPnadStatus = pnad_status;
        newInquiryDialog.dialog("open");
    }

    function replyto_inquiry(porder, pitem, cdate) {
        $("#new_inquiry_msg").text("");
        $("#new-inquiry-dialog").dialog('option', 'title', 'Send message');
        inqPorder = porder;
        inqPitem = pitem;
        inqCDate = cdate;
        newInquiryDialog.dialog("open");
    }

    function inquiry_recipient_changed() {
        @if (\Illuminate\Support\Facades\Auth::user()->role == "Referent")
            let _to = $('#inquiry_recipient').val();
            if (_to == "P") {
                $("#new_inquiry_div").hide();
                $("#new_pnad_div").show();
                $(".leftInquiryDialogButton").show();
                $(".inquirySendDialogButton").text("{{__('Save')}}");
                $("#new_inquiry_msg").text("");
            } else {
        @endif
        $("#new_inquiry_div").show();
        $("#new_pnad_div").hide();
        $(".leftInquiryDialogButton").hide();
        $(".inquirySendDialogButton").text("{{__('Send')}}");
        @if (\Illuminate\Support\Facades\Auth::user()->role == "Referent")
        }
        @endif
    }

</script>