<div id="inforecord-dialog" title="{{__('Inforecords')}}">
    <form action="POST" onsubmit="get_inforecords(this); return false;">
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
                    <label for="inforecord-lifnr" class="col-form-label text-md-left">{{__('Supplier')}}</label>
                </td>
                <td>
                    <input id="inforecord-lifnr" type="text" name="inforecord-lifnr" size="20"
                           class="form-control-sm input-sm" value="">
                </td>
                <td colspan="1">
                    <label for="inforecord-lifnr-name"
                           class="col-form-label text-md-left">{{__('Supplier name')}}</label>
                </td>
                <td>
                    <input id="inforecord-lifnr-name" type="text" name="inforecord-lifnr-name" size="30"
                           class="form-control-sm input-sm" value="">
                </td>
                <td colspan="1"></td>
                <td colspan="1" rowspan="3" style="text-align: center; vertical-align: center;">
                    <button style="height: 3rem;" onclick="get_inforecords(this); return false;">{{__('Read')}}</button>
                </td>
            </tr>
            <tr class="container" style="">
                <td>
                    <label for="inforecord-idnlf" class="col-form-label text-md-left">{{__('Vendor material')}}</label>
                </td>
                <td>
                    <input id="inforecord-idnlf" type="text" name="inforecord-idnlf" size="20"
                           class="form-control-sm input-sm" value="">
                </td>
                <td>
                    <label for="inforecord-mat-description"
                           class="col-form-label text-md-left">{{__('Description')}}</label>
                </td>
                <td>
                    <input id="inforecord-mat-description" type="text" name="inforecord-mat-description" size="30"
                           class="form-control-sm input-sm" value="">
                </td>
                <td colspan="1"></td>
            </tr>
            <tr class="container" style="">
                <td>
                    <label for="inforecord-material"
                           class="col-form-label text-md-left">{{__('Material')}}</label>
                </td>
                <td>
                    <input id="inforecord-material" type="text" name="inforecord-material" size="20"
                           class="form-control-sm input-sm" value="">
                </td>
                <td colspan="3"></td>
            </tr>
        </table>
        <i id="inforecord_msg" style="color: red"></i>
        <hr>
    </form>

    <div style="display: block; overflow-y: scroll; max-height: 350px">
        <table id="inforecord_table" class="table-striped">
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
                <th colspan="5">{{__('Supplier name')}}</th>
                <th colspan="2">{{__('Vendor material')}}</th>
                <th colspan="7">{{__('Description')}}</th>
                <th colspan="2">{{__('Material')}}</th>
                <th colspan="3">{{__('Price')}}</th>
            </tr>
        </table>
    </div>

</div>

<script>

    var inforecordDialog, inforecordForm, result_lifnr, result_lifnr_name, result_idnlf, result_mtext, result_matnr,
        result_price, result_currency;
    $(function () {
        inforecordDialog = $("#inforecord-dialog").dialog({
            autoOpen: false,
            height: 600,
            width: 960,
            modal: true,
            buttons: {
                Use: function () {
                    if (inforecord_last_selected_line != null) {
                        let current_row = inforecord_last_selected_line;
                        result_infnr = current_row.id;
                        result_lifnr = current_row.cells[0].innerHTML;
                        result_lifnr_name = current_row.cells[1].innerHTML;
                        result_idnlf = current_row.cells[2].innerHTML;
                        result_mtext = current_row.cells[3].innerHTML;
                        result_matnr = current_row.cells[4].innerHTML;
                        result_price = current_row.cells[5].innerHTML.split(' ')[0];
                        result_currency = current_row.cells[5].innerHTML.split(' ')[1];
                        inforecordDialog.dialog("close");
                        onselect_Inforecord(1, result_infnr, result_lifnr, result_lifnr_name, result_idnlf, result_mtext, result_matnr, result_price, result_currency);
                    }
                },
                Cancel: function () {
                    inforecordDialog.dialog("close");
                }
            },
            close: function () {
                inforecordForm[0].reset();
            },
            open: function () {
                $('#inforecord-dialog').css('overflow', 'hidden');
                inforecord_last_selected_line = null;
            },
            position: {
                my: "center",
                at:"center",
                of:window
            }
        });

        inforecordForm = inforecordDialog.find("form").on("submit", function (event) {
            event.preventDefault();
        });
    });

    function read_inforecords() {
        $("#inforecord_msg").text("");
        $("#inforecord_table").find("tr:gt(0)").remove();
        $("#inforecord-dialog").dialog('option', 'title', 'Inforecords');
        inforecordDialog.dialog("open");
    }

    function get_inforecords(_this) {

        var lifnr, lifnr_name, idnlf, mtext, matnr;

        lifnr = $("#inforecord-lifnr").val();
        lifnr_name = $("#inforecord-lifnr-name").val();
        idnlf = $("#inforecord-idnlf").val();
        mtext = $("#inforecord-mat-description").val();
        matnr = $("#inforecord-material").val();

        if (lifnr.length + lifnr_name.length + idnlf.length + mtext.length + matnr.length == 0) {
            alert("{{__('Please check at least one selection condition')}}");
            return;
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $("#inforecord_table").find("tr:gt(0)").remove();
        $('body').addClass('ajaxloading');
        jQuery.ajaxSetup({async: false});
        var _data, _status;
        $.get("webservice/read_inforecords",
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
            let table = $("#inforecord_table");
            for (let i = 0; i < _data.length; i++) {
                var newRow = $("<tr id='" + _data[i].infnr + "' style='height: 1.5rem;' onclick='inforecord_selected(this);return false;'>");
                var cols = "<td colspan='1'>" + _data[i].lifnr + "</td>" +
                    "<td colspan='5'>" + _data[i].lifnr_name + "</td>" +
                    "<td colspan='2'>" + _data[i].idnlf + "</td>" +
                    "<td colspan='7'>" + _data[i].mtext + "</td>" +
                    "<td colspan='2'>" + _data[i].matnr + "</td>" +
                    "<td colspan='3'>" + _data[i].price + " " + _data[i].curr + "</td>";
                newRow.append(cols); // .hide();
                table.append(newRow);
            }
        }
        $('body').removeClass('ajaxloading');
    }

    var inforecord_last_selected_line;
    var inforecord_last_color;
    function inforecord_selected(_this) {
        if (inforecord_last_selected_line != null)
            $(inforecord_last_selected_line).css("background-color", inforecord_last_color);

        inforecord_last_color = $(_this).css("background-color");
        inforecord_last_selected_line = _this;

        $(_this).css("background-color", "#ccccff");
    }
</script>
