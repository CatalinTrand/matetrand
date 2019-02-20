<?php
/**
 * Created by PhpStorm.
 * User: Radu
 * Date: 25.01.2019
 * Time: 05:07
 */

namespace App\Materom;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class System
{
    const is300 = true;

    const deftable_global_rfc_config = "global_rfc_config";
    const deftable_pitemchg = "pitemchg";
    const deftable_pitemchg_proposals = "pitemchg_proposals";
    const deftable_pitems = "pitems";
    const deftable_porders = "porders";
    const deftable_pitems_cache = "pitems_cache";
    const deftable_porders_cache = "porders_cache";
    const deftable_roles = "roles";
    const deftable_sap_kna1 = "sap_kna1";
    const deftable_sap_lfa1 = "sap_lfa1";
    const deftable_sap_t024 = "sap_t024";
    const deftable_user_agent_clients = "user_agent_clients";
    const deftable_users_agent = "users_agent";
    const deftable_users_cli = "users_cli";
    const deftable_users_sel = "users_sel";
    const deftable_sap_client_agents = "sap_client_agents";

    public static $system;
    public static $system_name;

    public static $table_global_rfc_config = self::deftable_global_rfc_config;
    public static $table_pitemchg = self::deftable_pitemchg;
    public static $table_pitemchg_proposals = self::deftable_pitemchg_proposals;
    public static $table_pitems = self::deftable_pitems;
    public static $table_porders = self::deftable_porders;
    public static $table_pitems_cache = self::deftable_pitems_cache;
    public static $table_porders_cache = self::deftable_porders_cache;
    public static $table_roles = self::deftable_roles;
    public static $table_sap_kna1 = self::deftable_sap_kna1;
    public static $table_sap_lfa1 = self::deftable_sap_lfa1;
    public static $table_sap_t024 = self::deftable_sap_t024;
    public static $table_user_agent_clients = self::deftable_user_agent_clients;
    public static $table_users_agent = self::deftable_users_agent;
    public static $table_users_cli = self::deftable_users_cli;
    public static $table_users_sel = self::deftable_users_sel;
    public static $table_sap_client_agents = self::deftable_sap_client_agents;

    public static function init($sap_system = "")
    {
        $sap_system = trim($sap_system);
        if ("X".$sap_system == "X200") $sap_system = "";
        self::$system = $sap_system;

        self::$system_name = $sap_system;
        if (empty($sap_system)) self::$system_name = "200";

        $userid = "<unknown>";
        if (Auth::check()) $userid = Auth::user()->id;

        self::$table_global_rfc_config = self::deftable_global_rfc_config;
        self::$table_pitemchg = self::deftable_pitemchg;
        self::$table_pitemchg_proposals = self::deftable_pitemchg_proposals;
        self::$table_pitems = self::deftable_pitems;
        self::$table_porders = self::deftable_porders;
        self::$table_pitems_cache = self::deftable_pitems_cache;
        self::$table_porders_cache = self::deftable_porders_cache;
        self::$table_roles = self::deftable_roles;
        self::$table_sap_kna1 = self::deftable_sap_kna1;
        self::$table_sap_lfa1 = self::deftable_sap_lfa1;
        self::$table_sap_t024 = self::deftable_sap_t024;
        self::$table_user_agent_clients = self::deftable_user_agent_clients;
        self::$table_users_agent = self::deftable_users_agent;
        self::$table_users_cli = self::deftable_users_cli;
        self::$table_users_sel = self::deftable_users_sel;

        if (empty($sap_system)) return;

        $sap_system = "_" . $sap_system;
        self::$table_global_rfc_config .= $sap_system;
        self::$table_pitemchg .= $sap_system;
        self::$table_pitemchg_proposals .= $sap_system;
        self::$table_pitems .= $sap_system;
        self::$table_porders .= $sap_system;
        self::$table_pitems_cache .= $sap_system;
        self::$table_porders_cache .= $sap_system;
        self::$table_roles .= $sap_system;
        self::$table_sap_kna1 .= $sap_system;
        self::$table_sap_lfa1 .= $sap_system;
        self::$table_sap_t024 .= $sap_system;
        self::$table_user_agent_clients .= $sap_system;
        self::$table_users_agent .= $sap_system;
        self::$table_users_cli .= $sap_system;
        self::$table_users_sel .= $sap_system;
    }
}