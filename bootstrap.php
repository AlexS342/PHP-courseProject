<?php

use Alexs\PhpAdvanced\Blog\Container\DIContainer;
use Alexs\PhpAdvanced\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use Alexs\PhpAdvanced\Blog\Repositories\AuthTokensRepository\SqliteAuthTokensRepository;
use Alexs\PhpAdvanced\Blog\Repositories\CommitRepository\CommitRepositoryInterface;
use Alexs\PhpAdvanced\Blog\Repositories\CommitRepository\SQLiteCommitRepository;
use Alexs\PhpAdvanced\Blog\Repositories\LikeRepository\LikeRepositoryInterface;
use Alexs\PhpAdvanced\Blog\Repositories\LikeRepository\SQLiteLikeRepository;
use Alexs\PhpAdvanced\Blog\Repositories\PostRepository\PostRepositoryInterface;
use Alexs\PhpAdvanced\Blog\Repositories\PostRepository\SQLitePostRepository;
use Alexs\PhpAdvanced\Blog\Repositories\UserRepository\SQLiteUserRepository;
use Alexs\PhpAdvanced\Blog\Repositories\UserRepository\UserRepositoryInterface;
use Alexs\PhpAdvanced\Http\auth\AuthenticationInterface;
use Alexs\PhpAdvanced\Http\auth\BearerTokenAuthentication;
use Alexs\PhpAdvanced\Http\auth\PasswordAuthentication;
use Alexs\PhpAdvanced\Http\auth\PasswordAuthenticationInterface;
use Alexs\PhpAdvanced\Http\auth\TokenAuthenticationInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

// Подключаем автозагрузчик Composer
require_once __DIR__ . '/vendor/autoload.php';
// Создаём объект контейнера ..
$container = new DIContainer();

$container->bind(
    LoggerInterface::class,
    (new Logger('blog'))
        ->pushHandler(new StreamHandler(
            __DIR__ . '/logs/blog.log'
        ))
        ->pushHandler(new StreamHandler(
            __DIR__ . '/logs/blog.error.log',
            level: Logger::ERROR,
            bubble: false,
        ))
        // Добавили ещё один обработчик;
        // он будет вызываться первым …
        ->pushHandler(
            // .. и вести запись в поток php://stdout, то есть в консоль
            new StreamHandler("php://stdout")
        )
);

// 1. подключение к БД
$container->bind(
    PDO::class,
    new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
);
// 2. репозиторий статей
$container->bind(
    PostRepositoryInterface::class,
    SqlitePostRepository::class
);
// 3. репозиторий пользователей
$container->bind(
    UserRepositoryInterface::class,
    SqliteUserRepository::class
);
// 4. репозиторий комитов
$container->bind(
    CommitRepositoryInterface::class,
    SqliteCommitRepository::class
);
// 5. репозиторий лайков
$container->bind(
    LikeRepositoryInterface::class,
    SqliteLikeRepository::class
);

$container->bind(
    AuthenticationInterface::class,
PasswordAuthentication::class
);

$container->bind(
    PasswordAuthenticationInterface::class,
    PasswordAuthentication::class
);
$container->bind(
    AuthTokensRepositoryInterface::class,
    SqliteAuthTokensRepository::class
);

//$container = new DIContainer();
$container->bind(
    PasswordAuthenticationInterface::class,
    PasswordAuthentication::class
);
$container->bind(
    TokenAuthenticationInterface::class,
    BearerTokenAuthentication::class
);

// Возвращаем объект контейнера
return $container;