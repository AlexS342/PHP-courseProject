<?php

use Alexs\PhpAdvanced\Blog\Exceptions\AppException;
use Alexs\PhpAdvanced\Http\Actions\Commits\CreateCommit;
use Alexs\PhpAdvanced\Http\Actions\Commits\FindCommitByUuid;
use Alexs\PhpAdvanced\Http\Actions\Posts\CreatePost;
use Alexs\PhpAdvanced\Http\Actions\Posts\FindPostByUuid;
use Alexs\PhpAdvanced\Http\Actions\Users\CreateUser;
use Alexs\PhpAdvanced\Http\Actions\Users\FindByUsername;
use Alexs\PhpAdvanced\Http\ErrorResponse;
use Alexs\PhpAdvanced\Http\Request;
 use Alexs\PhpAdvanced\Blog\Exceptions\HttpException;

// Подключаем файл bootstrap.php
// и получаем настроенный контейнер
$container = require __DIR__ . '/bootstrap.php';
$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input'),
);
try {
    $path = $request->path();
} catch (HttpException) {
    (new ErrorResponse)->send();
    return;
}
try {
    $method = $request->method();
} catch (HttpException) {
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
    ],
    'POST' => [
        '/user/create' => CreateUser::class,
        '/posts/create' => CreatePost::class,
        '/commit/create' => CreateCommit::class,
    ],
    'DELETE' => [
        '/user' => CreateUser::class,
        '/posts' => CreatePost::class,
        '/commit' => CreateCommit::class,
    ],
];
if (!array_key_exists($method, $routes)) {
    (new ErrorResponse("Route not found: $method $path"))->send();
    return;
}
if (!array_key_exists($path, $routes[$method])) {
    (new ErrorResponse("Route not found: $method $path"))->send();
    return;
}
// Получаем имя класса действия для маршрута
$actionClassName = $routes[$method][$path];
// С помощью контейнера
// создаём объект нужного действия
$action = $container->get($actionClassName);
try {
    $response = $action->handle($request);
} catch (AppException $e) {
    (new ErrorResponse($e->getMessage()))->send();
}
$response->send();





