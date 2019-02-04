<?php

namespace App\Materom;

use App\Materom\Orders\POrderItemChg;
use App\Materom\SAP\MasterData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class Mailservice
{
    static public function sendMessageCopy($userid, $from, $order, $content)
    {
        $user = DB::table("users")->where("id", $userid)->first();
        if ($user == null) return;
        Mail::send('email.messagecopy',['user' => $user,'from' => $from, 'order' => $order, 'content' => $content],
            function($message) use ($user, $from, $order) {
            $message->to($user->email, $user->username)->subject(__("Mesaj SRM pentru comanda ") . $order);
            $message->from('no_reply_srm@materom.ro','MATEROM SRM');
        });
        Log::debug("Sent mail 'Copie mesaj SRM' de la '$from' la '$user->email'");

    }

    static public function sendNotification($userid, $ebeln)
    {
        $user = DB::table("users")->where("id", $userid)->first();
        if ($user == null) return;
        $user->agent = $user->username;
        Mail::send('email.notification',['user' => $user,'ebeln' => $ebeln], function($message) use ($ebeln, $user) {
            $message->to($user->email, $user->username)->subject("Notificare comanda $ebeln");
            $message->from('no_reply_srm@materom.ro','MATEROM SRM');
        });
        Log::debug("Sent mail 'Notificare comanda $ebeln' to '$user->email'");
    }

    static public function sendSalesOrderNotification($userid, $vbeln, $posnr, $forcectv = false)
    {
        $user = DB::table("users")->where("id", $userid)->first();
        if ($user == null) return;
        $user->agent = $user->username;
        if ($user->role == "CTV" || $forcectv) {
            $kunnr = DB::table(System::$table_pitems)->where([["vbeln", "=", $vbeln],["posnr", "=", $posnr]])->value("kunnr");
            if (isset($kunnr) && $kunnr != null) {
                $dusers = DB::select("select id, count(*) as count from ". System::$table_users_agent ." join ". System::$table_user_agent_clients ." using (id) where kunnr = '$kunnr' group by id order by count");
                if ($dusers == null || empty($dusers))
                    $agent = DB::table(System::$table_user_agent_clients)->where("kunnr", $kunnr)->value("agent");
                else $agent = DB::table(System::$table_users_agent)->where("id", $dusers[0]->id)->value("agent");
            } else {
                $agent = DB::table(System::$table_users_agent)->where("id", $user->id)->value("agent");
            }
            if (isset($agent) && $agent != null) {
                $user->agent = $agent;
                $user->agent = MasterData::getKunnrName(SAP::alpha_input($user->agent));
            }
        }
        Mail::send('email.salesordernotification',['user' => $user,'vbeln' => $vbeln,'posnr' => $posnr],
            function($message) use ($vbeln, $posnr, $user) {
            $posnr = SAP::alpha_output($posnr);
            $message->to($user->email, $user->username)->subject("Notificare anulare pozitie comanda $vbeln/$posnr");
            $message->from('no_reply_srm@materom.ro','MATEROM SRM');
        });
        Log::debug("Sent mail 'Notificare anulare pozitie comanda $vbeln/$posnr' to '$user->email'");
    }

    static public function sendSalesOrderProposal($userid, $vbeln, $posnr)
    {
        $user = DB::table("users")->where("id", $userid)->first();
        if ($user == null) return;
        $user->agent = $user->username;
        if ($user->role == "CTV") {
            $kunnr = DB::table(System::$table_pitems)->where([["vbeln", "=", $vbeln],["posnr", "=", $posnr]])->value("kunnr");
            if (isset($kunnr) && $kunnr != null) {
                $dusers = DB::select("select id, count(*) as count from ". System::$table_users_agent ." join ". System::$table_user_agent_clients ." using (id) where kunnr = '$kunnr' group by id order by count");
                if ($dusers == null || empty($dusers))
                    $agent = DB::table(System::$table_user_agent_clients)->where("kunnr", $kunnr)->value("agent");
                else $agent = DB::table(System::$table_users_agent)->where("id", $dusers[0]->id)->value("agent");
            } else {
                $agent = DB::table(System::$table_users_agent)->where("id", $user->id)->value("agent");
            }
            if (isset($agent) && $agent != null) {
                $user->agent = $agent;
                $user->agent = MasterData::getKunnrName(SAP::alpha_input($user->agent));
            }
        }
        Mail::send('email.salesorderproposal',['user' => $user,'vbeln' => $vbeln,'posnr' => $posnr],
            function($message) use ($vbeln, $posnr, $user) {
                $posnr = SAP::alpha_output($posnr);
                $message->to($user->email, $user->username)->subject("Propunere modificare pozitie comanda $vbeln/$posnr");
                $message->from('no_reply_srm@materom.ro','MATEROM SRM');
            });
        Log::debug("Sent mail 'Propunere modificare pozitie comanda $vbeln/$posnr' to '$user->email'");
    }

    static public function sendSalesOrderChange($userid, $vbeln, $posnr, $newposnr)
    {
        $user = DB::table("users")->where("id", $userid)->first();
        if ($user == null) return;
        $user->agent = $user->username;
        if ($user->role == "CTV") {
            $kunnr = DB::table(System::$table_pitems)->where([["vbeln", "=", $vbeln],["posnr", "=", $posnr]])->value("kunnr");
            if (isset($kunnr) && $kunnr != null) {
                $dusers = DB::select("select id, count(*) as count from ". System::$table_users_agent ." join ". System::$table_user_agent_clients ." using (id) where kunnr = '$kunnr' group by id order by count");
                if ($dusers == null || empty($dusers))
                    $agent = DB::table(System::$table_user_agent_clients)->where("kunnr", $kunnr)->value("agent");
                else $agent = DB::table(System::$table_users_agent)->where("id", $dusers[0]->id)->value("agent");
            } else {
                $agent = DB::table(System::$table_users_agent)->where("id", $user->id)->value("agent");
            }
            if (isset($agent) && $agent != null) {
                $user->agent = $agent;
                $user->agent = MasterData::getKunnrName(SAP::alpha_input($user->agent));
            }
        }
        Mail::send('email.salesorderchange',['user' => $user,'vbeln' => $vbeln, 'posnr' => $posnr, 'newposnr' => $newposnr],
            function($message) use ($vbeln, $posnr, $user) {
                $posnr = SAP::alpha_output($posnr);
                $message->to($user->email, $user->username)->subject("Notificare inlocuire pozitie comanda $vbeln/$posnr");
                $message->from('no_reply_srm@materom.ro','MATEROM SRM');
            });
        Log::debug("Sent mail 'Notificare inlocuire pozitie comanda $vbeln/$posnr' to '$user->email'");
    }

    static public function orderHistory($user, $vbeln, $posnr)
    {
        $result = "";
        $item = DB::table(System::$table_pitems)->where(["vbeln" => $vbeln, "posnr" => $posnr])->first();
        if (is_null($item)) return $result;
        $itemhist = DB::select("select * from ".System::$table_pitemchg ." where ebeln = '$item->ebeln' and ebelp = '$item->ebelp' order by cdate desc");
        if (is_null($itemhist) || empty($itemhist)) return $result;

        $locale = app('translator')->getLocale();
        Session::put('locale', strtolower($user->lang));
        app('translator')->setLocale(Session::get("locale"));
        $result .= "<br>" . __("Istoricul actiunilor efectuate asupra pozitiei ") .
            SAP::alpha_output($item->ebeln) . "/" . SAP::alpha_output($item->ebelp) . "<br><table style='width: 120em;'>";

        $result .= "<thead style='line-height: 1.3rem;'>";
        $result .= "<tr style='background-color:#ADD8E6; vertical-align: middle;'>";
        $result .= "<th style='width: 12%; text-align: left; padding: 2px;'><b>". __('Data') . "</b></th>";
        $result .= "<th colspan='2' style='width: 20%; text-align: left; padding: 2px;'><b>". __('Utilizator') . "</b></th>";
        $result .= "<th style='width: 25%; text-align: left; padding: 2px;'><b>". __('Ce s-a schimbat') . "</b></th>";
        $result .= "<th style='width: 43%; text-align: left; padding: 2px;'><b>". __('Motiv') . "</b></th>";
        $result .= "</tr>";
        $result .= "</thead>";

        $result .= "<tbody style='line-height: 1.3rem;'>";
        $i = 0;
        foreach($itemhist as $itemh) {
            $pitemchg = new POrderItemChg($itemh, true);
            $pitemchg->fill($item);

            $i++;
            if (($i % 2) == 0)
                $result .= "<tr style='background-color:Azure; vertical-align: middle; text-align: left;'>";
            else
                $result .= "<tr style='background-color:LightCyan; vertical-align: middle; text-align: left;'>";
            $result .= "<td style='padding: 2px;'>". $pitemchg->cdate ."</td>";
            $result .= "<td style='padding: 2px;'>". $pitemchg->cuser ."</td>";
            $result .= "<td style='padding: 2px;'>". $pitemchg->cuser_name ."</td>";
            $result .= "<td style='padding: 2px;'>". $pitemchg->text ."</td>";
            $result .= "<td style='padding: 2px;'>". $pitemchg->reason ."</td>";
            $result .= "</tr>";
        }
        $result .= "</tbody>";

        $result .= "</table><br>";
        Session::put('locale', $locale);
        app('translator')->setLocale(Session::get("locale"));

        return $result;
    }

}