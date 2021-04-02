<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(
          // RoleSeeder::class

           //EmloyeeSeeder::class
           CompanySeeder::class
           //TerritorySeeder::class
           //Employee_to_Company_Seeder::class
        );
    }
}
