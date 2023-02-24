<?php

use Alexs\PhpAdvanced\Blog\Commands\Arguments;
use Alexs\PhpAdvanced\Blog\Commands\CreateUserCommand;
use Alexs\PhpAdvanced\Blog\Exceptions\AppException;
use Psr\Log\LoggerInterface;

$container = require __DIR__ . '/bootstrap.php';

$command = $container->get(CreateUserCommand::class);

// Получаем объект логгера из контейнера
$logger = $container->get(LoggerInterface::class);

try {
    $command->handle(Arguments::fromArgv($argv));
} catch (AppException $e) {
    // Логируем информацию об исключении.
    // Объект исключения передаётся логгеру с ключом "exception".
    // Уровень логирования – ERROR
    $logger->error($e->getMessage(), ['exception' => $e]);
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

