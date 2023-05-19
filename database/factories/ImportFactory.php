<?php

namespace Database\Factories;

use Faker\Generator;
use W360\ImportGpgExcel\Models\Import;
use W360\ImportGpgExcel\Models\User;


/** @var \Illuminate\Database\Eloquent\Factory $factory */

$factory->define(Import::class, function (Generator $faker) {
    $fileName = $faker->slug(4).".pgp";
    return [
        'name' => $fileName,
        'report' => 'log-'.$fileName,
        'author_id' => 1,
        'author_type' => User::class,
        'storage' => $faker->slug(2),
        'model_type' => User::class
    ];
});
