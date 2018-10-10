<?php

namespace App\Materom;

use App\User;
use Illuminate\Support\Facades\Mail;

class Mailservice
{
    static public function sendNotification($userid, $ebeln){

        $email = User::all()->find($userid)->email;
        $name = User::all()->find($userid)->username;
        $body = "Buna $name, comanda de aprovizionare $ebeln a fost creata/actualizata.";

        $data = array('name'=>"SRM");
        Mail::send(['text'=>'mail'], $data, function($message) use ($email,$name,$body) {
            $message->to($email, $name)->subject($body);
            $message->from('srm@materom.ro','SRM');
        });
    }
}