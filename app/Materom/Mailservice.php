<?php

namespace App\Materom;

use App\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class Mailservice
{
    static public function sendNotification($userid, $ebeln) {

        $user = DB::table("users")->where("id", $userid)->get()[0];
        if ($user == null) return;
        $user->agent = $user->username;
        Mail::send('email.notification',['user' => $user,'ebeln' => $ebeln], function($message) use ($ebeln, $user) {
            $message->to($user->email, $user->username)->subject("Notificare comanda $ebeln");
            $message->from('no_reply_srm@materom.ro','MATEROM SRM');
        });
        Log::debug("Sent mail 'Notificare comanda $ebeln' to '$user->email'");
    }

    static public function sendSalesOrderNotification($userid, $vbeln, $posnr) {

        $user = DB::table("users")->where("id", $userid)->get()[0];
        if ($user == null) return;
        $user->agent = $user->username;
        if ($user->role == "CTV") {
            $kunnr = DB::table("pitems")->where([["vbeln", "=", $vbeln],["posnr", "=", $posnr]])->value("kunnr");
            if ($kunnr != null)
                $agent = DB::table("user_agent_clients")->where(["id", "=", $user->id], ["kunnr", "=", $kunnr])->value("agent");
            else
                $agent = DB::table("users_agent")->where(["id", "=", $user->id])->value("agent");
            if (isset($agent) && $agent != null)
                $user->agent = $agent;
        }
        Mail::send('email.salesordernotification',['user' => $user,'vbeln' => $vbeln,'posnr' => $posnr],
            function($message) use ($vbeln, $posnr, $user) {
            $posnr = SAP::alpha_output($posnr);
            $message->to($user->email, $user->username)->subject("Notificare anulare pozitie comanda $vbeln/$posnr");
            $message->from('no_reply_srm@materom.ro','MATEROM SRM');
        });
        Log::debug("Sent mail 'Notificare anulare pozitie comanda $vbeln/$posnr' to '$user->email'");
    }

    static public function sendSalesOrderProposal($userid, $vbeln, $posnr) {

        $user = DB::table("users")->where("id", $userid)->get()[0];
        if ($user == null) return;
        $user->agent = $user->username;
        if ($user->role == "CTV") {
            $kunnr = DB::table("pitems")->where([["vbeln", "=", $vbeln],["posnr", "=", $posnr]])->value("kunnr");
            if ($kunnr != null)
                $agent = DB::table("user_agent_clients")->where(["id", "=", $user->id], ["kunnr", "=", $kunnr])->value("agent");
            else
                $agent = DB::table("users_agent")->where(["id", "=", $user->id])->value("agent");
            if (isset($agent) && $agent != null)
                $user->agent = $agent;
        }
        Mail::send('email.salesorderproposal',['user' => $user,'vbeln' => $vbeln,'posnr' => $posnr],
            function($message) use ($vbeln, $posnr, $user) {
                $posnr = SAP::alpha_output($posnr);
                $message->to($user->email, $user->username)->subject("Propunere modificare pozitie comanda $vbeln/$posnr");
                $message->from('no_reply_srm@materom.ro','MATEROM SRM');
            });
        Log::debug("Sent mail 'Propunere modificare pozitie comanda $vbeln/$posnr' to '$user->email'");
    }

    static public function sendSalesOrderChange($userid, $vbeln, $posnr, $newposnr) {

        $user = DB::table("users")->where("id", $userid)->get()[0];
        if ($user == null) return;
        $user->agent = $user->username;
        if ($user->role == "CTV") {
            $kunnr = DB::table("pitems")->where([["vbeln", "=", $vbeln],["posnr", "=", $posnr]])->value("kunnr");
            if ($kunnr != null)
                $agent = DB::table("user_agent_clients")->where(["id", "=", $user->id], ["kunnr", "=", $kunnr])->value("agent");
            else
                $agent = DB::table("users_agent")->where(["id", "=", $user->id])->value("agent");
            if (isset($agent) && $agent != null)
                $user->agent = $agent;
        }
        Mail::send('email.salesorderchange',['user' => $user,'vbeln' => $vbeln, 'posnr' => $posnr, 'newposnr' => $newposnr],
            function($message) use ($vbeln, $posnr, $user) {
                $posnr = SAP::alpha_output($posnr);
                $message->to($user->email, $user->username)->subject("Notificare inlocuire pozitie comanda $vbeln/$posnr");
                $message->from('no_reply_srm@materom.ro','MATEROM SRM');
            });
        Log::debug("Sent mail 'Notificare inlocuire pozitie comanda $vbeln/$posnr' to '$user->email'");
    }

}