<?php

require_once __DIR__ . '/vendor/autoload.php';

use Alexs\PhpCourseProject\Article;
use Alexs\PhpCourseProject\Commit;
use Alexs\PhpCourseProject\User;

$faker = Faker\Factory::create();

if($argv[1] === 'user'){
    $user = new User($faker->randomDigitNotNull, $faker->firstName, $faker->lastName);
    print $user . PHP_EOL;
}
if($argv[1] === 'post'){
    $post = new Article($faker->randomDigitNotNull, $faker->randomDigitNotNull, $faker->text(30), $faker->text(200));
    print $post . PHP_EOL;
}
if($argv[1] === 'commit'){
    $commit = new Commit($faker->randomDigitNotNull, $faker->randomDigitNotNull, $faker->randomDigitNotNull, $faker->text(150));
    print $commit . PHP_EOL;
}

