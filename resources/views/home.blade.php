@extends('layouts.app')

@section('content')
    @php
        if(isset($_GET['del'])){
            $id_del = $_GET['del'];
            $user = App\User::all()->find($id_del);
            $user->delete();
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

                        @if (Auth::user() && Auth::user()->name == 'Super Admin')
                            <a href="{{ route('register') }}">+ {{trans('strings.create_user')}} +</a>
                            <table style="width:100%">
                                <tr>
                                    <th>ID</th>
                                    <th>{{trans('strings.role')}}</th>
                                    <th>Email</th>
                                    <th>{{trans('strings.action')}}</th>
                                </tr>
                                @php
                                    $users = App\User::all();
                                    $table = "";

                                    foreach ($users as $user){
                                        $id = $user->id;
                                        $email = $user->email;
                                        $name = $user->name;
                                        $table .= "<tr><td>$id</td><td>$name</td><td>$email</td><td><a href='/home?del=$id'>".trans('strings.delete')."</a></td></tr>";
                                    }

                                    echo $table;
                                @endphp
                            </table>
                        @else
                            {{trans('strings.welcome_msg')}}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
