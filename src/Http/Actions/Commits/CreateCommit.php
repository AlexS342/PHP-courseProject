<?php

namespace Alexs\PhpAdvanced\Http\Actions\Commits;

use Alexs\PhpAdvanced\Blog\Commit;
use Alexs\PhpAdvanced\Blog\Exceptions\HttpException;
use Alexs\PhpAdvanced\Blog\Exceptions\InvalidArgumentException;
use Alexs\PhpAdvanced\Blog\Exceptions\PostNotFoundException;
use Alexs\PhpAdvanced\Blog\Exceptions\UserNotFoundException;
use Alexs\PhpAdvanced\Blog\Post;
use Alexs\PhpAdvanced\Blog\Repositories\CommitRepository\CommitRepositoryInterface;
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

class CreateCommit implements ActionInterface
{
// Внедряем репозитории коммита, статей и пользователей
    public function __construct(
        private CommitRepositoryInterface $commitRepository,
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
            $userUuid = new UUID($request->jsonBodyField('user_uuid'));
        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }
        // Пытаемся создать UUID поста из данных запроса
        try {
            $postUuid = new UUID($request->jsonBodyField('post_uuid'));
        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }
        // Пытаемся найти пользователя в репозитории
        try {
            $user = $this->usersRepository->get($userUuid);
        } catch (UserNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }
        // Пытаемся найти пост в репозитории
        try {
            $post = $this->postsRepository->get($postUuid);
        } catch (PostNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }
        // Генерируем UUID для новой статьи
        $newCommitUuid = UUID::random();
        try {
            // Пытаемся создать объект коммита из данных запроса
            $commit = new Commit(
                $newCommitUuid,
                $user,
                $post,
                $request->jsonBodyField('text'),
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }
        // Сохраняем новый коммит в репозитории
        $this->commitRepository->save($commit);
        // Возвращаем успешный ответ,
        // содержащий UUID нового поста
        return new SuccessfulResponse([
            'uuid' => (string)$newCommitUuid,
        ]);
    }
}