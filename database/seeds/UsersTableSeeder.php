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
            'id'       => "Admin",
            'username' => "Administrator (200)",
            'role'     => 'Administrator',
            'email'    => "admin@materom.ro",
            'password' => Hash::make('materom'),
            'created_at' => Carbon::now()->getTimestamp(),
            'sap_system' => '200'
        ]);
        User::create([
            'id'       => "Admin300",
            'username' => "Administrator (300)",
            'role'     => 'Administrator',
            'email'    => "admin@materom.ro",
            'password' => Hash::make('materom'),
            'created_at' => Carbon::now()->getTimestamp(),
            'sap_system' => '300'
        ]);
        User::create([
            'id'       => "radu",
            'username' => "Radu Trandafir (200)",
            'role'     => 'Administrator',
            'email'    => "admin@materom.ro",
            'password' => Hash::make('materom'),
            'created_at' => Carbon::now()->getTimestamp(),
            'sap_system' => '200'
        ]);
        User::create([
            'id'       => "radu300",
            'username' => "Radu Trandafir (300)",
            'role'     => 'Administrator',
            'email'    => "admin@materom.ro",
            'password' => Hash::make('materom'),
            'created_at' => Carbon::now()->getTimestamp(),
            'sap_system' => '300'
        ]);
    }
}
