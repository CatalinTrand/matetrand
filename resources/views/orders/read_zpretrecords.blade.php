<div id="zpret-dialog" title="ZPRET">
    <form action="POST" onsubmit="get_zpretrecords(this); return false;">
        <table style="border: none; padding: 1rem;" width="100%">
            <colgroup>
                <col width="9%">
                <col width="20%">
                <col width="10%">
                <col width="25%">
                <col width="26%">
                <col width="10%">
            </colgroup>
            <tr class="container" style="">
                <td>
                    <label for="zpret-lifnr" class="col-form-label text-md-left">{{__('Supplier')}}</label>
                </td>
                <td>
                    <input id="zpret-lifnr" type="text" name="zpret-lifnr" size="20"
                           class="form-control-sm input-sm" value="">
                </td>
                <td colspan="1">
                    <label for="zpret-lifnr-name"
                           class="col-form-label text-md-left">{{__('Supplier name')}}</label>
                </td>
                <td>
                    <input id="zpret-lifnr-name" type="text" name="zpret-lifnr-name" size="30"
                           class="form-control-sm input-sm" value="">
                </td>
                <td colspan="1"></td>
                <td colspan="1" rowspan="3" style="text-align: center; vertical-align: center;">
                    <button style="height: 3rem;" onclick="get_zpretrecords(this); return false;">{{__('Read')}}</button>
                </td>
            </tr>
            <tr class="container" style="">
                <td>
                    <label for="zpret-idnlf" class="col-form-label text-md-left">{{__('Vendor material')}}</label>
                </td>
                <td>
                    <input id="zpret-idnlf" type="text" name="zpret-idnlf" size="20"
                           class="form-control-sm input-sm" value="">
                </td>
                <td>
                    <label for="zpret-mat-description"
                           class="col-form-label text-md-left">{{__('Description')}}</label>
                </td>
                <td>
                    <input id="zpret-mat-description" type="text" name="zpret-mat-description" size="30"
                           class="form-control-sm input-sm" value="">
                </td>
                <td colspan="1"></td>
            </tr>
            <tr class="container" style="">
                <td>
                    <label for="zpret-material"
                           class="col-form-label text-md-left">{{__('Material')}}</label>
                </td>
                <td>
                    <input id="zpret-material" type="text" name="zpret-material" size="20"
                           class="form-control-sm input-sm" value="">
                </td>
                <td colspan="3"></td>
            </tr>
        </table>
        <i id="zpretrecord_msg" style="color: red"></i>
        <hr>
    </form>

    <div style="display: block; overflow-y: scroll; max-height: 350px">
        <table id="zpret_table" class="table-striped">
            <colgroup>
                <col width="5%">
                <col width="5%">
                <col width="5%">
                <col width="5%">
                <col width="5%">
                <col width="5%">
                <col width="5%">
                <col width="5%">
                <col width="5%">
                <col width="5%">
                <col width="5%">
                <col width="5%">
                <col width="5%">
                <col width="5%">
                <col width="5%">
                <col width="5%">
                <col width="5%">
                <col width="5%">
                <col width="5%">
                <col width="5%">
            </colgroup>
            <tr style='height: 1.5rem;'>
                <th colspan="1">{{__('Supplier')}}</th>
                <th colspan="4">{{__('Supplier name')}}</th>
                <th colspan="2">{{__('Vendor material')}}</th>
                <th colspan="5">{{__('Description')}}</th>
                <th colspan="2">{{__('Material')}}</th>
                <th colspan="3" style="text-align: right;">{{__('Purchase Price')}}</th>
                <th colspan="3" style="text-align: right;">{{__('Sales Price')}}</th>
            </tr>
        </table>
    </div>

</div>

<script>

    var zpretDialog, zpretForm, zpretCaller;
    var result_infnr, result_lifnr, result_lifnr_name, result_idnlf, result_mtext, result_matnr,
        result_purch_price, result_purch_currency, result_sales_price, result_sales_currency;
    $(function () {
        zpretDialog = $("#zpret-dialog").dialog({
            autoOpen: false,
            height: 600,
            width: 960,
            modal: true,
            buttons: {
                "Take over": function () {
                    if (zpret_last_selected_line != null) {
                        let current_row = zpret_last_selected_line;
                        result_infnr = current_row.id;
                        result_lifnr = current_row.cells[0].innerHTML;
                        result_lifnr_name = current_row.cells[1].innerHTML;
                        result_idnlf = current_row.cells[2].innerHTML;
                        result_mtext = current_row.cells[3].innerHTML;
                        result_matnr = current_row.cells[4].innerHTML;
                        result_purch_price = current_row.cells[5].innerHTML.split(' ')[0];
                        result_purch_currency = current_row.cells[5].innerHTML.split(' ')[1];
                        result_sales_price = current_row.cells[6].innerHTML.split(' ')[0];
                        result_sales_currency = current_row.cells[6].innerHTML.split(' ')[1];
                        zpretDialog.dialog("close");
                        onselect_zpretrecord(zpretCaller, result_infnr, result_lifnr, result_lifnr_name, result_idnlf, result_mtext, result_matnr,
                            result_purch_price, result_purch_currency, result_sales_price, result_sales_currency);
                    }
                },
                Cancel: function () {
                    zpretDialog.dialog("close");
                }
            },
            close: function () {
                zpretForm[0].reset();
            },
            open: function () {
                $('#zpret-dialog').css('overflow', 'hidden');
                zpret_last_selected_line = null;
            },
            position: {
                my: "center",
                at:"center",
                of:window
            }
        });

        zpretForm = zpretDialog.find("form").on("submit", function (event) {
            event.preventDefault();
        });
    });

    function read_zpretrecords(caller, lifnr, idnlf) {
        $("#zpret_msg").text("");
        $("#zpret_table").find("tr:gt(0)").remove();
        $("#zpret-dialog").dialog('option', 'title', 'zpret');
        $("#zpret-lifnr").val("");
        $("#zpret-lifnr-name").val("");
        $("#zpret-idnlf").val("");
        $("#zpret-mat-description").val("");
        $("#zpret-material").val("");
        if (lifnr != null) $("#zpret-lifnr").val(lifnr);
        if (idnlf != null) $("#zpret-idnlf").val(idnlf);
        zpretCaller = caller;
        zpretDialog.dialog("open");
    }

    function get_zpretrecords(_this) {

        var lifnr, lifnr_name, idnlf, mtext, matnr;

        lifnr = $("#zpret-lifnr").val();
        lifnr_name = $("#zpret-lifnr-name").val();
        idnlf = $("#zpret-idnlf").val();
        mtext = $("#zpret-mat-description").val();
        matnr = $("#zpret-material").val();

        if (lifnr.length + lifnr_name.length + idnlf.length + mtext.length + matnr.length == 0) {
            alert("{{__('Please check at least one selection condition')}}");
            return;
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $("#zpret_table").find("tr:gt(0)").remove();
        $('body').addClass('ajaxloading');
        jQuery.ajaxSetup({async: false});
        var _data, _status;
        $.get("webservice/read_zpretrecords",
            {
                lifnr: lifnr,
                lifnr_name: lifnr_name,
                idnlf: idnlf,
                mtext: mtext,
                matnr: matnr
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
            let table = $("#zpret_table");
            for (let i = 0; i < _data.length; i++) {
                var newRow = $("<tr id='" + _data[i].infnr + "' style='height: 1.5rem;' onclick='zpret_selected(this);return false;'>");
                var cols = "<td colspan='1'>" + _data[i].lifnr + "</td>" +
                    "<td colspan='4'>" + _data[i].lifnr_name + "</td>" +
                    "<td colspan='2'>" + _data[i].idnlf + "</td>" +
                    "<td colspan='5'>" + _data[i].mtext + "</td>" +
                    "<td colspan='2'>" + _data[i].matnr + "</td>" +
                    "<td colspan='3' style='text-align: right;'>" + _data[i].purch_price + " " + _data[i].purch_curr + "</td>" +
                    "<td colspan='3' style='text-align: right;'>" + _data[i].sales_price + " " + _data[i].sales_curr + "</td>";
                newRow.append(cols); // .hide();
                table.append(newRow);
            }
        }
        $('body').removeClass('ajaxloading');
    }

    var zpret_last_selected_line;
    var zpret_last_color;
    function zpret_selected(_this) {
        if (zpret_last_selected_line != null)
            $(zpret_last_selected_line).css("background-color", zpret_last_color);

        zpret_last_color = $(_this).css("background-color");
        zpret_last_selected_line = _this;

        $(_this).css("background-color", "#ccccff");
    }
</script>
