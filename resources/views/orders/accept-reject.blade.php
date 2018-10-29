<div id="accept-reject-dialog" title="Accept/reject">
    <p id="initial-text" type="text" style="margin-top: 0.5rem; font-weight: bold; color: darkred;" name="initial-text" width="95%" value=""></p>
    <input type="checkbox" id="require_sr_approval" onchange="ar_switchProposalMode(this); return false;">
    <label for="require_sr_approval">{{__("Require SR approval")}}</label>
    <br><br>
    <div id="ar-proposals-immed" width="95%" style="margin-right: 0.5rem;">
        <div class="row">
            <label for="ar-immed-lifnr" class="col-md-2 col-form-label text-md-left">{{__('Vendor')}}</label>&nbsp;&nbsp;
            <input id="ar-immed-lifnr" type="text" name="ar-immediate-lifnr" class="form-control col-md-2" value="">
        </div>
        <div class="row" style="padding-top: 3px;">
            <label for="ar-immed-idnlf" class="col-md-2 col-form-label text-md-left">{{__('Vendor mat.')}}</label>&nbsp;&nbsp;
            <input id="ar-immed-idnlf" type="text" name="ar-immed-idnlf" class="form-control col-md-2" value="">
        </div>
        <div class="row" style="padding-top: 3px;">
            <label for="ar-immed-mtext" class="col-md-2 col-form-label text-md-left">{{__('Material description')}}</label>&nbsp;&nbsp;
            <input id="ar-immed-mtext" type="text" name="ar-immed-mtext" class="form-control col-md-2" value="">
        </div>
        <div class="row" style="padding-top: 3px;">
            <label for="ar-immed-matnr" class="col-md-2 col-form-label text-md-left">{{__('Material')}}</label>&nbsp;&nbsp;
            <input id="ar-immed-matnr" type="text" name="ar-immed-matnr" class="form-control col-md-2" value="">
        </div>
        <div class="row" style="padding-top: 3px;">
            <label for="ar-immed-quantity" class="col-md-2 col-form-label text-md-left">{{__('Quantity')}}</label>&nbsp;&nbsp;
            <input id="ar-immed-quantity" type="text" name="ar-quantity" class="form-control col-md-2" value="">&nbsp;
            <input id="ar-immed-quantity-unit" type="text" name="ar-immed-quantity-unit" class="form-control col-md-1"
                   value="">
        </div>
        <div class="row" style="padding-top: 3px;">
            <label for="ar-immed-purch-price" class="col-md-2 col-form-label text-md-left">{{__('Purchase price')}}</label>&nbsp;&nbsp;
            <input id="ar-immed-purch-price" type="text" name="ar-immed-purch-price" class="form-control col-md-2" value="">&nbsp;
            <input id="ar-immed-purch-curr" type="text" name="ar-immed-purch-curr" class="form-control col-md-1" value="">
        </div>
        <div class="row" style="padding-top: 3px;">
            <label for="ar-immed-sales-price" class="col-md-2 col-form-label text-md-left">{{__('Sales price')}}</label>&nbsp;&nbsp;
            <input id="ar-immed-sales-price" type="text" name="ar-immed-sales-price" class="form-control col-md-2" value="">&nbsp;
            <input id="ar-immed-sales-curr" type="text" name="ar-immed-sales-curr" class="form-control col-md-1" value="">
        </div>
        <div class="row" style="padding-top: 0.5rem;">
            <button type="button" style="float:left; margin-left: 1rem; margin-right: 1rem; " class="ui-button ui-corner-all ui-widget"
                    onclick="get_infnr(1);return false;">{{__('Inforecord')}}</button>
            <button type="button" style="float:left; margin-right: 1rem; " class="ui-button ui-corner-all ui-widget"
                    onclick="get_zpret(1);return false;">{{__('ZPRET')}}</button>
        </div>
    </div>
    <div id="ar-proposals-approval" width="95%" style="margin-right: 0.5rem;">
        <div id="ar-proposals-table-1" style="overflow-y: scroll; min-height: 17rem;" width="95%">
            <table id="proposals-table-1" class="table-striped" width="100%">
                <colgroup>
                    <col width="8%">
                    <col width="17%">
                    <col width="12%">
                    <col width="24%">
                    <col width="7%">
                    <col width="8%">
                    <col width="3%">
                    <col width="9%">
                    <col width="3%">
                    <col width="9%">
                    <col width="3%">
                </colgroup>
                <tr>
                    <th>{{__('Vendor')}}</th>
                    <th>{{__('Vendor Name')}}</th>
                    <th>{{__('Vendor mat.')}}</th>
                    <th>{{__('Material description')}}</th>
                    <th>{{__('Material')}}</th>
                    <th colspan="2" style="text-align: right;">{{__('Quantity')}}</th>
                    <th colspan="2" style="text-align: right;">{{__('Purchase price')}}</th>
                    <th colspan="2" style="text-align: right;">{{__('Sales price')}}</th>
                </tr>
            </table>
        </div>
        <button type="button" style="float:left; margin-right: 1rem; " class="ui-button ui-corner-all ui-widget"
                onclick="add_edit_proposal(1, 1);return false;">{{__('New proposal')}}</button>
        <button type="button" style="float:left; margin-right: 1rem; " class="ui-button ui-corner-all ui-widget"
                onclick="add_edit_proposal(1, 2);return false;">{{__('Edit proposal')}}</button>
        <button type="button" style="float:left; margin-right: 1rem; " class="ui-button ui-corner-all ui-widget"
                onclick="delete_proposal(1);return false;">{{__('Delete proposal')}}</button>
    </div>
    <i id="new_acc_rej_msg" style="color: red"></i>
    <br>
</div>

<script>

    var arDialog, arForm, _ar_type, _ar_this, _ar_itemdata;
    var proposal_last_selected_line;
    var proposal_last_color;

    function ar_switchProposalMode(_this) {
        if ($(_this).is(":checked")) {
            $("#ar-proposals-immed").attr("style", "display: none; margin-right: 0.5rem;")
            $("#ar-proposals-approval").attr("style", "display: block; margin-right: 0.5rem;")
        } else {
            $("#ar-proposals-immed").attr("style", "display: block; margin-right: 0.5rem;")
            $("#ar-proposals-approval").attr("style", "display: none; margin-right: 0.5rem;")
        }
    }

    $(function () {
        arDialog = $("#accept-reject-dialog").dialog({
            autoOpen: false,
            height: 480,
            width: 920,
            modal: true,
            buttons: {
                '{{__("Send proposal")}}': function () {
                    var result = new Object();
                    result.type = _ar_type;
                    result.itemdata = _ar_itemdata;
                    if ($("#require_sr_approval").is(":checked")) {
                        let tablerows = $('#proposals-table-1 tr');
                        let n = tablerows.length;
                        if (n < 2) return;
                        result.items = [];
                        for (i = 1; i < n; i++) {
                            row = tablerows[i];
                            let item = new Object();
                            item.lifnr = row.cells[0].textContent;
                            item.idnlf = row.cells[2].textContent;
                            item.mtext = row.cells[3].textContent;
                            item.matnr = row.cells[4].textContent;
                            item.quantity = row.cells[5].textContent.split(" ")[0];
                            item.quantity_unit = row.cells[5].textContent.split(" ")[1];
                            item.purch_price = row.cells[6].textContent.split(" ")[0] ;
                            item.purch_curr = row.cells[6].textContent.split(" ")[1];
                            item.sales_price = row.cells[7].textContent.split(" ")[0];
                            item.sales_curr = row.cells[7].textContent.split(" ")[1];
                            result.items.push(item);
                        }
                    } else {
                        result.lifnr = $("#ar-immed-lifnr").val().trim();
                        result.idnlf = $("#ar-immed-idnlf").val().trim();
                        result.mtext = $("#ar-immed-mtext").val().trim();
                        result.matnr = $("#ar-immed-matnr").val().trim();
                        result.quantity = $("#ar-immed-quantity").val().trim();
                        result.quantity_unit = $("#ar-immed-quantity-unit").val().trim();
                        result.purch_price = $("#ar-immed-purch-price").val().trim();
                        result.purch_curr = $("#ar-immed-purch-curr").val().trim();
                        result.sales_price = $("#ar-immed-sales-price").val().trim();
                        result.sales_curr = $("#ar-immed-sales-curr").val().trim();
                    }
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    jQuery.ajaxSetup({async: false});
                    var _dataPP, _statusPP;
                    $.get("webservice/processproposal",
                        {
                            proposal: JSON.stringify(result)
                        },
                        function (data, status) {
                            _dataPP = data;
                            _statusPP = status;
                        }, "json");
                    jQuery.ajaxSetup({async: true});
                },
                Cancel: function () {
                    arDialog.dialog("close");
                }
            },
            close: function () {
                // arForm[0].reset();
            },
            open: function () {
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

    function accept_reject_dialog(type, this0, itemdata, title, initial_text) {
        $("#new_rej_msg").text("");
        $("#initial-text").text(initial_text);
        $("#require_sr_approval").prop("checked", false);
        $("#proposals-table-1").find("tr:gt(0)").remove();
        $("#ar-proposals-immed").attr("style", "display: block; margin-right: 0.5rem;");
        $("#ar-proposals-approval").attr("style", "display: none; margin-right: 0.5rem;");
        $("#accept-reject-dialog").dialog('option', 'title', title);
        _ar_type = type;
        _ar_this = this0;
        _ar_itemdata = itemdata;
        if (type == 1) {
            $("#ar-immed-lifnr").val(conv_exit_alpha_output(itemdata.lifnr));
            $("#ar-immed-idnlf").val(itemdata.idnlf);
            $("#ar-immed-mtext").val(itemdata.mtext);
            $("#ar-immed-matnr").val(itemdata.matnr);
            $("#ar-immed-quantity").val(itemdata.qty);
            $("#ar-immed-quantity-unit").val(itemdata.qty_uom);
            $("#ar-immed-purch-price").val(itemdata.purch_price);
            $("#ar-immed-purch-curr").val(itemdata.purch_curr);
            $("#ar-immed-sales-price").val(itemdata.sales_price);
            $("#ar-immed-sales-curr").val(itemdata.sales_curr);

            var newRow = $("<tr style='height: 1.2rem;' onclick='proposal_selected(this);return false;'>");
            var cols = "<td>" + conv_exit_alpha_output(itemdata.lifnr) + "</td>" +
                "<td>" + itemdata.lifnr_name + "</td>" +
                "<td>" + itemdata.idnlf + "</td>" +
                "<td>" + itemdata.mtext + "</td>" +
                "<td>" + itemdata.matnr + "</td>" +
                "<td colspan='2' style='text-align: right;'>" + itemdata.qty + " " + itemdata.qty_uom + "</td>" +
                "<td colspan='2' style='text-align: right;'>" + itemdata.purch_price + " " + itemdata.purch_curr + "</td>" +
                "<td colspan='2' style='text-align: right;'>" + itemdata.sales_price + " " + itemdata.sales_curr + "</td>";
            newRow.append(cols);
            $("#proposals-table-1").append(newRow);
        }
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
        <label for="aep-lifnr" class="col-md-3 col-form-label text-md-left">{{__('Vendor')}}</label>&nbsp;&nbsp;
        <input id="aep-lifnr" type="text" name="aep-lifnr" class="form-control col-md-3" value="">
    </div>
    <div class="row" style="padding-top: 3px;">
        <label for="aep-idnlf" class="col-md-3 col-form-label text-md-left">{{__('Vendor mat.')}}</label>&nbsp;&nbsp;
        <input id="aep-idnlf" type="text" name="aep-idnlf" class="form-control col-md-3" value="">
    </div>
    <div class="row" style="padding-top: 3px;">
        <label for="aep-mtext" class="col-md-3 col-form-label text-md-left">{{__('Material description')}}</label>&nbsp;&nbsp;
        <input id="aep-mtext" type="text" name="aep-mtext" class="form-control col-md-6" value="">
    </div>
    <div class="row" style="padding-top: 3px;">
        <label for="aep-matnr" class="col-md-3 col-form-label text-md-left">{{__('Material')}}</label>&nbsp;&nbsp;
        <input id="aep-matnr" type="text" name="aep-matnr" class="form-control col-md-3" value="">
    </div>
    <div class="row" style="padding-top: 3px;">
        <label for="aep-quantity" class="col-md-3 col-form-label text-md-left">{{__('Quantity')}}</label>&nbsp;&nbsp;
        <input id="aep-quantity" type="text" name="aep-quantity" class="form-control col-md-4" value="">&nbsp;
        <input id="aep-quantity-unit" type="text" name="aep-quantity-unit" class="form-control col-md-1"
               value="">
    </div>
    <div class="row" style="padding-top: 3px;">
        <label for="aep-purch-price" class="col-md-3 col-form-label text-md-left">{{__('Purchase price')}}</label>&nbsp;&nbsp;
        <input id="aep-purch-price" type="text" name="aep-purch-price" class="form-control col-md-4" value="">&nbsp;
        <input id="aep-purch-curr" type="text" name="aep-purch-curr" class="form-control col-md-1" value="">
    </div>
    <div class="row" style="padding-top: 3px;">
        <label for="aep-sales-price" class="col-md-3 col-form-label text-md-left">{{__('Sales price')}}</label>&nbsp;&nbsp;
        <input id="aep-sales-price" type="text" name="aep-sales-price" class="form-control col-md-4" value="">&nbsp;
        <input id="aep-sales-curr" type="text" name="aep-sales-curr" class="form-control col-md-1" value="">
    </div>
    <div class="row" style="padding-top: 0.5rem;">
        <button type="button" style="float:left; margin-left: 1rem; margin-right: 1rem; " class="ui-button ui-corner-all ui-widget"
                onclick="get_infnr(2);return false;">{{__('Inforecord')}}</button>
        <button type="button" style="float:left; margin-right: 1rem; " class="ui-button ui-corner-all ui-widget"
                onclick="get_zpret(2);return false;">{{__('ZPRET')}}</button>
    </div>
</div>

<script>
    var add_edit_Dialog, add_edit_Form, add_edit_current_row, add_edit_caller;

    $(function () {
        add_edit_Dialog = $("#add-edit-proposal").dialog({
            autoOpen: false,
            height: 400,
            width: 640,
            modal: true,
            buttons: {
                Save: function () {
                    let lifnr = $("#aep-lifnr").val();
                    let idnlf = $("#aep-idnlf").val();
                    let mtext = $("#aep-mtext").val();
                    let matnr = $("#aep-matnr").val();
                    let quantity = $("#aep-quantity").val();
                    let quantity_uom = $("#aep-quantity-unit").val();
                    let purch_price = $("#aep-purch-price").val();
                    let purch_curr = $("#aep-purch-curr").val();
                    let sales_price = $("#aep-sales-price").val();
                    let sales_curr = $("#aep-sales-curr").val();
                    if (add_edit_caller == 1) {
                        if (add_edit_current_row != null) {
                            add_edit_current_row.cells[0].innerHTML = lifnr;
                            add_edit_current_row.cells[1].innerHTML = readLifnrName(lifnr);
                            add_edit_current_row.cells[2].innerHTML = idnlf;
                            add_edit_current_row.cells[3].innerHTML = mtext;
                            add_edit_current_row.cells[4].innerHTML = matnr;
                            add_edit_current_row.cells[5].innerHTML = quantity + " " + quantity_uom;
                            add_edit_current_row.cells[6].innerHTML = purch_price + " " + purch_curr;
                            add_edit_current_row.cells[7].innerHTML = sales_price + " " + sales_curr;
                            add_edit_Dialog.dialog("close");
                        } else {
                            var newRow = $("<tr style='height: 1.2rem;' onclick='proposal_selected(this);return false;'>");
                            var cols = "<td>" + lifnr + "</td>" +
                                "<td>" + readLifnrName(lifnr) + "</td>" +
                                "<td>" + idnlf + "</td>" +
                                "<td>" + mtext + "</td>" +
                                "<td>" + matnr + "</td>" +
                                "<td colspan='2' style='text-align: right;'>" + quantity + " " + quantity_uom + "</td>" +
                                "<td colspan='2' style='text-align: right;'>" + purch_price + " " + purch_curr + "</td>" +
                                "<td colspan='2' style='text-align: right;'>" + sales_price + " " + sales_curr + "</td>";
                            newRow.append(cols);
                            $("#proposals-table-1").append(newRow);
                            add_edit_Dialog.dialog("close");
                        }
                    }
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

    function add_edit_proposal(caller, mode) {

        add_edit_caller = caller;
        let title = "";
        let lifnr = "";
        let idnlf = "";
        let mtext = "";
        let matnr = "";
        let quantity = "";
        let quantity_unit = "";
        let purch_price = "";
        let purch_curr = "";
        let sales_price = "";
        let sales_curr = "";

        if (caller == 1) {
            if (mode == 1) {
                add_edit_current_row = null;
                title = "Add new proposal";
                idnlf = $("#ar-immed-idnlf").val();
                mtext = $("#ar-immed-mtext").val();
                quantity  = $("#ar-immed-quantity").val();
                quantity_unit = $("#ar-immed-quantity-unit").val();
            } else {
                title = "Edit existing proposal";
                let current_row = proposal_last_selected_line;
                add_edit_current_row = proposal_last_selected_line;
                lifnr = current_row.cells[0].innerHTML;
                idnlf = current_row.cells[2].innerHTML;
                mtext = current_row.cells[3].innerHTML;
                matnr = current_row.cells[4].innerHTML;
                quantity = current_row.cells[5].innerHTML.split(" ")[0];
                quantity_unit = current_row.cells[5].innerHTML.split(" ")[1];
                purch_price = current_row.cells[6].innerHTML.split(" ")[0];
                purch_curr = current_row.cells[6].innerHTML.split(" ")[1];
                sales_price = current_row.cells[7].innerHTML.split(" ")[0];
                sales_curr = current_row.cells[7].innerHTML.split(" ")[1];
            }
        }

        $("#aep-lifnr").val(lifnr);
        $("#aep-idnlf").val(idnlf);
        $("#aep-mtext").val(mtext);
        $("#aep-matnr").val(matnr);
        $("#aep-quantity").val(quantity);
        $("#aep-quantity-unit").val(quantity_unit);
        $("#aep-purch-price").val(purch_price);
        $("#aep-purch-curr").val(purch_curr);
        $("#aep-sales-price").val(sales_price);
        $("#aep-sales-curr").val(sales_curr);

        $("#add-edit-proposal").dialog('option', 'title', title);
        $('#add-edit-proposal').css('overflow', 'hidden');
        add_edit_Dialog.dialog("open");
    }

</script>

<div id="select-proposal-dialog" title="Select a proposal">
    <form>
        <br>
        <div class="form-group container" align="left">
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
    </form>
</div>

<script>

    var select_proposal_dialog, select_proposal_form;
    $(function () {
        select_proposal_dialog = $("#select-proposal-dialog").dialog({
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
        $.post("webservice/readproposals",
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

    function proposal_selected(_this) {
        if (proposal_last_selected_line != null)
            $(proposal_last_selected_line).css("background-color", proposal_last_color);

        proposal_last_color = $(_this).css("background-color");
        proposal_last_selected_line = _this;

        $(_this).css("background-color", "#77FF77");
    }

    function get_infnr(caller)
    {
        let lifnr = null;
        let idnlf = null;
        if (caller == 1) {
            lifnr = $("#ar-immed-lifnr").val();
            idnlf = $("#ar-immed-idnlf").val();
        }
        if (caller == 2) {
            lifnr = $("#aep-lifnr").val();
            idnlf = $("#aep-idnlf").val();
        }
        read_inforecords(caller, lifnr, idnlf);
    }

    function get_zpret(mode)
    {

    }

    function delete_proposal(mode)
    {
        if (proposal_last_selected_line != null)
            $(proposal_last_selected_line).remove();
    }


</script>