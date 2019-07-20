<div id="file-upload-dialog" title="{{__('Upload Excel file with mass order item changes')}}">
    <form>
        <div class="form-group row" style="width: 95%; margin-left: 1rem;">
            <input id="upload-file-name" name="upload-file-name" type="file">
        </div>
    </form>
</div>


<script>
    var uploadFileDialog, fileUploadDialogForm, uploadFile;
    $(function () {
        uploadFileDialog = $("#file-upload-dialog").dialog({
            autoOpen: false,
            height: 180,
            width: 640,
            modal: true,
            buttons: {
                Upload: function () {
                    uploadFile = null;
                    let files = document.getElementById('upload-file-name').files;
                    if (files != null && files.length > 0) uploadFile = files[0];
                    if (uploadFile == null) {
                        alert("{{__('Please choose a (correct and readable) file')}}");
                        return;
                    }
                    var fr = new FileReader();
                    fr.onerror = function() {
                        uploadFile = null;
                        alert("{{__('Please choose a (correct and readable) file')}}");
                    }
                    fr.onload = performXLSFileUpload;
                    fr.readAsBinaryString(uploadFile);
                    if (uploadFile != null) uploadFileDialog.dialog("close");
                },
                Cancel: function () {
                    uploadFileDialog.dialog("close");
                }
            },
            close: function () {
                fileUploadDialogForm[0].reset();
                $(".ui-tooltip").hide();
            },
            position: {
                my: 'left top',
                at: 'center+80 top+120',
                of: window
            }
        });
        fileUploadDialogForm = uploadFileDialog.find("form").on("submit", function (event) {
            event.preventDefault();
        });
    });

    function xlsFileUpload() {
        uploadFile = null;
        uploadFileDialog.dialog("open");
    }
    function performXLSFileUpload() {
        if (uploadFile == null) return;
        let formData = new FormData();
        formData.append("file", uploadFile);
        $('body').addClass('ajaxloading');
        $.ajax({
            url: 'webservice/xlsfileupload',
            data: formData,
            type: 'POST',
            contentType: false,
            processData: false,
            async: false,
            cache: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(result, status) {
                $('body').removeClass('ajaxloading');
                if (status == "success") {
                    if (result != "OK") {
                        swal({
                            title: "{{__('An error was encountered processing the mass changes')}}",
                            text: result,
                            icon: 'error',
                            buttons: ["{{__('Ok')}}"],
                        });
                    } else {
                        uploadFile = null;
                        swal({
                            title: "{{__('File successfully uploaded')}}",
                            text: "{{__('The mass changes given in the file were successfully processed')}}",
                            icon: 'info',
                            buttons: ["{{__('Ok')}}"],
                        });
                    }
                } else {
                    uploadFile = null;
                    swal({
                        title: "{{__('File was NOT uploaded')}}",
                        text: "{{__('An error occurred uploading the file to Materom SRM')}}",
                        icon: 'error',
                        buttons: ["{{__('Ok')}}"],
                    });
                }
            }
        });
        $('body').removeClass('ajaxloading');
    }

    function mass_change_upload() {
        $("#mass-change-menu-upload").unbind("click");
        xlsFileUpload();
    }


</script>