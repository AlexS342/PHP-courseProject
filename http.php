<?php

use Alexs\PhpAdvanced\Blog\Exceptions\AppException;
use Alexs\PhpAdvanced\Blog\Repositories\CommitRepository\SQLiteCommitRepository;
use Alexs\PhpAdvanced\Blog\Repositories\PostRepository\SQLitePostRepository;
use Alexs\PhpAdvanced\Blog\Repositories\UserRepository\SQLiteUserRepository;
use Alexs\PhpAdvanced\Http\Actions\Commits\CreateCommit;
use Alexs\PhpAdvanced\Http\Actions\Commits\FindCommitByUuid;
use Alexs\PhpAdvanced\Http\Actions\Posts\CreatePost;
use Alexs\PhpAdvanced\Http\Actions\Posts\FindPostByUuid;
use Alexs\PhpAdvanced\Http\Actions\Users\CreateUser;
use Alexs\PhpAdvanced\Http\Actions\Users\FindByUsername;
use Alexs\PhpAdvanced\Http\ErrorResponse;
use Alexs\PhpAdvanced\Http\Request;
use Alexs\PhpAdvanced\Http\SuccessfulResponse;
use Alexs\PhpAdvanced\Blog\Exceptions\HttpException;

// Устанавливаем код ответа
require_once __DIR__ . '/vendor/autoload.php';

$request = new Request(
    $_GET,
    $_SERVER,
// Читаем поток, содержащий тело запроса
    file_get_contents('php://input'),
);

try {
    $path = $request->path();
} catch (HttpException) {
    (new ErrorResponse)->send();
    return;
}
try {
// Пытаемся получить HTTP-метод запроса
    $method = $request->method();
} catch (HttpException) {
// Возвращаем неудачный ответ,
// если по какой-то причине
// не можем получить метод
    (new ErrorResponse)->send();
    return;
}
$routes = [
// Добавили ещё один уровень вложенности
// для отделения маршрутов,
// применяемых к запросам с разными методами
    'GET' => [
        '/users/show' => new FindByUsername(
            new SQLiteUserRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),
        '/posts/show' => new FindPostByUuid(
            new SQLitePostRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),
        '/commit/show' => new FindCommitByUuid(
            new SQLiteCommitRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),
    ],
    'POST' => [
// Добавили новый маршрут
        '/user/create' => new CreateUser(
            new SQLiteUserRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),
        '/posts/create' => new CreatePost(
            new SQLitePostRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            ),
            new SQLiteUserRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),
        '/commit/create' => new CreateCommit(
            new SQLiteCommitRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            ),
            new SQLitePostRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            ),
            new SQLiteUserRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),
    ],
];
// Если у нас нет маршрутов для метода запроса -
// возвращаем неуспешный ответ
if (!array_key_exists($method, $routes)) {
    (new ErrorResponse('Not found'))->send();
    return;
}
// Ищем маршрут среди маршрутов для этого метода
if (!array_key_exists($path, $routes[$method])) {
    (new ErrorResponse('Not found'))->send();
    return;
}
// Выбираем действие по методу и пути
$action = $routes[$method][$path];
try {
    $response = $action->handle($request);
    $response->send();
} catch (AppException $e) {
    (new ErrorResponse($e->getMessage()))->send();
}





