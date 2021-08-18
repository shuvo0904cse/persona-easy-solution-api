<?php

namespace Database\Seeders;

use App\Models\Grocery;
use Illuminate\Database\Seeder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class GrocerySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $groceries = config("seeder.groceries");
        foreach ($groceries as $grocery){
            $array = [
                "name"    => $grocery
            ];
            $this->groceryModel()->storeData($array);
        }
    }

    public function groceryModel(){
        return new Grocery();
    }
}
