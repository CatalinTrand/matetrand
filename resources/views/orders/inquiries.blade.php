<div id="new-inquiry-dialog" title="Change user password">
    <form>
        @if (\Illuminate\Support\Facades\Auth::user()->role == "Referent")
            <div class="form-group row" style="width: 95%; margin-left: 0.5rem;">
                <label for="inquiry_recipient" id="label_inquiry_recipient"
                       class="col-md-2 col-form-label text-md-left">{{__("Recipient")}}</label>
                <select class="form-control-sm input-sm" style="height: 1.6rem; margin-left: 3px; margin-top: 3px; " id="inquiry_recipient">
                    <option value="F" selected>{{__("Vendor")}}</option>
                    <option value="C">{{__("CTV")}}</option>
                </select>
            </div>
        @else
            <br>
        @endif
        <div class="form-group row" style="width: 95%; margin-left: 1rem;">
            <textarea id="new_inquiry" type="text" name="new_inquiry" class="form-control"
                      style="word-break: break-word; height: 4rem;" maxlength="100" value=""></textarea>
        </div>
        <i id="new_inquiry_msg" style="color: red"></i>
    </form>
</div>


<script>

    var inqPorder, inqPitem, inqCDate, newInquiryDialog, newInquiryForm;
    var inquiryData, inquiryStatus;
    $(function () {
        newInquiryDialog = $("#new-inquiry-dialog").dialog({
            autoOpen: false,
            height: 240,
            width: 550,
            modal: true,
            buttons: {
                Send: function () {
                    var _to = '';
                    @if (\Illuminate\Support\Facades\Auth::user()->role == "Furnizor")
                    _to = 'R';
                    @elseif (\Illuminate\Support\Facades\Auth::user()->role == "CTV")
                    _to = 'R';
                    @elseif (\Illuminate\Support\Facades\Auth::user()->role == "Referent")
                    _to = $('#inquiry_recipient').val();
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
                            text: $("#new_inquiry").val(),
                            to: _to
                        },
                        function (data, status) {
                            inquiryData = data;
                            inquiryStatus = status;
                        });
                    jQuery.ajaxSetup({async: true});
                    if (inqCDate != null) replyack(inqPorder, inqPitem, inqCDate);
                    newInquiryDialog.dialog("close");
                    location.reload();
                },
                Cancel: function () {
                    newInquiryDialog.dialog("close");
                }
            },
            close: function () {
                newInquiryForm[0].reset();
            },
            position: {
                my: 'top',
                at: 'middle',
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
        $("#new-inquiry-dialog").dialog('option', 'title', 'Reply to inquiry/message');
        inqPorder = porder;
        inqPitem = pitem;
        inqCDate = cdate;
        newInquiryDialog.dialog("open");
    }

</script>