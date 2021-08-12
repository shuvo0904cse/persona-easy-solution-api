<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $arrays = [
            [
                "first_name" => "Developer",
                "last_name" => "Test",
                "email" => "developer@gmail.com",
                "password" => Hash::make("123456")
            ],
            [
                "first_name" => "Admin",
                "last_name" => "Admin",
                "email" => "admin@gmail.com",
                "password" => Hash::make("123456")
            ]
        ];

        foreach ($arrays as $array){
            $array = [
                "first_name"    => $array['first_name'],
                "last_name"     => $array['last_name'],
                "email"         => $array['email'],
                "password"      => $array['password'],
                "email_verified_at" => Carbon::now(),
                "status"        => "ACTIVE",
            ];
            $this->userModel()->storeData($array);
        }
    }

    public function userModel(){
        return new User();
    }
}
