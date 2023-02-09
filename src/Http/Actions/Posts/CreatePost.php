<?php

namespace Alexs\PhpAdvanced\Http\Actions\Posts;

use Alexs\PhpAdvanced\Blog\Exceptions\HttpException;
use Alexs\PhpAdvanced\Blog\Exceptions\InvalidArgumentException;
use Alexs\PhpAdvanced\Blog\Exceptions\UserNotFoundException;
use Alexs\PhpAdvanced\Blog\Post;
use Alexs\PhpAdvanced\Blog\Repositories\PostRepository\PostRepositoryInterface;
use Alexs\PhpAdvanced\Blog\Repositories\UserRepository\SQLiteUserRepository;
use Alexs\PhpAdvanced\Blog\Repositories\UserRepository\UserRepositoryInterface;
use Alexs\PhpAdvanced\Blog\UUID;
use Alexs\PhpAdvanced\Http\Request;
use Alexs\PhpAdvanced\Http\Response;
use Alexs\PhpAdvanced\Http\ErrorResponse;
use Alexs\PhpAdvanced\Http\SuccessfulResponse;
use Alexs\PhpAdvanced\Http\Actions\ActionInterface;
use JsonException;
use PDO;

class CreatePost implements ActionInterface
{
// Внедряем репозитории статей и пользователей
    public function __construct(
        private PostRepositoryInterface $postsRepository,
        private UserRepositoryInterface $usersRepository,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     * @throws ErrorResponse|JsonException
     */
    public function handle(Request $request): Response
    {
// Пытаемся создать UUID пользователя из данных запроса
        try {
            $authorUuid = new UUID($request->jsonBodyField('author_uuid'));
            //!!! Нужно получить юзера по ID
            $author = $this->usersRepository->get($authorUuid);
        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
//            throw new ErrorResponse($e->getMessage());
        }
// Пытаемся найти пользователя в репозитории
        try {
            $this->usersRepository->get($authorUuid);
        } catch (UserNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
//            throw new ErrorResponse($e->getMessage());
        }
// Генерируем UUID для новой статьи
        $newPostUuid = UUID::random();
        try {
// Пытаемся создать объект статьи
// из данных запроса
            $post = new Post(
                $newPostUuid,
                $author,
//                $authorUuid,
                $request->jsonBodyField('title'),
                $request->jsonBodyField('text'),
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
//            throw new ErrorResponse($e->getMessage());
        }
// Сохраняем новую статью в репозитории
        $this->postsRepository->save($post);
// Возвращаем успешный ответ,
// содержащий UUID новой статьи
        return new SuccessfulResponse([
            'uuid' => (string)$newPostUuid,
        ]);
    }
}