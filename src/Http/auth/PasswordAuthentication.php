<?php

namespace Alexs\PhpAdvanced\Http\auth;

use Alexs\PhpAdvanced\Blog\Exceptions\AuthException;
use Alexs\PhpAdvanced\Blog\Exceptions\HttpException;
use Alexs\PhpAdvanced\Blog\Exceptions\UserNotFoundException;
use Alexs\PhpAdvanced\Blog\Repositories\UserRepository\UserRepositoryInterface;
use Alexs\PhpAdvanced\Http\Request;
use Alexs\PhpAdvanced\Blog\User;
use JsonException;

class PasswordAuthentication implements PasswordAuthenticationInterface
{
    public function __construct(
        private UserRepositoryInterface $usersRepository
    ) {
    }

    /**
     * @throws AuthException
     * @throws JsonException
     */
    public function user(Request $request): User
    {
        // 1. Идентифицируем пользователя
        try {
            $username = $request->jsonBodyField('username');
        } catch (HttpException | JsonException $e) {
            throw new AuthException($e->getMessage());
        }

        try {
            $user = $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException $e) {
            throw new AuthException($e->getMessage());
        }
        // 2. Аутентифицируем пользователя
        // Проверяем, что предъявленный пароль соответствует сохранённому в БД
        try {
            $password = $request->jsonBodyField('password');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        // Проверяем пароль методом пользователя
        if (!$user->checkPassword($password)) {
            throw new AuthException('Wrong password');
        }

        // Пользователь аутентифицирован
        return $user;
    }
}