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
                        <input id="ar-immed-lifnr2" type="text" name="ar-immediate-lifnr2" style="width: 15em;" class="form-control" value=""
                            onkeypress="arf_lifnr2_check(event, this, 'ar-immed');" onblur="arf_lifnr2_check(event, this, 'ar-immed');">
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
                               onfocus="this.oldvalue = this.value;" oninput="ar2_purch_price2_check(event, this, 'ar-immed');">&nbsp;
                        <input id="ar-immed-purch-curr2" style="width: 5em;" type="text" name="ar-immed-purch-curr2" class="form-control" value=""
                            onblur="ar2_purch_curr2_check(event, this, 'ar-immed');">
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
                               onfocus="this.oldvalue = this.value;" oninput="ar2_sales_price2_check(event, this, 'ar-immed');">&nbsp;
                        <input id="ar-immed-sales-curr2" style="width: 5em;" type="text" name="ar-immed-sales-curr2" class="form-control" value=""
                               onblur="ar2_sales_curr2_check(event, this, 'ar-immed');">
                        <input type="checkbox" id="ar-immed-choose-sales-price2" style="margin-left: 0.5em; border: none;" onchange="ar2_choose_sales_price2_checkbox(this, true, 'ar-immed');">
                        <image id="ar-immed-icon-save-sales-price2" style="width: 32px; margin-left: 1em;" src='/images/icons8-save-40.png'/>
                        <input type="checkbox" id="ar-immed-save-sales-price2" onchange="ar2_choose_save_sales_price2_checkbox(this, 'ar-immed');">
                    </div>
                </td>
            </tr>

            <tr>
                <td style="width: 50%;"></td>
                <td style="width: 50%;">
                    <div class="row" style="padding-top: 3px;">
                        <label for="ar-immed-new-sales-margin-amt2"  style="margin-left: 1em; width: 10em;" class="col-form-label text-md-left">{{__('Extra sales margin')}}</label>&nbsp;&nbsp;
                        <input id="ar-immed-new-sales-margin-amt2"  style="width: 10em;" type="text" name="ar-immed-new-sales-margin-amt2" class="form-control" value=""
                               onfocus="this.oldvalue = this.value;" oninput="ar2_new_sales_margin_amt2_check(event, this, 'ar-immed');">&nbsp;
                        <input id="ar-immed-new-sales-margin-curr2" style="width: 5em;" type="text" name="ar-immed-new-sales-margin-curr2" class="form-control" value="" disabled>
                        <label for="ar-immed-new-sales-margin-perc2" style="margin-left: 0.4em;" class="col-form-label text-md-left">=</label>&nbsp;&nbsp;
                        <input id="ar-immed-new-sales-margin-perc2" style="width: 5em;" type="text" name="ar-immed-new-sales-margin-perc2" class="form-control" value=""
                               onfocus="this.oldvalue = this.value;" oninput="ar2_new_sales_margin_perc2_check(event, this, 'ar-immed');">
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
                               onfocus="this.oldvalue = this.value;" oninput="ar2_new_sales_price2_check(event, this, 'ar-immed');">&nbsp;
                        <input id="ar-immed-new-sales-curr2" style="width: 5em;" type="text" name="ar-immed-new-sales-curr2" class="form-control" value="" disabled>
                        <input type="checkbox" id="ar-immed-choose-new-sales-price2" style="margin-left: 0.5em;" onchange="ar2_choose_new_sales_price2_checkbox(this, true, 'ar-immed');">
                        <image id="ar-immed-icon-save-new-sales-price2" style="width: 32px; margin-left: 1em;" src='/images/icons8-save-40.png'/>
                        <input type="checkbox" id="ar-immed-save-new-sales-price2" onchange="ar2_choose_save_new_sales_price2_checkbox(this, 'ar-immed');">
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
                                // item.sales_price = row.cells[8].textContent.split(" ")[0];
                                // item.sales_curr = row.cells[8].textContent.split(" ")[1];
                                item.sales_curr = $(row).data("sales_curr");
                                if ($(row).data("sales_choice") == 1) {
                                    item.sales_price = $(row).data("sales_price");
                                } else {
                                    item.sales_price = $(row).data("new_sales_price");
                                }
                                item.sales_save = $(row).data("sales_save");
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
//                          result.sales_price = $("#ar-immed-sales-price2").val().trim();
                            result.sales_curr = $("#ar-immed-sales-curr2").val().trim().toUpperCase();
                            if ($("#ar-immed-choose-sales-price2").is(":checked")) {
                                result.sales_price = $("#ar-immed-sales-price2").val().trim();
                            } else {
                                result.sales_price = $("#ar-immed-new-sales-price2").val().trim();
                            }
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
                            result.sales_save = 0;
                            if ($("#ar-immed-save-sales-price2").is(":checked")) result.sales_save = 1;
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
        if (itemdata.vbeln == "!REPLENISH") {
            accept_reject_dialog(type, this0, itemdata, title, initial_text);
            return;
        }
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

        let defmargin = itemdata.defmargin.trim();
        if (defmargin.length == 0) defmargin = "<>";
        else defmargin = parseFloat(defmargin).toFixed(2);

        if (type == 1) {
            $("#ar-immed-lifnr2").val(conv_exit_alpha_output(itemdata.lifnr));
            $("#ar-immed-idnlf2").val(itemdata.idnlf);
            $("#ar-immed-mtext2").val(itemdata.mtext);
            $("#ar-immed-matnr2").val(itemdata.matnr);
            $("#ar-immed-lfdat2").val(itemdata.lfdat.substring(0, 10));
            $("#ar-immed-quantity2").val(itemdata.qty);
            $("#ar-immed-quantity-unit2").val(itemdata.qty_uom);
            $("#ar-immed-old-purch-price2").val(itemdata.orig_purch_price);
            $("#ar-immed-purch-price2").val(itemdata.purch_price);
            $("#ar-immed-old-purch-curr2").val(itemdata.purch_curr);
            let pcurr = $("#ar-immed-purch-curr2");
            pcurr.val(itemdata.purch_curr); check_currency(pcurr[0]);
            $("#ar-immed-old-sales-price2").val(itemdata.sales_price);
            $("#ar-immed-sales-price2").val(itemdata.sales_price);
            $("#ar-immed-old-sales-curr2").val(itemdata.sales_curr);
            $("#ar-immed-old-sales-margin-amt-curr2").val(itemdata.sales_curr);
            let scurr = $("#ar-immed-sales-curr2");
            scurr.val(itemdata.sales_curr); check_currency(scurr[0]);
            let pp = cvfx(itemdata.orig_purch_price, itemdata.purch_curr, itemdata.sales_curr);
            let amt = itemdata.sales_price - pp;
            $("#ar-immed-defmargin-amt2").val(amt.toFixed(2));
            $("#ar-immed-old-sales-margin-amt2").val(amt.toFixed(2));
            $("#ar-immed-new-sales-curr2").val(itemdata.sales_curr);
            $("#ar-immed-new-sales-margin-curr2").val(itemdata.sales_curr);
            $("#ar-immed-defmargin-perc2").val(defmargin);
            $("#ar-immed-defmargin-curr2").val(itemdata.sales_curr);
            let perc = 0;
            if (pp != 0) perc = amt * 100 / pp;
            $("#ar-immed-old-sales-margin-perc2").val(perc.toFixed(2));

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
            newRow.data("sales_choice", 1);
            newRow.data("sales_save", 0);
            newRow.data("sales_price", itemdata.sales_price);
            newRow.data("sales_curr", itemdata.sales_curr);
            newRow.data("new_sales_amt", "");
            newRow.data("new_sales_perc", "");
            newRow.data("new_sales_price", "");
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
            $("#ar-immed-defmargin-perc2").val(defmargin);
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
                    <label for="aep-lifnr2" style="margin-left: 2em; width: 10em;" class="col-form-label text-md-left">{{__('Vendor')}}</label>&nbsp;&nbsp;
                    <input id="aep-lifnr2" type="text" name="aep-lifnr2" style="width: 15em;" class="form-control" value=""
                           onkeypress="arf_lifnr2_check(event, this, 'aep');" onblur="arf_lifnr2_check(event, this, 'aep');">
                </div>
            </td>
            <td style="width: 50%;">
                <div class="row" style="padding-top: 3px;">
                    <label for="aep-old-purch-price2" style="margin-left: 1em; width: 10em;" class="col-form-label text-md-left">{{__('Initial purchase price')}}</label>&nbsp;&nbsp;
                    <input id="aep-old-purch-price2" style="width: 10em;" type="text" name="aep-old-purch-price2" class="form-control" value="" disabled>&nbsp;
                    <input id="aep-old-purch-curr2" style="width: 5em;" type="text" name="aep-old-purch-curr2" class="form-control" value="" disabled>
                </div>
            </td>
        </tr>

        <tr>
            <td style="width: 50%;">
                <div class="row" style="padding-top: 3px;">
                    <label for="aep-idnlf2"  style="margin-left: 2em; width: 10em;" class="col-form-label text-md-left">{{__('Vendor mat.')}}</label>&nbsp;&nbsp;
                    <input id="aep-idnlf2" style="width: 15em;" type="text" name="aep-idnlf2" class="form-control" value="">
                </div>
            </td>
            <td style="width: 50%;">
                <div class="row" style="padding-top: 3px;">
                    <label for="aep-old-sales-margin-amt2" style="margin-left: 1em; width: 10em;" class="col-form-label text-md-left">{{__('Initial sales margin')}}</label>&nbsp;&nbsp;
                    <input id="aep-old-sales-margin-amt2"  style="width: 10em;" type="text" name="aep-old-sales-margin-amt2" class="form-control" value="" disabled>&nbsp;
                    <input id="aep-old-sales-margin-amt-curr2" style="width: 5em;" type="text" name="aep-old-sales-margin-amt-curr2" class="form-control" value="" disabled>
                    <label for="aep-old-sales-margin-perc2" style="margin-left: 0.4em;" class="col-form-label text-md-left">=</label>&nbsp;&nbsp;
                    <input id="aep-old-sales-margin-perc2" style="width: 5em;" type="text" name="aep-old-sales-margin-perc2" class="form-control" value="" disabled>
                    <label for="aep-old-perc2-margin" style="margin-left: 0.2em;" class="col-form-label text-md-left">%</label>
                </div>
            </td>
        </tr>


        <tr>
            <td style="width: 50%;">
                <div class="row" style="padding-top: 3px;">
                    <label for="aep-mtext2"  style="margin-left: 2em; width: 10em;" class="col-form-label text-md-left">{{__('Material description')}}</label>&nbsp;&nbsp;
                    <input id="aep-mtext2" style="width: 22em;" type="text" name="aep-mtext2" class="form-control" value="">
                </div>
            </td>
            <td style="width: 50%;">
                <div class="row" style="padding-top: 3px;">
                    <label for="aep-old-sales-price2"  style="margin-left: 1em; width: 10em;" class="col-form-label text-md-left">{{__('Initial sales price')}}</label>&nbsp;&nbsp;
                    <input id="aep-old-sales-price2"  style="width: 10em;" type="text" name="aep-old-sales-price2" class="form-control" value="" disabled>&nbsp;
                    <input id="aep-old-sales-curr2" style="width: 5em;" type="text" name="aep-old-sales-curr2" class="form-control" value="" disabled>
                </div>
            </td>
        </tr>


        <tr>
            <td style="width: 50%;">
                <div class="row" style="padding-top: 3px;">
                    <label for="aep-matnr2"  style="margin-left: 2em; width: 10em;" class="col-form-label text-md-left">{{__('Material')}}</label>&nbsp;&nbsp;
                    <input id="aep-matnr2" style="width: 10em;" type="text" name="aep-matnr2" class="form-control" value="">
                </div>
            </td>
            <td style="width: 50%;">
                <div class="row" style="padding-top: 3px;">
                    <label for="aep-purch-price2"  style="margin-left: 1em; width: 10em;" class="col-form-label text-md-left">{{__('New purchase price')}}</label>&nbsp;&nbsp;
                    <input id="aep-purch-price2" style="width: 10em;" type="text" name="aep-purch-price2" class="form-control" value=""
                           onfocus="this.oldvalue = this.value;" oninput="ar2_purch_price2_check(event, this, 'aep');">&nbsp;
                    <input id="aep-purch-curr2" style="width: 5em;" type="text" name="aep-purch-curr2" class="form-control" value=""
                           onblur="ar2_purch_curr2_check(event, this, 'aep');">
                </div>
            </td>
        </tr>

        <tr>
            <td style="width: 50%;">
                <div class="row" style="padding-top: 3px;">
                    <label for="aep-lfdat2" style="margin-left: 2em; width: 10em;" class="col-form-label text-md-left">{{__('Delivery date')}}</label>&nbsp;&nbsp;
                    <input id="aep-lfdat2" style="width: 10em;" type="text" name="aep-lfdat2" class="form-control" value="">
                </div>
            </td>
            <td style="width: 50%;">
                <div class="row">
                    <label for="aep-defmargin-amt2"  style="margin-left: 1em; width: 10em;" class="col-form-label text-md-left">{{__('Default margin')}}</label>&nbsp;&nbsp;
                    <input id="aep-defmargin-amt2"  style="width: 10em;" type="text" name="aep-defmargin-amt2" class="form-control" value="" disabled>&nbsp;
                    <input id="aep-defmargin-curr2" style="width: 5em;" type="text" name="aep-defmargin-curr2" class="form-control" value="" disabled>
                    <label for="aep-defmargin-perc2" style="margin-left: 0.4em;" class="col-form-label text-md-left">=</label>&nbsp;&nbsp;
                    <input id="aep-defmargin-perc2" style="width: 5em;" type="text" name="aep-defmargin-perc2" class="form-control" value="" disabled>&nbsp;
                    <label for="aep-perc-defmargin2"  style="margin-left: 0.2em;" class="col-form-label text-md-left">%</label>&nbsp;&nbsp;
                </div>
            </td>
        </tr>

        <tr>
            <td style="width: 50%;">
                <div class="row" style="padding-top: 3px;">
                    <label for="aep-quantity2"  style="margin-left: 2em; width: 10em;" class="col-form-label text-md-left">{{__('Quantity')}}</label>&nbsp;&nbsp;
                    <input id="aep-quantity2" style="width: 10em;" type="text" name="aep-quantity2" class="form-control" value="">&nbsp;
                    <input id="aep-quantity-unit2" style="width: 5em;" type="text" name="aep-quantity-unit2" class="form-control" value="">
                </div>
            </td>
            <td style="width: 50%;">
                <div class="row" style="padding-top: 3px;">
                    <label for="aep-sales-price2"  style="margin-left: 1em; width: 10em;" class="col-form-label text-md-left">{{__('New sales price (1)')}}</label>&nbsp;&nbsp;
                    <input id="aep-sales-price2" style="width: 10em;" type="text" name="aep-sales-price2" class="form-control" value=""
                           onfocus="this.oldvalue = this.value;" oninput="ar2_sales_price2_check(event, this, 'aep');">&nbsp;
                    <input id="aep-sales-curr2" style="width: 5em;" type="text" name="aep-sales-curr2" class="form-control" value=""
                           onblur="ar2_sales_curr2_check(event, this, 'aep');">
                    <input type="checkbox" id="aep-choose-sales-price2" style="margin-left: 0.5em; border: none;" onchange="ar2_choose_sales_price2_checkbox(this, true, 'aep');">
                    <image id="aep-icon-save-sales-price2" style="width: 32px; margin-left: 1em;" src='/images/icons8-save-40.png'/>
                    <input type="checkbox" id="aep-save-sales-price2" onchange="ar2_choose_save_sales_price2_checkbox(this, 'aep');">
                </div>
            </td>
        </tr>

        <tr>
            <td style="width: 50%;"></td>
            <td style="width: 50%;">
                <div class="row" style="padding-top: 3px;">
                    <label for="aep-new-sales-margin-amt2"  style="margin-left: 1em; width: 10em;" class="col-form-label text-md-left">{{__('Extra sales margin')}}</label>&nbsp;&nbsp;
                    <input id="aep-new-sales-margin-amt2"  style="width: 10em;" type="text" name="aep-new-sales-margin-amt2" class="form-control" value=""
                           onfocus="this.oldvalue = this.value;" oninput="ar2_new_sales_margin_amt2_check(event, this, 'aep');">&nbsp;
                    <input id="aep-new-sales-margin-curr2" style="width: 5em;" type="text" name="aep-new-sales-margin-curr2" class="form-control" value="" disabled>
                    <label for="aep-new-sales-margin-perc2" style="margin-left: 0.4em;" class="col-form-label text-md-left">=</label>&nbsp;&nbsp;
                    <input id="aep-new-sales-margin-perc2" style="width: 5em;" type="text" name="aep-new-sales-margin-perc2" class="form-control" value=""
                           onfocus="this.oldvalue = this.value;" oninput="ar2_new_sales_margin_perc2_check(event, this, 'aep');">
                    <label for="aep-new-perc2-margin" style="margin-left: 0.2em;" class="col-form-label text-md-left">%</label>
                </div>
            </td>
        </tr>

        <tr>
            <td style="width: 50%;"></td>
            <td style="width: 50%;">
                <div class="row" style="padding-top: 3px;">
                    <label for="aep-new-sales-price2"  style="margin-left: 1em; width: 10em;" class="col-form-label text-md-left">{{__('New sales price (2)')}}</label>&nbsp;&nbsp;
                    <input id="aep-new-sales-price2" style="width: 10em;" type="text" name="aep-new-sales-price2" class="form-control" value=""
                           onfocus="this.oldvalue = this.value;" oninput="ar2_new_sales_price2_check(event, this, 'aep');">&nbsp;
                    <input id="aep-new-sales-curr2" style="width: 5em;" type="text" name="aep-new-sales-curr2" class="form-control" value="" disabled>
                    <input type="checkbox" id="aep-choose-new-sales-price2" style="margin-left: 0.5em;" onchange="ar2_choose_new_sales_price2_checkbox(this, true, 'aep');">
                    <image id="aep-icon-save-new-sales-price2" style="width: 32px; margin-left: 1em;" src='/images/icons8-save-40.png'/>
                    <input type="checkbox" id="aep-save-new-sales-price2" onchange="ar2_choose_save_new_sales_price2_checkbox(this, 'aep');">
                </div>
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
            height: 450,
            width: 930,
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
                    let sales_choice = $("#aep-choose-sales-price2").is(":checked")?1:2;
                    let sales_save = $("#aep-save-sales-price2").is(":checked")?1:0;
                    let sales_price = $("#aep-sales-price2").val().trim();
                    let sales_curr = $("#aep-sales-curr2").val().trim().toUpperCase();
                    let new_sales_amt = $("#aep-new-sales-margin-amt2").val().trim();
                    let new_sales_perc = $("#aep-new-sales-margin-perc2").val().trim();
                    let new_sales_price = $("#aep-new-sales-price2").val().trim();
                    if (lifnr.length == 0 ||
                        idnlf.length == 0 ||
                        mtext.length == 0 ||
                        matnr.length == 0 ||
                        lfdat.length == 0 ||
                        quantity.length == 0 ||
                        quantity_uom.length == 0 ||
                        purch_price.length == 0 ||
                        purch_curr.length == 0 ||
                        sales_curr.length == 0 ||
                        (
                          ((sales_choice == 1) && (sales_price.length == 0)) ||
                          ((sales_choice == 2) && (new_sales_price.length == 0))
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
                            add_edit_current_row2.cells[8].innerHTML = (sales_choice == 1 ? sales_price : new_sales_price) + " " + sales_curr;
                            let crow = $(add_edit_current_row2);
                            crow.data("sales_choice", sales_choice);
                            crow.data("sales_save", sales_save);
                            crow.data("sales_price", sales_price);
                            crow.data("sales_curr", sales_curr);
                            crow.data("new_sales_amt", new_sales_amt);
                            crow.data("new_sales_perc", new_sales_perc);
                            crow.data("new_sales_price", new_sales_price);
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
                                "<td colspan='2' style='text-align: right;'>" + (sales_choice == 1 ? sales_price : new_sales_price) + " " + sales_curr + "</td>";
                            newRow.data("sales_choice", sales_choice);
                            newRow.data("sales_save", sales_save);
                            newRow.data("sales_price", sales_price);
                            newRow.data("sales_curr", sales_curr);
                            newRow.data("new_sales_amt", new_sales_amt);
                            newRow.data("new_sales_perc", new_sales_perc);
                            newRow.data("new_sales_price", new_sales_price);
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
                at: "center+30",
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

        let sales_choice = 1;
        let sales_save = 0;
        let sales_price = "";
        let sales_curr = "";
        let new_sales_amt = "";
        let new_sales_perc = "";
        let new_sales_price = "";

        let pcurr = $("#aep-purch-curr2");
        let scurr = $("#aep-sales-curr2");

        if (caller == 1) {
            if (mode == 1) {
                add_edit_current_row2 = null;
                title = "Add new proposal";
                idnlf = $("#ar-immed-idnlf2").val();
                mtext = $("#ar-immed-mtext2").val();
                quantity  = $("#ar-immed-quantity2").val();
                quantity_unit = $("#ar-immed-quantity-unit2").val();
                pcurr.val(purch_curr = $("#ar-immed-old-purch-curr2").val()); check_currency(pcurr[0]);
                scurr.val(sales_curr = $("#ar-immed-old-sales-curr2").val()); check_currency(scurr[0]);
                purch_price = $("#ar-immed-old-purch-price2").val();
                sales_price = $("#ar-immed-old-sales-price2").val();
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
                pcurr.val(purch_curr);
                let crow = $(proposal_last_selected_line2);
                sales_choice = crow.data("sales_choice");
                sales_save = crow.data("sales_save");
                sales_price = crow.data("sales_price");
                sales_curr = crow.data("sales_curr");
                scurr.val(sales_curr);
                new_sales_amt = crow.data("new_sales_amt");
                new_sales_perc = crow.data("new_sales_perc");
                new_sales_price = crow.data("new_sales_price");
            }
        }

        $("#aep-lifnr2").val(lifnr);
        $("#aep-idnlf2").val(idnlf);
        $("#aep-mtext2").val(mtext);
        $("#aep-matnr2").val(matnr);
        $("#aep-lfdat2").val(lfdat);
        $("#aep-quantity2").val(quantity);
        $("#aep-quantity-unit2").val(quantity_unit);

        $("#aep-old-purch-price2").val($("#ar-immed-old-purch-price2").val());
        $("#aep-old-purch-curr2").val($("#ar-immed-old-purch-curr2").val());
        $("#aep-old-sales-price2").val($("#ar-immed-old-sales-price2").val());
        $("#aep-old-sales-curr2").val($("#ar-immed-old-sales-curr2").val());
        $("#aep-old-sales-margin-amt2").val($("#ar-immed-old-sales-margin-amt2").val());
        $("#aep-old-sales-margin-amt-curr2").val($("#ar-immed-old-sales-margin-amt-curr2").val());
        $("#aep-old-sales-margin-perc2").val($("#ar-immed-old-sales-margin-perc2").val());

        $("#aep-defmargin-amt2").val($("#ar-immed-defmargin-amt2").val());
        $("#aep-defmargin-perc2").val($("#ar-immed-defmargin-perc2").val());
        $("#aep-defmargin-curr2").val(sales_curr);

        $("#aep-purch-price2").val(purch_price);
        $("#aep-sales-price2").val(sales_price);
        $("#aep-new-sales-price2").val(new_sales_price);
        $("#aep-new-sales-curr2").val(sales_curr);
        $("#aep-new-sales-margin-amt2").val(new_sales_amt);
        $("#aep-new-sales-margin-perc2").val(new_sales_perc);
        $("#aep-new-sales-margin-curr2").val(sales_curr);

        $("#add-edit-proposal2").dialog('option', 'title', title);
        $('#add-edit-proposal2').css('overflow', 'hidden');

        $("#aep-choose-sales-price2").prop("checked", sales_choice == 1);
        $("#aep-choose-sales-price2").prop("disabled", false);
        $("#aep-choose-new-sales-price2").prop("checked", sales_choice == 2);
        $("#aep-choose-new-sales-price2").prop("disabled", new_sales_price.trim().length == 0);
        if (sales_choice == 1) {
            $("#aep-save-sales-price2").show();
            $("#aep-save-sales-price2").prop("checked", sales_save == 1);
            $("#aep-icon-save-sales-price2").show();
            $("#aep-icon-save-new-sales-price2").hide();
            $("#aep-save-new-sales-price2").hide();
            $("#aep-save-new-sales-price2").prop("checked", sales_save == 1);
        } else {
            $("#aep-save-sales-price2").hide();
            $("#aep-save-sales-price2").prop("checked", sales_save == 1);
            $("#aep-icon-save-sales-price2").hide();
            $("#aep-icon-save-new-sales-price2").show();
            $("#aep-save-new-sales-price2").show();
            $("#aep-save-new-sales-price2").prop("checked", sales_save == 1);
        }

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
        read_inforecords(caller + 10, lifnr, idnlf);
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
        read_zpretrecords(caller + 10, lifnr, idnlf);
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

    function arf_lifnr2_check(ev, _this, dlg) {
        if (ev.type == "keypress") {
            if (ev.which != 13) return;
            ev.preventDefault();
        }
        let new_margin = 0;
        let new_margin_d = "<>";
        new_margin = get_sales_margin2(_ar_itemdata2.lifnr, _ar_itemdata2.mfrnr, null);
        if (new_margin.length > 0) {
            new_margin = parseFloat(new_margin);
            new_margin_d = new_margin.toFixed(2);
        } else new_margin = 0;
        if (new_margin_d == $("#"+dlg+"-defmargin-perc2").val()) return;
        $("#"+dlg+"-defmargin-perc2").val(new_margin_d);
        var original_color = $("#"+dlg+"-defmargin-perc2").css('border-left-color');
        $("#"+dlg+"-defmargin-perc2")
            .animate({borderColor:'red'}, 200, 'linear')
            .delay(200)
            .animate({borderColor:original_color}, 200, 'easeOutCirc')
            .delay(200)
            .animate({borderColor:'red'}, 200, 'linear')
            .delay(200)
            .animate({borderColor:original_color}, 200, 'easeOutCirc');
    }

    function ar2_purch_price2_check(event, _this, dlg) {
        let margin = $("#"+dlg+"-defmargin-perc2").val().trim();
        if (margin == "<>") margin = 0; else margin = parseFloat(margin);
        let pprice = $("#"+dlg+"-purch-price2").val().trim();
        if (pprice.length == 0) {
            $("#"+dlg+"-sales-price2").val("");
            $("#"+dlg+"-defmargin-amt2").val("0.00");
            _this.oldvalue = "";
            return;
        }
        if (!$.isNumeric(pprice)) {
            $("#"+dlg+"-purch-price2").val(_this.oldvalue);
            return;
        }
        if (margin == 0) margin = parseFloat($("#"+dlg+"-old-sales-margin-perc2").val());
        pprice = parseFloat(pprice);
        _this.oldvalue = pprice.toFixed(2);
        let cpprice = cvfx(pprice, $("#"+dlg+"-purch-curr2").val(), $("#"+dlg+"-sales-curr2").val());
        let sprice = (cpprice * (1 + margin / 100));
        $("#"+dlg+"-sales-price2").val(sprice.toFixed(2));
        $("#"+dlg+"-defmargin-amt2").val((sprice - cpprice).toFixed(2));
        $("#"+dlg+"-new-sales-margin-amt2").val("");
        $("#"+dlg+"-new-sales-margin-perc2").val("");
        $("#"+dlg+"-new-sales-price2").val("");
        $("#"+dlg+"-choose-sales-price2").prop("checked", true);
        ar2_choose_sales_price2_checkbox(_this, false, dlg);
        $("#"+dlg+"-choose-sales-price2").prop("disabled", true);
        $("#"+dlg+"-choose-new-sales-price2").prop("disabled", true);
    }

    function ar2_purch_curr2_check(event, _this, dlg) {
        if (check_currency(_this))
            ar2_purch_price2_check(event, $("#"+dlg+"-purch-price2")[0], dlg);
    }

    function ar2_sales_price2_check(event, _this, dlg) {
        let margin = $("#"+dlg+"-defmargin-perc2").val().trim();
        if (margin == "<>") margin = 0; else margin = parseFloat(margin);
        if (margin == 0) margin = parseFloat($("#"+dlg+"-old-sales-margin-perc2").val());
        let pprice = $("#"+dlg+"-purch-price2").val().trim();
        let cpprice = 0;
        if (pprice.length == 0 || !$.isNumeric(pprice)) pprice = 0;
        else {
            pprice = parseFloat(pprice);
            cpprice = cvfx(pprice, $("#"+dlg+"-purch-curr2").val(), $("#"+dlg+"-sales-curr2").val());
        }
        let sprice = $("#"+dlg+"-sales-price2").val().trim();
        if (sprice.length == 0) {
            sprice = (cpprice * (1 + margin / 100));
            $("#"+dlg+"-sales-price2").val(sprice.toFixed(2));
            _this.oldvalue = sprice.toFixed(2);
            $("#"+dlg+"-defmargin-amt2").val((sprice - cpprice).toFixed(2));
            return;
        } else {
            if (!$.isNumeric(sprice)) {
                $("#"+dlg+"-sales-price2").val(_this.oldvalue);
                $("#"+dlg+"-defmargin-amt2").val("0.00");
                return;
            }
        }
        sprice = parseFloat(sprice);
        $("#"+dlg+"-defmargin-amt2").val((sprice - cpprice).toFixed(2));
        _this.oldvalue = sprice.toFixed(2);
        $("#"+dlg+"-new-sales-margin-amt2").val("");
        $("#"+dlg+"-new-sales-margin-perc2").val("");
        $("#"+dlg+"-new-sales-price2").val("");
        $("#"+dlg+"-choose-sales-price2").prop("checked", true);
        ar2_choose_sales_price2_checkbox(_this, false, dlg);
        $("#"+dlg+"-choose-sales-price2").prop("disabled", true);
        $("#"+dlg+"-choose-new-sales-price2").prop("disabled", true);
    }

    function ar2_new_sales_price2_check(event, _this, dlg) {
        let nsprice = $("#"+dlg+"-new-sales-price2").val().trim();
        if (nsprice.length == 0) {
            let amt2 = $("#"+dlg+"-new-sales-margin-amt2").val().trim();
            if (amt2.length == 0) {
                $("#"+dlg+"-new-sales-margin-amt2").val("");
                $("#"+dlg+"-new-sales-margin-perc2").val("");
                $("#"+dlg+"-new-sales-price2").val("");
                $("#"+dlg+"-choose-sales-price2").prop("checked", true);
                $("#"+dlg+"-choose-sales-price2").prop("disabled", true);
                ar2_choose_sales_price2_checkbox(_this, true, dlg);
                $("#"+dlg+"-choose-new-sales-price2").prop("disabled", true);
                return;
            }
            let sprice = $("#"+dlg+"-sales-price2").val().trim();
            nsprice = parseFloat(sprice) + parseFloat(amt2);
            $("#"+dlg+"-new-sales-price2").val(nsprice.toFixed(2));
        } else {
            if (!$.isNumeric(nsprice)) {
                $("#"+dlg+"-new-sales-price2").val(_this.oldvalue);
                return;
            }
            nsprice = parseFloat(nsprice);
        }
        _this.oldvalue = nsprice.toFixed(2);
        $("#"+dlg+"-choose-new-sales-price2").prop("checked", true);
        ar2_choose_new_sales_price2_checkbox(_this, false, dlg);
        $("#"+dlg+"-choose-sales-price2").prop("disabled", false);
        $("#"+dlg+"-choose-new-sales-price2").prop("disabled", false);
    }

    function ar2_new_sales_margin_amt2_check(event, _this, dlg) {
        let __this = $(_this);
        let newamt = __this.val().trim();
        if (newamt.length == 0) {
            $("#"+dlg+"-new-sales-margin-perc2").val("");
            __this.val("");
            _this.oldvalue = "";
            $("#"+dlg+"-choose-sales-price2").prop("checked", true);
            ar2_choose_sales_price2_checkbox(_this, true, dlg);
            $("#"+dlg+"-new-sales-price2").val("");
            $("#"+dlg+"-choose-sales-price2").prop("disabled", true);
            $("#"+dlg+"-choose-new-sales-price2").prop("disabled", true);
        }
        if (!$.isNumeric(newamt) && newamt != "-") {
            __this.val(_this.oldvalue);
            return;
        }
        if (newamt == "-") newperc = 0; else newamt = parseFloat(newamt);
        _this.oldvalue = newamt.toFixed(2);
        let sprice = $("#"+dlg+"-sales-price2").val().trim();
        if (sprice.length == 0) sprice = "0";
        sprice = parseFloat(sprice);
        let newperc = 0;
        if (sprice != 0) newperc = (newamt / sprice * 100);
        newperc = newperc.toFixed(2);
        $("#"+dlg+"-new-sales-margin-perc2").val(newperc);
        $("#"+dlg+"-new-sales-margin-perc2").oldvalue = newperc;
        sprice += newamt;
        $("#"+dlg+"-new-sales-price2").val(sprice.toFixed(2));
        $("#"+dlg+"-choose-new-sales-price2").prop("checked", true);
        ar2_choose_new_sales_price2_checkbox(_this, false, dlg);
        $("#"+dlg+"-choose-sales-price2").prop("disabled", false);
        $("#"+dlg+"-choose-new-sales-price2").prop("disabled", false);
    }

    function ar2_new_sales_margin_perc2_check(event, _this, dlg) {
        let __this = $(_this);
        let newperc = __this.val().trim();
        if (newperc.length == 0) {
            $("#"+dlg+"-new-sales-margin-amt2").val("");
            __this.val("");
            _this.oldvalue = "";
            $("#"+dlg+"-choose-sales-price2").prop("checked", true);
            ar2_choose_sales_price2_checkbox(_this, true, dlg);
            $("#"+dlg+"-new-sales-price2").val("");
            $("#"+dlg+"-choose-sales-price2").prop("disabled", true);
            $("#"+dlg+"-choose-new-sales-price2").prop("disabled", true);
        }
        if (!$.isNumeric(newperc) && newperc != "-") {
            __this.val(_this.oldvalue);
            return;
        }
        if (newperc == "-") newperc = 0; else newperc = parseFloat(newperc);
        _this.oldvalue = newperc.toFixed(2);
        let sprice = $("#"+dlg+"-sales-price2").val().trim();
        if (sprice.length == 0) sprice = "0";
        sprice = parseFloat(sprice);
        let newamt = sprice * (100 + newperc) / 100;
        $("#"+dlg+"-new-sales-margin-amt2").val((newamt - sprice).toFixed(2));
        $("#"+dlg+"-new-sales-margin-amt2").oldvalue = (newamt - sprice).toFixed(2);
        $("#"+dlg+"-new-sales-price2").val(newamt.toFixed(2));
        $("#"+dlg+"-choose-new-sales-price2").prop("checked", true);
        ar2_choose_new_sales_price2_checkbox(_this, false, dlg);
        $("#"+dlg+"-choose-sales-price2").prop("disabled", false);
        $("#"+dlg+"-choose-new-sales-price2").prop("disabled", false);
    }

    function ar2_sales_curr2_check(event, _this, dlg) {
        if (check_currency(_this)) {
            ar2_sales_price2_check(event, $("#"+dlg+"-sales-price2")[0], dlg);
            $("#"+dlg+"-new-sales-margin-curr2").val(_this.value);
            $("#"+dlg+"-new-sales-curr2").val(_this.value);
            $("#"+dlg+"-defmargin-curr2").val(_this.value);
            let margin = $("#"+dlg+"-defmargin-perc2").val().trim();
            if (margin == "<>") margin = 0; else margin = parseFloat(margin);
            if (margin == 0) margin = parseFloat($("#"+dlg+"-old-sales-margin-perc2").val());
            let pprice = $("#"+dlg+"-purch-price2").val().trim();
            let cpprice = 0;
            if (pprice.length == 0 || !$.isNumeric(pprice)) pprice = 0;
            else {
                pprice = parseFloat(pprice);
                cpprice = cvfx(pprice, $("#"+dlg+"-purch-curr2").val(), $("#"+dlg+"-sales-curr2").val());
            }
            $("#"+dlg+"-defmargin-amt2").val((cpprice * ( 1 + margin) / 100).toFixed(2));
        }
    }

    function ar2_choose_sales_price2_checkbox(_this, goto, dlg) {
        if ($("#"+dlg+"-choose-sales-price2").is(":checked")) {
            $("#"+dlg+"-choose-new-sales-price2").prop("checked", false);
            $("#"+dlg+"-icon-save-new-sales-price2").hide();
            $("#"+dlg+"-save-new-sales-price2").hide();
            $("#"+dlg+"-icon-save-sales-price2").show();
            $("#"+dlg+"-save-sales-price2").show();
            if (goto) {
                $("#"+dlg+"-sales-price2").focus();
                $("#"+dlg+"-sales-price2").select();
            }
        } else {
            $("#"+dlg+"-choose-new-sales-price2").prop("checked", true);
            ar2_choose_new_sales_price2_checkbox(_this, goto, dlg);
        }
    }

    function ar2_choose_new_sales_price2_checkbox(_this, goto, dlg) {
        if ($("#"+dlg+"-choose-new-sales-price2").is(":checked")) {
            $("#"+dlg+"-choose-sales-price2").prop("checked", false);
            $("#"+dlg+"-icon-save-new-sales-price2").show();
            $("#"+dlg+"-save-new-sales-price2").show();
            $("#"+dlg+"-icon-save-sales-price2").hide();
            $("#"+dlg+"-save-sales-price2").hide();
            if (goto) {
                $("#"+dlg+"-new-sales-price2").focus();
                $("#"+dlg+"-new-sales-price2").select();
            }
        } else {
            $("#"+dlg+"-choose-sales-price2").prop("checked", true);
            ar2_choose_sales_price2_checkbox(_this, goto, dlg);
        }
    }

    function ar2_choose_save_sales_price2_checkbox(_this, dlg) {
        $("#"+dlg+"-save-new-sales-price2").prop("checked",
            $("#"+dlg+"-save-sales-price2").is(":checked"));
    }

    function ar2_choose_save_new_sales_price2_checkbox(_this, dlg) {
        $("#"+dlg+"-save-sales-price2").prop("checked",
            $("#"+dlg+"-save-new-sales-price2").is(":checked"));
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
            let fx = {curr: curr, fxrate: parseFloat(_dataFX)};
            fxtable.push(fx);
            return fx.fxrate;
        }
        return 0;
    }

    function cvfx(amt, fromcurr, tocurr) {
        fromcurr = fromcurr.trim().toUpperCase();
        tocurr = tocurr.trim().toUpperCase();
        if (fromcurr == tocurr) return amt;
        fxf = get_fx_rate(fromcurr);
        fxt = get_fx_rate(tocurr);
        if (fxf == 0 || fxt == 0) return amt;
        amt = parseFloat(amt);
        return amt * fxf / fxt;
    }

    function check_currency(_this) {
        let curr = _this.value.trim().toUpperCase();
        if (curr != "RON" && curr != "EUR" && curr != "USD"
                          && curr != "CHF" && curr != "GBP"
                          && curr != "HUF" && curr != "BGN") {
            $(_this).val(_this.oldvalue);
            return false;
        }
        if (_this.oldvalue == curr) return false;
        _this.oldvalue = curr;
        $(_this).val(curr);
        return true;
    }

</script>