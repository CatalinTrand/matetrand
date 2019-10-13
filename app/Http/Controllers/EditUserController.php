<?php

namespace App\Http\Controllers;


use App\Materom\EditUsers;
use App\Materom\SAP;
use Illuminate\Support\Facades\Input;

class EditUserController
{
    public function editUsers(){
        return EditUsers::editUser(
            Input::get("id"),
            Input::get("role"),
            Input::get("username"),
            Input::get("api_token"),
            Input::get("lang"),
            Input::get("lifnr"),
            Input::get("ekgrp"),
            Input::get("active"),
            Input::get("email"),
            Input::get("sap_system"),
            Input::get("readonly"),
            Input::get("pnad"),
            Input::get("none"),
            Input::get("mirror_user1"),
            Input::get("ctvadmin"),
            Input::get("rgroup")
        );
    }

    public function selDel(){
        EditUsers::delSel(
            Input::get("id"),
            Input::get("sel")
        );
    }

    public function refDel(){
        EditUsers::refDel(
            Input::get("id"),
            Input::get("ref")
        );
    }

    public function agentDel(){
        EditUsers::agentDel(
            Input::get("id"),
            Input::get("agent")
        );
    }

    public function kunnrDel()
    {
        EditUsers::kunnrDel(
            Input::get("id"),
            SAP::alpha_input(Input::get("kunnr"))
        );
    }

}