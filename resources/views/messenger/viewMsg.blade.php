@extends('layouts.app')

@section('content')
    @guest
        @php
            header("Location: /");
            exit();
        @endphp
    @endguest
    @php
        $message = DB::select("select * from messages where id = '".$_GET['id']."'")[0];

        if($message == null || illegalMessage($message,Auth::user())){
            header("/messages");
            exit();
        }

        if($message->recipient == Auth::user()->username)
            DB::update("update messages set opened = '1' where id = '$message->id'");

        function illegalMessage($msg,$user){

            if($msg->recipient == $user->username || $msg->user_id == $user->id)
                return false;

            return true;

        }

    @endphp
    <div style="padding-left: 20px">
        @if(isset($_GET['from']) && strcmp($_GET['from'],"sentMessages") == 0)
            <a href="/messages/sentMessages">&larr; Back</a>
        @else
            <a href="/messages">&larr; Back</a>
        @endif
        <h4>Subject: <b>{{$message->subject}}</b></h4>
        <div class="col-md-6">
            <!-- Message -->
            <label class="control-label"><p>{{$message->body}}</p></label>
        </div>
        @if(isset($_GET['from']) && strcmp($_GET['from'],"messages") == 0)
            <a href="/messages/create?body={{$message->body}}&subject={{$message->subject}}&recipient={{App\User::where('id','=',$message->user_id)->get()[0]->username}}">Reply</a>
        @endif
    </div>
@stop