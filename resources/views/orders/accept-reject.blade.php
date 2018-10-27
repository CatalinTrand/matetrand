<div id="accept-reject-dialog" title="Accept/reject">
    <form>
        <br>
        <div class="form-group container" align="left">
            <div class="row" style="width: 95%; text-align: left;">
                <p id="initial-text" type="text" name="initial-text" width="100%" value=""></p>
            </div>
            <div class="row">
                <label for="accept-reject-category"
                       class="col-md-2 col-form-label text-md-left">{{__("Action")}}</label>&nbsp;&nbsp;
                <select id="accept-reject-category" name="accept-reject-category" class="form-control col-md-9"
                        onchange="acceptRejectCategoryChange(this);return false;">
                    <option value="1" selected>Accept as-is</option>
                    <option value="2">Reject</option>
                    <option value="3">New proposal</option>
                </select>
            </div>
            <br>
            <br>
            <div class="row">
                <label class="col-md-2 col-form-label text-md-left">{{__("New proposal")}}</label>&nbsp;
                <button type="button" onclick="add_edit_proposal(1,this);return false;">{{__('New')}}</button>
            </div>
        </div>
        <br>
        <input type="checkbox" id="require_ctv_approval"> Require CTV approval
        <br>
        <i id="new_acc_rej_msg" style="color: red"></i>
    </form>

    <br>

    <div id="proposal_list" style="overflow-y: scroll; display: block; max-height: 300px">
        <table class="table-striped" id="proposals_table_l">
            <colgroup>
                <col width="10%">
                <col width="15%">
                <col width="10%">
                <col width="20%">
                <col width="5%">
                <col width="15%">
                <col width="5%">
                <col width="10%">
                <col width="5%">
                <col width="2.5%">
                <col width="2.5%">
            </colgroup>
            <tr>
                <th>{{__('Vendor')}}</th>
                <th>{{__('Vendor Name')}}</th>
                <th>{{__('Material')}}</th>
                <th>{{__('Material description')}}</th>
                <th>{{__('Material group')}}</th>
                <th>{{__('Purchase price')}}</th>
                <th>{{__('Purchase currency')}}</th>
                <th>{{__('Sales price')}}</th>
                <th>{{__('Sales currency')}}</th>
                <th></th>
                <th></th>
            </tr>
        </table>
    </div>
</div>

<script>
    var arDialog, arForm, _ar_type, _ar_this, ar_last_value = 1;

    function acceptRejectCategoryChange(select) {
        return;
        ar_last_value = select.value;
        if (ar_last_value == 3) {
            $("#extra-fields").attr("style", "display: block");
        } else {
            $("#extra-fields").attr("style", "display: none");
        }
    }

    $(function () {
        arDialog = $("#accept-reject-dialog").dialog({
            autoOpen: false,
            height: 480,
            width: 680,
            modal: true,
            buttons: {
                Save: function () {
                    if (ar_last_value == 3)
                        if ($('#proposals_table_l').rows.length < 2) //no entries, only first row (thead row)
                            return;
                    if (_ar_type == 2) {
                        arDialog.dialog("close");
                        acceptPItem(_ar_this);
                        if (ar_last_value == 3)
                        /* createPurchReq(_ar_this, $("#ar-lifnr-text").val()); */
                            return;
                    }
                    if (_ar_type == 1) {
                        arDialog.dialog("close");
                        rejectPItem(_ar_this, 3, "");
                        if (ar_last_value == 3)
                        /* createPurchReq(_ar_this); */
                            return;
                    }
                },
                Cancel: function () {
                    arDialog.dialog("close");
                }
            },
            close: function () {
                arForm[0].reset();
            },
            open: function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $("#accept-reject-dialog").find("tr:gt(0)").remove();
                $('body').addClass('ajaxloading');
                jQuery.ajaxSetup({async: false});
                var _data, _status;

                let ar_d_ebeln = _ar_this.parentElement.parentElement.id.split('_')[1].substring(1);
                let ar_d_ebelp = _ar_this.parentElement.parentElement.id.split('_')[2];
                let ar_d_cdate = "2018-10-10 10:10:10";

                $.post("webservice/readAllProposals",
                    {
                        ebeln: ar_d_ebeln,
                        ebelp: ar_d_ebelp,
                        cdate: ar_d_cdate
                    },
                    function (data, status) {
                        _data = data;
                        _status = status;
                    }, "json");
                jQuery.ajaxSetup({async: true});
                if (_status != "success") {
                    $('body').removeClass('ajaxloading');
                    arDialog.dialog("close");
                    return;
                }
                if (_data.length > 0) {
                    let table = $("#proposals_table_l");//TODO???????????????????????
                    for (let i = 0; i < _data.length; i++) {
                        var newRow = $("<tr id='" + _data[i].ebeln + "-" + _data[i].ebelp + "-" + _data[i].cdate + "' style='height: 1.5rem;'>");
                        var cols = "<td>" + _data[i].lifnr + "</td>" +
                            "<td>" + _data[i].lifnr_name + "</td>" +
                            "<td>" + _data[i].idnlf + "</td>" +
                            "<td>" + _data[i].mtext + "</td>" +
                            "<td>" + _data[i].matnr + "</td>" +
                            "<td>" + _data[i].purch_price + "</td>" +
                            "<td>" + _data[i].purch_curr + "</td>" +
                            "<td>" + _data[i].sales_price + "</td>" +
                            "<td>" + _data[i].sales_curr + "</td>" +
                            "<td><button type='button' onclick='add_edit_proposal(0,this);return false;'>Edit</button></td>" +
                            "<td><button type='button' onclick='deleteProposal(this);return false;'>Delete</button></td>";
                        newRow.append(cols); // .hide();
                        table.append(newRow);
                    }
                }
                $('body').removeClass('ajaxloading');
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
        $("#initial-text").text(initial_text);
        $("#extra-fields").attr("style", "display: none");
        if (type == 1) {
            $('option[value="1"]', $('#accept-reject-category')).remove();
            $('#accept-reject-category').val(2);
        } else {
            if ($('#accept-reject-category option').size < 3) {
                $('#accept-reject-category').eq(0).before($('', {value: 1, text: 'Accept as-is'}));
                $('#accept-reject-category').val(1);
            }
        }
        $("#accept-reject-dialog").dialog('option', 'title', title);
        _ar_type = type;
        _ar_this = this0;
        arDialog.dialog("open");
    }
</script>

<div id="accept-reject-simple" title="Accept/reject">
    <div class="row">
        <label for="ar-message" class="col-md-2 col-form-label text-md-left">{{__('Message')}}</label>&nbsp;&nbsp;
        <textarea id="ar-message" type="text" name="aar-message" class="form-control col-md-9"
                  style="word-break: break-word; height: 4rem;" maxlength="100" value=""></textarea>
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
                Accept: function () {
                },
                Reject: function () {
                    //functia ta aici
                }
            },
            close: function () {
                ar_simple_Form[0].reset();
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

    function createPurchReq(_this, _lifnr, _idnlf, _mtext, _matnr, _price, _curr, _infnr) {

    }

</script>

<div id="add-edit-proposal" title="Add/edit proposal">
    <div class="row">
        <label for="ar-lifnr-text" class="col-md-3 col-form-label text-md-left">{{__('Vendor')}}</label>&nbsp;&nbsp;
        <input id="ar-lifnr-text" type="text" name="ar-lifnr-text" class="form-control col-md-5" value="">
    </div>
    <br>
    <div class="row">
        <label for="ar-lifnr-name-text" class="col-md-3 col-form-label text-md-left">{{__('Vendor Name')}}</label>&nbsp;&nbsp;
        <input id="ar-lifnr-name-text" type="text" name="ar-lifnr-name-text" class="form-control col-md-5" value="">
    </div>
    <br>
    <div class="row">
        <label for="ar-idnlf-text" class="col-md-3 col-form-label text-md-left">{{__('Material')}}</label>&nbsp;&nbsp;
        <input id="ar-idnlf-text" type="text" name="ar-idnlf-text" class="form-control col-md-8" value="">
    </div>
    <br>
    <div class="row">
        <label for="ar-mtext-text" class="col-md-3 col-form-label text-md-left">{{__('Material description')}}</label>&nbsp;&nbsp;
        <input id="ar-mtext-text" type="text" name="ar-mtext-text" class="form-control col-md-8" value="">
    </div>
    <br>
    <div class="row">
        <label for="ar-matnr-text" class="col-md-3 col-form-label text-md-left">{{__('Material group')}}</label>&nbsp;&nbsp;
        <input id="ar-matnr-text" type="text" name="ar-matnr-text" class="form-control col-md-8" value="">
    </div>
    <br>
    <div class="row">
        <label for="ar-purch-price-text" class="col-md-3 col-form-label text-md-left">{{__('Purchase price')}}</label>&nbsp;&nbsp;
        <input id="ar-purch-price-text" type="text" name="ar-purch-price-text" class="form-control col-md-4" value="">
    </div>
    <br>
    <div class="row">
        <label for="ar-purch-currency-text"
               class="col-md-3 col-form-label text-md-left">{{__('Purchase currency')}}</label>&nbsp;&nbsp;
        <input id="ar-purch-currency-text" type="text" name="ar-purch-currency-text" class="form-control col-md-2"
               value="">
    </div>
    <br>
    <div class="row">
        <label for="ar-sales-price-text" class="col-md-3 col-form-label text-md-left">{{__('Sales price')}}</label>&nbsp;&nbsp;
        <input id="ar-sales-price-text" type="text" name="ar-sales-price-text" class="form-control col-md-4" value="">
    </div>
    <br>
    <div class="row">
        <label for="ar-sales-currency-text"
               class="col-md-3 col-form-label text-md-left">{{__('Sales currency')}}</label>&nbsp;&nbsp;
        <input id="ar-sales-currency-text" type="text" name="ar-currency-text" class="form-sales-control col-md-2"
               value="">
    </div>
</div>

<script>
    var add_edit_Dialog, add_edit_Form, add_edit_this;

    $(function () {
        add_edit_Dialog = $("#add-edit-proposal").dialog({
            autoOpen: false,
            height: 520,
            width: 750,
            modal: true,
            buttons: {
                Save: function () {
                    let ebeln = _ar_this.parentElement.parentElement.id.split('_')[1].substring(1);
                    let ebelp = _ar_this.parentElement.parentElement.id.split('_')[2];
                    let cdate = add_edit_this == null ? "2018-10-10 10:10:10" : add_edit_this.parentElement.parentElement.id.split('-')[2];//TODO-de unde il iau
                    let pos = add_edit_this == null ? "-1" : add_edit_this.parentElement.parentElement.id.split('-')[3];

                    let lifnr = $("#ar-lifnr-text").val();
                    let lifnr_name = $("#ar-lifnr-name-text").val();
                    let idnlf = $("#ar-idnlf-text").val();
                    let mtext = $("#ar-mtext-text").val();
                    let matnr = $("#ar-matnr-text").val();
                    let purch_price = $("#ar-purch-price-text").val();
                    let purch_curr = $("#ar-purch-currency-text").val();
                    let sales_price = $("#ar-sales-price-text").val();
                    let sales_curr = $("#ar-sales-currency-text").val();

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $('body').addClass('ajaxloading');
                    jQuery.ajaxSetup({async: false});
                    var _data, _status;
                    $.post("webservice/modifyProposals",
                        {
                            ebeln: ebeln,
                            ebelp: ebelp,
                            cdate: cdate,
                            pos: pos,
                            lifnr: lifnr,
                            lifnr_name: lifnr_name,
                            idnlf: idnlf,
                            mtext: mtext,
                            matnr: matnr,
                            purch_price: purch_price,
                            purch_curr: purch_curr,
                            sales_price: sales_price,
                            sales_curr: sales_curr
                        },
                        function (data, status) {
                            _data = data;
                            _status = status;
                        });
                    jQuery.ajaxSetup({async: true});
                    $('body').removeClass('ajaxloading');
                    if (_status == "success") {
                        add_edit_Dialog.dialog("close");
                    } else alert('Error processing request!');
                },
                Cancel: function () {
                    add_edit_Dialog.dialog("close");
                }
            },
            position: {
                my: "center",
                at: "center",
                of: window
            }
        });
        add_edit_Form = add_edit_Dialog.find("form").on("submit", function (event) {
            event.preventDefault();
        });
    });

    function add_edit_proposal(mode, this0) {
        let title = "";

        if (mode == 1) {
            title = "Add new proposal";
            $("#ar-lifnr-text").val("");
            $("#ar-lifnr-name-text").val("");
            $("#ar-idnlf-text").val("");
            $("#ar-mtext-text").val("");
            $("#ar-matnr-text").val("");
            $("#ar-purch-price-text").val("");
            $("#ar-purch-currency-text").val("");
            $("#ar-sales-price-text").val("");
            $("#ar-sales-currency-text").val("");
        } else {
            title = "Edit existing proposal";

            let current_row = this0.parentElement.parentElement;

            let lifnr = current_row.cells[0].innerHTML;
            let lifnr_name = current_row.cells[1].innerHTML;
            let idnlf = current_row.cells[2].innerHTML;
            let mtext = current_row.cells[3].innerHTML;
            let matnr = current_row.cells[4].innerHTML;
            let purch_price = current_row.cells[5].innerHTML;
            let purch_curr = current_row.cells[6].innerHTML;
            let sales_price = current_row.cells[7].innerHTML;
            let sales_curr = current_row.cells[8].innerHTML;

            $("#ar-lifnr-text").val(lifnr);
            $("#ar-lifnr-name-text").val(lifnr_name);
            $("#ar-idnlf-text").val(idnlf);
            $("#ar-mtext-text").val(mtext);
            $("#ar-matnr-text").val(matnr);
            $("#ar-purch-price-text").val(purch_price);
            $("#ar-purch-currency-text").val(purch_curr);
            $("#ar-sales-price-text").val(sales_price);
            $("#ar-sales-currency-text").val(sales_curr);
        }

        $("#add-edit-proposal").dialog('option', 'title', title);
        if (mode != 1)
            add_edit_this = this0;
        else
            add_edit_this = null;
        $('#add-edit-proposal').css('overflow', 'hidden');
        add_edit_Dialog.dialog("open");
    }

</script>

<div id="select-proposal-dialog" title="Select a proposal">
    <form>
        <br>
        <div class="form-group container" align="left">
            <div id="extra-fields" style="display: none;height: 100%">
                <div style="overflow-y: scroll">
                    <table id="proposals_table">
                        <colgroup>
                            <col width="10%">
                            <col width="15%">
                            <col width="10%">
                            <col width="20%">
                            <col width="5%">
                            <col width="15%">
                            <col width="5%">
                            <col width="10%">
                            <col width="5%">
                            <col width="2.5%">
                            <col width="2.5%">
                        </colgroup>
                        <tr>
                            <th>{{__('Vendor')}}</th>
                            <th>{{__('Vendor Name')}}</th>
                            <th>{{__('Material')}}</th>
                            <th>{{__('Material description')}}</th>
                            <th>{{__('Material group')}}</th>
                            <th>{{__('Purchase price')}}</th>
                            <th>{{__('Purchase currency')}}</th>
                            <th>{{__('Sales price')}}</th>
                            <th>{{__('Sales currency')}}</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </form>
</div>

<script>

    var select_proposal_dialog, select_proposal_form;
    $(function () {
        select_proposal_dialog = $("#inforecord-dialog").dialog({
            autoOpen: false,
            height: 600,
            width: 960,
            modal: true,
            buttons: {
                Use: function () {
                    if (proposal_last_selected_line != null) {
                        let current_row = proposal_last_selected_line;
                        let result_set = current_row.id;
                        let ebeln = result_set.split('-')[0];
                        let ebelp = result_set.split('-')[1];
                        let cdate = result_set.split('-')[2];
                        let pos = result_set.split('-')[3];
                        acceptedVariant(ebeln, ebelp, cdate, pos);
                        select_proposal_dialog.dialog("close");
                    }
                },
                Cancel: function () {
                    select_proposal_dialog.dialog("close");
                }
            },
            close: function () {
                select_proposal_form[0].reset();
            },
            open: function () {
                $('#inforecord-dialog').css('overflow', 'hidden');
                proposal_last_selected_line = null;
            },
            position: {
                my: "center",
                at: "center",
                of: window
            }
        });

        select_proposal_form = select_proposal_dialog.find("form").on("submit", function (event) {
            event.preventDefault();
        });
    });

    function acceptVariantDlg(ebeln, ebelp, cdate) {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $("#select-proposal-dialog").find("tr:gt(0)").remove();
        $('body').addClass('ajaxloading');
        jQuery.ajaxSetup({async: false});
        var _data, _status;
        $.post("webservice/readAllProposals",
            {
                ebeln: ebeln,
                ebelp: ebelp,
                cdate: cdate
            },
            function (data, status) {
                _data = data;
                _status = status;
            }, "json");
        jQuery.ajaxSetup({async: true});
        if (_status != "success") {
            $('body').removeClass('ajaxloading');
            return;
        }
        if (_data.length > 0) {
            let table = $("#select-proposal-dialog");
            for (let i = 0; i < _data.length; i++) {
                var newRow = $("<tr id='" + _data[i].infnr + "' style='height: 1.5rem;' onclick='proposal_selected(this);return false;'>");
                var cols = "<td colspan='1'>" + _data[i].lifnr + "</td>" +
                    "<td colspan='5'>" + _data[i].lifnr_name + "</td>" +
                    "<td colspan='2'>" + _data[i].idnlf + "</td>" +
                    "<td colspan='7'>" + _data[i].mtext + "</td>" +
                    "<td colspan='2'>" + _data[i].matnr + "</td>" +
                    "<td colspan='3'>" + _data[i].purch_price + " " + _data[i].purch_curr + "</td>" +
                    "<td colspan='3'>" + _data[i].sales_price + " " + _data[i].sales_curr + "</td>";
                newRow.append(cols); // .hide();
                table.append(newRow);
            }
        }
        $('body').removeClass('ajaxloading');

        $("#select-proposal-dialog-dialog").dialog('option', 'title', 'Select a proposal');
        select_proposal_dialog.dialog("open");
    }

    var proposal_last_selected_line;
    var proposal_last_color;

    function proposal_selected(_this) {
        if (proposal_last_selected_line != null)
            $(proposal_last_selected_line).css("background-color", proposal_last_color);

        proposal_last_color = $(_this).css("background-color");
        proposal_last_selected_line = _this;

        $(_this).css("background-color", "#55bb55");
    }
</script>