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
                "name" => "Developer",
                "email" => "developer@gmail.com",
                "password" => Hash::make("123456"),
            ],
            [
                "name" => "Admin",
                "email" => "admin@gmail.com",
                "password" => Hash::make("123456")
            ]
        ];

        foreach ($arrays as $array){
            $array = [
                "name"          => $array['name'],
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
