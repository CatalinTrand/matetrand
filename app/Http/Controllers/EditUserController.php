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
            Input::get("email"),
            Input::get("passwd")
        );
    }
}