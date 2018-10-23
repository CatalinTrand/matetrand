<div id="accept-reject-dialog" title="Accept/reject" >
    <form>
        <br>
        <div class="form-group container" align="left">
            <div class="row">
                <label for="initial-text" class="col-md-2 col-form-label text-md-left">Initial text</label>&nbsp;&nbsp;
                <textarea id="initial-text" type="text" name="initial-text" class="form-control col-md-9" style="word-break: break-word; height: 4rem;" maxlength="100" value=""></textarea>
            </div>
            <div class="row">
                <label for="accept-reject-category" class="col-md-2 col-form-label text-md-left">{{__("Reason")}}</label>&nbsp;&nbsp;
                <select id="accept-reject-category" name="accept-reject-category" class="form-control col-md-9" onchange="acceptRejectCategoryChange(this);return false;">
                    <option value="1" selected>Accept as-is</option>
                    <option value="2">Reject</option>
                    <option value="3">Change vendor</option>
                </select>
            </div>

            <div id="extra-fields" style="display: none">
                <div class="row">
                    <label for="ar-lifnr-text" class="col-md-2 col-form-label text-md-left">{{__('Vendor')}}</label>&nbsp;&nbsp;
                    <textarea id="ar-lifnr-text" type="text" name="ar-lifnr-text" class="form-control col-md-9" style="word-break: break-word; height: 4rem;" maxlength="100" value=""></textarea>
                </div>
                <div class="row">
                    <label for="ar-idnlf-text" class="col-md-2 col-form-label text-md-left">{{__('Vendor material')}}</label>&nbsp;&nbsp;
                    <textarea id="ar-idnlf-text" type="text" name="ar-idnlf-text" class="form-control col-md-9" style="word-break: break-word; height: 4rem;" maxlength="100" value=""></textarea>
                </div>
                <div class="row">
                    <label for="ar-matnr-text" class="col-md-2 col-form-label text-md-left">{{__('Material')}}</label>&nbsp;&nbsp;
                    <textarea id="ar-matnr-text" type="text" name="ar-matnr-text" class="form-control col-md-9" style="word-break: break-word; height: 4rem;" maxlength="100" value=""></textarea>
                </div>
                <div class="row">
                    <label for="ar-price-text" class="col-md-2 col-form-label text-md-left">{{__('Price')}}</label>&nbsp;&nbsp;
                    <textarea id="ar-price-text" type="text" name="ar-price-text" class="form-control col-md-9" style="word-break: break-word; height: 4rem;" maxlength="100" value=""></textarea>
                </div>
                <div class="row">
                    <label for="ar-currency-text" class="col-md-2 col-form-label text-md-left">{{__('Currency')}}</label>&nbsp;&nbsp;
                    <textarea id="ar-currency-text" type="text" name="ar-currency-text" class="form-control col-md-9" style="word-break: break-word; height: 4rem;" maxlength="100" value=""></textarea>
                </div>
            </div>
        </div>

        <input type="checkbox" id="require_ctv_approval"> Require CTV approval
        <br>
        <button type="button" onclick="read_inforecords(); return false;">Inforecords</button>
        <br>
        <i id="new_acc_rej_msg" style="color: red"></i>
    </form>
</div>

<script>
    var arDialog, arForm, _type, _ar_this, last_value = 1;

    function acceptRejectCategoryChange(select){
        last_value = select.value;
        if(last_value == 3) {
            $("#extra-fields").style.display = "";
        }else {
            $("#extra-fields").style.display = "none";
        }
    }

    $(function () {
        arDialog = $("#accept-reject-dialog").dialog({
            autoOpen: false,
            height: 480,
            width: 480,
            modal: true,
            buttons: {
        Save: function (){
            if(last_value == 3)
                if( $("#ar-lifnr-text").val().length == 0 || $("#ar-idnlf-text").val().length == 0 || $("#ar-matnr-text").val().length == 0 || $("#ar-price-text").val().length == 0 || $("#ar-currency-text").val().length == 0)
                    return;

            //functia ta aici
        },
        Cancel: function () {
            arDialog.dialog("close");
        }
    },
        close: function () {
            arForm[0].reset();
        },
        position: {
            my: "center",
                at: "center",
                of: window
        }
    });
        arForm = arDialog.find("form").on("submit", function (event) {
            event.preventDefault();
        });
    });

    function accept_reject_complex(type, this0, title, initial_text) {
        $("#new_rej_msg").text("");
        $("#initial-text").val(initial_text);
        if(type == 1){
            $('option[value="1"]', $('#accept-reject-category')).remove();
            $('#accept-reject-category').val(2);
        }else{
            if($('#accept-reject-category').val() != 1){
                $('#accept-reject-category').append('<option value="1">Accept as-is</option>');;
                $('#accept-reject-category').val(1);
            }
        }
        $("#accept-reject-dialog").dialog('option', 'title', title);
        _type = type;
        _ar_this = this0;
        arDialog.dialog("open");
    }
</script>

<div id="accept-reject-simple" title="Accept/reject">
    <div class="row">
        <label for="ar-message" class="col-md-2 col-form-label text-md-left">{{__('Message')}}</label>&nbsp;&nbsp;
        <textarea id="ar-message" type="text" name="aar-message" class="form-control col-md-9" style="word-break: break-word; height: 4rem;" maxlength="100" value=""></textarea>
    </div>
</div>

<script>
    var ar_simple_Dialog, ar_simple_Form, _ar_simple_this;

    $(function () {
        ar_simple_Dialog = $("#init-rejection-simple").dialog({
            autoOpen: false,
            height: 250,
            width: 480,
            modal: true,
            buttons: {
                Accept: function (){
                    //functia ta aici
                },
                Reject: function () {
                    //functia ta aici
                }
            },
            close: function () {
                arForm[0].reset();
            },
            position: {
                my: "center",
                at: "center",
                of: window
            }
        });
        ar_simple_Form = ar_simple_Dialog.find("form").on("submit", function (event) {
            event.preventDefault();
        });
    });

    function accept_reject_simple(this0, title, initial_text) {
        $("#ar-message").val(initial_text);
        $("#accept-reject-simple").dialog('option', 'title', title);

        _ar_simple_this = this0;
        ar_simple_Dialog.dialog("open");
    }
</script>