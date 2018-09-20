@extends('layouts.app')

@section('content')
    @if (!(Auth::user() && Auth::user()->role == 'Administrator' && (isset($_POST['id']) || isset($_GET['id']))))
        @php
            header("/");
            exit();
        @endphp
    @endif

    @php
        $msg = "";
        $msg_sel = "";

        use Illuminate\Support\Facades\DB;

        if(isset($_POST['id']))
            $id = $_POST['id'];
        else
            $id = $_GET['id'];

        //load user data
        $users = DB::select("select * from users where id='$id'");

        if(!$users){
            echo "<h2>Error - no such user!</h2>";
            header("/");
            exit();
        }

        $user = $users[0];

        if(!$user->ekgrp){
            $user->ekgrp = "";
        }
        if(!$user->lifnr){
            $user->lifnr = "";
        }

        $selectedAdmin = "";
        $selectedReferent = "";
        $selectedFurnizor = "";
        $selectedCTV = "";

        switch ($user->role){
            case 'Administrator':
                $selectedAdmin = "selected";
            break;
            case 'Furnizor':
                $selectedFurnizor = "selected";
            break;
            case 'Referent':
                $selectedReferent = "selected";
            break;
            case 'CTV':
                $selectedCTV = "selected";
            break;
        }

        $selectedRO = "";
        $selectedHU = "";
        $selectedDE = "";
        $selectedEN = "";

        switch ($user->lang){
            case 'RO':
                $selectedRO = "selected"; break;
            case 'HU':
                $selectedHU = "selected"; break;
            case 'EN':
                $selectedEN = "selected"; break;
            case 'DE':
                $selectedDE = "selected"; break;
            break;
        }

        $selectedON = "";
        $selectedOFF = "";

        switch ($user->active){
            case 1:
                $selectedON = "selected";
            break;
            case 0:
                $selectedOFF = "selected";
            break;
        }

        //vendor::delete
        if(isset($_GET['wglifDEL'])){
            $wglif = $_GET['wglifDEL'];
            $mfrnr = $_GET['mfrnrDEL'];
            DB::delete("delete from users_sel where id = '$id' and wglif = '$wglif' and mfrnr = '$mfrnr'");
            $msg_sel = "Vendor deleted!";
        }
    @endphp
    <div class="container-fluid">
        <div class="container" style="width: 40%;">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header"><a style="padding-right: 20px" href="/users">&larr; Back</a>Edit User
                            Panel
                        </div>

                        <div class="card-body">
                            <form method="POST" action="/editUser/edit" aria-label="Edit User">
                                @csrf
                                <font color='green'>{{$msg}}</font>
                                <div class="form-group row">
                                    <label for="role"
                                           class="col-md-4 col-form-label text-md-left">{{ __('User Type') }}</label>

                                    <div class="col-md-6" style="margin-left: -3vw;">
                                        <select id="role" type="text" class="form-control" name="role" required
                                                autofocus onchange="selectCheck(this);">
                                            <option {{$selectedAdmin}}>Administrator</option>
                                            <option {{$selectedFurnizor}}>Furnizor</option>
                                            <option {{$selectedReferent}}>Referent</option>
                                            <option {{$selectedCTV}}>CTV</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="username"
                                           class="col-md-3 col-form-label text-md-left">{{ __('Username') }}</label>

                                    <div class="col-md-6">
                                        <input id="username" type="text" name="username" class="form-control" required
                                               value="{{$user->username}}">
                                    </div>
                                </div>

                                <div class="row" id="token_div" style="margin-left: -2.5vw; display: none;">
                                    <div class="form-group row">
                                        <label for="api_token"
                                               class="col-md-4 col-form-label text-md-right">API Token</label>

                                        <div class="col-md-6" style="padding-left: 5.9vw;">
                                            <input id="api_token" type="text" name="api_token" class="form-control"
                                                   value="{{$user->api_token}}">
                                        </div>
                                    </div>
                                    <button type="button" style="height: 30px" onclick="generateNew(); return false;">Generate new</button>
                                </div>

                                <div class="form-group row" id="lifnr_div" style="display: none;">
                                    <label for="lifnr"
                                           class="col-md-3 col-form-label text-md-left">Vendor</label>

                                    <div class="col-md-6">
                                        <input id="lifnr" type="text" name="lifnr" class="form-control"
                                               value="{{$user->lifnr}}">
                                    </div>
                                </div>

                                <div class="form-group row" id="ekgrp_div" style="display: none;">
                                    <label for="ekgrp"
                                           class="col-md-3 col-form-label text-md-left">Purchasing group</label>

                                    <div class="col-md-6">
                                        <input id="ekgrp" type="text" name="ekgrp" class="form-control"
                                               value="{{$user->ekgrp}}">
                                    </div>
                                </div>

                                <div class="form-group row" id="sapuser_div" style="display: none;">
                                    <label for="sapuser"
                                           class="col-md-3 col-form-label text-md-left">SAP User</label>

                                    <div class="col-md-6">
                                        <input id="sapuser" type="text" name="sapuser" class="form-control"
                                               value="{{$user->sapuser}}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="email"
                                           class="col-md-3 col-form-label text-md-left">{{ __('E-Mail Address') }}</label>

                                    <div class="col-md-6">
                                        <input id="email" type="email"
                                               class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                               name="email" value="{{$user->email}}" required>

                                        @if ($errors->has('email'))
                                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="lang"
                                           class="col-md-3 col-form-label text-md-left">Language</label>

                                    <div class="col-md-6">
                                        <select id="lang" type="text" class="form-control" name="lang" required
                                                autofocus>
                                            <option {{$selectedRO}}>RO</option>
                                            <option {{$selectedHU}}>HU</option>
                                            <option {{$selectedDE}}>DE</option>
                                            <option {{$selectedEN}}>EN</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="active"
                                           class="col-md-3 col-form-label text-md-left">Status</label>

                                    <div class="col-md-6">
                                        <select id="active" type="text" class="form-control" name="active" required
                                                autofocus>
                                            <option {{$selectedON}}>Active</option>
                                            <option {{$selectedOFF}}>Inactive</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row mb-0">
                                    <div class="col-md-6 offset-md-4">
                                        <button type="submit" class="btn btn-primary">
                                            Save Changes
                                        </button>
                                    </div>
                                </div>
                                <input type="hidden" name="id" value="{{$id}}">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <br>
        <br>

        <div class="container" id="vendor_div" style="display: none;">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card" style="height: 250px">
                        <div class="card-header">
                            <table width="100%">
                                <tr>
                                    <td width="90%">Branduri</td>
                                    <td align="right">
                                        <button id="new-vendor-button" type="button"
                                                onclick="new_vendor_id('{{$id}}');return false;">New
                                        </button>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div id="vendor-ids-card-body" class="card-body" style="overflow-y: scroll; height: 100%;">
                            <form method="POST" action="/editUser" aria-label="Edit Vendors" style="margin-top: -20px">
                                @csrf
                            </form>
                            <br>
                            <table class="basicTable table table-striped">
                                <tr>
                                    <th>
                                        Mat. group
                                    </th>
                                    <th>
                                        Manufacturer
                                    </th>
                                    <th>
                                        Action
                                    </th>
                                </tr>
                                @php
                                    $mySELs = DB::select("select * from users_sel where id='$id'");
                                    $table = "";
                                    foreach ($mySELs as $aSEL){
                                            $table .= "<tr style='line-height: 35px'><td>$aSEL->wglif</td><td>$aSEL->mfrnr</td><td><a href='/editUser?id=$id&wglifDEL=$aSEL->wglif&mfrnrDEL=$aSEL->mfrnr'><img src='/images/delete.png' class='delete'></a></td></tr>";
                                    }
                                    echo $table;
                                @endphp
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function generateNew() {
            var api_token = document.getElementById("api_token");
            api_token.value = Math.random().toString(36).substring(2, 30) + Math.random().toString(36).substring(2, 30) + Math.random().toString(36).substring(2, 30) + Math.random().toString(36).substring(2, 30) + Math.random().toString(36).substring(2, 30) + Math.random().toString(36).substring(2, 30);
        }
    </script>

    <script>
        function selectCheck(nameSelect) {
            var lifnr_div = document.getElementById("lifnr_div");
            var ekgrp_div = document.getElementById("ekgrp_div");
            var vendor_div = document.getElementById("vendor_div");
            var token_div = document.getElementById("token_div");
            var sapuser_div = document.getElementById("sapuser_div");

            if (nameSelect) {
                if (nameSelect.value == "Referent" || nameSelect.value == "Furnizor") {
                    if (nameSelect.value == "Referent") {
                        ekgrp_div.style.display = "";
                        lifnr_div.style.display = "none";
                        vendor_div.style.display = "none";
                        sapuser_div.style.display = "none";
                    } else {
                        ekgrp_div.style.display = "none";
                        lifnr_div.style.display = "";
                        vendor_div.style.display = "";
                        sapuser_div.style.display = "none";
                    }
                    token_div.style.display = "none";
                }
                else {
                    lifnr_div.style.display = "none";
                    ekgrp_div.style.display = "none";
                    vendor_div.style.display = "none";

                    if (nameSelect.value == "Administrator") {
                        token_div.style.display = "";
                    } else {
                        token_div.style.display = "none";
                    }

                    if (nameSelect.value == "CTV") {
                        sapuser_div.style.display = "";
                    } else {
                        sapuser_div.style.display = "none";
                    }
                }
            }
            else {
                lifnr_div.style.display = "none";
                ekgrp_div.style.display = "none";
                vendor_div.style.display = "none";
                token_div.style.display = "none";
            }
        }
    </script>

    <script>
        // Register ENTER as popup default button
        $(function () {
            $('body').on('keypress', '.ui-dialog', function (event) {
                if (event.keyCode === $.ui.keyCode.ENTER) {
                    $('.ui-dialog-buttonpane button:first', $(this)).click();
                    return false;
                }
            });
            selectCheck(document.getElementById("role"));
        });
    </script>

    //  Adding a new vendor ID
    <div id="new-vendor-dialog" title="Define new vendor">
        <form>
            <br>
            <div class="form-group row" style="width: 80%">
                <label for="new_sel_id" class="col-md-4 col-form-label text-md-left">Mat. Group</label>
                <input id="new_sel_id" type="text" name="new_sel_id" size="20" style="width: 200px;"
                       class="form-control col-md-6" required value="">
            </div>
            <div class="form-group row" style="width: 80%">
                <label for="new_mfrnr" class="col-md-4 col-form-label text-md-left">Manufacturer</label>
                <input id="new_mfrnr" type="text" name="new_mfrnr" size="20" style="width: 200px;"
                       class="form-control col-md-6" required value="">
            </div>
            <i id="new_sel_msg" style="color: red"></i>
        </form>
    </div>


    <script>

        var vendorForUser, newVendorDialog, newVendorForm;
        var vendorData, vendorStatus;
        $(function () {
            newVendorDialog = $("#new-vendor-dialog").dialog({
                autoOpen: false,
                height: 260,
                width: 400,
                modal: true,
                buttons: {
                    Add: function () {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        jQuery.ajaxSetup({async: false});
                        $.post("webservice/insertvendoruser",
                            {
                                user_id: vendorForUser,
                                wglif: $("#new_sel_id").val(),
                                mfrnr: $("#new_mfrnr").val()
                            },
                            function (data, status) {
                                vendorData = data;
                                vendorStatus = status;
                            });
                        jQuery.ajaxSetup({async: true});
                        if (vendorStatus == "success" && vendorData == "") {
                            newVendorDialog.dialog("close");
                        } else {
                            if (vendorData != "")
                                $("#new_sel_msg").text(followUpData);
                            else $("#new_sel_msg").text("An error occured checking/creating the follower");
                        }
                    },
                    Cancel: function () {
                        newVendorDialog.dialog("close");
                    }
                },
                close: function () {
                    newVendorForm[0].reset();
                    location.replace(location.pathname + "?id=" + vendorForUser);
                },
                position: {
                    my: 'top',
                    at: 'middle',
                    of: $('#vendor-ids-card-body')
                }
            });
            $("#new_sel_id").on('input', function () {
                if ($("#new_sel_msg").text() != "") $("#new_sel_msg").text("")
            });
            newVendorForm = newVendorDialog.find("form").on("submit", function (event) {
                event.preventDefault();
            });
        });

        function new_vendor_id(userid) {
            $("#new_sel_msg").text("");
            $("#new-vendor-dialog").dialog('option', 'title', 'Define new vendor for ' + userid);
            vendorForUser = userid;
            newVendorDialog.dialog("open");
        }
    </script>

@endsection