<?php

namespace Alexs\PhpAdvanced\Http\Actions\Users;

use Alexs\PhpAdvanced\Blog\Exceptions\HttpException;
use Alexs\PhpAdvanced\Blog\Exceptions\InvalidArgumentException;
use Alexs\PhpAdvanced\Blog\Exceptions\UserNotFoundException;
use Alexs\PhpAdvanced\Blog\Post;
use Alexs\PhpAdvanced\Blog\Repositories\PostRepository\PostRepositoryInterface;
use Alexs\PhpAdvanced\Blog\Repositories\UserRepository\SQLiteUserRepository;
use Alexs\PhpAdvanced\Blog\Repositories\UserRepository\UserRepositoryInterface;
use Alexs\PhpAdvanced\Blog\User;
use Alexs\PhpAdvanced\Blog\UUID;
use Alexs\PhpAdvanced\Http\Request;
use Alexs\PhpAdvanced\Http\Response;
use Alexs\PhpAdvanced\Http\ErrorResponse;
use Alexs\PhpAdvanced\Http\SuccessfulResponse;
use Alexs\PhpAdvanced\Http\Actions\ActionInterface;
use JsonException;
use PDO;

class CreateUser implements ActionInterface
{
// Внедряем репозитории статей и пользователей
    public function __construct(
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
//        try {
//            $authorUuid = new UUID($request->jsonBodyField('author_uuid'));
//            $author = $this->usersRepository->get($authorUuid);
//        } catch (HttpException | InvalidArgumentException $e) {
//            return new ErrorResponse($e->getMessage());
//        }
//// Пытаемся найти пользователя в репозитории
//        try {
//            $this->usersRepository->get($authorUuid);
//        } catch (UserNotFoundException $e) {
//            return new ErrorResponse($e->getMessage());
//        }
// Генерируем UUID для новой статьи
        $newUserUuid = UUID::random();
        try {
            // Пытаемся создать объект статьи
            // из данных запроса
            $user = new User(
                $newUserUuid,
                $request->jsonBodyField('firstName'),
                $request->jsonBodyField('lastName'),
                $request->jsonBodyField('username'),
                $request->jsonBodyField('password')
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }
// Сохраняем новую статью в репозитории
        $this->usersRepository->save($user);
// Возвращаем успешный ответ,
// содержащий UUID новой статьи
        return new SuccessfulResponse([
            'uuid' => (string)$newUserUuid,
        ]);
    }
}