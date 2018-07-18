@extends('layouts.app')

@section('content')
    @php
        $replyRecipient = "";
        $replyBody = "";
        $replySubject = "";
        if(isset($_GET['body']))
            $replyBody = $_GET['body'];
        if(isset($_GET['subject']))
            $replySubject = "RE: " . $_GET['subject'];
        if(isset($_GET['recipient']))
            $replyRecipient = $_GET['recipient'];

    @endphp
    <div style="padding-left: 20px">
        @if(isset($_GET['from']) && strcmp($_GET['from'],"sentMessages") == 0)
            <a href="/messages/sentMessages">&larr; Back</a>
        @else
            <a href="/messages">&larr; Back</a>
        @endif
        <h1>Create a new message</h1>
        <form action="{{ route('messages.store') }}" method="post">
            {{ csrf_field() }}
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label">Recipient</label>
                    <input type="text" class="form-control" name="recipient" placeholder="Recipient"
                           value="{{$replyRecipient}}">
                </div>
                <!-- Subject Form Input -->
                <div class="form-group">
                    <label class="control-label">Subject</label>
                    <input type="text" class="form-control" name="subject" placeholder="Subject"
                           value="{{$replySubject}}">
                </div>

                <!-- Message Form Input -->
                <div class="form-group">
                    <label class="control-label">Message</label>
                    @if(strcmp($replyBody,"") == 0)
                        <textarea name="message" class="form-control">{{$replyBody}}</textarea>
                    @else
                        <textarea name="message"
                                  class="form-control"><?php echo "\n\n$replyRecipient:\n$replyBody"; ?></textarea>
                    @endif
                </div>

                <!-- Submit Form Input -->
                <div class="form-group">
                    <button type="submit" class="btn btn-primary form-control">Send</button>
                </div>
            </div>
        </form>
    </div>
@stop
