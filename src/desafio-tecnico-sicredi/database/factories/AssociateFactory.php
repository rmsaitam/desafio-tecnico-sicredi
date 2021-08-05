<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Associate;
use Faker\Generator as Faker;
use JansenFelipe\FakerBR\FakerBR;

$factory->define(Associate::class, function (Faker $faker) {

    $faker->addProvider(new FakerBR($faker));

    return [
        'name' =>$faker->name,
        'document' => $faker->cpf
    ];
});
