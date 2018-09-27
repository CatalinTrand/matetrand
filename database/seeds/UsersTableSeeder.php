<?php

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::truncate();
        User::create([
            'id'       => "Administrator",
            'username' => "Administrator",
            'role'     => 'Administrator',
            'email'    => "admin@materom.ro",
            'password' => Hash::make('materom'),
            'created_at' => Carbon::now()->getTimestamp()
        ]);
        User::create([
            'id'       => "f20786",
            'username' => "AUTO HELP LTD",
            'role'     => 'Furnizor',
            'lifnr'    => '0000020786',
            'email'    => "oe@autohelp.bg",
            'password' => Hash::make('materom'),
            'created_at' => Carbon::now()->getTimestamp()
        ]);
        User::create([
            'id'       => "f16098",
            'username' => "TENET SRL",
            'role'     => 'Furnizor',
            'lifnr'    => '0000016098',
            'email'    => "dummy@mail.com",
            'password' => Hash::make('materom'),
            'created_at' => Carbon::now()->getTimestamp()
        ]);
        User::create([
            'id'       => "f20019",
            'username' => "STAHLGRUBER GmbHs",
            'role'     => 'Furnizor',
            'lifnr'    => '0000020019',
            'email'    => "dummy2@mail.com",
            'password' => Hash::make('materom'),
            'created_at' => Carbon::now()->getTimestamp()
        ]);
        User::create([
            'id'       => "ra01",
            'username' => "Referent A01",
            'role'     => 'Referent',
            'ekgrp'    => 'A01',
            'email'    => "dummy@mail.com",
            'password' => Hash::make('materom'),
            'created_at' => Carbon::now()->getTimestamp()
        ]);
        User::create([
            'id'       => "ra02",
            'username' => "Referent A02",
            'role'     => 'Referent',
            'ekgrp'    => 'A02',
            'email'    => "dummy@mail.com",
            'password' => Hash::make('materom'),
            'created_at' => Carbon::now()->getTimestamp()
        ]);
    }
}
