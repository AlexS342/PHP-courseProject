<?php

use Alexs\PhpAdvanced\Blog\Commands\Arguments;
use Alexs\PhpAdvanced\Blog\Commands\CreateUserCommand;
use Alexs\PhpAdvanced\Blog\Exceptions\AppException;
use Psr\Log\LoggerInterface;
use Alexs\PhpAdvanced\Blog\Commands\CreateUser;
use Symfony\Component\Console\Application;

$container = require __DIR__ . '/bootstrap.php';

//$command = $container->get(CreateUserCommand::class);
//
//// Получаем объект логгера из контейнера
//$logger = $container->get(LoggerInterface::class);
//
//try {
//    $command->handle(Arguments::fromArgv($argv));
//} catch (AppException $e) {
//    // Логируем информацию об исключении.
//    // Объект исключения передаётся логгеру с ключом "exception".
//    // Уровень логирования – ERROR
//    $logger->error($e->getMessage(), ['exception' => $e]);
//}

// Создаём объект приложения
$application = new Application();
// Перечисляем классы команд
$commandsClasses = [
    CreateUser::class,
];
foreach ($commandsClasses as $commandClass) {
    // Посредством контейнера
    // создаём объект команды
    $command = $container->get($commandClass);
    // Добавляем команду к приложению
    $application->add($command);
}
// Запускаем приложение
$application->run();

