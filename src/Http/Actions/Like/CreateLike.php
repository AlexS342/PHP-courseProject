<?php

namespace Alexs\PhpAdvanced\Http\Actions\Like;

use Alexs\PhpAdvanced\Blog\Exceptions\HttpException;
use Alexs\PhpAdvanced\Blog\Exceptions\InvalidArgumentException;
use Alexs\PhpAdvanced\Blog\Exceptions\PostNotFoundException;
use Alexs\PhpAdvanced\Blog\Exceptions\UserNotFoundException;
use Alexs\PhpAdvanced\Blog\Like;
use Alexs\PhpAdvanced\Blog\Repositories\LikeRepository\LikeRepositoryInterface;
use Alexs\PhpAdvanced\Blog\Repositories\PostRepository\PostRepositoryInterface;
use Alexs\PhpAdvanced\Blog\Repositories\UserRepository\UserRepositoryInterface;
use Alexs\PhpAdvanced\Blog\UUID;
use Alexs\PhpAdvanced\Http\Request;
use Alexs\PhpAdvanced\Http\Response;
use Alexs\PhpAdvanced\Http\ErrorResponse;
use Alexs\PhpAdvanced\Http\SuccessfulResponse;
use Alexs\PhpAdvanced\Http\Actions\ActionInterface;
use JsonException;

class CreateLike implements ActionInterface
{
// Внедряем репозитории коммита, статей и пользователей
    public function __construct(
        private LikeRepositoryInterface $likeRepository,
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
        // Пытаемся создать UUID лайка из данных запроса
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
        //--------------------------------------------------------------
        //Пример проверки на наличие перед удалением
//        try {
//            // Пытаемся получить искомое имя пользователя из запроса
//            $uuid = new UUID($request->query('uuid'));
//            $this->likeRepository->get($uuid);
//        } catch (HttpException | InvalidArgumentException $e) {
//            // Если в запросе нет параметра username - возвращаем неуспешный ответ, сообщение об ошибке берём из описания исключения
//            return new ErrorResponse($e->getMessage());
//        }
        //--------------------------------------------------------------
        // Генерируем UUID для новой статьи
        $newLikeUuid = UUID::random();
        try {
            // Пытаемся создать объект коммита из данных запроса
            $like = new Like(
                $newLikeUuid,
                $post,
                $user,
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }
        // Сохраняем новый коммит в репозитории
        $this->likeRepository->save($like);
        // Возвращаем успешный ответ,
        // содержащий UUID нового поста
        return new SuccessfulResponse([
            'uuid' => (string)$newLikeUuid,
        ]);
    }
}