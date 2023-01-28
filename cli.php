<?php
/*
Автозагрузчик заменяет в namespace то, что указано в composer.json в поле "psr-4" на то, что указано через двоеточие в
этом же поле, а не то, что указано в поле "name"
Часть кода из composer.json:
    "name": "alexs/php_advanced",
    "autoload": {
        "psr-4": {
            "Alexs\\PhpAdvanced\\": "src/"
        }
    }
Правильный namespace: namespace alexs\php_advanced\Blog
Правильный use: use alexs\php_advanced\Blog\User

Возможные ошибки:
    взять путь из поля "name",
    написать в пути основную папку src, которая подставляется автозагрузчиком

Неправильный namespace: namespace alexs\php_advanced\src\php_advanced\Person
Неправильный use: alexs\php_advanced\src\Person\Name
 */

use Alexs\PhpAdvanced\Blog\Post;
use Alexs\PhpAdvanced\Blog\User;
use Alexs\PhpAdvanced\Blog\Commit;
use Alexs\PhpAdvanced\Blog\Repositories\InMemoryUsersRepository;
use Alexs\PhpAdvanced\Blog\Repositories\InMemoriPostRepository;
use Alexs\PhpAdvanced\Blog\Repositories\InMemoryCommitRepository;
use Alexs\PhpAdvanced\Blog\Exceptions\ArgvIdNotArgumentException;
use Alexs\PhpAdvanced\Blog\Exceptions\AppException;

require_once __DIR__ . '/vendor/autoload.php';

$userRepository = new InMemoryUsersRepository();
$postRepository = new InMemoriPostRepository();
$commitRepository = new InMemoryCommitRepository();

$faker = Faker\Factory :: create ('ru_RU');

for($i = 1; $i <= 10; $i++){
    $user = new User($i, $faker->name(), $faker->userName());
    $post = new Post($i, $user, $faker->realText(rand(50, 70)), $faker->realText(rand(150, 300)) );
    $commit = new Commit($i, $user, $post, $faker->realText(rand(70, 120)));
    $userRepository->save($user);
    $postRepository->save($post);
    $commitRepository->save($commit);
}

try {
    if(isset($argv[2]) && (int)$argv[2] > 0){
        $id = (int) $argv[2];
    }else{
        throw new ArgvIdNotArgumentException("Отсутствует или неправильно указан id запрашиваемой сущности");
    }
    switch ($argv[1]){
        case 'user':
            echo 'User' . PHP_EOL;
            echo $userRepository->get($id) . PHP_EOL;
            break;
        case 'post':
            echo 'Post' . PHP_EOL;
            echo $postRepository->get($id) . PHP_EOL;
            break;
        case 'commit':
            echo 'Commit' . PHP_EOL;
            echo $commitRepository->get($id) . PHP_EOL;
            break;
        default:
            echo "Введены неправильные параметры";
    }
} catch (AppException | Exception $e) {
    echo "error switch" . PHP_EOL;
    echo $e->getMessage() . PHP_EOL;
}