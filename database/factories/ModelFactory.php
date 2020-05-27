<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\User::class, function (Faker\Generator $faker) {
    return [
        "role_id"           => rand(1, 4),
        "firstname"         => $faker->firstName,
        "lastname"          => $faker->lastName,
        "email"             => $faker->unique()->safeEmail,
        "password"          => bcrypt('123456'),
        "contact_person"    => $faker->name,
        "sex"               => rand(1, 2),
        "dob"               => $faker->date('Y-m-d'),
        "place_of_birth"    => $faker->city,
        "address"           => $faker->address,
        "country"           => $faker->country,
        "civil_status"      => $faker->name,
        "spouse_first_name" => $faker->firstName,
        "spouse_last_name"  => $faker->lastName,
        "exp_date"          => $faker->date('Y-m-d'),
        "pp_number"         => rand(11111, 2222222222),
        "pp_exp_date"       => $faker->date('Y-m-d'),
        "scan_id"           => $faker->imageUrl($width = 480, $height = 480),
        "department"        => rand(1, 2),
        "territory"         => rand(1, 5),
        "id_number"         => rand(11111, 2222222222),
        "transaction_type"  => rand(1, 2),
        "transaction_fee"   => rand(1, 100),
        "commission_type"   => rand(1, 2),
        "commission_fee"    => rand(1, 100),
        "status"            => rand(1, 4),
        "is_verified"       => rand(0, 1),
        "profile_pic"       => $faker->imageUrl($width = 480, $height = 480),
    ];
});

$factory->define(App\Models\UserBank::class, function (Faker\Generator $faker) {
    return [
        "bank_id"            => rand(1, 5),
        "account_number"     => rand(111111111, 9999999999),
        "name_on_account"    => $faker->name,
        "address_on_account" => $faker->address,
    ];
});

$factory->define(App\Models\UserWork::class, function (Faker\Generator $faker) {
    return [
        "employer"             => $faker->name,
        "address"              => $faker->address,
        "telephone"            => str_replace('+', '', $faker->e164PhoneNumber),
        "cellphone"            => str_replace('+', '', $faker->e164PhoneNumber),
        "position"             => $faker->name,
        "employed_since"       => $faker->date('Y-m-d'),
        "employment_type"      => rand(1, 3),
        "contract_expires"     => $faker->date('Y-m-d'),
        "department"           => $faker->name,
        "supervisor_name"      => $faker->name,
        "supervisor_telephone" => str_replace('+', '', $faker->e164PhoneNumber),
        "salary"               => rand(1, 1000000),
        "payment_frequency"    => rand(1, 3),
    ];
});

$factory->define(App\Models\UserReference::class, function (Faker\Generator $faker) {
    return [
        "first_name"   => $faker->firstName,
        "last_name"    => $faker->lastName,
        "relationship" => $faker->name,
        "telephone"    => str_replace('+', '', $faker->e164PhoneNumber),
        "cellphone"    => str_replace('+', '', $faker->e164PhoneNumber),
        "address"      => $faker->address,
    ];
});

$factory->defineAs(App\Models\UserInfo::class, 'telephone', function (Faker\Generator $faker) {
    return [
        "type"  => '1',
        "value" => str_replace('+', '', $faker->e164PhoneNumber),
    ];
});
$factory->defineAs(App\Models\UserInfo::class, 'cellphone', function (Faker\Generator $faker) {
    return [
        "type"  => '2',
        "value" => str_replace('+', '', $faker->e164PhoneNumber),
    ];
});
$factory->defineAs(App\Models\UserInfo::class, 'emails', function (Faker\Generator $faker) {
    return [
        "type"  => '3',
        "value" => $faker->email,
    ];
});