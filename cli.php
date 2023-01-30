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

use Alexs\PhpAdvanced\Blog\Commands\Arguments;
use Alexs\PhpAdvanced\Blog\Commands\CreateUserCommand;
use Alexs\PhpAdvanced\Blog\Post;
use Alexs\PhpAdvanced\Blog\Repositories\CommitRepository\SQLiteCommitRepository;
use Alexs\PhpAdvanced\Blog\Repositories\PostRepository\SQLitePostRepository;
use Alexs\PhpAdvanced\Blog\User;
use Alexs\PhpAdvanced\Blog\Commit;
use Alexs\PhpAdvanced\Blog\Repositories\UserRepository\InMemoryUsersRepository;
use Alexs\PhpAdvanced\Blog\Repositories\PostRepository\InMemoryPostRepository;
use Alexs\PhpAdvanced\Blog\Repositories\CommitRepository\InMemoryCommitRepository;
use Alexs\PhpAdvanced\Blog\Repositories\UserRepository\SQLiteUserRepository;
use Alexs\PhpAdvanced\Blog\UUID;

require_once __DIR__ . '/vendor/autoload.php';

$connect = include 'sqlite.php';

try {
    $userRepositorySQL = new SQLiteUserRepository($connect);
    $postRepositorySQL = new SQLitePostRepository($connect);
    $commitRepositorySQL = new SQLiteCommitRepository($connect);

    $userRepository = new InMemoryUsersRepository();
    $postRepository = new InMemoryPostRepository();
    $commitRepository = new InMemoryCommitRepository();

    $faker = Faker\Factory :: create ('ru_RU');

    $user = new User(UUID::random(), $faker->firstName(), $faker->lastName, $faker->userName(), $faker->password);
    $post = new Post(UUID::random(), $user, $faker->realText(rand(50, 70)), $faker->realText(rand(150, 300)));
    $commit = new Commit(UUID::random(), $user, $post, $faker->realText(rand(150, 300)));

    $userRepositorySQL->save($user);
    $postRepositorySQL->save($post);
    $commitRepositorySQL->save($commit);

    echo $userRepositorySQL->get(new UUID('ebf6a074-598d-4a28-b1f2-deaa8b380c31')) . PHP_EOL;
    echo $postRepositorySQL->get(new UUID('8610d66d-fe5f-41a5-ae19-6382ba09255a')) . PHP_EOL;
    echo $commitRepositorySQL->get(new UUID('89d83c5c-0445-45ce-a0a6-de4d7455e885')) . PHP_EOL;

    //Работа через терминал (пока только для юзера)
    $command = new CreateUserCommand($userRepositorySQL);
    $command->handle(Arguments::fromArgv($argv));

}catch (Exception $e){
    echo $e->getMessage() . PHP_EOL;
}
















//for($i = 1; $i <= 10; $i++){
//    $user = new User($i, $faker->firstName(), $faker->lastName, $faker->userName(), $faker->password);
//    $post = new Post($i, $user, $faker->realText(rand(50, 70)), $faker->realText(rand(150, 300)) );
//    $commit = new Commit($i, $user, $post, $faker->realText(rand(70, 120)));
//    $userRepository->save($user);
//    $postRepository->save($post);
//    $commitRepository->save($commit);
//}
//
//try {
//    if(!isset($argv[1])){
//        throw new ArgvIdNotArgumentException("Error: Неуказана запрашиваемая сущность");
//    }
//
//    if(isset($argv[2]) && (int)$argv[2] > 0){
//        $id = (int) $argv[2];
//    }else{
//        throw new ArgvIdNotArgumentException("Error: Отсутствует или неправильно указан id запрашиваемой сущности");
//    }
//    echo match ($argv[1]) {
//        'user' => $userRepository->get($id) . PHP_EOL,
//        'post' => $postRepository->get($id) . PHP_EOL,
//        'commit' => $commitRepository->get($id) . PHP_EOL,
//        default => throw new ArgvIdNotArgumentException("Error: Запрашивается несуществующая сущность"),
//    };
//} catch (AppException | Exception $e) {
//    echo $e->getMessage() . PHP_EOL;
//}
//
//echo " " . PHP_EOL;
//echo "Инструкция:" . PHP_EOL;
//echo "Команты для запуска приложения:" . PHP_EOL;
//echo "php cli.php [(string)тип сущности] [(int)id сущности]" . PHP_EOL;
//echo "Пример для получения пользователя: ";
//echo "php cli.php user 4" . PHP_EOL;
//echo "Типы сущностей: user, post, commit" . PHP_EOL;
//echo "id сущностей: от 1 до 10" . PHP_EOL;

