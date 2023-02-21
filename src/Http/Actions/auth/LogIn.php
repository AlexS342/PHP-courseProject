<?php

namespace Alexs\PhpAdvanced\Http\Actions\auth;

use Alexs\PhpAdvanced\AuthToken;
use Alexs\PhpAdvanced\Blog\Exceptions\AuthException;
use Alexs\PhpAdvanced\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use Alexs\PhpAdvanced\Http\Actions\ActionInterface;
use Alexs\PhpAdvanced\Http\auth\PasswordAuthenticationInterface;
use Alexs\PhpAdvanced\Http\ErrorResponse;
use Alexs\PhpAdvanced\Http\Request;
use Alexs\PhpAdvanced\Http\Response;
use Alexs\PhpAdvanced\Http\SuccessfulResponse;
use DateTimeImmutable;

class LogIn implements ActionInterface
{
    public function __construct(
// Авторизация по паролю
        private PasswordAuthenticationInterface $passwordAuthentication,
// Репозиторий токенов
        private AuthTokensRepositoryInterface $authTokensRepository
    ) {
    }
    public function handle(Request $request): Response
    {
        // Аутентифицируем пользователя
        try {
            $user = $this->passwordAuthentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }
        // Генерируем токен
        $authToken = new AuthToken(
            // Случайная строка длиной 40 символов
            bin2hex(random_bytes(40)),
            $user->getUuid(),
            // Срок годности - 1 день
            (new DateTimeImmutable())->modify('+1 day')
        );
        // Сохраняем токен в репозиторий
        $this->authTokensRepository->save($authToken);
        // Возвращаем токен
        return new SuccessfulResponse([
            'token' => (string)$authToken->token(),
        ]);
    }
}