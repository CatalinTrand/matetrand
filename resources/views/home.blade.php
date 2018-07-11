@extends('layouts.app')

@section('content')
    @php
        function slackIM($message, $channel){
            $ch = curl_init("https://slack.com/api/chat.postMessage");

            $data = http_build_query([
                "token" => "xoxp-396007825621-395857477250-397838432566-8e75b34e3c9434bf6bf043b7706820e4",
                "channel" => $channel,
                "text" => $message,
                "username" => Auth::user()->username,
            ]);

            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $result = curl_exec($ch);
            curl_close($ch);

            return $result;
        }

        if(isset($_GET['del'])){
            $id_del = $_GET['del'];
            $user = App\User::all()->find($id_del);
            $user->delete();
        }
        if(isset($_GET['msg']) && Auth::user()){
            $msg = $_GET['msg'];
            $link = "https://hooks.slack.com/services/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";
            $result = slackIM($msg,"#general");
        }
    @endphp
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{trans('strings.dashboard')}}</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if (Auth::user() && Auth::user()->role == 'Super Admin')
                            <a href="{{ route('register') }}">+ {{trans('strings.create_user')}} +</a>
                            <table style="width:100%">
                                <tr>
                                    <th>ID</th>
                                    <th>{{trans('strings.role')}}</th>
                                    <th>{{trans('strings.username')}}</th>
                                    <th>Email</th>
                                    <th>{{trans('strings.action')}}</th>
                                </tr>
                                @php
                                    $users = App\User::all();
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
                            <form action="/home" method="get">
                                {{trans('strings.slack_msg')}}:
                                <input type="text" name="msg">
                                <input type="submit" value="{{trans('strings.send')}}">
                            </form>
                        @else
                            {{trans('strings.welcome_msg')}}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
