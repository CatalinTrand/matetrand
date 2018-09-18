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
    }
}
