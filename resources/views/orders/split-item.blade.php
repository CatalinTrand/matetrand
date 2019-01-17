<div id="split-item-dialog" title="{{__('Split item')}}">
    <input type="checkbox" id="require_sr_sp_si_approval" onchange="si_switchMode(this); return false;">
    <label id="label_require_sr_sp_si_approval" for="require_sr_sp_si_approval">{{__("Require SR approval")}}</label>
    <br>
    <div id="si-splits-approval" width="95%" style="margin-right: 0.5rem;">
        <div id="si-splits-table-si-1" style="overflow-y: scroll; min-height: 17rem;" width="95%">
            <table id="splits-table-si-1" class="table-striped" width="100%">
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
                onclick="add_edit_split_split(1);return false;">{{__('New item')}}</button>
        <button type="button" style="float:left; margin-right: 1rem; " class="ui-button ui-corner-all ui-widget"
                onclick="add_edit_split_split(2);return false;">{{__('Edit item')}}</button>
        <button type="button" style="float:left; margin-right: 1rem; " class="ui-button ui-corner-all ui-widget"
                onclick="delete_split();return false;">{{__('Delete item')}}</button>
    </div>
    <i id="new_acc_rej_msg_si" style="color: red"></i>
    <br>
</div>

<script>
    $(document).ready(function () {
        $("#si-splits-approval").attr("style", "display: block; margin-right: 0.5rem;");
        $("#split-item-ok-button").text("{{__('Send split')}}");
    });
</script>

<script>

    var siDialog, siForm, _sp_si_type, _sp_si_this, _sp_si_itemdata;
    var split_last_sp_si_selected_line;
    var split_last_sp_si_color;

    $(function () {
        siDialog = $("#split-item-dialog").dialog({
            autoOpen: false,
            height: 420,
            width: 920,
            modal: true,
            buttons: [
                {
                    text: '{{__("Apply split")}}',
                    id: "split-item-ok-button",
                    click: function () {
                        var result = new Object();
                        result.type = 'S';
                        result.itemdata = _sp_si_itemdata;
                        let tablerows = $('#splits-table-si-1 tr');
                        let n = tablerows.length;
                        if (n < 2) return;
                        result.items = [];
                        for (let i = 1; i < n; i++) {
                            row = tablerows[i];
                            let item = new Object();
                            item.lifnr = row.cells[0].textContent.trim();
                            item.idnlf = row.cells[2].textContent.trim();
                            item.mtext = row.cells[3].textContent.trim();
                            item.matnr = row.cells[4].textContent.trim();
                            item.lfdat = row.cells[5].textContent.trim();
                            item.quantity = row.cells[6].textContent.split(" ")[0];
                            item.quantity_unit = row.cells[6].textContent.split(" ")[1];
                            item.purch_price = row.cells[7].textContent.split(" ")[0];
                            item.purch_curr = row.cells[7].textContent.split(" ")[1];
                            item.sales_price = row.cells[8].textContent.split(" ")[0];
                            item.sales_curr = row.cells[8].textContent.split(" ")[1];
                            @if (\Illuminate\Support\Facades\Auth::user()->role != "Furnizor")
                            if (_sp_si_itemdata.vbeln != "!REPLENISH") {
                                if ((item.sales_price.trim().length == 0) || (item.sales_curr.trim().length == 0)) {
                                    alert('{{__("There are missing sales prices - please fill them all before applying/proposing the split")}}');
                                    return;
                                }
                            }
                            @endif
                            result.items.push(item);
                        }

                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        jQuery.ajaxSetup({async: false});
                        var _dataPP, _statusPP;
                        if ($("#require_sr_sp_si_approval").is(":checked")) {
                            $.get("webservice/processproposal",
                                {
                                    proposal: JSON.stringify(result)
                                },
                                function (data, status) {
                                    _dataPP = data;
                                    _statusPP = status;
                                });
                        } else {
                            $.get("webservice/processsplit",
                                {
                                    split: JSON.stringify(result)
                                },
                                function (data, status) {
                                    _dataPP = data;
                                    _statusPP = status;
                                });
                        }
                        jQuery.ajaxSetup({async: true});
                        if (_dataPP != null && _dataPP != undefined && _dataPP.trim().length != 0) {
                            alert(_dataPP);
                            return;
                        }
                        siDialog.dialog("close");
                        location.reload();
                    }
                },
                {
                    text: '{{__("Cancel")}}',
                    click: function () {
                        siDialog.dialog("close");
                    }
                }
            ],
            close: function () {
                // siForm[0].reset();
            },
            open: function () {
                @if (\Illuminate\Support\Facades\Auth::user()->role == "Furnizor")
                $("#label_require_sr_sp_si_approval").hide();
                $("#require_sr_sp_si_approval").hide();
                $("#require_sr_sp_si_approval").prop("checked", true);
                $("#split-item-ok-button").text("{{__('Send split proposal')}}");
                @else
                if (_sp_si_itemdata.vbeln == "!REPLENISH") {
                    $("#require_sr_sp_si_approval").hide();
                    $("#label_require_sr_sp_si_approval").hide();
                } else {
                    $("#require_sr_sp_si_approval").show();
                    $("#label_require_sr_sp_si_approval").show();
                }
                $("#split-item-ok-button").text("{{__('Apply split')}}");
                @endif
                split_last_sp_si_selected_line = null;
            },
            position: {
                my: "center",
                at: "center",
                of: window
            }
        });
        siForm = siDialog.find("form").on("submit", function (event) {
            event.preventDefault();
        });
    });

    function split_dialog(type, this0, ebeln, ebelp, title) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajaxSetup({async: false});
        var _dataIR, _statusIR;
        $.get("webservice/readpitem",
            {
                order: ebeln,
                item: ebelp
            },
            function (data, status) {
                _dataIR = data;
                _statusIR = status;
            }, "json");
        jQuery.ajaxSetup({async: true});
        if (_statusIR != "success") return;
        $("#new_rej_msg").text("");
        $("#require_sr_sp_si_approval").prop("checked", true);
        $("#split-item-ok-button").text("{{__('Send split proposal')}}");
        $("#splits-table-si-1").find("tr:gt(0)").remove();
        $("#si-splits-approval").attr("style", "display: block; margin-right: 0.5rem;");
        _sp_si_type = type;
        _sp_si_this = this0;
        _sp_si_itemdata = _dataIR;

        if (type == '2') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajaxSetup({async: false});
            var _data2, _status2;
            $.post("webservice/readproposals",
                {
                    type: "S",
                    ebeln: ebeln,
                    ebelp: ebelp
                },
                function (data, status) {
                    _data2 = data;
                    _status2 = status;
                }, "json");
            jQuery.ajaxSetup({async: true});
            if (_data2 == undefined || _data2.length == 0) return;

            if (_data2.length > 0) {
                let table = $("#splits-table-si-1");
                for (let i = 0; i < _data2.length; i++) {
                    var newRow = $("<tr style='height: 1.2rem;' " +
                        "id='SPLIT_" + _data2[i].ebeln + '_' + _data2[i].ebelp + '_' + _data2[i].cdate.substring(0, 10) + _data2[i].cdate.substring(11, 8) + '_' + _data2[i].pos +
                        "' onclick='split_selected(this);return false;'>");
                    var cols = "<td>" + conv_exit_alpha_output(_data2[i].lifnr) + "</td>" +
                        "<td>" + _data2[i].lifnr_name + "</td>" +
                        "<td>" + _data2[i].idnlf + "</td>" +
                        "<td>" + _data2[i].mtext + "</td>" +
                        "<td>" + _data2[i].matnr + "</td>" +
                        "<td>" + _data2[i].lfdat.substring(0, 10) + "</td>" +
                        "<td colspan='2' style='text-align: right;'>" + _data2[i].qty + " " + _data2[i].qty_uom + "</td>" +
                        "<td colspan='2' style='text-align: right;'>" + _data2[i].purch_price + " " + _data2[i].purch_curr + "</td>" +
                        "<td colspan='2' style='text-align: right;'>" + _data2[i].sales_price + " " + _data2[i].sales_curr + "</td>";
                    newRow.append(cols);
                    table.append(newRow);
                }
            }
        }

        siDialog.dialog("open");
    }

    function si_switchMode(_this) {
        if ($(_this).is(":checked")) {
            $("#split-item-ok-button").text("{{__('Send split proposal')}}");
        } else {
            $("#split-item-ok-button").text("{{__('Apply split')}}");
        }
    }


</script>

<div id="add-edit-split" title="Add/edit spliit">
    <div class="row">
        <label for="aes-lifnr" class="col-md-3 col-form-label text-md-left">{{__('Vendor')}}</label>&nbsp;&nbsp;
        <input id="aes-lifnr" type="text" name="aes-lifnr" class="form-control col-md-3" value="">
    </div>
    <div class="row" style="padding-top: 3px;">
        <label for="aes-idnlf" class="col-md-3 col-form-label text-md-left">{{__('Vendor mat.')}}</label>&nbsp;&nbsp;
        <input id="aes-idnlf" type="text" name="aes-idnlf" class="form-control col-md-3" value="">
    </div>
    <div class="row" style="padding-top: 3px;">
        <label for="aes-mtext" class="col-md-3 col-form-label text-md-left">{{__('Material description')}}</label>&nbsp;&nbsp;
        <input id="aes-mtext" type="text" name="aes-mtext" class="form-control col-md-6" value="">
    </div>
    <div class="row" style="padding-top: 3px;">
        <label id="label-aes-matnr" for="aes-matnr" class="col-md-3 col-form-label text-md-left">{{__('Material')}}</label>&nbsp;&nbsp;
        <input id="aes-matnr" type="text" name="aes-matnr" class="form-control col-md-3" value="">
    </div>
    <div class="row" style="padding-top: 3px;">
        <label for="aes-lfdat" class="col-md-3 col-form-label text-md-left">{{__('Delivery date')}}</label>&nbsp;&nbsp;
        <input id="aes-lfdat" type="text" name="aes-lfdat" class="form-control col-md-3" value="">
    </div>
    <div class="row" style="padding-top: 3px;">
        <label for="aes-quantity" class="col-md-3 col-form-label text-md-left">{{__('Quantity')}}</label>&nbsp;&nbsp;
        <input id="aes-quantity" type="text" name="aes-quantity" class="form-control col-md-4" value="">&nbsp;
        <input id="aes-quantity-unit" type="text" name="aes-quantity-unit" class="form-control col-md-1"
               value="">
    </div>
    <div class="row" style="padding-top: 3px;">
        <label for="aes-purch-price" class="col-md-3 col-form-label text-md-left">{{__('Purchase price')}}</label>&nbsp;&nbsp;
        <input id="aes-purch-price" type="text" name="aes-purch-price" class="form-control col-md-4" value="">&nbsp;
        <input id="aes-purch-curr" type="text" name="aes-purch-curr" class="form-control col-md-1" value="">
    </div>
    <div class="row" style="padding-top: 3px;">
        <label id="label-aes-sales-price" for="aes-sales-price" class="col-md-3 col-form-label text-md-left">{{__('Sales price')}}</label>&nbsp;&nbsp;
        <input id="aes-sales-price" type="text" name="aes-sales-price" class="form-control col-md-4" value="">&nbsp;
        <input id="aes-sales-curr" type="text" name="aes-sales-curr" class="form-control col-md-1" value="">
    </div>
    <div class="row" style="padding-top: 0.5rem;">
        <button type="button" style="float:left; margin-left: 1rem; margin-right: 1rem; "
                class="ui-button ui-corner-all ui-widget"
                onclick="get_infnr_si();return false;">{{__('Inforecord')}}</button>
        @if (\Illuminate\Support\Facades\Auth::user()->role != "Furnizor")
        <button type="button" style="float:left; margin-right: 1rem; " class="ui-button ui-corner-all ui-widget"
                onclick="get_zpret_si();return false;">ZPRET
        @endif
        </button>
    </div>
</div>

<script>
    var add_edit_split_Dialog, add_edit_split_Form, add_edit_split_current_row, add_edit_split_caller;

    $(function () {
        add_edit_split_Dialog = $("#add-edit-split").dialog({
            autoOpen: false,
            height: 420,
            width: 640,
            modal: true,
            buttons: {
                Save: function () {
                    let lifnr = $("#aes-lifnr").val().trim();
                    let idnlf = $("#aes-idnlf").val().trim();
                    let mtext = $("#aes-mtext").val().trim();
                    // let matnr = $("#aes-matnr").val().trim();
                    let matnr = "PA-99";
                    let lfdat = $("#aes-lfdat").val().trim();
                    let quantity = $("#aes-quantity").val().trim();
                    let quantity_uom = $("#aes-quantity-unit").val().trim().toUpperCase();
                    let purch_price = $("#aes-purch-price").val().trim();
                    let purch_curr = $("#aes-purch-curr").val().trim().toUpperCase();
                    let sales_price = $("#aes-sales-price").val().trim();
                    let sales_curr = $("#aes-sales-curr").val().trim().toUpperCase();
                    if (lifnr.length == 0 ||
                        idnlf.length == 0 ||
                        mtext.length == 0 ||
                        matnr.length == 0 ||
                        lfdat.length == 0 ||
                        purch_price.length == 0 ||
                        purch_curr.length == 0
                        @if (\Illuminate\Support\Facades\Auth::user()->role != "Furnizor")
                        ||
                        ((_sp_si_itemdata.vbeln != "!REPLENISH") &&
                         (sales_price.length == 0 || sales_curr.length == 0))
                        @endif
                        ) return;
                    if (add_edit_split_caller == 2) {
                        if (add_edit_split_current_row != null) {
                            add_edit_split_current_row.cells[0].innerHTML = lifnr;
                            add_edit_split_current_row.cells[1].innerHTML = readLifnrName(lifnr);
                            add_edit_split_current_row.cells[2].innerHTML = idnlf;
                            add_edit_split_current_row.cells[3].innerHTML = mtext;
                            add_edit_split_current_row.cells[4].innerHTML = matnr;
                            add_edit_split_current_row.cells[5].innerHTML = lfdat;
                            add_edit_split_current_row.cells[6].innerHTML = quantity + " " + quantity_uom;
                            add_edit_split_current_row.cells[7].innerHTML = purch_price + " " + purch_curr;
                            add_edit_split_current_row.cells[8].innerHTML = sales_price + " " + sales_curr;
                            add_edit_split_Dialog.dialog("close");
                        }
                    } else {
                        var newRow = $("<tr style='height: 1.2rem;' onclick='split_selected(this);return false;'>");
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
                        $("#splits-table-si-1").append(newRow);
                        add_edit_split_Dialog.dialog("close");
                    }
                },
                Cancel: function () {
                    add_edit_split_Dialog.dialog("close");
                }
            },
            open: function () {
                @if (\Illuminate\Support\Facades\Auth::user()->role != "Furnizor")
                    if (_sp_si_itemdata.vbeln == "!REPLENISH") {
                        $("#label-aes-sales-price").hide();
                        $("#aes-sales-price").hide();
                        $("#aes-sales-curr").hide();
                    } else {
                        $("#label-aes-sales-price").show();
                        $("#aes-sales-price").show();
                        $("#aes-sales-curr").show();
                    }
                    $("#label-aes-matnr").show();
                    $("#aes-matnr").show();
                    $("#aes-lifnr").val(_sp_si_itemdata.lifnr);
                    $("#aes-lifnr").prop("disabled", false);
                @else
                    $("#label-aes-matnr").hide();
                    $("#aes-matnr").hide();
                    $("#label-aes-sales-price").hide();
                    $("#aes-sales-price").hide();
                    $("#aes-sales-curr").hide();
                    $("#aes-lifnr").val(conv_exit_alpha_output(_sp_si_itemdata.lifnr));
                    $("#aes-lifnr").prop("disabled", true);
                @endif
            },
            position: {
                my: "center",
                at: "center",
                of: window
            }
        });
        add_edit_split_Form = add_edit_split_Dialog.find("form").on("submit", function (event) {
            event.preventDefault();
        });
        $("#aes-lfdat").datepicker({dateFormat: "yy-mm-dd"});
    });

    function add_edit_split_split(caller) {

        add_edit_split_caller = caller;
        let title = "";
        let lifnr = "";
        let idnlf = "";
        let mtext = "";
        let matnr = "";
        let lfdat = "";
        let quantity = _sp_si_itemdata.qty;
        let quantity_unit = _sp_si_itemdata.qty_uom;
        let purch_price = "";
        let purch_curr = "";
        let sales_price = "";
        let sales_curr = "";

        if (caller == 1) {
            add_edit_split_current_row = null;
            title = "Add new item split";
        } else {
            if (split_last_sp_si_selected_line == null || split_last_sp_si_selected_line == undefined) return;
            title = "Edit existing item split";
            let current_row = split_last_sp_si_selected_line;
            add_edit_split_current_row = split_last_sp_si_selected_line;
            lifnr = current_row.cells[0].innerHTML;
            idnlf = current_row.cells[2].innerHTML;
            mtext = current_row.cells[3].innerHTML;
            matnr = current_row.cells[4].innerHTML;
            lfdat = current_row.cells[5].innerHTML;
            purch_price = current_row.cells[7].innerHTML.split(" ")[0];
            purch_curr = current_row.cells[7].innerHTML.split(" ")[1];
            sales_price = current_row.cells[8].innerHTML.split(" ")[0];
            sales_curr = current_row.cells[8].innerHTML.split(" ")[1];
        }

        $("#aes-lifnr").val(lifnr);
        $("#aes-idnlf").val(idnlf);
        $("#aes-mtext").val(mtext);
        $("#aes-matnr").val(matnr);
        $("#aes-lfdat").val(lfdat);
        $("#aes-quantity").val(quantity);
        $("#aes-quantity-unit").val(quantity_unit);
        $("#aes-purch-price").val(purch_price);
        $("#aes-purch-curr").val(purch_curr);
        $("#aes-sales-price").val(sales_price);
        $("#aes-sales-curr").val(sales_curr);

        $("#add-edit-split").dialog('option', 'title', title);
        $('#add-edit-split').css('overflow', 'hidden');
        add_edit_split_Dialog.dialog("open");
    }

</script>

<div id="select-split-dialog" title="Select a split">
    <p id="sel-split-initial-text" type="text" style="margin-top: 0.5rem; font-weight: bold; color: darkred;" name="sel-split-initial-text" width="95%" value=""></p>
    <div id="sel-split-container" width="95%" style="margin-right: 0.5rem; overflow-y: scroll; min-height: 17rem;">
        <table id="sel-split-table" class="table-striped" width="100%">
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
    <i id="sel-split-msg" style="color: red"></i>
    <br>
</div>

<script>

    var select_split_dialog;
    var _sp_si_ebeln, _sp_si_ebelp, _sp_si_cdate;
    $(function () {
        select_split_dialog = $("#select-split-dialog").dialog({
            autoOpen: false,
            height: 400,
            width: 960,
            modal: true,
            buttons: {
                Accept: function () {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    jQuery.ajaxSetup({async: false});
                    var _data, _status;
                    $.get("webservice/acceptsplit",
                        {
                            ebeln: _sp_si_ebeln,
                            ebelp: _sp_si_ebelp,
                            cdate: _sp_si_cdate,
                        },
                        function (data, status) {
                            _data = data;
                            _status = status;
                        });
                    jQuery.ajaxSetup({async: true});
                    if (_data != null && _data != undefined && _data.trim().length != 0) {
                        alert(_data);
                        return;
                    }
                    select_split_dialog.dialog("close");
                    location.reload();
                },
                Reject: function () {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    jQuery.ajaxSetup({async: false});
                    var _data, _status;
                    $.get("webservice/rejectsplit",
                        {
                            ebeln: _sp_si_ebeln,
                            ebelp: _sp_si_ebelp,
                            cdate: _sp_si_cdate
                        },
                        function (data, status) {
                            _data = data;
                            _status = status;
                        });
                    jQuery.ajaxSetup({async: true});
                    if (_data != null && _data != undefined && _data.trim().length != 0) {
                        alert(_data);
                        return;
                    }
                    select_split_dialog.dialog("close");
                    location.reload();
                },
                Cancel: function () {
                    select_split_dialog.dialog("close");
                }
            },
            close: function () {
                // select_split_form[0].reset();
            },
            open: function () {
                $('#select-split-dialog').css('overflow', 'hidden');
                split_last_sp_si_selected_line = null;
            },
            position: {
                my: "center",
                at: "center",
                of: window
            }
        });
    });

    function select_split(type, this0, itemdata, title, initial_text, _data) {

        $("#sel-split-msg").text("");
        $("#sel-split-initial-text").text(initial_text);
        $("#sel-split-table").find("tr:gt(0)").remove();
        $("#select-split-dialog").dialog('option', 'title', title);
        _sp_si_type = type;
        _sp_si_this = this0;
        _sp_si_itemdata = itemdata;

        if (_data.length > 0) {
            let table = $("#sel-split-table");
            for (let i = 0; i < _data.length; i++) {
                if (i == 0) {
                    _sp_si_ebeln = _data[i].ebeln;
                    _sp_si_ebelp = _data[i].ebelp;
                    _sp_si_cdate = _data[i].cdate;
                }
                var newRow = $("<tr style='height: 1.2rem;' id='SPLIT_" + _data[i].ebeln + '_' + _data[i].ebelp + '_' + _data[i].cdate.substring(0, 10) + _data[i].cdate.substring(11, 8) + '_' + _data[i].pos + "'>");
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
        select_split_dialog.dialog("open");
    }

    function split_selected(_this) {
        if (split_last_sp_si_selected_line != null)
            $(split_last_sp_si_selected_line).css("background-color", split_last_sp_si_color);
        split_last_sp_si_color = $(_this).css("background-color");
        split_last_sp_si_selected_line = _this;
        $(_this).css("background-color", "#AACCAA");
    }

    function get_infnr_si() {
        let lifnr = null;
        let idnlf = null;
        lifnr = $("#aes-lifnr").val();
        idnlf = $("#aes-idnlf").val();
        read_inforecords(3, lifnr, idnlf);
    }

    function get_zpret_si() {
        let lifnr = null;
        let idnlf = null;
        lifnr = $("#aes-lifnr").val();
        idnlf = $("#aes-idnlf").val();
        read_zpretrecords(3, lifnr, idnlf);
    }

    function delete_split() {
        if (split_last_sp_si_selected_line != null)
            $(split_last_sp_si_selected_line).remove();
        split_last_sp_si_selected_line = null;
    }


</script>