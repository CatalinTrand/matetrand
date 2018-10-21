<div id="search-dialog" title="{{__('Search info')}}">
    <form>
        <br>
        <div class="form-group container-fluid" align="middle">
            <div class="row" style="display: inline-block">
                <label for="search-lifnr" class="col-md-4 col-form-label text-md-left">{{__('Supplier')}}</label>
                <input id="search-lifnr" type="text" name="search-lifnr" size="20"
                       class="form-control col-md-8" value="">
                <label for="search-lifnr-name"
                       class="col-md-4 col-form-label text-md-left">{{__('Supplier name')}}</label>
                <input id="search-lifnr-name" type="text" name="search-lifnr-name" size="20"
                       class="form-control col-md-8" value="">
            </div>
            <div class="row" style="display: inline-block">
                <label for="mat-vendor" class="col-md-4 col-form-label text-md-left">{{__('Material vendor')}}</label>
                <input id="mat-vendor" type="text" name="mat-vendor" size="20"
                       class="form-control col-md-8" value="">
                <label for="mat-description"
                       class="col-md-4 col-form-label text-md-left">{{__('Material description')}}</label>
                <input id="mat-description" type="text" name="mat-description" size="20"
                       class="form-control col-md-8" value="">
            </div>
            <label for="material"
                   class="col-md-4 col-form-label text-md-left">{{__('Material')}} Materom</label>
            <input id="material" type="text" name="material" size="20"
                   class="form-control col-md-8" value="">
            <br>
            <div align="middle">
                <button onclick="filter(this); return false;">{{__('Filter')}}</button>
            </div>
        </div>

        <table id="filter_table">
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
                <th colspan="2">{{__('Material vendor')}}</th>
                <th colspan="8">{{__('Description')}}</th>
                <th colspan="2">{{__('Material')}}</th>
                <th colspan="2">{{__('Price')}}</th>
                <th colspan="2">{{__('Currency')}}</th>
            </tr>
        </table>

        <i id="new_sch_msg" style="color: red"></i>
    </form>
</div>

<script>

    var searchDialog, searchForm;
    $(function () {
        searchDialog = $("#search-dialog").dialog({
            autoOpen: false,
            height: 200,
            width: 400,
            modal: true,
            buttons: {
                Process: function () {
                    for(let i = 0; i < checkedList_extra.length; i++){
                        let current_row = checkedList_extra[i].parent().parent();
                        let lifnr = current_row.cell(1).html();
                        let lifnr_name = current_row.cell(2).html();
                        let mat_vendor = current_row.cell(3).html();
                        let desc = current_row.cell(4).html();
                        let mat_materom = current_row.cell(5).html();
                        let price = current_row.cell(6).html();
                        let curr = current_row.cell(7).html();

                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        jQuery.ajaxSetup({async: false});
                        var _data, _status;
                        $.get("webservice/REPLACE_FUNCTION_2", //TODO
                            {
                                arg1: lifnr,
                                arg2: lifnr_name,
                                arg3: mat_vendor,
                                arg4: desc,
                                arg5: mat_materom,
                                arg6: price,
                                arg7: curr
                            },
                            function (data, status) {
                                _data = data;
                                _status = status;
                            }, "json");
                        jQuery.ajaxSetup({async: true});
                    }
                },
                Cancel: function () {
                    searchDialog.dialog("close");
                }
            },
            close: function () {
                searchForm[0].reset();
            },
            position: {
                my: "center",
                at: "center",
                of: window
            }
        });

        searchForm = searchDialog.find("form").on("submit", function (event) {
            event.preventDefault();
        });
    });

    function search_init() {
        $("#new_sch_msg").text("");
        $("#search-dialog").dialog('option', 'title', 'Formular de cautare');
        searchDialog.dialog("open");
    }

    function filter(_this){
        let currentrow = $("#filter_table").row(0);
        var supplier,supplier_name,mat_vendor,mat_desc,mat_materom;

        supplier_name = $("#search-lifnr-name").val();
        supplier = $("#search-lifnr").val();
        mat_vendor = $("#mat-vendor").val();
        mat_desc = $("#mat-description").val();
        mat_materom = $("#material").val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajaxSetup({async: false});
        var _data, _status;
        $.get("webservice/REPLACE_FUNCTION_1", //TODO
            {
                arg1: supplier,
                arg2: supplier_name,
                arg3: mat_vendor,
                arg4: mat_desc,
                arg5: mat_materom
            },
            function (data, status) {
                _data = data;
                _status = status;
            }, "json");

        jQuery.ajaxSetup({async: true});
        if (_status != "success") return;
        if (_data.length > 0) {
            ("#filter_table").empty();
            for(let i = 0; i < _data.length; i++){
                var newRow = $("<tr>");
                var cols = "<tr><input type='checkbox' onclick='boxCheck_extra(this)'></tr><tr>" + _data[i].lifnr + "</tr><tr>\" + _data[i].lifnr_name + \"</tr><tr>\" + _data[i].material_vendor + \"</tr><tr>\" + _data[i].description + \"</tr><tr>\" + _data[i].material + \"</tr><tr>\" + _data[i].price + \"</tr><tr>\" + _data[i].currency + \"</tr>";
                newRow.append(cols).hide();
                $(currentrow).after(newRow);
                newRow.attr('id', "tr_e_" + _data[i].lifnr + "_" + _data[i].material + "_" + _data[i].price);
                currentrow = newRow;
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
