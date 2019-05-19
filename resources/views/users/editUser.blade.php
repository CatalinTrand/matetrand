@extends('layouts.app')

@section('content')
    @if (!(\Illuminate\Support\Facades\Auth::user() && (\Illuminate\Support\Facades\Auth::user()->role == 'Administrator' || (\Illuminate\Support\Facades\Auth::user()->role == 'CTV' && \Illuminate\Support\Facades\Auth::user()->ctvadmin == 1)) && (isset($_POST['id']) || isset($_GET['id']))))
        @php
            header("Location: /users");
            exit();
        @endphp
    @endif

    @php
        $msg = "";
        $msg_sel = "";

        use Illuminate\Support\Facades\DB;

        $id = "";
        if(isset($_POST['id']))
            $id = $_POST['id'];
        else
            $id = $_GET['id'];

        //load user data
        $users = DB::select("select * from users where id = '$id'");

        if(!$users){
            header("Location: /users");
            exit();
        }

        $user = $users[0];
        if (\Illuminate\Support\Facades\Auth::user()->role == 'CTV' && $user->role != 'CTV') { // CTV Admin
            header("Location: /users");
            exit();
        }

        if(empty(trim($user->sap_system))) $user->sap_system = "200";
        $currentsystem200 = "";
        $currentsystem300 = "";
        if ($user->sap_system == "200") $currentsystem200 = "selected";
        if ($user->sap_system == "300") $currentsystem300 = "selected";

        if(is_null($user->ekgrp)) $user->ekgrp = "";
        if(is_null($user->lifnr)) $user->lifnr = "";

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

        switch (strtoupper($user->lang)){
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

        $readonly = "";
        if ($user->readonly == 1) $readonly = "checked";
        $none = "";
        if ($user->none == 1) $none = "checked";
        $ctvadmin = "";
        if ($user->ctvadmin == 1) $ctvadmin = "checked";
        $userctvadmin = "disabled";
        if (\Illuminate\Support\Facades\Auth::user()->role == 'Administrator'
        || (\Illuminate\Support\Facades\Auth::user()->ctvadmin == 1 && \Illuminate\Support\Facades\Auth::user()->id != $user->id)) $userctvadmin = "";

    @endphp
    <div class="container-fluid">
        <div class="container" style="width: 60%;">
            <div class="row justify-content-center">
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header"><a style="padding-right: 20px" href="/users">&larr; Back</a>Edit User
                            Panel
                        </div>

                        <div class="card-body">
                            <form method="POST" action="/editUser/edit" aria-label="Edit User">
                                @csrf
                                <font color='green'>{{$msg}}</font>

                                <div class="form-group row">
                                    <label for="__userid"
                                           class="col-md-2 col-form-label text-md-left">{{ __('User ID') }}</label>

                                    <div class="col-md-5">
                                        <input id="__userid" type="text" name="__userid" class="form-control" required
                                               value="{{$user->id}}" disabled>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="__role"
                                           class="col-md-2 col-form-label text-md-left">{{ __('User Type') }}</label>

                                    <div class="col-md-5">
                                        <input id="__role" type="text" name="__role" class="form-control" required
                                               value="{{$user->role}}" disabled>
                                    </div>

                                    <div id="ctvadmin_div" class="col-md-5" style="display: block;">
                                        <input type="checkbox" style="float: left; margin-top: 1em;" id="ctvadmin" name="ctvadmin" {{$ctvadmin}} {{$userctvadmin}}>
                                        <label for="ctvadmin" style="padding-left: 5px; padding-top: 0.75em;"
                                               class="col-form-label text-md-left">{{ __('Limited CTV administrator') }}</label>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="sap_system"
                                           class="col-md-2 col-form-label text-md-left">{{ __('System') }}</label>

                                    <div class="col-md-5">
                                        <select id="sap_system" type="text" class="form-control" name="sap_system" required>
                                            <option value="200" {{$currentsystem200}}>200</option>
                                            <option value="300" {{$currentsystem300}}>300</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="username"
                                           class="col-md-2 col-form-label text-md-left">{{ __('Username') }}</label>

                                    <div class="col-md-5">
                                        <input id="username" type="text" name="username" class="form-control" required
                                               value="{{$user->username}}">
                                    </div>
                                </div>

                                <div id="token_div" style="display: none;">
                                    <div class="form-group row">
                                        <label for="api_token"
                                               class="col-md-2 col-form-label text-md-left">API Token</label>

                                        <div class="col-md-5">
                                            <input id="api_token" type="text" name="api_token" class="form-control"
                                                   value="{{$user->api_token}}">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" style="height: 30px" onclick="generateNew(); return false;">
                                                Generate new
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row" id="lifnr_div" style="display: none;">
                                    <label for="lifnr"
                                           class="col-md-2 col-form-label text-md-left">Vendor</label>

                                    <div class="col-md-5">
                                        <input id="lifnr" type="text" name="lifnr" class="form-control"
                                               value="{{App\Materom\SAP::alpha_output($user->lifnr)}}">
                                    </div>
                                </div>

                                <div class="form-group row" id="ekgrp_div" style="display: none;">
                                    <label for="ekgrp"
                                           class="col-md-2 col-form-label text-md-left">Purchasing group</label>

                                    <div class="col-md-5">
                                        <input id="ekgrp" type="text" name="ekgrp" class="form-control"
                                               value="{{$user->ekgrp}}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="email"
                                           class="col-md-2 col-form-label text-md-left">{{ __('E-Mail Address') }}</label>

                                    <div class="col-md-5">
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
                                           class="col-md-2 col-form-label text-md-left">Language</label>

                                    <div class="col-md-5">
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
                                           class="col-md-2 col-form-label text-md-left">Status</label>

                                    <div class="col-md-5">
                                        <select id="active" type="text" class="form-control" name="active" required
                                                autofocus>
                                            <option {{$selectedON}}>Active</option>
                                            <option {{$selectedOFF}}>Inactive</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="readonly"
                                           class="col-md-2 col-form-label text-md-left">{{ __('Read-only') }}</label>

                                    <div class="col-md-5">
                                        <input id="readonly" type="checkbox" name="readonly" style="float: left; margin-top: 1em;" {{$readonly}}>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="none"
                                           class="col-md-2 col-form-label text-md-left">{{ __('Empty list (NONE)') }}</label>
                                    <div class="col-md-5">
                                        <input id="none" type="checkbox" name="none" style="float: left; margin-top: 1em;" {{$none}}>
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

        <div class="container" style="display: inline-block; float: left; margin-left: 7%">

            <div class="container" id="vendor_div" style="display: none; margin-left: 25%; width: 100%">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card" style="height: 250px">
                            <div class="card-header">
                                <table width="100%">
                                    <tr>
                                        <td width="90%">{{__('Manufacturers')}}</td>
                                        <td align="right">
                                            <button id="new-vendor-button" type="button"
                                                    onclick="new_vendor_id('{{$id}}');return false;">New
                                            </button>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div id="vendor-ids-card-body" class="card-body" style="overflow-y: scroll; height: 100%;">
                                <form method="POST" action="/editUser" aria-label="Edit Vendors"
                                      style="margin-top: -20px">
                                    @csrf
                                </form>
                                <br>
                                <table class="basicTable table table-striped">
                                    <tr>
                                        <th>
                                            {{ __('Manufacturer') }}
                                        </th>
                                        <th>
                                            {{ __('Manufacturer name') }}
                                        </th>
                                        <th>
                                            Action
                                        </th>
                                    </tr>
                                    @php
                                        $mySELs = \App\Materom\EditUsers::getSel($id);
                                        $table = "";
                                        foreach ($mySELs as $aSEL){
                                                $mfrnr = $aSEL->mfrnr;
                                                if ($mfrnr != '0000000000' && ctype_digit($mfrnr))
                                                    while (substr($mfrnr, 0, 1) == '0') $mfrnr = substr($mfrnr, 1, 10);
                                                $table .= "<tr style='line-height: 20px'><td>$mfrnr</td><td>$aSEL->mfrnr_name</td><td><button type='button' onclick='selDel(\"$id\",\"$aSEL->mfrnr\");return false;'><img src='/images/delete.png' class='delete'></button></td></tr>";
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

        <div class="container" id="ref_div" style="display: none; width: 60%">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card" style="height: 250px">
                        <div class="card-header">
                            <table width="100%">
                                <tr>
                                    <td width="90%">{{__('Referenti')}}</td>
                                    <td align="right">
                                        <button id="new-vendor-button" type="button"
                                                onclick="new_refferal_id('{{$id}}');return false;">New
                                        </button>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div id="refferal-ids-card-body" class="card-body" style="overflow-y: scroll; height: 100%;">
                            <form method="POST" action="/editUser" aria-label="Edit Refferals"
                                  style="margin-top: -20px">
                                @csrf
                            </form>
                            <br>
                            <table class="basicTable table table-striped">
                                <tr>
                                    <th>
                                        {{ __('Referent') }}
                                    </th>
                                    <th>
                                        {{ __('Nume referent') }}
                                    </th>
                                    <th>
                                        Action
                                    </th>
                                </tr>
                                @php
                                    use App\User;
                                    $myREFs = \App\Materom\EditUsers::getRefs($id);
                                    $table = "";
                                    foreach ($myREFs as $aREF){
                                            $id_in_ref = $aREF->refid;
                                            $ref_name = User::where('id','=',$id_in_ref)->get()[0]->username;
                                            $table .= "<tr style='line-height: 20px'><td>$id_in_ref</td><td>$ref_name</td><td><button type='button' onclick='refDel(\"$id\",\"$aREF->refid\");return false;'><img src='/images/delete.png' class='delete'></button></td></tr>";
                                    }
                                    echo $table;
                                @endphp
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">

            <div class="container" style="display: inline-block; margin-left: 0%" align="left" width="50%">
                <div class="container" id="agent_div" style="display: none; margin-left: 25%; width: 100%">
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="card" style="height: 250px">
                                <div class="card-header">
                                    <table width="100%">
                                        <tr>
                                            <td width="90%">{{__('Agents')}}</td>
                                            <td align="right">
                                                <button id="new-agent-button" type="button"
                                                        onclick="new_agent_id('{{$id}}');return false;">New
                                                </button>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div id="agent-ids-card-body" class="card-body" style="overflow-y: scroll; height: 100%;">
                                    <form method="POST" action="/editUser" aria-label="Edit Agents"
                                          style="margin-top: -20px">
                                        @csrf
                                    </form>
                                    <br>
                                    <table class="table-striped" style="line-height: 1.4rem;" width="100%">
                                        <tr>
                                            <th>
                                                {{ __('Agent') }}
                                            </th>
                                            <th>
                                                {{ __('Agent name') }}
                                            </th>
                                            <th>
                                                Action
                                            </th>
                                        </tr>
                                        @php
                                            $myAGENTs = \App\Materom\EditUsers::getAgents($id);
                                            $table = "";
                                            foreach ($myAGENTs as $aAGENT){
                                                    $agent = $aAGENT->agent;
                                                    $agent_name = \App\Materom\SAP\MasterData::getAgentName($agent);
                                                    $table .= "<tr><td>$agent</td><td>$agent_name</td><td><button type='button' onclick='agentDel(\"$id\",\"$agent\");return false;'><img src='/images/delete.png' class='delete' style='height: 1.4rem;'></button></td></tr>";
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

            <div class="container" style="display: inline-block; margin-left: -40%" align="left" width="50%">
                <div class="container" id="customers_div" style="display: none; margin-left: 25%; width: 100%">
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="card" style="height: 250px">
                                <div class="card-header">
                                    <table width="100%">
                                        <tr>
                                            <td width="90%">{{__('SAP Customers')}}</td>
                                            <td align="right">
                                                <button id="new-user-customer-button" type="button"
                                                        onclick="new_user_customer('{{$id}}');return false;">New
                                                </button>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div id="user-customer-ids-card-body" class="card-body" style="overflow-y: scroll; height: 100%;">
                                    <form method="POST" action="/editUser" aria-label="Edit Customers"
                                          style="margin-top: -20px">
                                        @csrf
                                    </form>
                                    <br>
                                    <table class="table-striped" style="line-height: 1.4rem;" width="100%">
                                        <tr>
                                            <th>
                                                {{ __('Customer') }}
                                            </th>
                                            <th>
                                                {{ __('Customer name') }}
                                            </th>
                                            <th>
                                                Action
                                            </th>
                                        </tr>
                                        @php
                                            $myCustomers = \App\Materom\EditUsers::getCustomers($id);
                                            $table = "";
                                            foreach ($myCustomers as $customer) {
                                                    $kunnr = $customer->kunnr;
                                                    $kunnr_name = \App\Materom\SAP\MasterData::getKunnrName($kunnr, 2);
                                                    $kunnr = \App\Materom\SAP::alpha_output($kunnr);
                                                    $table .= "<tr><td>$kunnr</td><td>$kunnr_name</td><td><button type='button' onclick='kunnrDel(\"$id\",\"$kunnr\");return false;'><img src='/images/delete.png' class='delete' style='height: 1.4rem;'></button></td></tr>";
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
        </div>
    </div>

    </div>

    <script>
        function selDel(id, sel) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajaxSetup({async: false});
            $.post("editUser/selDel",
                {
                    id: id,
                    sel: sel
                },
                function (data, status) {});
            jQuery.ajaxSetup({async: true});
            location.replace(location.pathname + "?id=" + id);
        }

        function refDel(id, ref) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajaxSetup({async: false});
            $.post("editUser/refDel",
                {
                    id: id,
                    ref: ref
                },
                function (data, status) {});
            jQuery.ajaxSetup({async: true});
            location.replace(location.pathname + "?id=" + id);
        }

        function agentDel(id, agent) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajaxSetup({async: false});
            $.post("editUser/agentDel",
                {
                    id: id,
                    agent: agent
                },
                function (data, status) {});
            jQuery.ajaxSetup({async: true});
            location.replace(location.pathname + "?id=" + id);
        }

        function kunnrDel(id, kunnr) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            jQuery.ajaxSetup({async: false});
            $.post("editUser/kunnrDel",
                {
                    id: id,
                    kunnr: kunnr
                },
                function (data, status) {});
            jQuery.ajaxSetup({async: true});
            location.replace(location.pathname + "?id=" + id);
        }

        function generateNew() {
            var api_token = document.getElementById("api_token");
            api_token.value = Math.random().toString(36).substring(2, 30) + Math.random().toString(36).substring(2, 30) + Math.random().toString(36).substring(2, 30) + Math.random().toString(36).substring(2, 30) + Math.random().toString(36).substring(2, 30) + Math.random().toString(36).substring(2, 30);
        }

        $(document).ready(function () {
            selectCheck('{{$user->role}}');
        });

        function selectCheck(nameSelect) {
            if (nameSelect == null)
                return;

            var lifnr_div = document.getElementById("lifnr_div");
            var ref_div = document.getElementById("ref_div");
            var ekgrp_div = document.getElementById("ekgrp_div");
            var vendor_div = document.getElementById("vendor_div");
            var token_div = document.getElementById("token_div");
            var agent_div = document.getElementById("agent_div");
            var customer_div = document.getElementById("customers_div");
            var ctvadmin_div = document.getElementById("ctvadmin_div");

            if (nameSelect) {
                if (nameSelect == "Referent" || nameSelect == "Furnizor") {
                    if (nameSelect == "Referent") {
                        ekgrp_div.style.display = "";
                        ref_div.style.display = "none";
                        lifnr_div.style.display = "none";
                        vendor_div.style.display = "none";
                    } else {
                        ekgrp_div.style.display = "none";
                        ref_div.style.display = "";
                        lifnr_div.style.display = "";
                        vendor_div.style.display = "";
                    }
                    token_div.style.display = "none";
                    agent_div.style.display = "none";
                    customer_div.style.display = "none";
                    ctvadmin_div.style.display = "none";
                }
                else {
                    ref_div.style.display = "none";
                    lifnr_div.style.display = "none";
                    ekgrp_div.style.display = "none";
                    vendor_div.style.display = "none";

                    if (nameSelect == "Administrator") {
                        token_div.style.display = "";
                        ctvadmin_div.style.display = "none";
                    } else {
                        token_div.style.display = "none";
                        ctvadmin_div.style.display = "";
                    }

                    if (nameSelect == "CTV") {
                        agent_div.style.display = "";
                        customer_div.style.display = "";
                    } else {
                        agent_div.style.display = "none";
                        customer_div.style.display = "none";
                    }
                }
            }
            else {
                lifnr_div.style.display = "none";
                ekgrp_div.style.display = "none";
                ref_div.style.display = "none";
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

    <div id="new-vendor-dialog" title="Define new manufacturer selection">
        <form>
            <br>
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
                height: 160,
                width: 400,
                modal: true,
                dialogClass: 'extra-detail',
                buttons: {
                    Add: function () {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        jQuery.ajaxSetup({async: false});
                        $.post("webservice/insertmanufacturer",
                            {
                                user_id: vendorForUser,
                                mfrnr: $("#new_mfrnr").val()
                            },
                            function (data, status) {
                                vendorData = data;
                                vendorStatus = status;
                            });
                        jQuery.ajaxSetup({async: true});
                        if (vendorStatus == "success" && vendorData == "") {
                            location.reload();
                            newVendorDialog.dialog("close");
                        } else {
                            if (vendorData != "")
                                $("#new_sel_msg").text(vendorData);
                            else $("#new_sel_msg").text("An error occured checking/creating the manufacturer selections");
                        }
                    },
                    Cancel: function () {
                        newVendorDialog.dialog("close");
                    },
                },
                close: function () {
                    newVendorForm[0].reset();
                    //location.replace(location.pathname + "?id=" + vendorForUser);
                },
                position: {
                    my: 'top',
                    at: 'middle',
                    of: $('#vendor-ids-card-body')
                }
            });
            $("#new_mfrnr").on('input', function () {
                if ($("#new_sel_msg").text() != "") $("#new_sel_msg").text("")
            });
            newVendorForm = newVendorDialog.find("form").on("submit", function (event) {
                event.preventDefault();
            });
        });

        function new_vendor_id(userid) {
            $("#new_sel_msg").text("");
            $("#new-vendor-dialog").dialog('option', 'title', 'Define new manufacturer selection for ' + userid);
            vendorForUser = userid;
            newVendorDialog.dialog("open");
            $(":button:contains('Cancel')").addClass("cancelBtn");
            $(":button:contains('Add')").addClass("addBtn");
        }
    </script>

    <div id="new-refferal-dialog" title="Define new reference user">
        <form>
            <br>
            <div class="form-group row" style="width: 80%">
                <label for="new_ref_id" class="col-md-4 col-form-label text-md-left">Refferal ID</label>
                <input id="new_ref_id" type="text" name="new_rel_id" size="20" style="width: 200px;"
                       class="form-control col-md-6" required value="">
            </div>

            <i id="new_sel_msg" style="color: red"></i>
        </form>
    </div>


    <script>

        var refferalForUser, newRefferalDialog, newRefferalForm;
        var refferalData, refferalStatus;
        $(function () {
            newRefferalDialog = $("#new-refferal-dialog").dialog({
                autoOpen: false,
                height: 160,
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
                        $.post("webservice/insertreferenceuser",
                            {
                                id: refferalForUser,
                                refid: $("#new_ref_id").val()
                            },
                            function (data, status) {
                                refferalData = data;
                                refferalStatus = status;
                            });
                        jQuery.ajaxSetup({async: true});
                        if (refferalStatus == "success" && refferalData == "") {
                            location.reload();
                            newRefferalDialog.dialog("close");
                        } else {
                            if (refferalData != "")
                                alert(refferalData);
                            else $("#new_ref_msg").text("An error occured checking/creating the reference user");
                        }
                    },
                    Cancel: function () {
                        newRefferalDialog.dialog("close");
                    }
                },
                close: function () {
                    newRefferalForm[0].reset();
                    //location.replace(location.pathname + "?id=" + refferalForUser);
                },
                position: {
                    my: 'top',
                    at: 'middle',
                    of: $('#refferal-ids-card-body')
                }
            });
            $("#new_ref_id").on('input', function () {
                if ($("#new_ref_msg").text() != "") $("#new_ref_msg").text("")
            });
            newRefferalForm = newRefferalDialog.find("form").on("submit", function (event) {
                event.preventDefault();
            });
        });

        function new_refferal_id(userid) {
            $("#new_ref_msg").text("");
            $("#new-refferal-dialog").dialog('option', 'title', 'Define new reference user for ' + userid);
            refferalForUser = userid;
            newRefferalDialog.dialog("open");
            $(":button:contains('Cancel')").addClass("cancelBtn");
            $(":button:contains('Add')").addClass("addBtn");
        }
    </script>

    <div id="new-agent-dialog" title="Define new agent for CTV user">
        <form>
            <br>
            <div class="form-group row" style="width: 80%">
                <label for="new_agent" class="col-md-4 col-form-label text-md-left">Agent</label>
                <input id="new_agent" type="text" name="new_agent" size="20" style="width: 200px;"
                       class="form-control col-md-6" required value="">
            </div>
            <i id="new_agent_msg" style="color: red"></i>
        </form>
    </div>


    <script>

        var agentForUser, newAgentDialog, newAgentForm;
        var agentData, agentStatus;
        $(function () {
            newAgentDialog = $("#new-agent-dialog").dialog({
                autoOpen: false,
                height: 160,
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
                        $.post("webservice/insertagent",
                            {
                                userid: agentForUser,
                                agent: $("#new_agent").val()
                            },
                            function (data, status) {
                                agentData = data;
                                agentStatus = status;
                            });
                        jQuery.ajaxSetup({async: true});
                        if (agentStatus == "success" && agentData == "") {
                            location.reload();
                            newAgentDialog.dialog("close");
                        } else {
                            if (agentData != "")
                                $("#new_agent_msg").text(agentData);
                            else $("#new_agent_msg").text("An error occured checking/creating the agent selections");
                        }
                    },
                    Cancel: function () {
                        newAgentDialog.dialog("close");
                    }
                },
                close: function () {
                    newAgentForm[0].reset();
                    //location.replace(location.pathname + "?id=" + agentForUser);
                },
                position: {
                    my: 'top',
                    at: 'middle',
                    of: $('#agent-ids-card-body')
                }
            });
            $("#new_agent").on('input', function () {
                if ($("#new_agent_msg").text() != "") $("#new_agent_msg").text("");
            });
            newAgentForm = newAgentDialog.find("form").on("submit", function (event) {
                event.preventDefault();
            });
        });

        function new_agent_id(userid) {
            $("#new_agent_msg").text("");
            $("#new-agent-dialog").dialog('option', 'title', 'Define new agent for ' + userid);
            agentForUser = userid;
            newAgentDialog.dialog("open");
            $(":button:contains('Cancel')").addClass("cancelBtn");
            $(":button:contains('Add')").addClass("addBtn");
        }
    </script>

    <div id="new-customer-dialog" title="Define new direct customer">
        <form>
            <br>
            <div class="form-group row" style="width: 80%">
                <label for="new_customer" class="col-md-4 col-form-label text-md-left">Customer</label>
                <input id="new_customer" type="text" name="new_customer" size="20" style="width: 200px;"
                       class="form-control col-md-6" required value="">
            </div>
            <i id="new_customer_msg" style="color: red"></i>
        </form>
    </div>


    <script>

        var customerForUser, newCustomerDialog, newCustomerForm;
        var customerData, customerStatus;
        $(function () {
            newCustomerDialog = $("#new-customer-dialog").dialog({
                autoOpen: false,
                height: 160,
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
                        $.post("webservice/insertcustomer",
                            {
                                userid: customerForUser,
                                kunnr: $("#new_customer").val()
                            },
                            function (data, status) {
                                customerData = data;
                                customerStatus = status;
                            });
                        jQuery.ajaxSetup({async: true});
                        if (customerStatus == "success" && customerData == "") {
                            location.reload();
                            newCustomerDialog.dialog("close");
                        } else {
                            if (customerData != "")
                                $("#new_customer_msg").text(customerData);
                            else $("#new_customer_msg").text("An error occured checking/creating the new customer");
                        }
                    },
                    Cancel: function () {
                        newCustomerDialog.dialog("close");
                    }
                },
                close: function () {
                    newCustomerForm[0].reset();
                },
                position: {
                    my: 'top',
                    at: 'middle',
                    of: $('#user-customer-ids-card-body')
                }
            });
            $("#new_customer").on('input', function () {
                if ($("#new_customer_msg").text() != "") $("#new_customer_msg").text("");
            });
            newCustomerForm = newCustomerDialog.find("form").on("submit", function (event) {
                event.preventDefault();
            });
        });

        function new_user_customer(userid) {
            $("#new_customer_msg").text("");
            $("#new-customer-dialog").dialog('option', 'title', 'Define new customer for ' + userid);
            customerForUser = userid;
            newCustomerDialog.dialog("open");
            $(":button:contains('Cancel')").addClass("cancelBtn");
            $(":button:contains('Add')").addClass("addBtn");
        }
    </script>


@endsection