<?php

namespace App\Materom;

use App\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class Mailservice
{
    static public function sendNotification($userid, $ebeln){

        $user = DB::table("users")->where("id", $userid)->get()[0];
        if ($user == null) return;
        Mail::send('email.notification',['user' => $user,'ebeln' => $ebeln], function($message) use ($ebeln, $user) {
            $message->to($user->email, $user->username)->subject("Notificare comanda $ebeln");
            $message->from('no_reply_srm@materom.ro','MATEROM SRM');
        });

    }
}