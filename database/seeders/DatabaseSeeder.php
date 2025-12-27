<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\City;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $yde = City::create(['name' => 'Yaoundé', "country_name" => "Cameroun", "country_code" => "CM"]);
        $dla = City::create(['name' => 'Douala', "country_name" => "Cameroun", "country_code" => "CM"]);


        $role = Role::create(['name' => 'volunteer']);
        $role = Role::create(['name' => 'partner']);
        $role = Role::create(['name' => 'user']);
        $role = Role::create(['name' => 'admin']);

        $admin = User::create([
            "name" => "Admin ONOH",
            "email" => "admin@onoh.org",
            "password" => Hash::make("passwordOnoh"),
        ]);
        
        $admin->assignRole(['admin']);
    }
}
