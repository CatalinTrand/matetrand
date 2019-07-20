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


<script>
    var downloadFileDialog, fileDownloadDialogForm, downloadFileMode;
    $(function () {
        downloadFileDialog = $("#file-download-dialog").dialog({
            autoOpen: false,
            height: 360,
            width: 480,
            modal: true,
            buttons: {
                {{__("Download")}}: function () {
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
                },
                {{__("Cancel")}}: function () {
                    downloadFileDialog.dialog("close");
                    $(".ui-tooltip").hide();
                }
            },
            open: function() {
                if (downloadFileMode == 1) $("#file-download-dialog").dialog('option', "title", "{{__("Download Excel report")}}");
                if (downloadFileMode == 2) $("#file-download-dialog").dialog('option', "title", "{{__("Download position data for Excel mass changes")}}");
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
        for (i = 0; i < supplierList.length; i++)
            if (supplierList[i].lifnr == selected_supplier) break;
        if (i == supplierList.length) return;
        supplier = supplierList[i];
        for (i = 0; i < supplier.orders.length; i++) {
            let order = $("<div style='vertical-align: center;'>");
            order.html("<label><input id='file-download-order-" + supplier.orders[i] + "' type='checkbox' " + (supplier.orders.length == 1 ? "checked " : "") +
                "style='position: relative; top: 1px;' onchange='file_download_order_changed()'>&nbsp;" + supplier.orders[i]) + "</label>";
            orders.append(order);
        }
        $("#file-download-all-orders").prop("checked", supplier.orders.length == 1);
    }

    function mass_change_download() {
        $("#mass-change-menu-download").unbind("click");
        downloadXLSFile(2);
    }

</script>