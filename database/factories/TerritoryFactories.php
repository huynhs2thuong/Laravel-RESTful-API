<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Company;
use App\Model;
use App\Territory;
use Faker\Generator as Faker;

$factory->define(Territory::class, function (Faker $faker) {
    return [
        'sid' => $faker->uuid,
        'code' => $faker->countryCode,
        'name' => $faker->country,
        'company_sid' =>Company::inRandomOrder()->first()->sid,
        'is_active' => true,
    ];
});
