<?php

namespace App\Http\Controllers;


use App\Materom\EditUsers;
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
            Input::get("sapuser"),
            Input::get("lifnr"),
            Input::get("ekgrp"),
            Input::get("active"),
            Input::get("email")
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
}