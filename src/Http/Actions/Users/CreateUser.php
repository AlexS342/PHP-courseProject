<?php

namespace Alexs\PhpAdvanced\Http\Actions\Users;

use Alexs\PhpAdvanced\Blog\Exceptions\HttpException;
use Alexs\PhpAdvanced\Blog\Exceptions\InvalidArgumentException;
use Alexs\PhpAdvanced\Blog\Repositories\UserRepository\UserRepositoryInterface;
use Alexs\PhpAdvanced\Blog\User;
use Alexs\PhpAdvanced\Blog\UUID;
use Alexs\PhpAdvanced\Http\Request;
use Alexs\PhpAdvanced\Http\Response;
use Alexs\PhpAdvanced\Http\ErrorResponse;
use Alexs\PhpAdvanced\Http\SuccessfulResponse;
use Alexs\PhpAdvanced\Http\Actions\ActionInterface;
use JsonException;
use Psr\Log\LoggerInterface;

class CreateUser implements ActionInterface
{
    // Внедряем репозитории статей и пользователей
    public function __construct(
        private UserRepositoryInterface $usersRepository,
        // Внедряем контракт логгера
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     * @throws ErrorResponse|JsonException
     */
    public function handle(Request $request): Response
    {
        // Генерируем UUID для новой статьи
        $newUserUuid = UUID::random();
        try {
            // Пытаемся создать объект статьи из данных запроса
            $user = new User(
            $newUserUuid,
            $request->jsonBodyField('firstName'),
            $request->jsonBodyField('lastName'),
            $request->jsonBodyField('username'),
            $request->jsonBodyField('password')
            );
        } catch (HttpException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            return new ErrorResponse($e->getMessage());
        }

        // Сохраняем нового пользователя в репозитории
        $this->usersRepository->save($user);

        // Логируем UUID нового пользователя
        $this->logger->info("User created: $newUserUuid");

        // Возвращаем успешный ответ, содержащий UUID нового пользователя
        return new SuccessfulResponse([
            'uuid' => (string)$newUserUuid,
        ]);
    }
}