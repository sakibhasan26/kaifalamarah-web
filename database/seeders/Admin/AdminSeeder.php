<?php

namespace Database\Seeders\Admin;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Admin::create([
            'firstname'     => "App",
            'lastname'      => "Devs",
            'username'      => "appdevs",
            'email'         => "superadmin@appdevs.net",
            'password'      => Hash::make("appdevs"),
            'created_at'    => now(),
            'status'        => true,
        ]);
    }
}
