@extends('layouts.app')

@section('content')
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
                    case 'name':
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
                    case 'name':
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
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{trans('strings.dashboard')}}<a style="padding-left: 20px"
                                                                              href="/messages">{{trans('strings.messages')}}</a>
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if (Auth::user() && Auth::user()->role == 'Super Admin')
                            <a href="{{ route('register') }}">+ {{trans('strings.create_user')}} +</a>
                            <br><br>
                            <form method="{{Request::url()}}" method="get">
                                <div class="row">
                                    <div class="form-group col-sm-1 input-group-sm">
                                        <input type="text" class="form-control input-sm" name="id" placeholder=""
                                               value="">
                                    </div>
                                    <div class="form-group col-sm-2 input-group-sm">
                                        <input type="text" class="form-control input-sm" name="role" placeholder=""
                                               value="">
                                    </div>
                                    <div class="form-group col-sm-3 input-group-sm">
                                        <input type="text" class="form-control input-sm" name="user" placeholder=""
                                               value="">
                                    </div>
                                    <div class="form-group col-xs-3 input-group-sm">
                                        <input type="text" class="form-control input-sm" name="email" placeholder=""
                                               value="">
                                    </div>
                                    <input type="hidden" name="sort" value="{{$sort}}">
                                    <input type="hidden" name="val" value="{{$val}}">
                                    <input type="submit"
                                           style="position: absolute; left: -9999px; width: 1px; height: 1px;"
                                           tabindex="-1"/>
                                    <a href="/home" style="padding-left: 10px">Reset</a>
                                </div>
                            </form>
                            <table style="width:100%;clear:left">
                                <tr>
                                    <th><a href="/home?sort=ID&val=desc">&#x25BC;</a>ID<a href="/home?sort=ID&val=asc">&#x25B2;</a>
                                    </th>
                                    <th><a href="/home?sort=role&val=desc">&#x25BC;</a>{{trans('strings.role')}}<a
                                                href="/home?sort=role&val=asc">&#x25B2;</a></th>
                                    <th><a href="/home?sort=name&val=desc">&#x25BC;</a>{{trans('strings.username')}}<a
                                                href="/home?sort=user&val=asc">&#x25B2;</a></th>
                                    <th><a href="/home?sort=email&val=desc">&#x25BC;</a>Email<a
                                                href="/home?sort=email&val=asc">&#x25B2;</a></th>
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
                                        $user = $_GET['role'];
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
                                        $table .= "<tr><td>$id</td><td>$role</td><td>$name</td><td>$email</td><td><a href='/home?del=$id'>".trans('strings.delete')."</a></td></tr>";
                                    }

                                    echo $table;
                                @endphp
                            </table>
                        @elseif (Auth::user() && Auth::user()->role == 'Admin')
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
@endsection
