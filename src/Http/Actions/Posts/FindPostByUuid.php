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

class FindPostByUuid implements ActionInterface
{
// Нам понадобится репозиторий пользователей,
    // внедряем его контракт в качестве зависимости
    public function __construct(
        private PostRepositoryInterface $postRepository
    ) {
    }

    // Функция, описанная в контракте
    public function handle(Request $request): Response
    {
        try {
            // Пытаемся получить искомое имя пользователя из запроса
            $uuid = $request->query('uuid');
        } catch (HttpException $e) {
            // Если в запросе нет параметра username -
            // возвращаем неуспешный ответ,
            // сообщение об ошибке берём из описания исключения
            return new ErrorResponse($e->getMessage());
        }
        try {
            // Пытаемся найти пользователя в репозитории
            $post = $this->postRepository->get(new UUID($uuid));
            // Возвращаем успешный ответ
            return new SuccessfulResponse([
                'username' => $post->getAuthor()->getUsername(),
                'header' => $post->getHeader(),
                'text' => $post->getText()
            ]);
        } catch (PostNotFoundException $e) {
            // Если пользователь не найден - возвращаем неуспешный ответ
            return new ErrorResponse($e->getMessage());
        }
    }
}