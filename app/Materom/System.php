<?php
/**
 * Created by PhpStorm.
 * User: Radu
 * Date: 25.01.2019
 * Time: 05:07
 */

namespace App\Materom;


use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class System
{
    const is300 = true;

    const deftable_global_rfc_config = "global_rfc_config";
    const deftable_ewm_rfc_config = "ewm_rfc_config";
    const deftable_pitemchg = "pitemchg";
    const deftable_pitemchg_proposals = "pitemchg_proposals";
    const deftable_pitems = "pitems";
    const deftable_porders = "porders";
    const deftable_pitems_cache = "pitems_cache";
    const deftable_porders_cache = "porders_cache";
    const deftable_roles = "roles";
    const deftable_ewm_roles = "ewm_roles";
    const deftable_sap_kna1 = "sap_kna1";
    const deftable_sap_lfa1 = "sap_lfa1";
    const deftable_sap_t024 = "sap_t024";
    const deftable_sap_zpret_adaos = "sap_zpret_adaos";
    const deftable_user_agent_clients = "user_agent_clients";
    const deftable_users_agent = "users_agent";
    const deftable_users_cli = "users_cli";
    const deftable_users_sel = "users_sel";
    const deftable_sap_client_agents = "sap_client_agents";
    const deftable_stat_orders = "stat_orders";

    public static $system;
    public static $system_name;

    public static $table_global_rfc_config = self::deftable_global_rfc_config;
    public static $table_ewm_rfc_config = self::deftable_ewm_rfc_config;
    public static $table_pitemchg = self::deftable_pitemchg;
    public static $table_pitemchg_proposals = self::deftable_pitemchg_proposals;
    public static $table_pitems = self::deftable_pitems;
    public static $table_porders = self::deftable_porders;
    public static $table_pitems_cache = self::deftable_pitems_cache;
    public static $table_porders_cache = self::deftable_porders_cache;
    public static $table_roles = self::deftable_roles;
    public static $table_ewm_roles = self::deftable_ewm_roles;
    public static $table_sap_kna1 = self::deftable_sap_kna1;
    public static $table_sap_lfa1 = self::deftable_sap_lfa1;
    public static $table_sap_t024 = self::deftable_sap_t024;
    public static $table_sap_zpret_adaos = self::deftable_sap_zpret_adaos;
    public static $table_user_agent_clients = self::deftable_user_agent_clients;
    public static $table_users_agent = self::deftable_users_agent;
    public static $table_users_cli = self::deftable_users_cli;
    public static $table_users_sel = self::deftable_users_sel;
    public static $table_sap_client_agents = self::deftable_sap_client_agents;
    public static $table_stat_orders = self::deftable_stat_orders;

    public static $mirroring;

    public static function init($sap_system = "")
    {
        self::$mirroring = false;
        $sap_system = trim($sap_system);
        if ("X" . $sap_system == "X200") $sap_system = "";
        self::$system = $sap_system;

        self::$system_name = $sap_system;
        if (empty($sap_system)) self::$system_name = "200";

        $userid = "<unknown>";
        if (Auth::check()) $userid = Auth::user()->id;

        self::$table_global_rfc_config = self::deftable_global_rfc_config;
        self::$table_ewm_rfc_config = self::deftable_ewm_rfc_config;
        self::$table_pitemchg = self::deftable_pitemchg;
        self::$table_pitemchg_proposals = self::deftable_pitemchg_proposals;
        self::$table_pitems = self::deftable_pitems;
        self::$table_porders = self::deftable_porders;
        self::$table_pitems_cache = self::deftable_pitems_cache;
        self::$table_porders_cache = self::deftable_porders_cache;
        self::$table_roles = self::deftable_roles;
        self::$table_ewm_roles = self::deftable_ewm_roles;
        self::$table_sap_kna1 = self::deftable_sap_kna1;
        self::$table_sap_lfa1 = self::deftable_sap_lfa1;
        self::$table_sap_t024 = self::deftable_sap_t024;
        self::$table_sap_zpret_adaos = self::deftable_sap_zpret_adaos;
        self::$table_user_agent_clients = self::deftable_user_agent_clients;
        self::$table_users_agent = self::deftable_users_agent;
        self::$table_users_cli = self::deftable_users_cli;
        self::$table_users_sel = self::deftable_users_sel;
        self::$table_stat_orders = self::deftable_stat_orders;

        if (empty($sap_system)) return;

        $sap__system = "_" . $sap_system;
        self::$table_global_rfc_config .= $sap__system;
        self::$table_ewm_rfc_config .= $sap__system;
        self::$table_pitemchg .= $sap__system;
        self::$table_pitemchg_proposals .= $sap__system;
        self::$table_pitems .= $sap__system;
        self::$table_porders .= $sap__system;
        self::$table_pitems_cache .= $sap__system;
        self::$table_porders_cache .= $sap__system;
        self::$table_roles .= $sap__system;
        self::$table_ewm_roles .= $sap__system;
        self::$table_sap_kna1 .= $sap__system;
        self::$table_sap_lfa1 .= $sap__system;
        self::$table_sap_t024 .= $sap__system;
        self::$table_sap_zpret_adaos .= $sap__system;
        self::$table_user_agent_clients .= $sap__system;
        self::$table_users_agent .= $sap__system;
        self::$table_users_cli .= $sap__system;
        self::$table_users_sel .= $sap__system;
        self::$table_stat_orders .= $sap__system;
    }

    public static function init_mirror($sap_system = "")
    {
        self::init($sap_system);
        self::$mirroring = true;
    }

    public static function ic_on()
    {
        if (self::$mirroring) return false;
        $ic = strtoupper(trim(env("MATEROM_INTERCOMPANY", "N")));
        if ($ic == "TEST") return substr(Auth::user()->id, 0, 3) == "ic_";
        return $ic == "Y" || $ic == "1" || $ic == "ON" || $ic == "YES" || $ic == "SELF";
    }

    public static function getMirrorUser($stage, $mirror_ebeln, $mirror_ebelp) // get intercompany user
    {
        $mirror_user1 = null;
        if (!(self::ic_on() && !empty(trim($mirror_ebeln)) && !empty(trim(Auth::user()->mirror_user1)))) return null;
        $role = Auth::user()->role;
        if ($role == "Administrator") {
            if (empty(trim($stage)) || $stage == "F") $role = "Furnizor";
            elseif ($stage == "R") $role = "Referent";
            elseif ($stage == "C") $role = "CTV";
            else return null;
        }
        if (($role == "Furnizor" || $role == "Referent" ) && empty(self::$system)) return null;
        if (($role == "CTV") && !empty(self::$system)) return null;
        $curr_sap_system = self::$system;
        System::init(empty(self::$system) ? "300" : "");
        $item = DB::table(System::$table_pitems)->where([['ebeln', '=', $mirror_ebeln], ['ebelp', '=', $mirror_ebelp]])->first();
        if (empty($item)) return null;
        $order = DB::table(System::$table_porders)->where('ebeln', $mirror_ebeln)->first();
        if (empty($order)) return null;
        if ($stage <> $item->stage) {
            Log::error("Mirroring error: unsynchronized order items (system ".self::$system_name.", order/item $mirror_ebeln/".SAP::alpha_output($mirror_ebelp).")");
            System::init($curr_sap_system);
            return null;
        }
        if ($role == "Furnizor") {
            $mirror_user1 = self::getMirrorVendorUser($order->lifnr);
        } elseif ($role == "Referent") {
            $mirror_user1 = self::getMirrorRefUser($order->ekgrp, $order->lifnr);
        } elseif ($role == "CTV") {
            if (empty($item->vbeln) || empty($item->kunnr) || ($item->vbeln == Orders::stockorder)) return null;
            $mirror_user1 = self::getMirrorCTVuser($item->kunnr);
        }
        System::init($curr_sap_system);
        if (is_null($mirror_user1)) $mirror_user1 = "";
        return $mirror_user1;
    }

    public static function d_ic($stage, $mirror_ebeln, $mirror_ebelp) // direct intercompany (300->200)
    {
        if (!(self::ic_on() && self::$system == "300" && !empty(trim($mirror_ebeln)) && !empty(trim(Auth::user()->mirror_user1)))) return null;
        return self::getMirrorUser($stage, $mirror_ebeln, $mirror_ebelp);
    }

    public static function r_ic($stage, $mirror_ebeln, $mirror_ebelp) // reversed intercompany (200->300)
    {
        if (!(self::ic_on() && self::$system == "" && !empty(trim($mirror_ebeln)) && !empty(trim(Auth::user()->mirror_user1)))) return null;
        return self::getMirrorUser($stage, $mirror_ebeln, $mirror_ebelp);
    }

    public static function getMirrorVendorUser($lifnr)
    {
        return DB::table("users")
            ->where([["role", "=", "Furnizor"],
                     ["lifnr", "=", $lifnr],
                     ["active", "=", 1],
                     ["sap_system", "=", System::$system]])->value("id");
    }

    public static function getMirrorRefUser($ekgrp, $lifnr)
    {
        $users = DB::table("users")
            ->where([["role", "=", "Referent"],
                ["ekgrp", "=", $ekgrp],
                ["active", "=", 1],
                ["sap_system", "=", System::$system]])->get(["id"]);
        if (empty($users)) return null;
        if (count($users) == 1) return $users[0]->id;
        foreach ($users as $user) {
            $vendors = DB::select("select distinct id from users join users_ref using (id) ".
                              "where users_ref.refid = '$user->id' and ".
                              "users.role = 'Furnizor' and users.lifnr = '$lifnr' and users.active = 1 and users.sap_system = '".System::$system."'");
            if (!empty($vendors)) return $user->id;
        }
        return $users[0]->id;
    }

    public static function getMirrorCTVUser($kunnr)
    {
        $local_table_users_agent = System::deftable_users_agent;
        $local_table_user_agent_clients = System::deftable_user_agent_clients;
        if (empty(Auth::user()->sap_system)) {
            $local_table_users_agent .= "_300";
            $local_table_user_agent_clients .= "_300";
        }
        $dusers = DB::select("select id, count(*) as count from ". $local_table_users_agent ." join ". $local_table_user_agent_clients ." using (id) where kunnr = '$kunnr' group by id order by count, id");
        if ($dusers == null || empty($dusers)) $duser = DB::table($local_table_user_agent_clients)->where("kunnr", $kunnr)->value("id");
        else $duser = $dusers[0]->id;
        return $duser;
    }

    public static function dateOnly($date)
    {
        $ldate = new Carbon($date);
        $ldate->hour = 0;
        $ldate->minute = 0;
        $ldate->second = 0;
        return $ldate;
    }
}