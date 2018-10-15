<?php

namespace App\Materom;

use App\User;
use Illuminate\Support\Facades\Mail;

class Mailservice
{
    static public function sendNotification($userid, $ebeln){

        Mail::send('email.notification',['user'=>User::all()->find($userid),'ebeln'=>$ebeln], function($message) use ($userid,$ebeln) {
            $message->to(User::all()->find($userid)->email, User::all()->find($userid)->username)->subject("Notificare comanda $ebeln");
            $message->from('srm@materom.ro','SRM');
        });

    }
}