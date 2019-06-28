@extends('layouts.app')

@section('content')
    @if (!(\Illuminate\Support\Facades\Auth::user() && (\Illuminate\Support\Facades\Auth::user()->role == 'Administrator' || (\Illuminate\Support\Facades\Auth::user()->role == 'CTV' && \Illuminate\Support\Facades\Auth::user()->ctvadmin == 1))))
        @php
            header("Location: /login");
            exit();
        @endphp
    @endif
    @php
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

        $message_count = App\Materom\Orders::unreadMessageCount();
        $message_svg = "";
        if ($message_count > 0) {
            if ($message_count > 99) $message_count = '>99';
            else $message_count = "&nbsp;" . $message_count;
            $message_svg = '&nbsp;<svg style="vertical-align: middle;" width="41" height="38">
                  <g>
                  <rect x="2" y="2" rx="8" ry="8" width="35" height="32" style="fill:red;stroke:black;stroke-width:2;opacity:0.99" />
                  <text x="5" y="23" font-family="Arial" font-size="16" fill="white">' . $message_count . '</text>
                  </g>
                </svg>';
        }

    @endphp
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header" style="border-bottom-width: 0px;">
                        @if(\Illuminate\Support\Facades\Auth::user()->role == "Administrator" && \Illuminate\Support\Facades\Auth::user()->readonly != 1)
                            <a href="/roles">
                                <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;"
                                   class="card-line first">
                                    <image style='height: 2.2rem; margin-left: -1.5rem;'
                                           src='/images/icons8-administrative-tools-48.png'/>
                                    {{__("Roles")}}
                                </p>
                            </a>
                            <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;"
                               class="card-line selector">
                                <image style='height: 2.2rem; margin-left: -1.5rem;'
                                       src='/images/icons8-user-account-80.png'/>
                                {{__("Users")}}
                            </p>
                            <a href="/messages">
                                <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;"
                                   class="card-line">
                                    <image style='height: 2.2rem; margin-left: -1.5rem;'
                                           src='/images/icons8-chat-80.png'/>
                                    {{__("Messages")}}{!!$message_svg!!}
                                </p>
                            </a>
                            <a href="/orders">
                                <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;"
                                   class="card-line">
                                    <image style='height: 2.2rem; margin-left: -1.5rem;'
                                           src='/images/icons8-todo-list-96.png'/>
                                    {{__("Orders")}}
                                </p>
                            </a>
                        @else
                            @if(\Illuminate\Support\Facades\Auth::user()->role == "CTV" && \Illuminate\Support\Facades\Auth::user()->ctvadmin == 1)
                                <p style="display: inline-block;" class="card-line first selector">
                                    <image style='height: 2.2rem; margin-left: -1.5rem;' src='/images/icons8-user-account-80.png'/>
                                    {{__("Users")}}
                                </p>
                            @endif
                            <a href="/messages">
                                <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;" class="card-line">
                                    <image style='height: 2.2rem; margin-left: -1.5rem;' src='/images/icons8-chat-80.png'/>
                                    {{__("Messages")}}{!!$message_svg!!}
                                </p>
                            </a>
                            <a href="/orders">
                                <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;" class="card-line">
                                    <image style='height: 2.2rem; margin-left: -1.5rem;' src='/images/icons8-todo-list-96.png'/>
                                    {{__('Orders')}}
                                </p>
                            </a>
                        @endif
                        @if(\Illuminate\Support\Facades\Auth::user()->role != "CTV")
                            <a href="/stats">
                                <p style="display: inline-block; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;" class="card-line">
                                    <image style='height: 2.2rem; margin-left: -1.5rem;' src='/images/icons8-area-chart-64.png'/>
                                    {{__("Statistics")}}
                                </p>
                            </a>
                        @endif
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if (\Illuminate\Support\Facades\Auth::user() && (\Illuminate\Support\Facades\Auth::user()->role == 'Administrator' ||(\Illuminate\Support\Facades\Auth::user()->role == 'CTV' && \Illuminate\Support\Facades\Auth::user()->ctvadmin == 1)))
                            <a href="{{ route('register') }}">+ {{trans('strings.create_user')}} +</a>
                            <br><br>
                            <form method="{{Request::url()}}" method="get" class="filterForm">
                                <div class="row">
                                    <div style="width: 6rem;" class="form-group input-group-sm">
                                        <input type="text" class="form-control input-sm" name="id" placeholder=""
                                               value="" style="border-radius: 2px; border-color: black">
                                    </div>
                                    <div style="width: 6rem;"></div>
                                    <div style="width: 8rem; margin-left: -0.5rem;" class="form-group input-group-sm">
                                        <input type="text" class="form-control input-sm" name="role" placeholder=""
                                               value=""
                                               style="border-radius: 2px; border-color: black;margin-left: 10px">
                                    </div>
                                    <div  style="width: 15rem;" class="form-group input-group-sm">
                                        <input type="text" class="form-control input-sm" name="user" placeholder=""
                                               value=""
                                               style="border-radius: 2px; border-color: black; margin-left: 20px">
                                    </div>
                                    <div  style="width: 15rem;" class="form-group input-group-sm">
                                        <input type="text" class="form-control input-sm" name="email" placeholder=""
                                               value=""
                                               style="border-radius: 2px; border-color: black; margin-left: 25px">
                                    </div>
                                    <input type="hidden" name="sort" value="{{$sort}}">
                                    <input type="hidden" name="val" value="{{$val}}">
                                    <input type="submit"
                                           style="position: absolute; left: -9999px; width: 1px; height: 1px;"
                                           tabindex="-1"/>
                                    <a href="/users" style="padding-left: 30px;padding-top: 3px">{{__('Reset')}}</a>
                                </div>
                            </form>
                            <table id="user-list-table" class="table"
                                   style="width:100%; clear:left; line-height: 1em;">
                                <thead class="thead-light">
                                <tr>
                                    <th style="width: 6rem;"><a href="/users?sort=ID&val=desc">&#x25BC;</a>ID<a
                                                href="/users?sort=ID&val=asc">&#x25B2;</a>
                                    </th>
                                    <th style="width: 6rem;">{{__('System')}}</th>
                                    <th style="width: 9rem;"><a href="/users?sort=role&val=desc">&#x25BC;</a>{{trans('strings.role')}}<a
                                                href="/users?sort=role&val=asc">&#x25B2;</a></th>
                                    <th style="width: 15rem;"><a href="/users?sort=user&val=desc">&#x25BC;</a>{{trans('strings.username')}}<a
                                                href="/users?sort=user&val=asc">&#x25B2;</a></th>
                                    <th style="width: 15rem;"><a href="/users?sort=email&val=desc">&#x25BC;</a>{{__('Email')}}<a
                                                href="/users?sort=email&val=asc">&#x25B2;</a></th>
                                    <th>
                                        {{__('Language')}}
                                    </th>
                                    <th>
                                        {{__('Status')}}
                                    </th>
                                    <th>
                                        {{__('Mode')}}
                                    </th>
                                    <th>{{trans('strings.action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $sap_system = \Illuminate\Support\Facades\Auth::user()->sap_system;
                                    $andctvrole = "";
                                    if (\Illuminate\Support\Facades\Auth::user()->role == 'CTV') $andctvrole = "and role = 'CTV'";
                                    if(isset($_GET['id']) && strcmp($_GET['id'],"") != 0){
                                        $id = $_GET['id'];
                                        $users = DB::select("select * from users where id='$id' and sap_system = '$sap_system' $andctvrole");
                                    }else if (isset($_GET['role']) && strcmp($_GET['role'],"") != 0){
                                        $role = $_GET['role'];
                                        $users = DB::select("select * from users where role like '%$role%' and sap_system = '$sap_system' $andctvrole");
                                    }else if (isset($_GET['user']) && strcmp($_GET['user'],"") != 0){
                                        $user = $_GET['user'];
                                        $users = DB::select("select * from users where username like '%$user%' and sap_system = '$sap_system' $andctvrole");
                                    }else if (isset($_GET['email']) && strcmp($_GET['email'],"") != 0){
                                        $email = $_GET['email'];
                                        $users = DB::select("select * from users where email like '%$email%' and sap_system = '$sap_system' $andctvrole");
                                    } else {
                                        $users = DB::select("select * from users where sap_system = '$sap_system' $andctvrole");
                                    }

                                    if($users != null && count($users) > 1)
                                        usort($users, "cmp");

                                    $table = "";

                                    foreach ($users as $user){
                                        $id = $user->id;
                                        $sap_system = $user->sap_system;
                                        if (empty(trim($sap_system))) $sap_system = "200";
                                        $email = $user->email;
                                        $role = $user->role;
                                        $name = $user->username;
                                        $lang = strtoupper($user->lang);

                                        $active = "Active";
                                        if($user->active == 0)
                                            $active = "Inactive";

                                        $readonly = "Normal";
                                        if($user->readonly == 1)
                                            $readonly = "Read-only";

                                        $chPass = __('Change password');
                                        $editUser = __('Change user data');
                                        $deleteUser = __('Delete user');
                                        $impersonateAsUser = __('Impersonate as user');
                                        $impersonateButton = "<button type='button' onclick='impersonateAsUser(\"$id\");return false;' title='$impersonateAsUser'><img src='images/icons8-circled-up-right-50-2.png' style='height: 1.5rem; padding-left: 0.2rem;' class='edit_user_button' title='".__("Impersonate as user")."'></button>";
                                        if (\Illuminate\Support\Facades\Auth::user()->role == 'CTV') $impersonateButton = "";

                                        $table .= "<tr><td>$id</td><td>$sap_system</td><td>$role</td><td>$name</td><td>$email</td><td>$lang</td><td>$active</td><td>$readonly</td>".
                                        "<td style='padding-top: 0px; padding-bottom: 0px; vertical-align: middle;'><button type='button' onclick='change_user_password(\"$id\");return false;' title='$chPass'><img id='edit_button_$id' src='images/icons8-password-reset-80.png' style='height: 1.5rem; padding-left: 0.2rem;' class='edit_user_button' title='Change password'></button>".
                                        "<button type='button' onclick='editUser(\"$id\");return false;' title='$editUser'><img id='edit_button_$id' src='images/edit.png' style='height: 1.5rem; padding-left: 0.2rem;' class='edit_user_button' title='Change user data'></button>".
                                        "<button type='button' onclick='deleteUser(\"$id\");return false;' title='$deleteUser'><img src='images/delete.png' style='height: 1.5rem; padding-left: 0.2rem;' class='delete' title='".__("Delete user")."'></button>".
                                        "$impersonateButton".
                                        "</td></tr>";
                                    }

                                    echo $table;
                                @endphp
                                </tbody>
                            </table>
                        @else
                            {{trans('strings.welcome_msg')}}<br>
                            <a href="/messages">{{__('Messages')}}</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function deleteUser(id){
            var content = document.createElement('div');
            content.innerHTML = "Are you sure that you want to delete <u><b>" + id + "</b></u> ?";
            swal({
                title: "Delete User",
                content: content,
                icon: "warning",
                buttons: true,
                dangerMode: true
            }).then(function(isConfirm) {
                if(isConfirm)
                    location.replace(location.pathname + '?del=' + id);
            });
        }

        function impersonateAsUser(id){
            var content = document.createElement('div');
            var _data2, _status2;
            content.innerHTML = "Are you sure that you want to impersonate as user <u><b>" + id + "</b></u> ?";
            swal({
                title: "Impersonate as User",
                content: content,
                icon: "warning",
                buttons: true,
                dangerMode: true
            }).then(function(isConfirm) {
                if(isConfirm) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    jQuery.ajaxSetup({async: false});
                    $.post("webservice/impersonate_as_user",
                        {
                            id: id
                        },
                        function (data, status) {
                            _data2 = data;
                            _status2 = status;
                        });
                    jQuery.ajaxSetup({async: true});
                    location.replace("/orders");
                }
            });

        }

    </script>

    <script>
        function deleteUser2(id) {
            if (confirm("{{__('Are you sure you want to delete ')}}" + id + "?")) {
                location.replace(location.pathname + '?del=' + id);
            } else {
            }
        }

        function editUser(id) {
            location.replace('/editUser?id=' + id);
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
        });
    </script>

    <div id="new-password-dialog" title="Change user password">
        <form>
            <br>
            <div class="form-group row" style="width: 80%">
                <label for="new_password" class="col-md-4 col-form-label text-md-left">{{__('New Password')}}</label>
                <input id="new_password" type="password" name="new_password" size="20" style="width: 200px;"
                       class="form-control col-md-6" required value="">
            </div>
            <div class="form-group row" style="width: 80%">
                <label for="conf_password"
                       class="col-md-4 col-form-label text-md-left">{{__('Confirm Password')}}</label>
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
