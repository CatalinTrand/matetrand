<div id="new-inquiry-dialog" title="Change user password">
    <form>
        <br>
        <div class="form-group row" style="width: 80%">
            <label for="new_inquiry" class="col-md-4 col-form-label text-md-left">{{__('New Inquiry')}}</label>
            <input id="new_inquiry" type="text" name="new_inquiry" size="20" style="width: 200px;"
                   class="form-control col-md-6" required value="">
        </div>
        <i id="new_inquiry_msg" style="color: red"></i>
    </form>
</div>


<script>

    var inqIdForUser, inqPorder, inqPitem, newInquiryDialog, newInquiryForm;
    var inquiryData, inquiryStatus;
    $(function () {
        newInquiryDialog = $("#new-inquiry-dialog").dialog({
            autoOpen: false,
            height: 200,
            width: 550,
            modal: true,
            buttons: {
                Send: function () {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    jQuery.ajaxSetup({async: false});
                    $.post("webservice/sendinquiry",
                        {
                            from: inqIdForUser,
                            ebeln: inqPorder,
                            ebelp: inqPitem,
                            text: $("#new_inquiry").val()
                        },
                        function (data, status) {
                            inquiryData = data;
                            inquiryStatus = status;
                        });
                    jQuery.ajaxSetup({async: true});

                    if (inquiryStatus == "success") {
                        newInquiryDialog.dialog("close");
                    }
                    else {
                        if (inquiryData != "OK")
                            $("#new_inquiry_msg").text(inquiryData);
                        else $("#new_inquiry_msg").text("An error occured sending the inquiry");
                    }
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

    function send_inquiry(userid,porder,pitem) {
        $("#new_inquiry_msg").text("");
        $("#new-inquiry-dialog").dialog('option', 'title', 'Send inquiry to ' + userid);
        inqIdForUser = userid;
        inqPorder = porder;
        inqPitem = pitem;
        newInquiryDialog.dialog("open");
    }
</script>