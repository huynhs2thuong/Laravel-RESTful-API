<?php

use App\Employee_to_company;
use Illuminate\Database\Seeder;

class Employee_to_Company_Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Employee_to_company::class, 10)->create();
    }
}
