<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use App\Materom\Data;

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
        'id','role', 'username', 'email', 'password','lang','ekgrp','lifnr','sapuser', 'sap_system'
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

    // specific table names
    public $pitemchg;
    public $pitemchg_proposals;
    public $pitems;
    public $porders;
    public $pitems_cache;
    public $porders_cache;
    public $roles;

}
