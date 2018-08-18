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
            Input::get("lifnr"),
            Input::get("ekgrp"),
            Input::get("active"),
            Input::get("email")
        );
    }
}