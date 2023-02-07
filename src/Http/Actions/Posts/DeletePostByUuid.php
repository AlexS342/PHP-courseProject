<?php

namespace Alexs\PhpAdvanced\Http\Actions\Posts;

use Alexs\PhpAdvanced\Blog\Exceptions\HttpException;
use Alexs\PhpAdvanced\Blog\Exceptions\InvalidArgumentException;
use Alexs\PhpAdvanced\Blog\Exceptions\PostNotFoundException;
use Alexs\PhpAdvanced\Blog\UUID;
use Alexs\PhpAdvanced\Http\Response;
use Alexs\PhpAdvanced\Http\Request;
use Alexs\PhpAdvanced\Http\SuccessfulResponse;
use Alexs\PhpAdvanced\Http\ErrorResponse;
use Alexs\PhpAdvanced\Blog\Repositories\PostRepository\PostRepositoryInterface;
use Alexs\PhpAdvanced\Http\Actions\ActionInterface;

class DeletePostByUuid implements ActionInterface
{
    // Нам понадобится репозиторий пользователей, внедряем его контракт в качестве зависимости
    public function __construct(
        private PostRepositoryInterface $postRepository
    ) {
    }

    // Функция, описанная в контракте
    public function handle(Request $request): Response
    {
        try {
            // Пытаемся получить искомое имя пользователя из запроса
            $uuid = new UUID($request->query('uuid'));
        } catch (HttpException | InvalidArgumentException $e) {
            // Если в запросе нет параметра username - возвращаем неуспешный ответ, сообщение об ошибке берём из описания исключения
            return new ErrorResponse($e->getMessage());
        }
        try {
            // Пытаемся найти пользователя в репозитории
            $this->postRepository->delete(new UUID($uuid));
            // Возвращаем успешный ответ
            return new SuccessfulResponse([
                'uuid' => (string)$uuid
            ]);
        } catch (PostNotFoundException | InvalidArgumentException $e) {
            // Если пользователь не найден - возвращаем неуспешный ответ
            return new ErrorResponse($e->getMessage());
        }
    }
}