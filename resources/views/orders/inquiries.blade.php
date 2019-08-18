<div id="new-inquiry-dialog" title="Send message">
    <form>
        @if (\Illuminate\Support\Facades\Auth::user()->role == "Referent")
            <div id="new_inquiry_dest" class="form-group" style="width: 95%; margin-left: 0.5rem;">
                <div class="row" style="width: 100%; height: 1.75rem;">
                    <label for="inquiry_recipient" id="label_inquiry_recipient"
                           class="col-md-2 col-form-label text-md-left">{{__("Recipient")}}</label>
                    <select class="form-control-sm input-sm" style="height: 1.5rem; margin-top: 3px; padding: 2px;" id="inquiry_recipient"
                        onchange="inquiry_recipient_changed();">
                        <option value="F" selected>{{__("Vendor")}}</option>
                        <option value="C">{{__("CTV")}}</option>
                        <option value="P">{{__("PNAD")}}</option>
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
                <label for="inquiry_pnad_cause" id="label_inquiry_pnad_cause"
                       class="col-md-2 col-form-label text-md-left">{{__("Cause")}}</label>
                <select id="inquiry_pnad_cause" type="text" name="inquiry_pnad_cause" style="width: 15em; padding: 2px; height: 1.5rem;"
                        class="form-control-sm input-sm" value="">
                </select>
            </div>
            <div class="row" style="width: 100%; height: 1.75rem;">
                <label for="inquiry_pnad_solution" id="label_inquiry_pnad_solution"
                       class="col-md-2 col-form-label text-md-left">{{__("Solution")}}</label>
                <select id="inquiry_pnad_solution" type="text" name="inquiry_pnad_solution" style="width: 15em; padding: 2px;height: 1.5rem;"
                        class="form-control-sm input-sm" value="">
                </select>
            </div>
            <div class="row" style="width: 100%; height: 1.75rem;">
                <label for="inquiry_pnad_details" id="label_inquiry_pnad_details"
                       class="col-md-2 col-form-label text-md-left">{{__("Details")}}</label>
                <input id="inquiry_pnad_details" style="width: 75%; height: 1.5rem;" type="text" name="inquiry_pnad_details"
                       class="form-control-sm input-sm" maxlength="100" value="">
            </div>
        </div>
    </form>
</div>


<script>

    var inqPorder, inqPitem, inqCDate, newInquiryDialog, newInquiryForm;
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
                        let _text = "" +
                            $("#inquiry_pnad_cause option:selected").text().trim() + "//@@//" +
                            $("#inquiry_pnad_solution option:selected").text().trim() + "//@@//" +
                            $("#inquiry_pnad_details").val().trim();
                        if (_text.length == 12) return;
                        $.ajaxSetup({
                            headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        jQuery.ajaxSetup({async: false});
                        $.post("webservice/sendinquiry",
                            {
                                ebeln: inqPorder,
                                ebelp: inqPitem,
                                text: _text,
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
                                _text = "" + $("#inquiry_pnad_cause option:selected").text().trim() +"//@@//" +
                                             $("#inquiry_pnad_solution option:selected").text().trim() + "//@@//" +
                                             $("#inquiry_pnad_details").val().trim();
                                if (_text.length == 12) return;
                            }
                        @endif
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        jQuery.ajaxSetup({async: false});
                        $.post("webservice/sendinquiry",
                            {
                                ebeln: inqPorder,
                                ebelp: inqPitem,
                                text: _text,
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
                jQuery.ajaxSetup({async: false});
                $.get("webservice/sap_read_pnad_dd",
                    {},
                    function (data, status) {
                        _odata = data;
                        _ostatus = status;
                    }, "json");
                jQuery.ajaxSetup({async: true});
                if (_ostatus != undefined && _ostatus == "success" && _odata != null) {
                    var control;
                    control = $('#inquiry_pnad_cause').find('option').remove().end();
                    control.append('<option value="" selected></option>');
                    if (_odata.cause != null)
                        for (i = 0; i < _odata.cause.length; i++)
                            control.append('<option value="' + _odata.cause[i].value + '">' +
                                _odata.cause[i].value + ' ' + _odata.cause[i].text + '</option>');
                    control = $('#inquiry_pnad_solution').find('option').remove().end();
                    control.append('<option value="" selected></option>');
                    if (_odata.solution != null)
                        for (i = 0; i < _odata.solution.length; i++)
                            control.append('<option value="' + _odata.solution[i].value + '">' +
                                _odata.solution[i].value + ' ' + _odata.solution[i].text + '</option>');
                }
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

    function send_inquiry(porder, pitem) {
        $("#new_inquiry_msg").text("");
        $("#new-inquiry-dialog").dialog('option', 'title', 'Send inquiry/message');
        inqPorder = porder;
        inqPitem = pitem;
        inqCDate = null;
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