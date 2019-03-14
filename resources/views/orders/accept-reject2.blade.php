<div id="accept-reject-dialog2" title="Accept/reject" style="display: none;">
    <p id="initial-text2" type="text" style="margin-top: 0.5rem; font-weight: bold; color: darkred;" name="initial-text2" width="95%" value=""></p>
    <input type="checkbox" id="require_sr_approval2" onchange="ar_switchProposalMode2(this); return false;">
    <label id="label_require_sr_approval2" for="require_sr_approval2">{{__("Require SR approval")}}</label>
    <br><br>
    <div id="ar-proposals-immed2" width="95%" style="margin-right: 0.5rem;">
        <table style="width: 100%; border-style: none">
            <tr>
                <td style="width: 50%;">
                    <div class="row">
                        <label for="ar-immed-lifnr2" style="margin-left: 2em; width: 10em;" class="col-form-label text-md-left">{{__('Vendor')}}</label>&nbsp;&nbsp;
                        <input id="ar-immed-lifnr2" type="text" name="ar-immediate-lifnr2" style="width: 15em;" class="form-control" value="">
                    </div>
                </td>
                <td style="width: 50%;">
                    <div class="row" style="padding-top: 3px;">
                        <label for="ar-immed-old-purch-price2" style="margin-left: 1em; width: 10em;" class="col-form-label text-md-left">{{__('Initial purchase price')}}</label>&nbsp;&nbsp;
                        <input id="ar-immed-old-purch-price2" style="width: 10em;" type="text" name="ar-immed-old-purch-price2" class="form-control" value="" disabled>&nbsp;
                        <input id="ar-immed-old-purch-curr2" style="width: 5em;" type="text" name="ar-immed-old-purch-curr2" class="form-control" value="" disabled>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="width: 50%;">
                    <div class="row" style="padding-top: 3px;">
                        <label for="ar-immed-idnlf2"  style="margin-left: 2em; width: 10em;" class="col-form-label text-md-left">{{__('Vendor mat.')}}</label>&nbsp;&nbsp;
                        <input id="ar-immed-idnlf2" style="width: 15em;" type="text" name="ar-immed-idnlf2" class="form-control" value="">
                    </div>
                </td>
                <td style="width: 50%;">
                    <div class="row" style="padding-top: 3px;">
                        <label for="ar-immed-old-sales-margin-amt2" style="margin-left: 1em; width: 10em;" class="col-form-label text-md-left">{{__('Initial sales margin')}}</label>&nbsp;&nbsp;
                        <input id="ar-immed-old-sales-margin-amt2"  style="width: 10em;" type="text" name="ar-immed-old-sales-margin-amt2" class="form-control" value="" disabled>&nbsp;
                        <input id="ar-immed-old-sales-margin-amt-curr2" style="width: 5em;" type="text" name="ar-immed-old-sales-margin-amt-curr2" class="form-control" value="" disabled>
                        <label for="ar-immed-old-sales-margin-perc2" style="margin-left: 0.4em;" class="col-form-label text-md-left">=</label>&nbsp;&nbsp;
                        <input id="ar-immed-old-sales-margin-perc2" style="width: 5em;" type="text" name="ar-immed-old-sales-margin-perc2" class="form-control" value="" disabled>
                        <label for="ar-immed-old-perc2-margin" style="margin-left: 0.2em;" class="col-form-label text-md-left">%</label>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="width: 50%;">
                    <div class="row" style="padding-top: 3px;">
                        <label for="ar-immed-mtext2"  style="margin-left: 2em; width: 10em;" class="col-form-label text-md-left">{{__('Material description')}}</label>&nbsp;&nbsp;
                        <input id="ar-immed-mtext2" style="width: 22em;" type="text" name="ar-immed-mtext2" class="form-control" value="">
                    </div>
                </td>
                <td style="width: 50%;">
                    <div class="row" style="padding-top: 3px;">
                        <label for="ar-immed-old-sales-price2"  style="margin-left: 1em; width: 10em;" class="col-form-label text-md-left">{{__('Initial sales price')}}</label>&nbsp;&nbsp;
                        <input id="ar-immed-old-sales-price2"  style="width: 10em;" type="text" name="ar-immed-old-sales-price2" class="form-control" value="" disabled>&nbsp;
                        <input id="ar-immed-old-sales-curr2" style="width: 5em;" type="text" name="ar-immed-old-sales-curr2" class="form-control" value="" disabled>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="width: 50%;">
                    <div class="row" style="padding-top: 3px;">
                        <label for="ar-immed-matnr2"  style="margin-left: 2em; width: 10em;" class="col-form-label text-md-left">{{__('Material')}}</label>&nbsp;&nbsp;
                        <input id="ar-immed-matnr2" style="width: 10em;" type="text" name="ar-immed-matnr2" class="form-control" value="">
                    </div>
                </td>
                <td style="width: 50%;">
                    <div class="row" style="padding-top: 3px;">
                        <label for="ar-immed-purch-price2"  style="margin-left: 1em; width: 10em;" class="col-form-label text-md-left">{{__('New purchase price')}}</label>&nbsp;&nbsp;
                        <input id="ar-immed-purch-price2" style="width: 10em;" type="text" name="ar-immed-purch-price2" class="form-control" value=""
                               onfocus="this.oldvalue = this.value;" oninput="ar_immed_purch_price2_check(event, this);">&nbsp;
                        <input id="ar-immed-purch-curr2" style="width: 5em;" type="text" name="ar-immed-purch-curr2" class="form-control" value="">
                    </div>
                </td>
            </tr>
            <tr>
                <td style="width: 50%;">
                    <div class="row" style="padding-top: 3px;">
                        <label for="ar-immed-lfdat2" style="margin-left: 2em; width: 10em;" class="col-form-label text-md-left">{{__('Delivery date')}}</label>&nbsp;&nbsp;
                        <input id="ar-immed-lfdat2" style="width: 10em;" type="text" name="ar-immed-lfdat2" class="form-control" value="">
                    </div>
                </td>
                <td style="width: 50%;">
                    <div class="row">
                        <label for="ar-immed-defmargin-amt2"  style="margin-left: 1em; width: 10em;" class="col-form-label text-md-left">{{__('Default margin')}}</label>&nbsp;&nbsp;
                        <input id="ar-immed-defmargin-amt2"  style="width: 10em;" type="text" name="ar-immed-defmargin-amt2" class="form-control" value="" disabled>&nbsp;
                        <input id="ar-immed-defmargin-curr2" style="width: 5em;" type="text" name="ar-immed-defmargin-curr2" class="form-control" value="" disabled>
                        <label for="ar-immed-defmargin-perc2" style="margin-left: 0.4em;" class="col-form-label text-md-left">=</label>&nbsp;&nbsp;
                        <input id="ar-immed-defmargin-perc2" style="width: 5em;" type="text" name="ar-immed-defmargin-perc2" class="form-control" value="" disabled>&nbsp;
                        <label for="ar-immed-perc-defmargin2"  style="margin-left: 0.2em;" class="col-form-label text-md-left">%</label>&nbsp;&nbsp;
                    </div>
                </td>
            </tr>
            <tr>
                <td style="width: 50%;">
                    <div class="row" style="padding-top: 3px;">
                        <label for="ar-immed-quantity2"  style="margin-left: 2em; width: 10em;" class="col-form-label text-md-left">{{__('Quantity')}}</label>&nbsp;&nbsp;
                        <input id="ar-immed-quantity2" style="width: 10em;" type="text" name="ar-immed-quantity2" class="form-control" value="">&nbsp;
                        <input id="ar-immed-quantity-unit2" style="width: 5em;" type="text" name="ar-immed-quantity-unit2" class="form-control" value="">
                    </div>
                </td>
                <td style="width: 50%;">
                    <div class="row" style="padding-top: 3px;">
                        <label for="ar-immed-sales-price2"  style="margin-left: 1em; width: 10em;" class="col-form-label text-md-left">{{__('New sales price (1)')}}</label>&nbsp;&nbsp;
                        <input id="ar-immed-sales-price2" style="width: 10em;" type="text" name="ar-immed-sales-price2" class="form-control" value=""
                               onfocus="this.oldvalue = this.value;" oninput="ar_immed_sales_price2_check(event, this);">&nbsp;
                        <input id="ar-immed-sales-curr2" style="width: 5em;" type="text" name="ar-immed-sales-curr2" class="form-control" value="" oninput="ar_immed_sales_curr2_check();">
                        <input type="checkbox" id="ar-immed-choose-sales-price2" style="margin-left: 0.5em; border: none;" onchange="ar_immed_choose_sales_price2_checkbox(this);">
                        <image id="ar-immed-icon-save-sales-price2" style="width: 32px; margin-left: 1em;" src='/images/icons8-save-40.png'/>
                        <input type="checkbox" id="ar-immed-save-sales-price2" onchange="ar_immed_choose_save_sales_price2_checkbox(this);">
                    </div>
                </td>
            </tr>
            <tr>
                <td style="width: 50%;"></td>
                <td style="width: 50%;">
                    <div class="row" style="padding-top: 3px;">
                        <label for="ar-immed-new-sales-margin-amt2"  style="margin-left: 1em; width: 10em;" class="col-form-label text-md-left">{{__('Extra sales margin')}}</label>&nbsp;&nbsp;
                        <input id="ar-immed-new-sales-margin-amt2"  style="width: 10em;" type="text" name="ar-immed-new-sales-margin-amt2" class="form-control" value=""
                               onfocus="this.oldvalue = this.value;" oninput="ar_immed_new_sales_margin_amt2_check(event, this);">&nbsp;
                        <input id="ar-immed-new-sales-margin-curr2" style="width: 5em;" type="text" name="ar-immed-new-sales-margin-curr2" class="form-control" value="" disabled>
                        <label for="ar-immed-new-sales-margin-perc2" style="margin-left: 0.4em;" class="col-form-label text-md-left">=</label>&nbsp;&nbsp;
                        <input id="ar-immed-new-sales-margin-perc2" style="width: 5em;" type="text" name="ar-immed-new-sales-margin-perc2" class="form-control" value=""
                               onfocus="this.oldvalue = this.value;" oninput="ar_immed_new_sales_margin_perc2_check(event, this);">
                        <label for="ar-immed-new-perc2-margin" style="margin-left: 0.2em;" class="col-form-label text-md-left">%</label>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="width: 50%;"></td>
                <td style="width: 50%;">
                    <div class="row" style="padding-top: 3px;">
                        <label for="ar-immed-new-sales-price2"  style="margin-left: 1em; width: 10em;" class="col-form-label text-md-left">{{__('New sales price (2)')}}</label>&nbsp;&nbsp;
                        <input id="ar-immed-new-sales-price2" style="width: 10em;" type="text" name="ar-immed-new-sales-price2" class="form-control" value=""
                               onfocus="this.oldvalue = this.value;" oninput="ar_immed_new_sales_price2_check(event, this);">&nbsp;
                        <input id="ar-immed-new-sales-curr2" style="width: 5em;" type="text" name="ar-immed-new-sales-curr2" class="form-control" value="" disabled>
                        <input type="checkbox" id="ar-immed-choose-new-sales-price2" style="margin-left: 0.5em;" onchange="ar_immed_choose_new_sales_price2_checkbox(this);">
                        <image id="ar-immed-icon-save-new-sales-price2" style="width: 32px; margin-left: 1em;" src='/images/icons8-save-40.png'/>
                        <input type="checkbox" id="ar-immed-save-new-sales-price2" onchange="ar_immed_choose_save_new_sales_price2_checkbox(this);">
                    </div>
                </td>
            </tr>
        </table>
        <div class="row" style="padding-top: 0.5rem;">
            <button type="button" style="float:left; margin-left: 1rem; margin-right: 1rem; " class="ui-button ui-corner-all ui-widget"
                    onclick="get_infnr2(1);return false;">{{__('Inforecord')}}</button>
            <button type="button" style="float:left; margin-right: 1rem; " class="ui-button ui-corner-all ui-widget" id="accept-reject-zpret2"
                    onclick="get_zpret2(1);return false;">ZPRET</button>
        </div>
    </div>
    <div id="ar-proposals-approval2" width="95%" style="margin-right: 0.5rem;">
        <div id="ar-proposals-table-2" style="overflow-y: scroll; min-height: 17rem;" width="95%">
            <table id="proposals-table-2" class="table-striped" width="100%">
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
                onclick="add_edit_proposal2(1, 1);return false;">{{__('New proposal')}}</button>
        <button type="button" style="float:left; margin-right: 1rem; " class="ui-button ui-corner-all ui-widget"
                onclick="add_edit_proposal2(1, 2);return false;">{{__('Edit proposal')}}</button>
        <button type="button" style="float:left; margin-right: 1rem; " class="ui-button ui-corner-all ui-widget"
                onclick="delete_proposal2(1);return false;">{{__('Delete proposal')}}</button>
    </div>
    <i id="new_acc_rej_msg2" style="color: red"></i>
    <br>
</div>

<script>

    var arDialog2, arForm2, _ar_type2, _ar_this2, _ar_itemdata2;
    var proposal_last_selected_line2;
    var proposal_last_color2;
    var fxtable;

    function ar_switchProposalMode2(_this) {
        if ($(_this).is(":checked")) {
            $("#ar-proposals-immed2").attr("style", "display: none; margin-right: 0.5rem;")
            $("#ar-proposals-approval2").attr("style", "display: block; margin-right: 0.5rem;")
            $("#accept-reject-ok-button2").text("{{__('Send proposal')}}");
        } else {
            $("#ar-proposals-immed2").attr("style", "display: block; margin-right: 0.5rem;")
            $("#ar-proposals-approval2").attr("style", "display: none; margin-right: 0.5rem;")
            $("#accept-reject-ok-button2").text("{{__('Modificare comanda')}}");
        }
    }

    $(function () {
        arDialog2 = $("#accept-reject-dialog2").dialog({
            autoOpen: false,
            height: 520,
            width: 920,
            modal: true,
            buttons: [
                {
                    text: '{{__("Send proposal")}}',
                    id: "accept-reject-ok-button2",
                    click: function () {
                        var result = new Object();
                        result.type = 'O';
                        result.itemdata = _ar_itemdata2;
                        if ($("#require_sr_approval2").is(":checked")) {
                            let tablerows = $('#proposals-table-2 tr');
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
                            result.lifnr = $("#ar-immed-lifnr2").val().trim();
                            result.idnlf = $("#ar-immed-idnlf2").val().trim();
                            result.mtext = $("#ar-immed-mtext2").val().trim();
                            result.matnr = $("#ar-immed-matnr2").val().trim();
                            result.lfdat = $("#ar-immed-lfdat2").val().trim();
                            result.quantity = $("#ar-immed-quantity2").val().trim();
                            result.quantity_unit = $("#ar-immed-quantity-unit2").val().trim();
                            result.purch_price = $("#ar-immed-purch-price2").val().trim();
                            result.purch_curr = $("#ar-immed-purch-curr2").val().trim().toUpperCase();
                            result.sales_price = $("#ar-immed-sales-price2").val().trim();
                            result.sales_curr = $("#ar-immed-sales-curr2").val().trim().toUpperCase();
                            if (result.lifnr.length == 0 ||
                                result.idnlf.length == 0 ||
                                result.mtext.length == 0 ||
                                result.matnr.length == 0 ||
                                result.lfdat.length == 0 ||
                                result.quantity.length == 0 ||
                                result.quantity_unit.length == 0 ||
                                result.purch_price.length == 0 ||
                                result.purch_curr.length == 0 ||
                                ((_ar_itemdata2.vbeln != "!REPLENISH") &&
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
                        $.get("webservice/processproposal2",
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
                        arDialog2.dialog("close");
                        location.reload();
                    }
                },
                {
                    text: '{{__("Cancel")}}',
                    click: function () {
                        arDialog2.dialog("close");
                    }
                }
            ],
            close: function () {
                // arForm[0].reset();
            },
            open: function () {
                $("#require_sr_approval2").prop("checked", true);
                $("#require_sr_approval2").show();
                $("#label_require_sr_approval2").show();
                $("#label-ar-immed-sales-price2").show();
                $("#ar-immed-sales-price2").show();
                $("#ar-immed-sales-curr2").show();
                $("#accept-reject-zpret2").show();
                $("#accept-reject-ok-button2").text("{{__('Send proposal')}}");
                $("#ar-immed-choose-sales-price2").prop("checked", true);
                $("#ar-immed-choose-sales-price2").prop("disabled", true);
                $("#ar-immed-icon-save-sales-price2").show();
                $("#ar-immed-save-sales-price2").show();
                $("#ar-immed-choose-new-sales-price2").prop("checked", false);
                $("#ar-immed-choose-new-sales-price2").prop("disabled", true);
                $("#ar-immed-icon-save-new-sales-price2").hide();
                $("#ar-immed-save-new-sales-price2").hide();
                $("#ar-immed-new-sales-margin-amt2").val("");
                $("#ar-immed-new-sales-margin-perc2").val("");
                $("#ar-immed-new-sales-price2").val("");
            },
            position: {
                my: "center",
                at: "center",
                of: window
            }
        });
        arForm2 = arDialog2.find("form").on("submit", function (event) {
            event.preventDefault();
        });
        $("#ar-immed-lfdat2").datepicker({dateFormat: "yy-mm-dd"});
    });

    function accept_reject_dialog2(type, this0, itemdata, title, initial_text) {
        $("#new_rej_msg2").text("");
        $("#initial-text2").text(initial_text);
        $("#proposals-table-2").find("tr:gt(0)").remove();
        $("#require_sr_approval2").prop("checked", true);
        $("#ar-proposals-immed2").attr("style", "display: none; margin-right: 0.5rem;")
        $("#ar-proposals-approval2").attr("style", "display: block; margin-right: 0.5rem;")
        $("#accept-reject-ok-button2").text("{{__('Send proposal')}}");

        $("#accept-reject-dialog2").dialog('option', 'title', title);
        _ar_type2 = type;
        _ar_this2 = this0;
        _ar_itemdata2 = itemdata;
        fxtable = [{curr:_ar_itemdata2.curr, fxrate:parseFloat(_ar_itemdata2.fxrate)}];

        if (type == 1) {
            $("#ar-immed-lifnr2").val(conv_exit_alpha_output(itemdata.lifnr));
            $("#ar-immed-idnlf2").val(itemdata.idnlf);
            $("#ar-immed-mtext2").val(itemdata.mtext);
            $("#ar-immed-matnr2").val(itemdata.matnr);
            $("#ar-immed-lfdat2").val(itemdata.lfdat.substring(0, 10));
            $("#ar-immed-quantity2").val(itemdata.qty);
            $("#ar-immed-quantity-unit2").val(itemdata.qty_uom);
            $("#ar-immed-old-purch-price2").val(itemdata.purch_price);
            $("#ar-immed-purch-price2").val(itemdata.purch_price);
            $("#ar-immed-old-purch-curr2").val(itemdata.purch_curr);
            $("#ar-immed-purch-curr2").val(itemdata.purch_curr);
            $("#ar-immed-old-sales-price2").val(itemdata.sales_price);
            $("#ar-immed-sales-price2").val(itemdata.sales_price);
            $("#ar-immed-old-sales-curr2").val(itemdata.sales_curr);
            $("#ar-immed-old-sales-margin-amt-curr2").val(itemdata.sales_curr);
            $("#ar-immed-sales-curr2").val(itemdata.sales_curr);
            $("#ar-immed-defmargin-amt2").val((itemdata.sales_price - itemdata.purch_price).toFixed(2));
            $("#ar-immed-new-sales-curr2").val(itemdata.sales_curr);
            $("#ar-immed-new-sales-margin-curr2").val(itemdata.sales_curr);
            let defmargin = itemdata.defmargin.trim();
            if (defmargin.length == 0) defmargin = "<>";
            else defmargin = parseFloat(defmargin).toFixed(2);
            $("#ar-immed-defmargin-perc2").val(defmargin);
            $("#ar-immed-defmargin-curr2").val(itemdata.sales_curr);
            if (itemdata.purch_curr == itemdata.sales_curr) {
                let amt = itemdata.sales_price - itemdata.purch_price;
                let perc = 0;
                if (itemdata.purch_price != 0) {
                    perc = amt * 100 / itemdata.purch_price;
                }
                $("#ar-immed-old-sales-margin-amt2").val(amt.toFixed(2));
                $("#ar-immed-old-sales-margin-perc2").val(perc.toFixed(2));
            }

            var newRow = $("<tr style='height: 1.2rem;' onclick='proposal_selected2(this);return false;'>");
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
            $("#proposals-table-2").append(newRow);
        } else {
            $("#ar-immed-lifnr2").val("");
            $("#ar-immed-idnlf2").val(itemdata.idnlf);
            $("#ar-immed-mtext2").val(itemdata.mtext);
            $("#ar-immed-matnr2").val(itemdata.matnr);
            $("#ar-immed-lfdat2").val(itemdata.lfdat.substring(0, 10));
            $("#ar-immed-quantity2").val(itemdata.qty);
            $("#ar-immed-quantity-unit2").val(itemdata.qty_uom);
            $("#ar-immed-purch-price2").val("");
            $("#ar-immed-purch-curr2").val("");
            $("#ar-immed-sales-price2").val("");
            $("#ar-immed-sales-curr2").val("");
            $("#ar-immed-defmargin-perc2").val(itemdata.defmargin);
            $("#ar-immed-new-sales-price2").val("");
            $("#ar-immed-new-sales-curr2").val("");
        }
        arDialog2.dialog("open");
    }
</script>

<div id="add-edit-proposal2" title="Add/edit proposal" style="display: none;">
    <table style="width: 100%; border-style: none">
        <tr>
            <td style="width: 50%;">
                <div class="row">
                    <label for="aep-lifnr2" class="col-md-2 col-form-label text-md-left">{{__('Vendor')}}</label>&nbsp;&nbsp;
                    <input id="aep-lifnr2" type="text" name="aep-lifnr2" class="form-control col-md-3" value="">
                </div>
            </td>
            <td style="width: 50%;">
                <div class="row">
                    <label for="aep-defmargin2" style="margin-left: 7.3em;" class="col-md-2 col-form-label text-md-left">{{__('Default margin')}}</label>&nbsp;&nbsp;
                    <input id="aep-defmargin2" style="margin-left: -2em;" type="text" name="aep-defmargin2" class="form-control col-md-1" value="" disabled>&nbsp;
                </div>
            </td>
        </tr>
        <tr>
            <td style="width: 50%;">
                <div class="row" style="padding-top: 3px;">
                    <label for="aep-idnlf2" class="col-md-2 col-form-label text-md-left">{{__('Vendor mat.')}}</label>&nbsp;&nbsp;
                    <input id="aep-idnlf2" type="text" name="aep-idnlf2" class="form-control col-md-3" value="">
                </div>
            </td>
            <td style="width: 50%;">
                <div class="row" style="padding-top: 3px;">
                    <label for="aep-purch-price2" style="margin-left: 7.3em;" class="col-md-2 col-form-label text-md-left">{{__('Initial purchase price')}}</label>&nbsp;&nbsp;
                    <input id="aep-purch-price2"  style="margin-left: -2em;" type="text" name="aep-purch-price2" class="form-control col-md-2" value="" disabled>&nbsp;
                    <input id="aep-purch-curr2" type="text" name="aep-purch-curr2" class="form-control col-md-1" value="" disabled>
                </div>
            </td>
        </tr>
        <tr>
            <td style="width: 50%;">
                <div class="row" style="padding-top: 3px;">
                    <label for="aep-mtext2" class="col-md-2 col-form-label text-md-left">{{__('Material description')}}</label>&nbsp;&nbsp;
                    <input id="aep-mtext2" type="text" name="aep-mtext2" class="form-control col-md-4" value="">
                </div>
            </td>
            <td style="width: 50%;">
                <div class="row" style="padding-top: 3px;">
                    <label for="aep-sales-price2"  style="margin-left: 1em;" class="col-md-2 col-form-label text-md-left">{{__('Initial sales price')}}</label>&nbsp;&nbsp;
                    <input id="aep-sales-price2"  style="margin-left: -2em;" type="text" name="aep-sales-price2" class="form-control col-md-2" value="" disabled>&nbsp;
                    <input id="aep-sales-curr2" type="text" name="aep-sales-curr2" class="form-control col-md-1" value="" disabled>
                </div>
            </td>
        </tr>
        <tr>
            <td style="width: 50%;">
                <div class="row" style="padding-top: 3px;">
                    <label for="aep-matnr2" class="col-md-2 col-form-label text-md-left">{{__('Material')}}</label>&nbsp;&nbsp;
                    <input id="aep-matnr2" type="text" name="aep-matnr2" class="form-control col-md-3" value="">
                </div>
            </td>
            <td style="width: 50%;">
            </td>
        </tr>
        <tr>
            <td style="width: 50%;">
                <div class="row" style="padding-top: 3px;">
                    <label for="aep-lfdat2" class="col-md-2 col-form-label text-md-left">{{__('Delivery date')}}</label>&nbsp;&nbsp;
                    <input id="aep-lfdat2" type="text" name="aep-lfdat2" class="form-control col-md-3" value="">
                </div>
            </td>
            <td style="width: 50%;">
            </td>
        </tr>
        <tr>
            <td style="width: 50%;">
                <div class="row" style="padding-top: 3px;">
                    <label for="aep-quantity2" class="col-md-2 col-form-label text-md-left">{{__('Quantity')}}</label>&nbsp;&nbsp;
                    <input id="aep-quantity2" type="text" name="aep-quantity2" class="form-control col-md-3" value="">&nbsp;
                    <input id="aep-quantity-unit2" type="text" name="aep-quantity-unit2" class="form-control col-md-1"
                           value="">
                </div>
            </td>
            <td style="width: 50%;">
            </td>
        </tr>
    </table>
    <div class="row" style="padding-top: 0.5rem;">
        <button type="button" style="float:left; margin-left: 1rem; margin-right: 1rem; " class="ui-button ui-corner-all ui-widget"
                onclick="get_infnr2(2);return false;">{{__('Inforecord')}}</button>
        <button type="button" style="float:left; margin-right: 1rem; " class="ui-button ui-corner-all ui-widget"
                onclick="get_zpret2(2);return false;">ZPRET</button>
    </div>
</div>

<script>
    var add_edit_Dialog2, add_edit_Form2, add_edit_current_row2, add_edit_caller2;

    $(function () {
        add_edit_Dialog2 = $("#add-edit-proposal2").dialog({
            autoOpen: false,
            height: 420,
            width: 910,
            modal: true,
            buttons: {
                Save: function () {
                    let lifnr = $("#aep-lifnr2").val().trim();
                    let idnlf = $("#aep-idnlf2").val().trim();
                    let mtext = $("#aep-mtext2").val().trim();
                    let matnr = $("#aep-matnr2").val().trim();
                    let lfdat = $("#aep-lfdat2").val().trim();
                    let quantity = $("#aep-quantity2").val().trim();
                    let quantity_uom = $("#aep-quantity-unit2").val().trim();
                    let purch_price = $("#aep-purch-price2").val().trim();
                    let purch_curr = $("#aep-purch-curr2").val().trim().toUpperCase();
                    let sales_price = $("#aep-sales-price2").val().trim();
                    let sales_curr = $("#aep-sales-curr2").val().trim().toUpperCase();
                    if (lifnr.length == 0 ||
                        idnlf.length == 0 ||
                        mtext.length == 0 ||
                        matnr.length == 0 ||
                        lfdat.length == 0 ||
                        quantity.length == 0 ||
                        quantity_uom.length == 0 ||
                        purch_price.length == 0 ||
                        purch_curr.length == 0 ||
                        ((_ar_itemdata2.vbeln != "!REPLENISH") &&
                            (sales_price.length == 0 ||
                             sales_curr.length == 0)
                        )
                    ) return;
                    if (add_edit_caller2 == 1) {
                        if (add_edit_current_row2 != null) {
                            add_edit_current_row2.cells[0].innerHTML = lifnr;
                            add_edit_current_row2.cells[1].innerHTML = readLifnrName(lifnr);
                            add_edit_current_row2.cells[2].innerHTML = idnlf;
                            add_edit_current_row2.cells[3].innerHTML = mtext;
                            add_edit_current_row2.cells[4].innerHTML = matnr;
                            add_edit_current_row2.cells[5].innerHTML = lfdat;
                            add_edit_current_row2.cells[6].innerHTML = quantity + " " + quantity_uom;
                            add_edit_current_row2.cells[7].innerHTML = purch_price + " " + purch_curr;
                            add_edit_current_row2.cells[8].innerHTML = sales_price + " " + sales_curr;
                            add_edit_Dialog2.dialog("close");
                        } else {
                            var newRow = $("<tr style='height: 1.2rem;' onclick='proposal_selected2(this);return false;'>");
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
                            $("#proposals-table-2").append(newRow);
                            add_edit_Dialog2.dialog("close");
                        }
                    }
                },
                Cancel: function () {
                    add_edit_Dialog2.dialog("close");
                }
            },
            position: {
                my: "center",
                at: "center",
                of: window
            }
        });
        add_edit_Form2 = add_edit_Dialog2.find("form").on("submit", function (event) {
            event.preventDefault();
        });
        $("#aep-lfdat2").datepicker({dateFormat: "yy-mm-dd"});
    });

    function add_edit_proposal2(caller, mode) {

        add_edit_caller2 = caller;
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
                add_edit_current_row2 = null;
                title = "Add new proposal";
                idnlf = $("#ar-immed-idnlf2").val();
                mtext = $("#ar-immed-mtext2").val();
                quantity  = $("#ar-immed-quantity2").val();
                quantity_unit = $("#ar-immed-quantity-unit2").val();
            } else {
                title = "Edit existing proposal";
                let current_row = proposal_last_selected_line2;
                add_edit_current_row2 = proposal_last_selected_line2;
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

        $("#aep-lifnr2").val(lifnr);
        $("#aep-idnlf2").val(idnlf);
        $("#aep-mtext2").val(mtext);
        $("#aep-matnr2").val(matnr);
        $("#aep-lfdat2").val(lfdat);
        $("#aep-quantity2").val(quantity);
        $("#aep-quantity-unit2").val(quantity_unit);
        $("#aep-purch-price2").val(purch_price);
        $("#aep-purch-curr2").val(purch_curr);
        $("#aep-sales-price2").val(sales_price);
        $("#aep-sales-curr2").val(sales_curr);

        $("#add-edit-proposal2").dialog('option', 'title', title);
        $('#add-edit-proposal2').css('overflow', 'hidden');
        add_edit_Dialog2.dialog("open");
    }

    function proposal_selected2(_this) {
        if (proposal_last_selected_line2 != null)
            $(proposal_last_selected_line2).css("background-color", proposal_last_color2);
        proposal_last_color2 = $(_this).css("background-color");
        proposal_last_selected_line2 = _this;
        $(_this).css("background-color", "#AACCAA");
    }

    function get_infnr2(caller)
    {
        let lifnr = null;
        let idnlf = null;
        if (caller == 1) {
            lifnr = $("#ar-immed-lifnr2").val();
            idnlf = $("#ar-immed-idnlf2").val();
        }
        if (caller == 2) {
            lifnr = $("#aep-lifnr2").val();
            idnlf = $("#aep-idnlf2").val();
        }
        read_inforecords(caller, lifnr, idnlf);
    }

    function get_zpret2(caller)
    {
        let lifnr = null;
        let idnlf = null;
        if (caller == 1) {
            lifnr = $("#ar-immed-lifnr2").val();
            idnlf = $("#ar-immed-idnlf2").val();
        }
        if (caller == 2) {
            lifnr = $("#aep-lifnr2").val();
            idnlf = $("#aep-idnlf2").val();
        }
        read_zpretrecords(caller, lifnr, idnlf);
    }

    function delete_proposal2(mode)
    {
        if (proposal_last_selected_line2 != null)
            $(proposal_last_selected_line2).remove();
    }

    function get_sales_margin2(lifnr, mfrnr, wglif)
    {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajaxSetup({async: false});
        var _dataSM, _statusSM;
        $.get("webservice/get_sales_margin",
            {
                lifnr: lifnr,
                mfrnr: mfrnr,
                wglif: wglif
            },
            function (data, status) {
                _dataSM = data;
                _statusSM = status;
            });
        jQuery.ajaxSetup({async: true});
        if (_statusSM != "success") return "";
        return _dataSM;
    }

    $(function(){
        $("#ar-immed-lifnr2").on("keypress", function(e){if (e.which == 13) {ar_immed_lifnr2_check(this);e.preventDefault();}});
        $("#ar-immed-lifnr2").blur(ar_immed_lifnr2_check(this));
    });

    function ar_immed_lifnr2_check(_this) {
        let new_margin = "";
        new_margin = get_sales_margin2(_ar_itemdata2.lifnr, _ar_itemdata2.mfrnr, null) + " %";
        if (new_margin == $("#ar-immed-defmargin-perc2").val()) return;
        $("#ar-immed-defmargin-perc2").val(new_margin);
        var original_color = $("#ar-immed-defmargin-perc2").css('border-left-color');
        $("#ar-immed-defmargin-perc2")
            .animate({borderColor:'red'}, 200, 'linear')
            .delay(200)
            .animate({borderColor:original_color}, 200, 'easeOutCirc')
            .delay(200)
            .animate({borderColor:'red'}, 200, 'linear')
            .delay(200)
            .animate({borderColor:original_color}, 200, 'easeOutCirc');
    }

    function ar_immed_purch_price2_check(event, _this) {
        let margin = $("#ar-immed-defmargin-perc2").val().trim();
        if (margin == "<>") margin = 0; else margin = parseFloat(margin);
        let pprice = $("#ar-immed-purch-price2").val().trim();
        if (pprice.length == 0) {
            $("#ar-immed-sales-price2").val("");
            $("#ar-immed-defmargin-amt2").val("0.00");
            _this.oldvalue = "";
            return;
        }
        if (!$.isNumeric(pprice)) {
            $("#ar-immed-purch-price2").val(_this.oldvalue);
            return;
        }
        pprice = parseFloat(pprice);
        _this.oldvalue = pprice.toFixed(2);
        sprice = (pprice * (1 + margin / 100));
        $("#ar-immed-sales-price2").val(sprice.toFixed(2));
        $("#ar-immed-defmargin-amt2").val((sprice - pprice).toFixed(2));
        $("#ar-immed-new-sales-margin-amt2").val("");
        $("#ar-immed-new-sales-margin-perc2").val("");
        $("#ar-immed-new-sales-price2").val("");
        $("#ar-immed-choose-sales-price2").prop("checked", true);
        ar_immed_choose_sales_price2_checkbox(_this, false);
        $("#ar-immed-choose-sales-price2").prop("disabled", true);
        $("#ar-immed-choose-new-sales-price2").prop("disabled", true);
    }

    function ar_immed_sales_price2_check(event, _this) {
        let margin = $("#ar-immed-defmargin-perc2").val().trim();
        if (margin == "<>") margin = 0; else margin = parseFloat(margin);
        let pprice = $("#ar-immed-purch-price2").val().trim();
        if (pprice.length == 0 || !$.isNumeric(pprice)) pprice = 0;
        else pprice = parseFloat(pprice);
        let sprice = $("#ar-immed-sales-price2").val().trim();
        if (sprice.length == 0) {
            sprice = (pprice * (1 + margin / 100));
            $("#ar-immed-sales-price2").val(sprice.toFixed(2));
            _this.oldvalue = sprice.toFixed(2);
            $("#ar-immed-defmargin-amt2").val((sprice - pprice).toFixed(2));
            return;
        } else {
            if (!$.isNumeric(sprice)) {
                $("#ar-immed-sales-price2").val(_this.oldvalue);
                $("#ar-immed-defmargin-amt2").val("0.00");
                return;
            }
        }
        sprice = parseFloat(sprice);
        $("#ar-immed-defmargin-amt2").val((sprice - pprice).toFixed(2));
        _this.oldvalue = sprice.toFixed(2);
        $("#ar-immed-new-sales-margin-amt2").val("");
        $("#ar-immed-new-sales-margin-perc2").val("");
        $("#ar-immed-new-sales-price2").val("");
        $("#ar-immed-choose-sales-price2").prop("checked", true);
        ar_immed_choose_sales_price2_checkbox(_this, false);
        $("#ar-immed-choose-sales-price2").prop("disabled", true);
        $("#ar-immed-choose-new-sales-price2").prop("disabled", true);
    }

    function ar_immed_new_sales_price2_check(event, _this) {
        let nsprice = $("#ar-immed-new-sales-price2").val().trim();
        if (nsprice.length == 0) {
            let amt2 = $("#ar-immed-new-sales-margin-amt2").val().trim();
            if (amt2.length == 0) {
                $("#ar-immed-new-sales-margin-amt2").val("");
                $("#ar-immed-new-sales-margin-perc2").val("");
                $("#ar-immed-new-sales-price2").val("");
                $("#ar-immed-choose-sales-price2").prop("checked", true);
                $("#ar-immed-choose-sales-price2").prop("disabled", true);
                $("#ar-immed-choose-new-sales-price2").prop("disabled", true);
                return;
            }
            let sprice = $("#ar-immed-sales-price2").val().trim();
            nsprice = parseFloat(sprice) + parseFloat(amt2);
            $("#ar-immed-new-sales-price2").val(nsprice.toFixed(2));
        } else {
            if (!$.isNumeric(nsprice)) {
                $("#ar-immed-new-sales-price2").val(_this.oldvalue);
                return;
            }
            nsprice = parseFloat(nsprice);
        }
        _this.oldvalue = nsprice.toFixed(2);
        $("#ar-immed-choose-new-sales-price2").prop("checked", true);
        ar_immed_choose_new_sales_price2_checkbox(_this, false);
        $("#ar-immed-choose-sales-price2").prop("disabled", false);
        $("#ar-immed-choose-new-sales-price2").prop("disabled", false);
    }

    function ar_immed_new_sales_margin_amt2_check(event, _this) {
        let __this = $(_this);
        let newamt = __this.val().trim();
        if (newamt.length == 0) {
            $("#ar-immed-new-sales-margin-perc2").val("");
            __this.val("");
            _this.oldvalue = "";
            $("#ar-immed-choose-sales-price2").prop("checked", true);
            ar_immed_choose_sales_price2_checkbox(_this);
            $("#ar-immed-new-sales-price2").val("");
            $("#ar-immed-choose-sales-price2").prop("disabled", true);
            $("#ar-immed-choose-new-sales-price2").prop("disabled", true);
        }
        if (!$.isNumeric(newamt) && newamt != "-") {
            __this.val(_this.oldvalue);
            return;
        }
        if (newamt == "-") newperc = 0; else newamt = parseFloat(newamt);
        _this.oldvalue = newamt.toFixed(2);
        let sprice = $("#ar-immed-sales-price2").val().trim();
        if (sprice.length == 0) sprice = "0";
        sprice = parseFloat(sprice);
        let newperc = 0;
        if (sprice != 0) newperc = (newamt / sprice * 100);
        newperc = newperc.toFixed(2);
        $("#ar-immed-new-sales-margin-perc2").val(newperc);
        $("#ar-immed-new-sales-margin-perc2").oldvalue = newperc;
        sprice += newamt;
        $("#ar-immed-new-sales-price2").val(sprice.toFixed(2));
        $("#ar-immed-choose-new-sales-price2").prop("checked", true);
        ar_immed_choose_new_sales_price2_checkbox(_this, false);
        $("#ar-immed-choose-sales-price2").prop("disabled", false);
        $("#ar-immed-choose-new-sales-price2").prop("disabled", false);
    }

    function ar_immed_new_sales_margin_perc2_check(event, _this) {
        let __this = $(_this);
        let newperc = __this.val().trim();
        if (newperc.length == 0) {
            $("#ar-immed-new-sales-margin-amt2").val("");
            __this.val("");
            _this.oldvalue = "";
            $("#ar-immed-choose-sales-price2").prop("checked", true);
            ar_immed_choose_sales_price2_checkbox(_this);
            $("#ar-immed-new-sales-price2").val("");
            $("#ar-immed-choose-sales-price2").prop("disabled", true);
            $("#ar-immed-choose-new-sales-price2").prop("disabled", true);
        }
        if (!$.isNumeric(newperc) && newperc != "-") {
            __this.val(_this.oldvalue);
            return;
        }
        if (newperc == "-") newperc = 0; else newperc = parseFloat(newperc);
        _this.oldvalue = newperc.toFixed(2);
        let sprice = $("#ar-immed-sales-price2").val().trim();
        if (sprice.length == 0) sprice = "0";
        sprice = parseFloat(sprice);
        let newamt = sprice * (100 + newperc) / 100;
        $("#ar-immed-new-sales-margin-amt2").val((newamt - sprice).toFixed(2));
        $("#ar-immed-new-sales-margin-amt2").oldvalue = (newamt - sprice).toFixed(2);
        $("#ar-immed-new-sales-price2").val(newamt.toFixed(2));
        $("#ar-immed-choose-new-sales-price2").prop("checked", true);
        ar_immed_choose_new_sales_price2_checkbox(_this, false);
        $("#ar-immed-choose-sales-price2").prop("disabled", false);
        $("#ar-immed-choose-new-sales-price2").prop("disabled", false);
    }

    function ar_immed_sales_curr2_check() {
        let curr = $("#ar-immed-sales-curr2").val().trim();
        $("#ar-immed-new-sales-margin-curr2").val(curr);
        $("#ar-immed-new-sales-curr2").val(curr);
    }

    function ar_immed_choose_sales_price2_checkbox(_this, goto = true) {
        if ($("#ar-immed-choose-sales-price2").is(":checked")) {
            $("#ar-immed-choose-new-sales-price2").prop("checked", false);
            $("#ar-immed-icon-save-new-sales-price2").hide();
            $("#ar-immed-save-new-sales-price2").hide();
            $("#ar-immed-icon-save-sales-price2").show();
            $("#ar-immed-save-sales-price2").show();
            if (goto) {
                $("#ar-immed-sales-price2").focus();
                $("#ar-immed-sales-price2").select();
            }
        } else {
            $("#ar-immed-choose-new-sales-price2").prop("checked", true);
            ar_immed_choose_new_sales_price2_checkbox(_this, goto);
        }
    }

    function ar_immed_choose_new_sales_price2_checkbox(_this, goto = true) {
        if ($("#ar-immed-choose-new-sales-price2").is(":checked")) {
            $("#ar-immed-choose-sales-price2").prop("checked", false);
            $("#ar-immed-icon-save-new-sales-price2").show();
            $("#ar-immed-save-new-sales-price2").show();
            $("#ar-immed-icon-save-sales-price2").hide();
            $("#ar-immed-save-sales-price2").hide();
            if (goto) {
                $("#ar-immed-new-sales-price2").focus();
                $("#ar-immed-new-sales-price2").select();
            }
        } else {
            $("#ar-immed-choose-sales-price2").prop("checked", true);
            ar_immed_choose_sales_price2_checkbox(_this, goto);
        }
    }

    function ar_immed_choose_save_sales_price2_checkbox(_this) {
        $("#ar-immed-save-new-sales-price2").prop("checked",
            $("#ar-immed-save-sales-price2").is(":checked"));
    }

    function ar_immed_choose_save_new_sales_price2_checkbox(_this) {
        $("#ar-immed-save-sales-price2").prop("checked",
            $("#ar-immed-save-new-sales-price2").is(":checked"));
    }

    function get_fx_rate(curr) {
        if (curr == "RON") return 1;
        for (i = 0; i < fxtable.length; i++)
            if (curr == fxtable[i].curr) return fxtable[i].fxrate;
        jQuery.ajaxSetup({async: false});
        var _dataFX, _statusFX;
        $.get("webservice/get_fx_rate",
            {
                curr: curr
            },
            function (data, status) {
                _dataFX = data;
                _statusFX = status;
            });
        jQuery.ajaxSetup({async: true});
        if (_dataFX != null && _dataFX != undefined && _dataFX.trim().length != 0) {
            let fx = {curr: curr, fxrate: parseFloat(dataFX)};
            fxtable.push(fx);
            return fx.fxrate;
        }
        return 0;
    }

</script>