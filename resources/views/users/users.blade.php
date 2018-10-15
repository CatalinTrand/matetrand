@extends('layouts.app')

@section('content')
    @if (!(Auth::user() && Auth::user()->role == 'Administrator'))
        @php
            header("/");
            exit();
        @endphp
    @endif
    @php
        if(isset($_GET['del'])){
            $id_del = $_GET['del'];
            $user = App\User::all()->find($id_del);
            $user->delete();
        }

        if(isset($_GET['sort']) && isset($_GET['val'])){
                $sort = $_GET['sort'];
                $val = $_GET['val'];
        } else {
                $sort = "";
                $val = "";
        }

        function cmp($a, $b){

            $sort = 'ID';
            $val = 'asc';

            if(isset($_GET['sort']) && isset($_GET['val'])){
                $sort = $_GET['sort'];
                $val = $_GET['val'];
            }

            if(strcmp($val,'desc') == 0){
                switch ($sort){
                    case 'role':
                        return strcmp($b->role, $a->role);
                    break;
                    case 'user':
                        return strcmp($b->username, $a->username);
                    break;
                    case 'email':
                        return strcmp($b->email, $a->email);
                    break;
                    default:
                        return strcmp($b->id, $a->id);
                    break;
                }
            } else {
                switch ($sort){
                    case 'role':
                        return strcmp($a->role, $b->role);
                    break;
                    case 'user':
                        return strcmp($a->username, $b->username);
                    break;
                    case 'email':
                        return strcmp($a->email, $b->email);
                    break;
                    default:
                        return strcmp($a->id, $b->id);
                    break;
                }
            }
        }
    @endphp
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header" style="border-bottom-width: 0px;">
                        @if(strcmp( (\Illuminate\Support\Facades\Auth::user()->role), "Administrator" ) == 0)
                            <a href="/roles"><p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;" class="card-line first">
                                <image style='height: 2.2rem; margin-left: -1.5rem;' src='/images/icons8-administrative-tools-48.png'/>
                                {{__("Roles")}}
                                </p></a>
                            <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;" class="card-line selector">
                                <image style='height: 2.2rem; margin-left: -1.5rem;' src='/images/icons8-user-account-80.png'/>
                                {{__("Users")}}
                            </p>
                            <a href="/messages"><p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;" class="card-line">
                                <image style='height: 2.2rem; margin-left: -1.5rem;' src='/images/icons8-chat-80.png'/>
                                {{__("Messages")}}
                                </p></a>
                            <a href="/orders"><p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;" class="card-line">
                                <image style='height: 2.2rem; margin-left: -1.5rem;' src='/images/icons8-todo-list-96.png'/>
                                {{__("Orders")}}
                                </p></a>
                        @else
                            <p style="display: inline-block;" class="card-line first">Messages</p>
                            <a href="/orders"><p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;" class="card-line">Comenzi</p></a>
                        @endif
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if (Auth::user() && Auth::user()->role == 'Administrator')
                            <a href="{{ route('register') }}">+ {{trans('strings.create_user')}} +</a>
                            <br><br>
                            <form method="{{Request::url()}}" method="get" class="filterForm">
                                <div class="row">
                                    <div class="form-group col-sm-1 input-group-sm">
                                        <input type="text" class="form-control input-sm" name="id" placeholder=""
                                               value="" style="border-radius: 2px; border-color: black">
                                    </div>
                                    <div class="form-group col-xs-2 input-group-sm">
                                        <input type="text" class="form-control input-sm" name="role" placeholder=""
                                               value="" style="border-radius: 2px; border-color: black;margin-left: 10px">
                                    </div>
                                    <div class="form-group col-sm-2 input-group-sm">
                                        <input type="text" class="form-control input-sm" name="user" placeholder=""
                                               value="" style="border-radius: 2px; border-color: black; margin-left: 20px">
                                    </div>
                                    <div class="form-group col-xs-3 input-group-sm">
                                        <input type="text" class="form-control input-sm" name="email" placeholder=""
                                               value="" style="border-radius: 2px; border-color: black; margin-left: 25px">
                                    </div>
                                    <input type="hidden" name="sort" value="{{$sort}}">
                                    <input type="hidden" name="val" value="{{$val}}">
                                    <input type="submit"
                                           style="position: absolute; left: -9999px; width: 1px; height: 1px;"
                                           tabindex="-1"/>
                                    <a href="/users" style="padding-left: 30px;padding-top: 3px">Reset</a>
                                </div>
                            </form>
                            <table id="user-list-table" class="basicTable table table-striped" style="width:100%;clear:left;">
                                <tr>
                                    <th><a href="/users?sort=ID&val=desc">&#x25BC;</a>ID<a href="/users?sort=ID&val=asc">&#x25B2;</a>
                                    </th>
                                    <th><a href="/users?sort=role&val=desc">&#x25BC;</a>{{trans('strings.role')}}<a
                                                href="/users?sort=role&val=asc">&#x25B2;</a></th>
                                    <th><a href="/users?sort=user&val=desc">&#x25BC;</a>{{trans('strings.username')}}<a
                                                href="/users?sort=user&val=asc">&#x25B2;</a></th>
                                    <th><a href="/users?sort=email&val=desc">&#x25BC;</a>Email<a
                                                href="/users?sort=email&val=asc">&#x25B2;</a></th>
                                    <th>
                                        Language
                                    </th>
                                    <th>
                                        Status
                                    </th>
                                    <th>{{trans('strings.action')}}</th>
                                </tr>
                                @php
                                    if(isset($_GET['id']) && strcmp($_GET['id'],"") != 0){
                                        $id = $_GET['id'];
                                        $users = DB::select("select * from users where id='$id'");
                                    }else if (isset($_GET['role']) && strcmp($_GET['role'],"") != 0){
                                        $role = $_GET['role'];
                                        $users = DB::select("select * from users where role like '%$role%'");
                                    }else if (isset($_GET['user']) && strcmp($_GET['user'],"") != 0){
                                        $user = $_GET['user'];
                                        $users = DB::select("select * from users where username like '%$user%'");
                                    }else if (isset($_GET['email']) && strcmp($_GET['email'],"") != 0){
                                        $email = $_GET['email'];
                                        $users = DB::select("select * from users where email like '%$email%'");
                                    } else {
                                        $users = DB::select("select * from users");
                                    }

                                    if($users != null && count($users) > 1)
                                        usort($users, "cmp");

                                    $table = "";

                                    foreach ($users as $user){
                                        $id = $user->id;
                                        $email = $user->email;
                                        $role = $user->role;
                                        $name = $user->username;
                                        $lang = $user->lang;

                                        $active = "Active";
                                        if($user->active == 0)
                                            $active = "Inactive";

                                        $table .= "<tr style='line-height: 35px'><td>$id</td><td>$role</td><td>$name</td><td>$email</td><td>$lang</td><td>$active</td><td><button style='margin-right:40px;margin-left:-80px' id='new-password-button' type='button' onclick='change_user_password(\"$id\");return false;'>Change Password</button><a href='/editUser?id=$id'><img id='edit_button_$id' src='images/edit.png' class='edit edit_user_button'></a><a href='/users?del=$id'><img src='images/delete.png' class='delete'></a></td></tr>";
                                    }

                                    echo $table;
                                @endphp
                            </table>
                        @elseif (Auth::user() && Auth::user()->role == 'Administrator')
                            {{trans('strings.welcome_msg')}}<br>
                            <a href="/messages">Messages</a>
                        @else
                            {{trans('strings.welcome_msg')}}<br>
                            <a href="/messages">Messages</a>
                        @endif
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

    //  Adding a new password
    <div id="new-password-dialog" title="Change user password">
        <form>
            <br>
            <div class="form-group row" style="width: 80%">
                <label for="new_password" class="col-md-4 col-form-label text-md-left">New Password</label>
                <input id="new_password" type="password" name="new_password" size="20" style="width: 200px;"
                       class="form-control col-md-6" required value="">
            </div>
            <div class="form-group row" style="width: 80%">
                <label for="conf_password" class="col-md-4 col-form-label text-md-left">Confirm Password</label>
                <input id="conf_password" type="password" name="conf_password" size="20" style="width: 200px;"
                       class="form-control col-md-6" required value="">
            </div>
            <i id="new_password_msg" style="color: red"></i>
        </form>
    </div>


    <script>

        var idForUser, newPasswordDialog, newPasswordForm;
        var passwordData, passwordStatus;
        $(function () {
            newPasswordDialog = $("#new-password-dialog").dialog({
                autoOpen: false,
                height: 200,
                width: 550,
                modal: true,
                buttons: {
                    Change: function () {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        jQuery.ajaxSetup({async: false});
                        $.post("webservice/changepassword",
                            {
                                user_id: idForUser,
                                new_password: $("#new_password").val()
                            },
                            function (data, status) {
                                passwordData = data;
                                passwordStatus = status;
                            });
                        jQuery.ajaxSetup({async: true});

                        if (passwordStatus == "success" && passwordData == "OK") {
                            newPasswordDialog.dialog("close");
                            // $("#app").prepend("<div class='alert alert-success'><b class='blinking-text'>Password successfully changed</b></div>");
                        }
                        else {
                            if (passwordData != "OK")
                                $("#new_password_msg").text(passwordData);
                            else $("#new_password_msg").text("An error occured updating the password");
                        }
                    },
                    Cancel: function () {
                        newPasswordDialog.dialog("close");
                    }
                },
                close: function () {
                    newPasswordForm[0].reset();
                    location.replace(location.pathname + "?id=" + passwordUser);
                },
                position: {
                    my: 'top',
                    at: 'middle',
                    of: $('#user-list-table')
                }
            });
            $("#new_password").on('input', function () {
                if ($("#new_password_msg").text() != "") $("#new_password_msg").text("")
            });
            newPasswordForm = newPasswordDialog.find("form").on("submit", function (event) {
                event.preventDefault();
            });
        });

        function change_user_password(userid) {
            $("#new_password_msg").text("");
            $("#new-password-dialog").dialog('option', 'title', 'Change password for ' + userid);
            idForUser = userid;
            newPasswordDialog.dialog("open");
        }
    </script>
@endsection
