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
            if (isset($kunnr) && $kunnr != null) $user->agent = MasterData::getAgentForClient($kunnr)->agent_name;
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
            $kunnr = DB::table(System::$table_pitems)->where([["vbeln", "=", $vbeln], ["posnr", "=", $posnr]])->value("kunnr");
            if (isset($kunnr) && $kunnr != null) $user->agent = MasterData::getAgentForClient($kunnr)->agent_name;
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
            if (isset($kunnr) && $kunnr != null) $user->agent = MasterData::getAgentForClient($kunnr)->agent_name;
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
        $item = DB::table(System::$table_pitems)->where(["vbeln" => $vbeln, "posnr" => $posnr])->first();
        if (is_null($item)) return "";
        return self::orderHistoryByItem($user, $item);
    }

    static public function orderHistoryByItem($user, $item, $width = "120em")
    {
        $result = "";
        if (is_null($item)) return $result;
        $itemhist = DB::select("select * from ".System::$table_pitemchg ." where ebeln = '$item->ebeln' and ebelp = '$item->ebelp' order by cdate desc");
        if (is_null($itemhist) || empty($itemhist)) return $result;

        $locale = app('translator')->getLocale();
        Session::put('locale', strtolower($user->lang));
        app('translator')->setLocale(Session::get("locale"));
        $result .= "<br>" . __("Istoricul actiunilor efectuate asupra pozitiei ") .
            SAP::alpha_output($item->ebeln) . "/" . SAP::alpha_output($item->ebelp) . "<br><table style='width: $width;'>";

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
            if ((Auth::user()->role == "Furnizor") && ($itemh->internal == 1)) continue;
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

    static public function sendCTVReminders($ctvid = null)
    {
        $stockorder = Orders::stockorder;
        $kunnrs = DB::select("select distinct kunnr from ". System::$table_pitems." where stage = 'C' and vbeln <> '$stockorder'");
        if (count($kunnrs) == 0) {
            Log::debug("sendCTVReminders: no items pending for CTVs");
            return;
        }
        $fallbackctv = trim(DB::table(System::$table_roles)->where("rfc_role", "CTV")->value("user1"));
        if (!empty($fallbackctv))
            $fallbackctv = DB::table("users")->where([["id", "=", $fallbackctv],["role", "=", "CTV"],["active", "=", 1], ["sap_system", "=", System::$system]])->first();
        $sql = "";
        if ($ctvid != null) $sql = "id = '$ctvid' and ";
        if (!empty($fallbackctv)) $sql .= "id <> '$fallbackctv->id' and ";
        $ctvs = DB::select("select * from users where $sql role = 'CTV' and active = 1 and sap_system = '". System::$system. "'");
        if (count($ctvs) == 0) {
            Log::debug("sendCTVReminders: No active CTVs found");
            return;
        }
        $nukunnrs = array();
        $mails = array();
        foreach($kunnrs as $kunnr) $nukunnrs[$kunnr->kunnr] = clone $kunnr;
        if (!empty($fallbackctv)) array_push($ctvs, $fallbackctv);
        foreach ($ctvs as $ctv) {
            if (!empty($fallbackctv) && $fallbackctv->id == $ctv->id) {
                $ckunnrs = $nukunnrs;
                $ctv->agent = $fallbackctv->username;
            } else {
                $ckunnrs = array();
                foreach ($kunnrs as $kunnr) {
                    if (!DB::table(System::$table_user_agent_clients)->where([["id", "=", $ctv->id], ["kunnr", "=", $kunnr->kunnr]])->exists())
                        continue;
                    unset($nukunnrs[$kunnr->kunnr]);
                    $dusers = DB::select("select id, count(*) as count from " . System::$table_users_agent . " join " . System::$table_user_agent_clients . " using (id) where kunnr = '$kunnr->kunnr' group by id order by count, id");
                    if (!empty($dusers) && $ctv->id != $dusers[0]->id) continue;
                    array_push($ckunnrs, $kunnr);
                }
                if (!empty($ckunnrs))
                    $ctv->agent = MasterData::getAgentForClient($ckunnrs[0]->kunnr)->agent_name;
            }
            if (empty($ckunnrs)) continue;
            $sql = "";
            foreach($ckunnrs as $kunnr) $sql .= "or kunnr = '$kunnr->kunnr'"; $sql = "(". substr($sql, 3) . ")";
            $items = DB::select("select distinct vbeln, posnr, kunnr from ". System::$table_pitems.
                " where stage = 'C' and vbeln <> '$stockorder' and $sql");
            if (empty($items)) continue;
            $mails[$ctv->id] = $items;
            // $ctv->email = "radu@etrandafir.ro";
            Mail::send('email.ctvreminder',['user' => $ctv,'items' => $items],
                function($message) use ($ctv, $items) {
                    $message->to($ctv->email, $ctv->username)->subject("Notificare SRM de pozitii ce necesita atentia dv.");
                    $message->from('no_reply_srm@materom.ro','MATEROM SRM');
                });
            Log::debug("Sent mail 'Notificare CTV de pozitii in lucru' to '$ctv->id ($ctv->email)'");
        }

        $adminctvs = DB::table("users")->where([["ctvadmin", "=", 1],["role", "=", "CTV"],["active", "=", 1],["sap_system", "=", System::$system]])->get();
        if (!empty($mails) && !empty($adminctvs)) {
            foreach($adminctvs as $adminctv) {
                Mail::send('email.adminctvreminder', ['user' => $adminctv, 'mails' => $mails],
                    function ($message) use ($adminctv) {
                        $message->to($adminctv->email, $adminctv->username)->subject("Notificare SRM cu privire la pozitiile deschise CTV in sistemul ".System::$system_name);
                        $message->from('no_reply_srm@materom.ro', 'MATEROM SRM');
                    });
                if (1 == 2)
                Mail::send('email.adminctvreminder', ['user' => $adminctv, 'mails' => $mails],
                    function ($message) use ($adminctv) {
                        $message->to("radu@etrandafir.ro", $adminctv->username)->subject("Notificare SRM cu privire la pozitiile deschise CTV in sistemul ".System::$system_name);
                        $message->from('no_reply_srm@materom.ro', 'MATEROM SRM');
                    });
                Log::debug("Sent mail 'Notificare Admin CTV de pozitii in lucru' to '$adminctv->id ($adminctv->email)'");
            }
        }
    }

    static public function CTVReminderList($user, $items, $width = "40em")
    {
        $result = "";

        $locale = app('translator')->getLocale();
        Session::put('locale', strtolower($user->lang));
        app('translator')->setLocale(Session::get("locale"));

        $result .= "<table style='width: $width;'>";
        $result .= "<thead style='line-height: 1.3rem;'>";
        $result .= "<tr style='background-color:#ADD8E6; vertical-align: middle;'>";
        $result .= "<th style='width: 30%; text-align: left; padding: 2px;'><b>". __('Comanda')." (".__("sistem")." ".System::$system_name.")</b></th>";
        $result .= "<th style='width: 70%; text-align: left; padding: 2px;'><b>". __('Client') . "</b></th>";
        $result .= "</tr>";
        $result .= "</thead>";
        $result .= "<tbody style='line-height: 1.3rem;'>";

        $i = 0;
        foreach($items as $item) {
            $i++;
            if (($i % 2) == 0)
                $result .= "<tr style='background-color:Azure; vertical-align: middle; text-align: left;'>";
            else
                $result .= "<tr style='background-color:LightCyan; vertical-align: middle; text-align: left;'>";
            $result .= "<td style='padding: 2px;'>". SAP::alpha_output($item->vbeln)."/".SAP::alpha_output($item->posnr). "</td>";
            $result .= "<td style='padding: 2px;'>". SAP::alpha_output($item->kunnr)." ".MasterData::getKunnrName($item->kunnr). "</td>";
            $result .= "</tr>";
        }

        $result .= "</tbody>";
        $result .= "</table><br>";

        Session::put('locale', $locale);
        app('translator')->setLocale(Session::get("locale"));
        return $result;
    }

    static public function AdminCTVReminderList($user, $mails, $width = "40em")
    {
        $result = "";

        $locale = app('translator')->getLocale();
        Session::put('locale', strtolower($user->lang));
        app('translator')->setLocale(Session::get("locale"));

        $result .= "<table style='border-collapse: collapse; border: 1px solid black; width: $width;'>";
        $result .= "<thead style='line-height: 1.3rem;'>";
        $result .= "<tr style='background-color:#ADD8E6; vertical-align: middle; border: 1px solid black;'>";
        $result .= "<th style='border: 1px solid black; width: 70%; text-align: left; padding: 2px;'><b>". __('CTV') . "</b></th>";
        $result .= "<th style='border: 1px solid black; width: 30%; text-align: left; padding: 2px;'><b>". __('Comanda')." (".__("sistem")." ".System::$system_name.")</b></th>";
        $result .= "</tr>";
        $result .= "</thead>";
        $result .= "<tbody style='line-height: 1.3rem;'>";

        $i = 0;
        foreach($mails as $ctvid => $mail) {
            $ctv = DB::table("users")->where("id", $ctvid)->first();
            if (empty($ctv)) continue;
            $i++;
            if (($i % 2) == 0) $bgcolor = "Azure"; else $bgcolor = "Cyan";
            $count = count($mail);
            $firstrow = true;
            foreach($mail as $item) {
                $result .= "<tr style='border: 1px solid black; background-color:$bgcolor; vertical-align: middle; text-align: left;'>";
                if ($firstrow) {
                    $result .= "<td rowspan='$count' style='padding: 2px; border: 1px solid black;'>" . $ctv->id . " " . $ctv->username . "</td>";
                    $firstrow = false;
                }
                $result .= "<td style='padding: 2px; border: 1px solid black;'>" . SAP::alpha_output($item->vbeln) . "/" . SAP::alpha_output($item->posnr) . "</td>";
                $result .= "</tr>";
            }
        }

        $result .= "</tbody>";
        $result .= "</table><br>";

        Session::put('locale', $locale);
        app('translator')->setLocale(Session::get("locale"));
        return $result;
    }

}