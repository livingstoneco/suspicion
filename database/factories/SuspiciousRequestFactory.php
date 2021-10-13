<?php 

use Livingstoneco\Suspicion\SuspiciousRequest;

$factory->define(SuspiciousRequest::class, function(Faker\Generator $faker) {
    return [
        'ip' => $faker->ipv4,
        'url' => $faker->url,
        'params' => [
            'name' => $faker->name,
            'email' => $faker->email,
            'message' => $faker->text(200)
        ],
        headers => [
            'host' => [$faker->url],
            'user-agent' [$faker->userAgent],
            'dnt' => [1].
        ],
        cookies => [
            'sid' => $faker->sha256,
        ],
        'userAgent' => $faker->userAgent,
        'fingerprint' => $faker->sha256

    ];
});