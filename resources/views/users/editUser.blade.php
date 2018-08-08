@extends('layouts.app')

@section('content')
    @if (!(Auth::user() && Auth::user()->role == 'Administrator' && (isset($_POST['id']) || isset($_GET['id']))))
        @php
            header("/");
            exit();
        @endphp
    @endif

    @php
        use Illuminate\Support\Facades\DB;

        $msg = "";
        $msg_wf = "";
        $msg_ref = "";

        if(isset($_POST['id']))
            $id = $_POST['id'];
        else
            $id = $_GET['id'];

        //modify if needed
        if(isset($_POST['role'])){
            $role = $_POST['role'];
            $user = $_POST['username'];
            $email = $_POST['email'];

            DB::update("update users set role = '$role', username = '$user', email = '$email' where id = '$id'");

            if(isset($_POST['password'])){
                $passwd = $_POST['password'];
                $hash = \Illuminate\Support\Facades\Hash::make($passwd);

                DB::update("update users set password = '$hash' where id = '$id'");
            }

            $msg = "Changes saved!";
        }

        //load user data
        $users = DB::select("select * from users where id='$id'");

        if(!$users){
            echo "<h2>Error - no such user!</h2>";
            header("/");
            exit();
        }

        $user = $users[0];

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

        //followup::add
        if(isset($_POST['id_wf'])){
            $id_wf = $_POST['id_wf'];

            $result = DB::select("select * from users where id = '$id_wf'");

            if($result){
                $find = DB::select("select * from users_wf where id = '$id' and follow_up_id = '$id_wf'");

                if(count($find) == 0){
                    DB::insert("insert into users_wf (id,follow_up_id) values ('$id','$id_wf')");
                    $msg_wf = "Follow-up set!";
                } else $msg_wf = "Entry already exists!";
            } else $msg_wf = "No such user or user with specified ID is not a verified follow-up!";
        }

        //followup::delete
        if(isset($_GET['delWF'])){
            $delWF = $_GET['delWF'];
            DB::delete("delete from users_wf where id = '$id' and follow_up_id = '$delWF'");
            $msg_wf = "Follow-up deleted!";
        }

        //refferal::add
        if(isset($_POST['id_ref'])){
            $id_ref = $_POST['id_ref'];

            $result = DB::select("select * from users where id = '$id_ref'");

            if($result){
                $find = DB::select("select * from users_ref where id = '$id' and refferal_id = '$id_ref'");

                if(count($find) == 0){
                    DB::insert("insert into users_ref (id,refferal_id) values ('$id','$id_ref')");
                    $msg_ref = "Refferal set!";
                } else $msg_ref = "Entry already exists!";
            } else $msg_ref = "No such user or user with specified ID is not a verified refferal!";
        }

        //refferal::delete
        if(isset($_GET['delREF'])){
            $delREF = $_GET['delREF'];
            DB::delete("delete from users_ref where id = '$id' and refferal_id = '$delREF'");
            $msg_ref = "Refferal deleted!";
        }
    @endphp
    <div class="row container-fluid">
        <div class="container" style="width: 40%;margin-right: -30vw">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header"><a style="padding-right: 20px" href="/users">&larr; Back</a>Edit User
                            Panel
                        </div>

                        <div class="card-body">
                            <form method="POST" action="/editUser" aria-label="Edit User">
                                @csrf
                                <font color='green'>{{$msg}}</font>
                                <div class="form-group row">
                                    <label for="role"
                                           class="col-md-4 col-form-label text-md-right">{{ __('User Type') }}</label>

                                    <div class="col-md-6">
                                        <select id="role" type="text" class="form-control" name="role" required
                                                autofocus>
                                            <option {{$selectedAdmin}}>Administrator</option>
                                            <option {{$selectedFurnizor}}>Furnizor</option>
                                            <option {{$selectedReferent}}>Referent</option>
                                            <option {{$selectedCTV}}>CTV</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="username"
                                           class="col-md-4 col-form-label text-md-right">{{ __('Username') }}</label>

                                    <div class="col-md-6">
                                        <input id="username" type="text" name="username" class="form-control" required
                                               value="{{$user->username}}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="email"
                                           class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

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
                                    <label for="password"
                                           class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                                    <div class="col-md-6">
                                        <input id="password" type="password"
                                               class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                               name="password">

                                        @if ($errors->has('password'))
                                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                        @endif
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

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card" style="height: 250px">
                        <div class="card-header">
                            <table width="100%">
                                <tr>
                                    <td width="90%">Edit Workflow Follow-up</td>
                                    <td align="right">
                                        <button id="new-followup-button" type="button"
                                                onclick="new_workflow_followup_id('{{$id}}');return false;">New
                                        </button>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div id="followup-workflow-ids-card-body" class="card-body" style="overflow-y: scroll; height: 100%;">
                            <form method="POST" action="/editUser" aria-label="Edit Workflow Follow-up">
                                @csrf
                            </form>
                            <br>
                            <table id="followup-workflow-ids-table" class="basicTable table table-striped" style="margin-top: -20px">
                                <tr>
                                    <th>
                                        Follow-up ID
                                    </th>
                                    <th>
                                        Username
                                    </th>
                                    <th>
                                        Action
                                    </th>
                                </tr>
                                @php
                                    $myIDs = DB::select("select * from users_wf where id='$id'");
                                    $table = "";
                                    foreach ($myIDs as $anID){
                                        $aUser = App\User::all()->find($anID->follow_up_id);
                                        if($aUser)
                                            $table .= "<tr style='line-height: 35px'><td>$anID->follow_up_id</td><td>$aUser->username</td><td><a href='/editUser?delWF=$anID->follow_up_id&id=$id'><img src='images/delete.png' class='delete'></a></td></tr>";
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
    <br>
    <br>
    <div class="container" style="margin-right: 21.5vw;margin-top: -6vw">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card" style="height: 250px">
                    <div class="card-header">
                        <table width="100%">
                            <tr>
                                <td width="90%">Edit Refferals</td>
                                <td align="right">
                                    <button id="new-refferal-button" type="button"
                                            onclick="new_refferal_id('{{$id}}');return false;">New
                                    </button>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div id="refferal-ids-card-body" class="card-body" style="overflow-y: scroll; height: 100%;">
                        <form method="POST" action="/editUser" aria-label="Edit Refferals" style="margin-top: -20px">
                            @csrf
                        </form>
                        <br>
                        <table class="basicTable table table-striped">
                            <tr>
                                <th>
                                    Refferal ID
                                </th>
                                <th>
                                    Username
                                </th>
                                <th>
                                    Action
                                </th>
                            </tr>
                            @php
                                $myREFs = DB::select("select * from users_ref where id='$id'");
                                $table = "";
                                foreach ($myREFs as $aREF){
                                    $aUser = App\User::all()->find($aREF->refferal_id);
                                    if($aUser)
                                        $table .= "<tr style='line-height: 35px'><td>$aREF->refferal_id</td><td>$aUser->username</td><td><a href='/editUser?delREF=$aREF->refferal_id&id=$id'><img src='images/delete.png' class='delete'></a></td></tr>";
                                }
                                echo $table;
                            @endphp
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Register ENTER as popup default button
        $(function () {
            $('body').on('keypress', '.ui-dialog', function (event) {
                if (event.keyCode === $.ui.keyCode.ENTER) {
                    $('.ui-dialog-buttonpane button:first', $(this)).click();
                    return false;
                }
            });
        });
    </script>

    //  Adding a new follow-up ID
    <div id="new-followup-dialog" title="Define new follower">
        <form>
            <br>
            <div class="form-group row" style="width: 80%">
                <label for="new_wf_id" class="col-md-4 col-form-label text-md-left">Follower ID</label>
                <input id="new_wf_id" type="text" name="new_wf_id" size="20" style="width: 200px;"
                       class="form-control col-md-6" required value="">
            </div>
            <i id="new_wf_msg" style="color: red"></i>
        </form>
    </div>


    <script>

        var followupForUser, newFollowupDialog, newFollowupForm;
        var followUpData, followUpStatus;
        $(function () {
            newFollowupDialog = $("#new-followup-dialog").dialog({
                autoOpen: false,
                height: 200,
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
                        $.post("webservice/insertfollowupuser",
                            {
                                user_id: followupForUser,
                                followup_user_id: $("#new_wf_id").val()
                            },
                            function (data, status) {
                                followUpData = data;
                                followUpStatus = status;
                            });
                        jQuery.ajaxSetup({async: true});

                        if (followUpStatus == "success" && followUpData == "")
                            newFollowupDialog.dialog("close");
                        else {
                            if (followUpData != "")
                                $("#new_wf_msg").text(followUpData);
                            else $("#new_wf_msg").text("An error occured checking/creating the follower");
                        }
                    },
                    Cancel: function () {
                        newFollowupDialog.dialog("close");
                    }
                },
                close: function () {
                    newFollowupForm[0].reset();
                    location.replace(location.pathname + "?id=" + followupForUser);
                },
                position: {
                    my: 'top',
                    at: 'middle',
                    of: $('#followup-workflow-ids-card-body')
                }
            });
            $("#new_wf_id").on('input', function () {
                if ($("#new_wf_msg").text() != "") $("#new_wf_msg").text("")
            });
            newFollowupForm = newFollowupDialog.find("form").on("submit", function (event) {
                event.preventDefault();
            });
        });

        function new_workflow_followup_id(userid) {
            $("#new_wf_msg").text("");
            $("#new-followup-dialog").dialog('option', 'title', 'Define new follower for ' + userid);
            followupForUser = userid;
            newFollowupDialog.dialog("open");
        }
    </script>

    //  Adding a new refferal ID
    <div id="new-refferal-dialog" title="Define new refferal">
        <form>
            <br>
            <div class="form-group row" style="width: 80%">
                <label for="new_ref_id" class="col-md-4 col-form-label text-md-left">Refferal ID</label>
                <input id="new_ref_id" type="text" name="new_ref_id" size="20" style="width: 200px;"
                       class="form-control col-md-6" required value="">
            </div>
            <i id="new_ref_msg" style="color: red"></i>
        </form>
    </div>


    <script>

        var refferalForUser, newRefferalDialog, newRefferalForm;
        var refferalData, refferalStatus;
        $(function () {
            newRefferalDialog = $("#new-refferal-dialog").dialog({
                autoOpen: false,
                height: 200,
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
                        $.post("webservice/insertrefferaluser",
                            {
                                user_id: refferalForUser,
                                refferal_id: $("#new_ref_id").val()
                            },
                            function (data, status) {
                                refferalData = data;
                                refferalStatus = status;
                            });
                        jQuery.ajaxSetup({async: true});

                        if (refferalStatus == "success" && refferalData == "")
                            newRefferalDialog.dialog("close");
                        else {
                            if (refferalData != "")
                                $("#new_ref_msg").text(refferalData);
                            else $("#new_ref_msg").text("An error occured checking/creating the refferal");
                        }
                    },
                    Cancel: function () {
                        newRefferalDialog.dialog("close");
                    }
                },
                close: function () {
                    newRefferalForm[0].reset();
                    location.replace(location.pathname + "?id=" + refferalForUser);
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
            $("#new-refferal-dialog").dialog('option', 'title', 'Define new refferal for ' + userid);
            refferalForUser = userid;
            newRefferalDialog.dialog("open");
        }
    </script>

@endsection