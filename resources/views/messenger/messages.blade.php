@extends('layouts.app')

@section('content')
    @guest
        @php
            header("/");
            exit();
        @endphp
    @endguest
    @php
        if(isset($_GET['del'])){
            $id_del = $_GET['del'];
            DB::delete("delete from messages where id = '$id_del'");
        }
    @endphp
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        @if(strcmp( (\Illuminate\Support\Facades\Auth::user()->role), "Super Admin" ) == 0)
                            <p class="card-line first"><a href="/roles">Roles</a></p>
                            <p class="card-line"><a href="/home">Users</a></p>
                            <p class="card-line selector">Messages</p>
                            <p class="card-line"><a href="/orders">Comenzi</a></p>
                        @else
                            <p class="card-line first selector">Messages</p>
                            <p class="card-line"><a href="/orders">Comenzi</a></p>
                        @endif
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        <br><br>
                        @php
                                function cmp($a, $b){
                                    return strcmp($b->created_at, $a->created_at);
                                }

                                //connect to DB
                                use \Cmgmyr\Messenger\Models\Message;
                                use Illuminate\Support\Facades\Auth;
                                use App\User;
                                use Illuminate\Support\Facades\DB;

                                //$messages = Message::where('recipient', '=', Auth::user()->username)->get();
                                $messages = DB::select("select * from messages where recipient = '".Auth::user()->username."'");
                                usort($messages, "cmp");
                                if(count($messages) > 0){
                                    echo"<table style='width:100%'><tr>
                                            <th>Subject</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>";
                                    $table = "";
                                    foreach ($messages as $message){
                                        $subject = $message->subject;
                                        $from = User::where('id','=',$message->user_id)->get()[0]->username;
                                        $date = $message->created_at;
                                        $id = $message->id;

                                        if($message->opened == 1)
                                            $table .= "<tr><td><a href='/messages/viewMsg?from=messages&id=$id'>$subject</a></td><td>$date</td><td><a href='/messages?del=$id'>".trans('strings.delete')."</a></td></tr>";
                                        else
                                            $table .= "<tr><td><a href='/messages/viewMsg?from=messages&id=$id'><b>$subject</b></a></td><td>$date</td><td><a href='/messages?del=$id'>".trans('strings.delete')."</a></td></tr>";
                                    }
                                    echo $table;
                                    echo "</table>";
                                } else
                                    echo "<p>Sorry, no messages to show.</p>";
                        @endphp
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection