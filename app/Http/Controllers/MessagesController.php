<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Cmgmyr\Messenger\Models\Message;
use Cmgmyr\Messenger\Models\Participant;
use Cmgmyr\Messenger\Models\Thread;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;

class MessagesController extends Controller
{
    /**
     * Show all of the message threads to the user.
     *
     * @return mixed
     */
    public function index()
    {
        return view('messenger.messages');
    }

    /**
     * Shows a message thread.
     *
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        return view('messenger.messages');
    }

    /**
     * Views a message.
     *
     * @return mixed
     */

    public function viewMsg()
    {
            return view('messenger.viewMsg');
    }

    public function sentMessages()
    {
        return view('messenger.sentMessages');
    }

    /**
     * Creates a new message.
     *
     * @return mixed
     */

    public function create()
    {
        $users = User::where('id', '!=', Auth::id())->get();

        return view('messenger.create', compact('users'));
    }

    /**
     * Stores a new message thread.
     *
     * @return mixed
     */
    public function store()
    {
        $input = Input::all();

        // Message
        Message::create([
            'subject' => $input['subject'],
            'recipient' => $input['recipient'],
            'user_id' => Auth::id(),
            'body' => $input['message'],
        ]);

        return redirect()->route('messages');
    }

}
