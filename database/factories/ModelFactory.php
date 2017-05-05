<?php

use App\User;
use App\Concert;
use Carbon\Carbon;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(Concert::class, function (Faker\Generator $faker) {

    return [

        'title' => 'Example Bane',

        'subtitle' => 'with the Fake Openers',

        'date' => Carbon::parse('+2 weeks'),

        'ticket_price' => 2000,

        'venue' => 'The Example Theatre',

        'venue_address' => '123 Example Lane',

        'city' => 'Fakeville',

        'state' => 'ON',

        'zip' => '90210',

        'additional_information' => 'Some sample additional information'

    ];

});

$factory->state(Concert::class, 'published', function ($faker) {
    return [
        'published_at' => Carbon::parse('-1 week'),
    ];
});

$factory->state(Concert::class, 'unpublished', function ($faker) {
    return [
        'published_at' => null,
    ];
});
