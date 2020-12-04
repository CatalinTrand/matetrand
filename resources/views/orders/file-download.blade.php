@php
@endphp

<div id="file-download-dialog" title="{{__('Download Excel file')}}">
    <form>
        <div class="form-group" style="width: 95%; margin-left: 0.5rem;">
            <label for="file-download-supplier">{{__('Select supplier')}}</label><br>
            <select id="file-download-supplier" class="form-control-sm input-sm" name="file-download-supplier"
                    style="height: 1.4rem; padding: 2px; width: 100%;"
                    onchange="file_download_supplier_changed()"
                @if (\Illuminate\Support\Facades\Auth::user()->role == 'Furnizor')
                    disabled
                @endif
            >
            </select>
        </div>
        <br>
        <label for="file-download-orders" style="margin-left: 0.5rem;">{{__('Select orders to download')}}</label>
        <div class="form-group" id="file-download-orders-div" style="width: 95%; margin-left: 0.5rem; height: 8.5rem; overflow: auto;">
            <fieldset id="file-download-orders" style="width: 100%;"></fieldset>
        </div>
        <br>
        <div class="form-group" style="vertical-align: center; margin-left: 0.5rem;">
            <label><input id="file-download-all-orders" type="checkbox" style="position: relative; top: 1px;" onchange="file_download_all_orders_changed()">
                {{__("Select all orders")}}
            </label>
        </div>
    </form>
</div>

<div id="file-download-field-selection-dialog" title="{{__('Fields selection for download')}}">
    <form>
        <label for="file-download-field-list" style="margin-left: 0rem;">{{__('Select fields and their order')}}</label>
        <table style="margin-left: 0rem;">
            <tr>
            <td style="width: 80%">
                <div class="form-group" id="file-download-field-list-div" style="width: 95%; margin-left: 0rem; height: 208px; overflow: auto;">
                    <fieldset id="file-download-field-list" style="width: 100%;"></fieldset>
                </div>
            </td>
            <td>
                <br>
                <button type="button" style="width: 5rem;" onclick="file_download_field_selection_move('up');return false;">{{__("Up")}}</button>
                <br>
                <br>
                <button type="button" style="width: 5rem;" onclick="file_download_field_selection_move('down');return false;">{{__("Down")}}</button>
            </td>
            </tr>
        </table>
    </form>
</div>

<script>
    var downloadFileDialog, fileDownloadDialogForm, downloadFileMode;
    $(function () {
        downloadFileDialog = $("#file-download-dialog").dialog({
            autoOpen: false,
            height: 360,
            width: 480,
            modal: true,
            buttons: {
                Fields: {
                    text: '{{__("Fields...")}}',
                    class: "leftInquiryDialogButton",
                    id: "file-download-fields-button",
                    click: function () {
                        if (downloadFileMode == 1) selectDownloadFields("xls01");
                    }
                },
                Download: {
                    text: '{{__("Download")}}',
                    click: function () {
                        let lifnr = $("#file-download-supplier option:selected").val();
                        let orders = file_download_order_changed();
                        if (orders.length == 0) {
                            alert("{{__('No order selected, nothing to download')}}");
                            return;
                        }
                        let order_list = "";
                        for (i = 0; i < orders.length; i++)
                            order_list = order_list + "@" + orders[i];
                        order_list = order_list.substr(1);
                        downloadFileDialog.dialog("close");
                        location.replace("webservice/xlsfiledownload?mode=" + downloadFileMode.toString() + "&lifnr=" + lifnr + "&orders=" + order_list);
                    }
                },
                {{__("Cancel")}}: function () {
                    downloadFileDialog.dialog("close");
                    $(".ui-tooltip").hide();
                }
            },
            open: function() {
                if (downloadFileMode == 1) {
                    $("#file-download-dialog").dialog('option', "title", "{{__("Download Excel report")}}");
                    $("#file-download-fields-button").show();
                }
                if (downloadFileMode == 2) {
                    $("#file-download-dialog").dialog('option', "title", "{{__("Download position data for Excel mass changes")}}");
                    $("#file-download-fields-button").hide();
                }
            },
            close: function () {
                fileDownloadDialogForm[0].reset();
                $(".ui-tooltip").hide();
            },
            position: {
                my: 'left top',
                at: 'center+160 top+120',
                of: window
            }
        });
        fileDownloadDialogForm = downloadFileDialog.find("form").on("submit", function (event) {
            event.preventDefault();
        });
    });

    function downloadXLSFile(mode) {
        if (supplierList.length == 0) {
            swal({
                title: "{{__('Empty selection')}}",
                text: "{{__('Please first select and display the purchase/sales orders for downloading')}}",
                icon: 'info',
                buttons: ["{{__('Ok')}}"],
            });
            return;
        }
        let suppliers = $("#file-download-supplier");
        suppliers.empty();
        let selected = false;
        for (i = 0; i < supplierList.length; i++) {
            let supplier = supplierList[i];
            let option = $('<option>',
                {
                    value: supplier.lifnr,
                    text: conv_exit_alpha_output(supplier.lifnr) + " " + supplier.lifnr_name
                }
            );
            if (!selected) {
                option.attr("selected", true);
                selected = true;
            }
            suppliers.append(option);
        }
        let option = $('<option>',
            {
                value: '<>',
                text: '{{__("All suppliers")}}'
            }
        );
        if (!selected) {
            option.attr("selected", true);
            selected = true;
        }
        suppliers.append(option);
        downloadFileMode = mode;
        file_download_supplier_changed();
        downloadFileDialog.dialog("open");
    }

    function file_download_all_orders_changed() {
        let all_checked = $("#file-download-all-orders").is(":checked");
        let orders = $("#file-download-orders").children();
        for (i = 0; i < orders.length; i++) {
            $(orders[i]).find("input").prop("checked", all_checked);
        }
    }

    function file_download_order_changed() {
        let orders = $("#file-download-orders").children();
        let order_list = [];
        let all_checked = true;
        for (i = 0; i < orders.length; i++) {
            order = $(orders[i]).find("input");
            if (order.is(":checked")) {
                order_list.push(order[0].id.split("-")[3]);
            } else all_checked = false;
        }
        $("#file-download-all-orders").prop("checked", all_checked);
        return order_list;
    }

    function file_download_supplier_changed() {
        let orders = $("#file-download-orders");
        orders.empty();
        let selected_supplier = $("#file-download-supplier option:selected").val();
        let order_count = 0;
        if (selected_supplier == "<>") {
            for (i = 0; i < supplierList.length; i++) {
                supplier = supplierList[i];
                for (j = 0; j < supplier.orders.length; j++) {
                    let order = $("<div style='vertical-align: center;'>");
                    order.html("<label><input id='file-download-order-" + supplier.orders[j] + "' type='checkbox' " + (supplier.orders.length == 1 && supplierList.length == 1 ? "checked " : "") +
                        "style='position: relative; top: 1px;' onchange='file_download_order_changed()'>&nbsp;" + supplier.orders[j] + "</label>");
                    orders.append(order);
                    order_count++;
                }
            }
        } else {
            for (i = 0; i < supplierList.length; i++)
                if (supplierList[i].lifnr == selected_supplier) break;
            if (i == supplierList.length) return;
            supplier = supplierList[i];
            for (i = 0; i < supplier.orders.length; i++) {
                let order = $("<div style='vertical-align: center;'>");
                order.html("<label><input id='file-download-order-" + supplier.orders[i] + "' type='checkbox' " + (supplier.orders.length == 1 ? "checked " : "") +
                    "style='position: relative; top: 1px;' onchange='file_download_order_changed()'>&nbsp;" + supplier.orders[i]) + "</label>";
                orders.append(order);
                order_count++;
            }
        }
        $("#file-download-all-orders").prop("checked", order_count == 1);
    }

    function mass_change_download() {
        $("#mass-change-menu-download").unbind("click");
        downloadXLSFile(2);
    }

    var fileDownloadFieldSelectionDialog, fileDownloadFieldSelectionDialogForm, fileDownloadFieldSelectionMode;
    $(function () {
        fileDownloadFieldSelectionDialog = $("#file-download-field-selection-dialog").dialog({
            autoOpen: false,
            height: 333,
            width: 400,
            modal: true,
            buttons: {
                Save: {
                    text: '{{__("Save")}}',
                    click: function() {
                        let fields = $("#file-download-field-list").children();
                        let field_list = "";
                        let field = null;
                        for (i = 0; i < fields.length; i++) {
                            field = $(fields[i]).find("input");
                            field_list = field_list + "@" + field[0].id.split("-")[3] + (field.is(":checked")?"1":"0");
                        }

                        fileDownloadFieldSelectionDialog.dialog("close");
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        jQuery.ajaxSetup({async: false});
                        var fdfs_data, fdfs_status;
                        $.post("webservice/filedownloadfieldselection",
                            {
                                file: fileDownloadFieldSelectionMode,
                                fieldlist: field_list
                            },
                            function (data, status) {
                                fdfs_data = data;
                                fdfs_status = status;
                            });
                        jQuery.ajaxSetup({async: true});
                    }
                },
                Cancel: {
                    text: '{{__("Cancel")}}',
                    click: function () {
                        fileDownloadFieldSelectionDialog.dialog("close");
                        $(".ui-tooltip").hide();
                    }
                }
            },
            open: function() {
                if (fileDownloadFieldSelectionMode == "xls01") {
                }
            },
            close: function () {
                fileDownloadFieldSelectionDialogForm[0].reset();
                $(".ui-tooltip").hide();
            },
            position: {
                my: 'left top',
                    at: 'center+180 top+140',
                    of: window
            }
        });
        fileDownloadFieldSelectionDialogForm = fileDownloadFieldSelectionDialog.find("form").on("submit", function (event) {
            event.preventDefault();
        });
    });

    function selectDownloadFields(file) {
        fileDownloadFieldSelectionMode = file;

        let field_list = $("#file-download-field-list");
        field_list.empty();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajaxSetup({async: false});
        $.get("webservice/filedownloadfieldselection",
            {
                file: fileDownloadFieldSelectionMode,
                fieldlist: null
            },
            function (data, status) {
                if (status == "success" && data != undefined && data != null) {
                    let field = null;
                    for (i = 0; i < data.fields.length; i++) {
                        field = $("<div style='vertical-align: center; margin: 1px; padding: 2px;' class='list-item'; onclick='file_download_item_list_selected(this);'>");
                        field.html("<input id='file-download-field-" + data.fields[i].field + "' type='checkbox' " + (data.fields[i].checked == 0 ? "" : "checked ") +
                        "style='position: relative; top: 1px;' onchange='return false;'>&nbsp;" + data.fields[i].name);
                        field_list.append(field);
                    }
                }
            }, "json");
        jQuery.ajaxSetup({async: true});

        fileDownloadFieldSelectionDialog.dialog("open");

    }

    function file_download_item_list_selected(_this) {
        if ($(_this).hasClass("list-item-selected")) {
            $(_this).removeClass("list-item-selected").addClass("list-item");
        } else {
            $("#file-download-field-list").children().each( function(){
                $(this).removeClass("list-item-selected").addClass("list-item");
            });
            $(_this).removeClass("list-item").addClass("list-item-selected");
        }
    }

    function file_download_field_selection_move(_direction) {
        var crt = $("#file-download-field-list").children(".list-item-selected");
        if (crt.length == 0) return;
        crt = crt[0];
        if (_direction == 'up') {
            if (crt.previousSibling == null) return;
            $(crt).swapWith(crt.previousSibling);
        } else if (_direction == 'down') {
            if (crt.nextSibling == null) return;
            $(crt).swapWith(crt.nextSibling);
        }
    }

</script>