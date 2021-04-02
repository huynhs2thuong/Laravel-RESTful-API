<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Company;
use App\Employee;
use App\Employee_to_company;
use App\Model;
use App\Territory;
use Faker\Generator as Faker;

$factory->define(Employee_to_company::class, function (Faker $faker) {
    $company_sid = Company::whereSid('72c614f5-91a7-3002-8e2b-0602aebdc854')->first()->sid;
    $territory_sid = Territory::whereCompanySid($company_sid)->first();
    return [
        'employee_sid'=>Employee::inRandomOrder()->first()->sid,
        'company_sid' =>$company_sid,
        'territory_sid'=> $territory_sid->sid,
        'is_active' => true,
    ];
});
