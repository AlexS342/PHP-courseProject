<?php

namespace Alexs\PhpAdvanced\Http\Actions\Commits;

use Alexs\PhpAdvanced\Blog\Exceptions\CommitNotFoundException;
use Alexs\PhpAdvanced\Blog\Exceptions\HttpException;
use Alexs\PhpAdvanced\Blog\Exceptions\InvalidArgumentException;
use Alexs\PhpAdvanced\Blog\Repositories\CommitRepository\CommitRepositoryInterface;
use Alexs\PhpAdvanced\Blog\UUID;
use Alexs\PhpAdvanced\Http\Request;
use Alexs\PhpAdvanced\Http\Response;
use Alexs\PhpAdvanced\Http\ErrorResponse;
use Alexs\PhpAdvanced\Http\SuccessfulResponse;
use Alexs\PhpAdvanced\Http\Actions\ActionInterface;

class DeleteCommitByUuid  implements ActionInterface
{
    // Нам понадобится репозиторий пользователей,
    // внедряем его контракт в качестве зависимости
    public function __construct(
        private CommitRepositoryInterface $commitRepository
    ) {
    }
    // Функция, описанная в контракте
    public function handle(Request $request): Response
    {
        try {
            // Пытаемся получить искомое имя пользователя из запроса
            $uuid = new UUID($request->query('uuid'));
            $this->commitRepository->get($uuid);
        } catch (HttpException | InvalidArgumentException $e) {
            // Если в запросе нет параметра username -
            // возвращаем неуспешный ответ,
            // сообщение об ошибке берём из описания исключения
            return new ErrorResponse($e->getMessage());
        }
        try {
            // Пытаемся найти пользователя в репозитории
//            $commit = $this->commitRepository->delete(new UUID($uuid));
            $this->commitRepository->delete((string)$uuid);
            // Возвращаем успешный ответ
            return new SuccessfulResponse([
                'uuid' => (string)$uuid
            ]);
        } catch (CommitNotFoundException | InvalidArgumentException $e) {
            // Если пользователь не найден - возвращаем неуспешный ответ
            return new ErrorResponse($e->getMessage());
        }

    }
}