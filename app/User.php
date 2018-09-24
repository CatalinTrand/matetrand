<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

use Cmgmyr\Messenger\Traits\Messagable;

class User extends Authenticatable
{
    use Notifiable;
    use Messagable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','role', 'username', 'email', 'password','lang','ekgrp','lifnr','sapuser',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public $keyType = 'string';
    public $incrementing = false;

    public static function getOrders($id) {
        $users = DB::select("select * from users where id ='$id'");
        if (count($users) == 0) return array();
        $user = $users[0];

        if ($user->role == "Administrator")
            return DB::select("select * from porders order by vbeln, ebeln");

        if ($user->role == "CTV")
            return DB::select("select * from porders where ctv = '$id' order by vbeln, ebeln");

        if ($user->role == "Referent")
            return DB::select("select * from porders where ekgrp = '$user->ekgrp' order by vbeln, ebeln");

        // Furnizor
        $sql = "select * from porders where id ='$id'";
        /*
        $sql = "select * from porders where lifnr = '$user->lifnr'";
        $brands = DB::select("select * from users_sel where id ='$id'");
        $xsql = "";
        foreach($brands as $brand) {
            $sel1 = "";
            if (!empty(trim($brand->wglif)))
                $sel1 = "wglif = '$brand->wglif'";
            if (!empty(trim($brand->mfrnr))) {
                if (!empty($sel1)) $sel1 .= " and ";
                $sel1 = "wglif = '$brand->wglif'";
            }
            if (empty($sel1)) continue;
            $sel1 = "(". $sel1 . ")";
            if (empty($zsql)) $xsql = $sel1;
            else $xsql .= ' or ' . $sel1;
        }
        if (!empty($xsql)) $sql .= " and (" . $xsql . ")";
        $sql .= " order by ebeln";
        */
        return DB::select($sql);
    }
}
