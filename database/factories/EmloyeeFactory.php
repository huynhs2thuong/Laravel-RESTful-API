<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */


use App\Employee;
use App\Role;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\Hash;

$factory->define(Employee::class, function (Faker $faker) {
    return [
        'sid' => $faker->uuid,
        'username' => $faker->userName,
        'phone_number' => $faker->phoneNumber,
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->unique()->safeEmail,
        'password' => Hash::make('1234'),
        'address_1' => $faker->address,
        'city' => $faker->city,
        'avatar' => $faker->imageUrl(),
        'role' => 'FIELD',
        'first_login' => 0,
        'is_active' => true,
    ];
});
