<?php

namespace Alexs\PhpAdvanced\Http\Actions\Users;

use Alexs\PhpAdvanced\Blog\Exceptions\InvalidArgumentException;
use Alexs\PhpAdvanced\Blog\Repositories\UserRepository\SQLiteUserRepository;
use Alexs\PhpAdvanced\Blog\UUID;
use Alexs\PhpAdvanced\Http\Actions\ActionInterface;
use Alexs\PhpAdvanced\Http\ErrorResponse;
use Alexs\PhpAdvanced\Blog\Exceptions\HttpException;
use Alexs\PhpAdvanced\Http\Request;
use Alexs\PhpAdvanced\Http\Response;
use Alexs\PhpAdvanced\Http\SuccessfulResponse;
use Alexs\PhpAdvanced\Blog\Exceptions\UserNotFoundException;
use Alexs\PhpAdvanced\Blog\Repositories\UserRepository\UserRepositoryInterface;



class DeleteByUuid implements ActionInterface
{
    // Нам понадобится репозиторий пользователей,
    // внедряем его контракт в качестве зависимости
    public function __construct(
        private UserRepositoryInterface $usersRepository
    ) {
    }
    // Функция, описанная в контракте
    public function handle(Request $request): Response
    {
        try {
            // Пытаемся получить искомое имя пользователя из запроса
            $uuid = new UUID($request->query('uuid'));
        } catch (HttpException |InvalidArgumentException $e) {
            // Если в запросе нет параметра username - возвращаем неуспешный ответ,
            // сообщение об ошибке берём из описания исключения
            return new ErrorResponse($e->getMessage());
        }

        try {
            // Пытаемся найти пользователя в репозитории
            $this->usersRepository->delete($uuid);
        } catch (UserNotFoundException $e) {
            // Если пользователь не найден - возвращаем неуспешный ответ
            return new ErrorResponse($e->getMessage());
        }
        // Возвращаем успешный ответ
        return new SuccessfulResponse([
            'uuid' => $uuid->getUuidString(),
        ]);
    }
}