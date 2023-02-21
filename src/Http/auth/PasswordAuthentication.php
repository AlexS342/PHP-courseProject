<?php

namespace Alexs\PhpAdvanced\Http\auth;

use Alexs\PhpAdvanced\Blog\Exceptions\AuthException;
use Alexs\PhpAdvanced\Blog\Exceptions\HttpException;
use Alexs\PhpAdvanced\Blog\Exceptions\UserNotFoundException;
use Alexs\PhpAdvanced\Blog\Repositories\UserRepository\UserRepositoryInterface;
use Alexs\PhpAdvanced\Http\Request;
use Alexs\PhpAdvanced\Blog\User;

class PasswordAuthentication implements PasswordAuthenticationInterface
{
    public function __construct(
        private UserRepositoryInterface $usersRepository
    ) {
    }

    public function user(Request $request): User
    {
// 1. Идентифицируем пользователя
        try {
            $username = $request->jsonBodyField('username');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }
        try {
            $user = $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException $e) {
            throw new AuthException($e->getMessage());
        }
        // 2. Аутентифицируем пользователя
        // Проверяем, что предъявленный пароль
        // соответствует сохранённому в БД
        try {
            $password = $request->jsonBodyField('password');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        // Проверяем пароль методом пользователя
        if (!$user->checkPassword($password)) {
            throw new AuthException('Wrong password');
        }

//        // Вычисляем SHA-256-хеш предъявленного пароля
//        $hash = hash('sha256', $password);
//        if ($hash !== $user->getPassword()) {
//        // Если хеши не совпадают — бросаем исключение
//            throw new AuthException('Wrong password');
//        }
//        if ($password !== $user->getPassword()) {
//        // Если пароли не совпадают — бросаем исключение
//            throw new AuthException('Wrong password');
//        }
        // Пользователь аутентифицирован
        return $user;
    }
}