<?php

use Alexs\PhpAdvanced\Blog\Exceptions\AppException;
use Alexs\PhpAdvanced\Http\Actions\Commits\CreateCommit;
use Alexs\PhpAdvanced\Http\Actions\Commits\FindCommitByUuid;
use Alexs\PhpAdvanced\Http\Actions\Like\CreateLike;
use Alexs\PhpAdvanced\Http\Actions\Like\FindAllLikeByPostUuid;
use Alexs\PhpAdvanced\Http\Actions\Like\FindLikeByUuid;
use Alexs\PhpAdvanced\Http\Actions\Posts\CreatePost;
use Alexs\PhpAdvanced\Http\Actions\Posts\FindPostByUuid;
use Alexs\PhpAdvanced\Http\Actions\Users\CreateUser;
use Alexs\PhpAdvanced\Http\Actions\Users\FindByUsername;
use Alexs\PhpAdvanced\Http\ErrorResponse;
use Alexs\PhpAdvanced\Http\Request;
use Alexs\PhpAdvanced\Blog\Exceptions\HttpException;
use Alexs\PhpAdvanced\Http\Actions\Users\DeleteUserByUuid;
use Alexs\PhpAdvanced\Http\Actions\Posts\DeletePostByUuid;
use Alexs\PhpAdvanced\Http\Actions\Commits\DeleteCommitByUuid;
use Alexs\PhpAdvanced\Http\Actions\Like\DeleteLikeByUuid;
use Psr\Log\LoggerInterface;


// Подключаем файл bootstrap.php
// и получаем настроенный контейнер
$container = require __DIR__ . '/bootstrap.php';
$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input'),
);
// Получаем объект логгера из контейнера
$logger = $container->get(LoggerInterface::class);
try {
    $path = $request->path();
} catch (HttpException $e) {
    // Логируем сообщение с уровнем WARNING
    $logger->warning($e->getMessage());
    (new ErrorResponse)->send();
    return;
}
try {
    $method = $request->method();
} catch (HttpException $e) {
    // Логируем сообщение с уровнем WARNING
    $logger->warning($e->getMessage());
    (new ErrorResponse)->send();
    return;
}
// Ассоциируем маршруты с именами классов действий,
// вместо готовых объектов
$routes = [
    'GET' => [
        '/users/show' => FindByUsername::class,
        '/posts/show' => FindPostByUuid::class,
        '/commit/show' => FindCommitByUuid::class,
        '/like/show' => FindLikeByUuid::class,
        '/like/allShow' => FindAllLikeByPostUuid::class
    ],
    'POST' => [
        '/user/create' => CreateUser::class,
        '/posts/create' => CreatePost::class,
        '/commit/create' => CreateCommit::class,
        '/like/create' => CreateLike::class
    ],
    'DELETE' => [
        '/user' => DeleteUserByUuid::class,
        '/posts' => DeletePostByUuid::class,
        '/commit' => DeleteCommitByUuid::class,
        '/like' => DeleteLikeByUuid::class,
    ],
];
if (!array_key_exists($method, $routes) || !array_key_exists($path, $routes[$method])) {
    // Логируем сообщение с уровнем NOTICE
    $message = "Route not found: $method $path";
    $logger->notice($message);
    (new ErrorResponse($message))->send();
    return;
}
// Получаем имя класса действия для маршрута
$actionClassName = $routes[$method][$path];

//$logger->info("http.php запускает действие по маршруту $actionClassName");
// С помощью контейнера
// создаём объект нужного действия
$action = $container->get($actionClassName);
try {
    $response = $action->handle($request);
} catch (AppException $e) {
    // Логируем сообщение с уровнем ERROR
    $logger->error($e->getMessage(), ['exception' => $e]);
    // Больше не отправляем пользователю
    // конкретное сообщение об ошибке,
    // а только логируем его
    (new ErrorResponse)->send();
    return;
}
$response->send();





