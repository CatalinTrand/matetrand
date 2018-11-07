<div id="accept-reject-dialog" title="Accept/reject">
    <p id="initial-text" type="text" style="margin-top: 0.5rem; font-weight: bold; color: darkred;" name="initial-text" width="95%" value=""></p>
    <input type="checkbox" id="require_sr_approval" onchange="ar_switchProposalMode(this); return false;">
    <label id="label_require_sr_approval" for="require_sr_approval">{{__("Require SR approval")}}</label>
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
            <label for="ar-immed-lfdat" class="col-md-2 col-form-label text-md-left">{{__('Delivery date')}}</label>&nbsp;&nbsp;
            <input id="ar-immed-lfdat" type="text" name="ar-immed-lfdat" class="form-control col-md-2" value="">
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
            <label id="label-ar-immed-sales-price" for="ar-immed-sales-price" class="col-md-2 col-form-label text-md-left">{{__('Sales price')}}</label>&nbsp;&nbsp;
            <input id="ar-immed-sales-price" type="text" name="ar-immed-sales-price" class="form-control col-md-2" value="">&nbsp;
            <input id="ar-immed-sales-curr" type="text" name="ar-immed-sales-curr" class="form-control col-md-1" value="">
        </div>
        <div class="row" style="padding-top: 0.5rem;">
            <button type="button" style="float:left; margin-left: 1rem; margin-right: 1rem; " class="ui-button ui-corner-all ui-widget"
                    onclick="get_infnr(1);return false;">{{__('Inforecord')}}</button>
            <button type="button" style="float:left; margin-right: 1rem; " class="ui-button ui-corner-all ui-widget" id="accept-reject-zpret"
                    onclick="get_zpret(1);return false;">ZPRET</button>
        </div>
    </div>
    <div id="ar-proposals-approval" width="95%" style="margin-right: 0.5rem;">
        <div id="ar-proposals-table-1" style="overflow-y: scroll; min-height: 17rem;" width="95%">
            <table id="proposals-table-1" class="table-striped" width="100%">
                <colgroup>
                    <col width="8%">
                    <col width="17%">
                    <col width="10%">
                    <col width="20%">
                    <col width="7%">
                    <col width="7%">
                    <col width="6%">
                    <col width="2%">
                    <col width="8.5%">
                    <col width="2.5%">
                    <col width="8.5%">
                    <col width="2.5%">
                </colgroup>
                <tr>
                    <th>{{__('Vendor')}}</th>
                    <th>{{__('Vendor Name')}}</th>
                    <th>{{__('Vendor mat.')}}</th>
                    <th>{{__('Material description')}}</th>
                    <th>{{__('Material')}}</th>
                    <th>{{__('Dlv. date')}}</th>
                    <th colspan="2" style="text-align: right;">{{__('Quantity')}}</th>
                    <th colspan="2" style="text-align: right;">{{__('Purch. price')}}</th>
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
            $("#accept-reject-ok-button").text("{{__('Send proposal')}}");
        } else {
            $("#ar-proposals-immed").attr("style", "display: block; margin-right: 0.5rem;")
            $("#ar-proposals-approval").attr("style", "display: none; margin-right: 0.5rem;")
            $("#accept-reject-ok-button").text("{{__('Modificare comanda')}}");
        }
    }

    $(function () {
        arDialog = $("#accept-reject-dialog").dialog({
            autoOpen: false,
            height: 500,
            width: 920,
            modal: true,
            buttons: [
                {
                    text: '{{__("Send proposal")}}',
                    id: "accept-reject-ok-button",
                    click: function () {
                        var result = new Object();
                        result.type = 'O';
                        result.itemdata = _ar_itemdata;
                        if ($("#require_sr_approval").is(":checked")) {
                            let tablerows = $('#proposals-table-1 tr');
                            let n = tablerows.length;
                            if (n < 2) return;
                            result.items = [];
                            for (i = 1; i < n; i++) {
                                row = tablerows[i];
                                let item = new Object();
                                item.lifnr = row.cells[0].textContent.trim();
                                item.idnlf = row.cells[2].textContent.trim();
                                item.mtext = row.cells[3].textContent.trim();
                                item.matnr = row.cells[4].textContent.trim();
                                item.lfdat = row.cells[5].textContent.trim();
                                item.quantity = row.cells[6].textContent.split(" ")[0];
                                item.quantity_unit = row.cells[6].textContent.split(" ")[1];
                                item.purch_price = row.cells[7].textContent.split(" ")[0] ;
                                item.purch_curr = row.cells[7].textContent.split(" ")[1];
                                item.sales_price = row.cells[8].textContent.split(" ")[0];
                                item.sales_curr = row.cells[8].textContent.split(" ")[1];
                                result.items.push(item);
                            }
                        } else {
                            let item = new Object();
                            result.lifnr = $("#ar-immed-lifnr").val().trim();
                            result.idnlf = $("#ar-immed-idnlf").val().trim();
                            result.mtext = $("#ar-immed-mtext").val().trim();
                            result.matnr = $("#ar-immed-matnr").val().trim();
                            result.lfdat = $("#ar-immed-lfdat").val().trim();
                            result.quantity = $("#ar-immed-quantity").val().trim();
                            result.quantity_unit = $("#ar-immed-quantity-unit").val().trim();
                            result.purch_price = $("#ar-immed-purch-price").val().trim();
                            result.purch_curr = $("#ar-immed-purch-curr").val().trim();
                            result.sales_price = $("#ar-immed-sales-price").val().trim();
                            result.sales_curr = $("#ar-immed-sales-curr").val().trim();
                            if (result.lifnr.length == 0 ||
                                result.idnlf.length == 0 ||
                                result.mtext.length == 0 ||
                                result.matnr.length == 0 ||
                                result.lfdat.length == 0 ||
                                result.quantity.length == 0 ||
                                result.quantity_unit.length == 0 ||
                                result.purch_price.length == 0 ||
                                result.purch_curr.length == 0 ||
                                ((_ar_itemdata.vbeln != "!REPLENISH") &&
                                    (result.sales_price.length == 0 ||
                                     result.sales_curr.length == 0)
                                )
                            ) return;
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
                            });
                        jQuery.ajaxSetup({async: true});
                        if (_dataPP != null && _dataPP != undefined && _dataPP.trim().length != 0) {
                            alert(_dataPP);
                            return;
                        }
                        arDialog.dialog("close");
                        location.reload();
                    }
                },
                {
                    text: '{{__("Cancel")}}',
                    click: function () {
                        arDialog.dialog("close");
                    }
                }
            ],
            close: function () {
                // arForm[0].reset();
            },
            open: function () {
                if (_ar_itemdata.vbeln == "!REPLENISH") {
                    $("#require_sr_approval").hide();
                    $("#label_require_sr_approval").hide();
                    $("#label-ar-immed-sales-price").hide();
                    $("#ar-immed-sales-price").hide();
                    $("#ar-immed-sales-curr").hide();
                    $("#accept-reject-zpret").hide();
                } else {
                    $("#require_sr_approval").show();
                    $("#label_require_sr_approval").show();
                    $("#label-ar-immed-sales-price").show();
                    $("#ar-immed-sales-price").show();
                    $("#ar-immed-sales-curr").show();
                    $("#accept-reject-zpret").show();
                }
                $("#accept-reject-ok-button").text("{{__('Modificare comanda')}}");
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
        $("#ar-immed-lfdat").datepicker({dateFormat: "yy-mm-dd"});
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
            $("#ar-immed-lfdat").val(itemdata.lfdat.substring(0, 10));
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
                "<td>" + itemdata.lfdat.substring(0, 10) + "</td>" +
                "<td colspan='2' style='text-align: right;'>" + itemdata.qty + " " + itemdata.qty_uom + "</td>" +
                "<td colspan='2' style='text-align: right;'>" + itemdata.purch_price + " " + itemdata.purch_curr + "</td>" +
                "<td colspan='2' style='text-align: right;'>" + itemdata.sales_price + " " + itemdata.sales_curr + "</td>";
            newRow.append(cols);
            $("#proposals-table-1").append(newRow);
        } else {
            $("#ar-immed-lifnr").val("");
            $("#ar-immed-idnlf").val(itemdata.idnlf);
            $("#ar-immed-mtext").val(itemdata.mtext);
            $("#ar-immed-matnr").val(itemdata.matnr);
            $("#ar-immed-lfdat").val(itemdata.lfdat.substring(0, 10));
            $("#ar-immed-quantity").val(itemdata.qty);
            $("#ar-immed-quantity-unit").val(itemdata.qty_uom);
            $("#ar-immed-purch-price").val("");
            $("#ar-immed-purch-curr").val("");
            $("#ar-immed-sales-price").val("");
            $("#ar-immed-sales-curr").val("");
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
        <label for="aep-lfdat" class="col-md-3 col-form-label text-md-left">{{__('Delivery date')}}</label>&nbsp;&nbsp;
        <input id="aep-lfdat" type="text" name="aep-lfdat" class="form-control col-md-3" value="">
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
                onclick="get_zpret(2);return false;">ZPRET</button>
    </div>
</div>

<script>
    var add_edit_Dialog, add_edit_Form, add_edit_current_row, add_edit_caller;

    $(function () {
        add_edit_Dialog = $("#add-edit-proposal").dialog({
            autoOpen: false,
            height: 420,
            width: 640,
            modal: true,
            buttons: {
                Save: function () {
                    let lifnr = $("#aep-lifnr").val().trim();
                    let idnlf = $("#aep-idnlf").val().trim();
                    let mtext = $("#aep-mtext").val().trim();
                    let matnr = $("#aep-matnr").val().trim();
                    let lfdat = $("#aep-lfdat").val().trim();
                    let quantity = $("#aep-quantity").val().trim();
                    let quantity_uom = $("#aep-quantity-unit").val().trim();
                    let purch_price = $("#aep-purch-price").val().trim();
                    let purch_curr = $("#aep-purch-curr").val().trim();
                    let sales_price = $("#aep-sales-price").val().trim();
                    let sales_curr = $("#aep-sales-curr").val().trim();
                    if (lifnr.length == 0 ||
                        idnlf.length == 0 ||
                        mtext.length == 0 ||
                        matnr.length == 0 ||
                        lfdat.length == 0 ||
                        quantity.length == 0 ||
                        quantity_uom.length == 0 ||
                        purch_price.length == 0 ||
                        purch_curr.length == 0 ||
                        ((_ar_itemdata.vbeln != "!REPLENISH") &&
                            (sales_price.length == 0 ||
                             sales_curr.length == 0)
                        )
                    ) return;
                    if (add_edit_caller == 1) {
                        if (add_edit_current_row != null) {
                            add_edit_current_row.cells[0].innerHTML = lifnr;
                            add_edit_current_row.cells[1].innerHTML = readLifnrName(lifnr);
                            add_edit_current_row.cells[2].innerHTML = idnlf;
                            add_edit_current_row.cells[3].innerHTML = mtext;
                            add_edit_current_row.cells[4].innerHTML = matnr;
                            add_edit_current_row.cells[5].innerHTML = lfdat;
                            add_edit_current_row.cells[6].innerHTML = quantity + " " + quantity_uom;
                            add_edit_current_row.cells[7].innerHTML = purch_price + " " + purch_curr;
                            add_edit_current_row.cells[8].innerHTML = sales_price + " " + sales_curr;
                            add_edit_Dialog.dialog("close");
                        } else {
                            var newRow = $("<tr style='height: 1.2rem;' onclick='proposal_selected(this);return false;'>");
                            var cols = "<td>" + lifnr + "</td>" +
                                "<td>" + readLifnrName(lifnr) + "</td>" +
                                "<td>" + idnlf + "</td>" +
                                "<td>" + mtext + "</td>" +
                                "<td>" + matnr + "</td>" +
                                "<td>" + lfdat + "</td>" +
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
        $("#aep-lfdat").datepicker({dateFormat: "yy-mm-dd"});
    });

    function add_edit_proposal(caller, mode) {

        add_edit_caller = caller;
        let title = "";
        let lifnr = "";
        let idnlf = "";
        let mtext = "";
        let matnr = "";
        let lfdat = "";
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
                lfdat = current_row.cells[5].innerHTML;
                quantity = current_row.cells[6].innerHTML.split(" ")[0];
                quantity_unit = current_row.cells[6].innerHTML.split(" ")[1];
                purch_price = current_row.cells[7].innerHTML.split(" ")[0];
                purch_curr = current_row.cells[7].innerHTML.split(" ")[1];
                sales_price = current_row.cells[8].innerHTML.split(" ")[0];
                sales_curr = current_row.cells[8].innerHTML.split(" ")[1];
            }
        }

        $("#aep-lifnr").val(lifnr);
        $("#aep-idnlf").val(idnlf);
        $("#aep-mtext").val(mtext);
        $("#aep-matnr").val(matnr);
        $("#aep-lfdat").val(lfdat);
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
    <p id="sel-proposal-initial-text" type="text" style="margin-top: 0.5rem; font-weight: bold; color: darkred;" name="initial-text" width="95%" value=""></p>
    <div id="sel-proposal-container" width="95%" style="margin-right: 0.5rem; overflow-y: scroll; min-height: 17rem;">
        <table id="sel-proposal-table" class="table-striped" width="100%">
            <colgroup>
                <col width="8%">
                <col width="17%">
                <col width="10%">
                <col width="20%">
                <col width="7%">
                <col width="7%">
                <col width="6%">
                <col width="2%">
                <col width="8.5%">
                <col width="2.5%">
                <col width="8.5%">
                <col width="2.5%">
            </colgroup>
            <tr>
                <th>{{__('Vendor')}}</th>
                <th>{{__('Vendor Name')}}</th>
                <th>{{__('Vendor mat.')}}</th>
                <th>{{__('Material description')}}</th>
                <th>{{__('Material')}}</th>
                <th>{{__('Dlv. date')}}</th>
                <th colspan="2" style="text-align: right;">{{__('Quantity')}}</th>
                <th colspan="2" style="text-align: right;">{{__('Purch. price')}}</th>
                <th colspan="2" style="text-align: right;">{{__('Sales price')}}</th>
            </tr>
        </table>
    </div>
    <i id="sel-prop-msg" style="color: red"></i>
    <br>
</div>

<script>

    var select_proposal_dialog;
    var _sp_type, _sp_this, _sp_itemdata;
    var _sp_ebeln, _sp_ebelp, _sp_cdate;
    $(function () {
        select_proposal_dialog = $("#select-proposal-dialog").dialog({
            autoOpen: false,
            height: 400,
            width: 960,
            modal: true,
            buttons: {
                Select: function () {
                    if (proposal_last_selected_line != null) {
                        let current_row = proposal_last_selected_line;
                        let result_set = current_row.id;
                        let _sp_pos = result_set.split('_')[4];
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        jQuery.ajaxSetup({async: false});
                        var _data, _status;
                        $.get("webservice/acceptproposal",
                            {
                                ebeln: _sp_ebeln,
                                ebelp: _sp_ebelp,
                                cdate: _sp_cdate,
                                pos: _sp_pos
                            },
                            function (data, status) {
                                _data = data;
                                _status = status;
                            }, "json");
                        jQuery.ajaxSetup({async: true});
                        if (_data != null && _data != undefined && _data.trim().length != 0) {
                            alert(_data);
                            return;
                        }
                        select_proposal_dialog.dialog("close");
                        location.reload();
                    }
                },
                Reject: function () {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    jQuery.ajaxSetup({async: false});
                    var _data, _status;
                    $.get("webservice/rejectproposal",
                        {
                            ebeln: _sp_ebeln,
                            ebelp: _sp_ebelp,
                            cdate: _sp_cdate
                        },
                        function (data, status) {
                            _data = data;
                            _status = status;
                        }, "json");
                    jQuery.ajaxSetup({async: true});
                    if (_data != null && _data != undefined && _data.trim().length != 0) {
                        alert(_data);
                        return;
                    }
                    select_proposal_dialog.dialog("close");
                    location.reload();
                },
                Cancel: function () {
                    select_proposal_dialog.dialog("close");
                }
            },
            close: function () {
                // select_proposal_form[0].reset();
            },
            open: function () {
                $('#select-proposal-dialog').css('overflow', 'hidden');
                proposal_last_selected_line = null;
            },
            position: {
                my: "center",
                at: "center",
                of: window
            }
        });
    });

    function select_proposal(type, this0, itemdata, title, initial_text) {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajaxSetup({async: false});
        var _data, _status;
        $.post("webservice/readproposals",
            {
                type: "O",
                ebeln: itemdata.ebeln,
                ebelp: itemdata.ebelp
            },
            function (data, status) {
                _data = data;
                _status = status;
            }, "json");
        jQuery.ajaxSetup({async: true});
        if (_status != "success") return;
        if (_data.length == 0) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajaxSetup({async: false});
            var _data, _status;
            $.post("webservice/readproposals",
                {
                    type: "S",
                    ebeln: itemdata.ebeln,
                    ebelp: itemdata.ebelp
                },
                function (data, status) {
                    _data = data;
                    _status = status;
                }, "json");
            jQuery.ajaxSetup({async: true});
            if (_data.length != 0) select_split(type, this0, itemdata, title, initial_text, _data);
            return;
        }

        $("#sel-prop-msg").text("");
        $("#sel-proposal-initial-text").text(initial_text);
        $("#sel-proposal-table").find("tr:gt(0)").remove();
        $("#select-proposal-dialog").dialog('option', 'title', title);
        _sp_type = type;
        _sp_this = this0;
        _sp_itemdata = itemdata;

        if (_data.length > 0) {
            let table = $("#sel-proposal-table");
            for (let i = 0; i < _data.length; i++) {
                if (i == 0) {
                    _sp_ebeln = _data[i].ebeln;
                    _sp_ebelp = _data[i].ebelp;
                    _sp_cdate = _data[i].cdate;
                }
                var newRow = $("<tr style='height: 1.2rem;' id='PROP_" + _data[i].ebeln + '_' + _data[i].ebelp + '_' +_data[i].cdate.substring(0, 10) + _data[i].cdate.substring(11, 8) + '_' +_data[i].pos + "' onclick='proposal_selected(this);return false;'>");
                var cols = "<td>" + conv_exit_alpha_output(_data[i].lifnr) + "</td>" +
                    "<td>" + _data[i].lifnr_name + "</td>" +
                    "<td>" + _data[i].idnlf + "</td>" +
                    "<td>" + _data[i].mtext + "</td>" +
                    "<td>" + _data[i].matnr + "</td>" +
                    "<td>" + _data[i].lfdat.substring(0, 10) + "</td>" +
                    "<td colspan='2' style='text-align: right;'>" + _data[i].qty + " " + _data[i].qty_uom + "</td>" +
                    "<td colspan='2' style='text-align: right;'>" + _data[i].purch_price + " " + _data[i].purch_curr + "</td>" +
                    "<td colspan='2' style='text-align: right;'>" + _data[i].sales_price + " " + _data[i].sales_curr + "</td>";
                newRow.append(cols);
                table.append(newRow);
            }
        }
        select_proposal_dialog.dialog("open");
    }

    function proposal_selected(_this) {
        if (proposal_last_selected_line != null)
            $(proposal_last_selected_line).css("background-color", proposal_last_color);
        proposal_last_color = $(_this).css("background-color");
        proposal_last_selected_line = _this;
        $(_this).css("background-color", "#AACCAA");
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

    function get_zpret(caller)
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
        read_zpretrecords(caller, lifnr, idnlf);
    }

    function delete_proposal(mode)
    {
        if (proposal_last_selected_line != null)
            $(proposal_last_selected_line).remove();
    }


</script>