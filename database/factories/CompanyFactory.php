<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Company;

use Faker\Generator as Faker;

$factory->define(Company::class, function (Faker $faker) {
    return [
        'sid' => $faker->uuid,
        'name' => $faker->company,
        'email' => $faker->unique()->companyEmail,
        'phone' =>$faker->phoneNumber,
        'address_1' => $faker->address,
        'city' => $faker->city,
        'state' => $faker->state,
        'is_active' => true,
    ];
});
