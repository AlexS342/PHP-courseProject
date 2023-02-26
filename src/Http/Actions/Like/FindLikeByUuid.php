<?php

namespace Alexs\PhpAdvanced\Http\Actions\Like;

use Alexs\PhpAdvanced\Blog\Exceptions\CommitNotFoundException;
use Alexs\PhpAdvanced\Blog\Exceptions\HttpException;
use Alexs\PhpAdvanced\Blog\Exceptions\InvalidArgumentException;
use Alexs\PhpAdvanced\Blog\Exceptions\UserNotFoundException;
use Alexs\PhpAdvanced\Blog\Repositories\LikeRepository\LikeRepositoryInterface;
use Alexs\PhpAdvanced\Blog\UUID;
use Alexs\PhpAdvanced\Http\Request;
use Alexs\PhpAdvanced\Http\Response;
use Alexs\PhpAdvanced\Http\ErrorResponse;
use Alexs\PhpAdvanced\Http\SuccessfulResponse;
use Alexs\PhpAdvanced\Http\Actions\ActionInterface;

class FindLikeByUuid  implements ActionInterface
{
    // Нам понадобится репозиторий пользователей, внедряем его контракт в качестве зависимости
    public function __construct(
        private LikeRepositoryInterface $likeRepository
    ) {
    }
    // Функция, описанная в контракте
    public function handle(Request $request): Response
    {
        try {
            // Пытаемся получить искомое имя пользователя из запроса
            $uuid = $request->query('uuid');
        } catch (HttpException $e) {
            // Если в запросе нет параметра username - возвращаем неуспешный ответ,
            // сообщение об ошибке берём из описания исключения
            return new ErrorResponse($e->getMessage());
        }
        try {
            // Пытаемся найти пользователя в репозитории
            $like = $this->likeRepository->get(new UUID($uuid));
        } catch (CommitNotFoundException | UserNotFoundException | InvalidArgumentException $e) {
            // Если пользователь не найден - возвращаем неуспешный ответ
            return new ErrorResponse($e->getMessage());
        }

        // Возвращаем успешный ответ
        return new SuccessfulResponse([
            'uuid' => $like->getUuid(),
            'author' => $like->getUser()->getUsername(),
            'post' => $like->getPost()->getHeader(),
        ]);
    }
}