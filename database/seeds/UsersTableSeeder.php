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
            'id'       => "f11325",
            'username' => "Robert Bosch GmbH",
            'role'     => 'Furnizor',
            'lifnr'    => '0000011325',
            'email'    => "robert@materom.ro",
            'password' => Hash::make('materom'),
            'created_at' => Carbon::now()->getTimestamp()
        ]);
        User::create([
            'id'       => "f20310",
            'username' => "EFA Autoteilewelt Frankfurt",
            'role'     => 'Furnizor',
            'lifnr'    => '0000020310',
            'email'    => "efa@materom.ro",
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
        User::create([
            'id'       => "rc02",
            'username' => "Referent C02",
            'role'     => 'Referent',
            'ekgrp'    => 'C02',
            'email'    => "dummy@mail.com",
            'password' => Hash::make('materom'),
            'created_at' => Carbon::now()->getTimestamp()
        ]);
        User::create([
            'id'       => "rc03",
            'username' => "Referent C03",
            'role'     => 'Referent',
            'ekgrp'    => 'C03',
            'email'    => "dummy@mail.com",
            'password' => Hash::make('materom'),
            'created_at' => Carbon::now()->getTimestamp()
        ]);
    }
}
