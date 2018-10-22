<div id="inforecord-dialog" title="{{__('Inforecords')}}">
    <form>
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

    <table id="inforecord_table">
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
        <tr>
            <th></th>
            <th colspan="1">{{__('Supplier')}}</th>
            <th colspan="2">{{__('Supplier name')}}</th>
            <th colspan="2">{{__('Vendor material')}}</th>
            <th colspan="8">{{__('Description')}}</th>
            <th colspan="2">{{__('Material')}}</th>
            <th colspan="3">{{__('Price')}}</th>
            <th colspan="2"></th>
        </tr>
    </table>

</div>

<script>

    var inforecordDialog, inforecordForm;
    $(function () {
        inforecordDialog = $("#inforecord-dialog").dialog({
            autoOpen: false,
            height: 600,
            width: 960,
            modal: true,
            buttons: {
                {{__("Use")}}: function () {
                    for(let i = 0; i < checkedList_extra.length; i++){
                        let current_row = checkedList_extra[i].parent().parent();
                        let lifnr = current_row.cell(1).html();
                        let lifnr_name = current_row.cell(2).html();
                        let idnlf = current_row.cell(3).html();
                        let mtext = current_row.cell(4).html();
                        let matnr = current_row.cell(5).html();
                    }
                },
                Cancel: function () {
                    let lifnr = null;
                    let lifnr_name = null;
                    let idnlf = null;
                    let mtext = null;
                    let matnr = null;
                    inforecordDialog.dialog("close");
                }
            },
            close: function () {
                inforecordForm[0].reset();
            },
            position: {
                my: "center",
                at: "center",
                of: window
            }
        });

        inforecordForm = inforecordDialog.find("form").on("submit", function (event) {
            event.preventDefault();
        });
    });

    function read_inforecords() {
        $("#inforecord_msg").text("");
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

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
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
        if (_status != "success") return;
        if (_data.length > 0) {
            $("#inforecord_table").find("tr:gt(0)").remove();
            for (let i = 0; i < _data.length; i++){
                var newRow = $("<tr>");
                var cols = "<td><input type='checkbox' onclick='boxCheck_extra(this)'></td>" +
                           "<td>" + _data[i].lifnr + "</td>" +
                           "<td>" + _data[i].lifnr_name + "</td>" +
                           "<td>" + _data[i].idnlf + "</td>" +
                           "<td>" + _data[i].mtext + "</td>" +
                           "<td>" + _data[i].matnr + "</td>" +
                           "<td>" + _data[i].price + " " + _data[i].curr + "</td>";
                newRow.append(cols); // .hide();
                $("#inforecord_table").append(newRow);
                newRow.attr('id', "tr_R" + _data[i].lifnr + "_" + _data[i].idnlf + "_" + i);
                if (i == 50) break;
            }
        }
    }

    var checkedList_extra = [];

    function isChecked_extra(id) {

        if ($.inArray(id, checkedList) > -1)
            return true;

        return false;

    }

    function boxCheck_extra(_this) {
        if (!isChecked_extra(_this)) {
            addToChecked_extra(_this);
        } else {
            removeFromChecked_extra(_this);
            removeFromChecked_extra(_this);
        }
    }

    function addToChecked_extra(id) {
        if ($.inArray(id, checkedList_extra) <= -1) {
            checkedList_extra.push(id);
        }
    }

    function removeFromChecked_extra(id) {
        if ($.inArray(id, checkedList_extra) > -1)
            checkedList_extra.splice($.inArray(id, checkedList_extra), 1);
    }
</script>
