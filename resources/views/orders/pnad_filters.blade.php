<div id="pnad-filters-dialog" title="Filtrare PNAD" style="display: none;">
    <div id="pnad-filters" width="95%" style="margin-right: 0.5rem;">
        <table style="width: 100%; border-style: none">
            <tr>
                <td style="width: 40%;">
                    <div class="row">
                        <label for="pnad-filter-status" style="margin-left: 1em;" class="col-form-label text-md-left">{{__('Stare')}}</label>
                    </div>
                </td>
                <td style="width: 60%;">
                    <div class="row">
                        <select id="pnad-filter-status" name="pnad-filter-status" required
                                class="form-control" style="height: 1.4rem; padding: 2px;">
                            <option value="0">{{__('Nicio filtrare')}}</option>
                            <option value="1">{{__('Numai cele nerezolvate')}}</option>
                            <option value="2">{{__('Numai cele rezolvate')}}</option>
                            <option value="3">{{__('Toate inregistrarile')}}</option>
                        </select>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="width: 40%;">
                    <div class="row">
                        <label for="pnad-filter-type" style="margin-left: 1em;" class="col-form-label text-md-left">{{__('Tip')}}</label>
                    </div>
                </td>
                <td style="width: 60%;">
                    <div class="row">
                        <select id="pnad-filter-type" name="pnad-filter-type" required
                                class="form-control" style="height: 1.4rem; padding: 2px;">
                            <option value="0">{{__('Nicio filtrare')}}</option>
                            <option value="1">{{__('Numai cele cu diferente')}}</option>
                            <option value="2">{{__('Numai cele deteriorate')}}</option>
                        </select>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="width: 40%;">
                    <div class="row">
                        <label for="pnad-filter-mblnr" style="margin-left: 1em;" class="col-form-label text-md-left">{{__('Document MIGO')}}</label>
                    </div>
                </td>
                <td style="width: 60%;">
                    <div class="row">
                        <input id="pnad-filter-mblnr" name="pnad-filter-mblnr" value=""
                                class="form-control" style="height: 1.4rem; padding: 2px; width: 10em;" maxlength="10">
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>

<script>
    var pnadFiltersDialog, pnadFiltersForm;

    function showPNADFilters(e, _this) {
        pnadFiltersForm = _this.form;
        pnadFiltersDialog.dialog("open");
    }

    $(function () {
        pnadFiltersDialog = $("#pnad-filters-dialog").dialog({
            autoOpen: false,
            height: 320,
            width: 400,
            modal: true,
            buttons: [
                {
                    text: '{{__("Clear all filters")}}',
                    id: "pnad-filters-button-clear-all",
                    class: "leftInquiryDialogButton",
                    click: function () {
                        $("#filter_pnad_status").val("0");
                        $("#filter_pnad_type").val("0");
                        $("#filter_pnad_mblnr").val("");
                        pnadFiltersDialog.dialog("close");
                        pnadFiltersForm.submit();
                    }
                },
                {
                    text: '{{__("Cancel")}}',
                    click: function () {
                        pnadFiltersDialog.dialog("close");
                    }
                },
                {
                    text: '{{__("Apply")}}',
                    class: "rightInquiryDialogButton",
                    click: function () {
                        $("#filter_pnad_status").val($("#pnad-filter-status").val());
                        $("#filter_pnad_type").val($("#pnad-filter-type").val());
                        $("#filter_pnad_mblnr").val($("#pnad-filter-mblnr").val());
                        pnadFiltersDialog.dialog("close");
                        pnadFiltersForm.submit();
                    }
                }
            ],
            close: function () {
                $(".ui-tooltip").hide();
            },
            open: function () {
                $("#pnad-filter-status").val($("#filter_pnad_status").val());
                $("#pnad-filter-type").val($("#filter_pnad_type").val());
                $("#pnad-filter-mblnr").val($("#filter_pnad_mblnr").val());
            },
            show: {effect: "size", duration: 100},
            hide: {effect: "size", duration: 100},
            position: {
                my: 'left-40 top-20',
                at: 'center top',
                of: $("#show-pnad-filters")
            }
        });
    });

</script>
