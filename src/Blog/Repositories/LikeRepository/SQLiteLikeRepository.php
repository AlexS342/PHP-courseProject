<?php

namespace Alexs\PhpAdvanced\Blog\Repositories\LikeRepository;

use Alexs\PhpAdvanced\Blog\Exceptions\InvalidArgumentException;
use Alexs\PhpAdvanced\Blog\Exceptions\LikeNotFoundException;
use Alexs\PhpAdvanced\Blog\Exceptions\PostNotFoundException;
use Alexs\PhpAdvanced\Blog\Exceptions\UserNotFoundException;
use Alexs\PhpAdvanced\Blog\Like;
use Alexs\PhpAdvanced\Blog\Post;
use Alexs\PhpAdvanced\Blog\Repositories\UserRepository\UserRepositoryInterface;
use Alexs\PhpAdvanced\Blog\User;
use Alexs\PhpAdvanced\Blog\UUID;
use Alexs\PhpAdvanced\Http\ErrorResponse;
use Alexs\PhpAdvanced\Http\Response;
use PDO;
use PDOStatement;
use Psr\Log\LoggerInterface;

class SQLiteLikeRepository implements LikeRepositoryInterface
{

    public function __construct(
        private PDO $connect,
        // Внедряем контракт логгера
        private LoggerInterface $logger,
    )
    {
    }

    public function save(Like $like): void
    {
        $statement = $this->connect->prepare("SELECT * FROM likes WHERE uuidUser = :uuidUser and uuidPost = :uuidPost");
        $statement->execute([
            'uuidPost' => (string)$like->getPost()->getUuid(),
            'uuidUser' => (string)$like->getUser()->getUuid()
        ]);

        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if($result !== false){
            $this->logger->warning("Лайк к посту " . $like->getUuid() . " уже поставлен");
            throw new PostNotFoundException("Лайк к посту " . $like->getPost()->getUuid() . " уже поставлен");
        }

        $statement = $this->connect->prepare(
            "INSERT INTO likes (uuid, uuidPost, uuidUser) 
            VALUES (:uuid, :uuidPost, :uuidUser)"
        );
        $statement->execute([
            'uuid' => (string)$like->getUuid(),
            'uuidPost' => (string)$like->getPost()->getUuid(),
            'uuidUser' => (string)$like->getUser()->getUuid()
        ]);
        $this->logger->info("Лайк " . $like->getUuid() . " добавлен в базу данных");
    }

    public function get(UUID $uuid):Like
    {
        //Подготавливаем запрос на получение лайка
        $statement = $this->connect->prepare("SELECT * FROM likes WHERE uuid = :uuid");
        //Выполнаяв запрос
        $statement->execute(['uuid' => (string)$uuid]);

        //Получаем лайк из таблицы
        $resultLike = $statement->fetch(PDO::FETCH_ASSOC);
        if($resultLike===false){
            $this->logger->warning("лайк $uuid ненайден в базе данных");
            throw new UserNotFoundException("лайк $uuid ненайден в базе данных");
        }

        //Создаем пользователя
        $user = $this->getUserByUuid($resultLike['uuidUser']);

        $post =$this->getPostByUuid($resultLike['uuidPost']);

        $this->logger->info("Лайк " . $uuid . " успешно найден");
        return new Like(new UUID($resultLike['uuid']), $post, $user);
    }

    public function getAllLikeByPostUuid(string $uuidPost):array
    {
        $statement = $this->connect->prepare(
            "SELECT * FROM likes WHERE uuidPost = :uuidPost"
        );
        $statement->execute(['uuidPost' => $uuidPost]);

        $likeArr = $statement->fetchAll(PDO::FETCH_ASSOC);
        var_dump($likeArr);

//        if($likeArr===false){
        if(count ($likeArr) === 0){
//        if(!empty($likeArr)){
            $this->logger->warning("Лайки для поста $uuidPost не найдены в базе данных");
            throw new LikeNotFoundException("Комментария $uuidPost нет в базе данных");
        }

        $allLike = [];
        foreach ($likeArr as $oneLike)
        {
            //Получаем пользователя который поставил лайк
            $user = $this->getUserByUuid($oneLike['uuidUser']);
            $post =$this->getPostByUuid($oneLike['uuidPost']);
            $allLike[] = new Like(new UUID($oneLike['uuid']), $post, $user);
        }

        return $allLike;
    }

    private function getUserByUuid($uuid):User
    {
        $statement = $this->connect->prepare("SELECT * FROM users WHERE uuid = :uuidUser");
        $statement->execute(['uuidUser' => $uuid]);
        $resultUser = $statement->fetch(PDO::FETCH_ASSOC);
        if($resultUser===false){
            throw new UserNotFoundException("статья $uuid ненайдена в базе данных");
        }
        return new User(
            new UUID($resultUser['uuid']),
            $resultUser['firstName'],
            $resultUser['lastName'],
            $resultUser['username'],
            $resultUser['password']
        );
    }

    private function getPostByUuid($uuid):Post
    {
        //Получаем пост
        $statement = $this->connect->prepare("SELECT * FROM posts WHERE uuid = :uuidPost");
        $statement->execute(['uuidPost' => $uuid]);

        $resultPost = $statement->fetch(PDO::FETCH_ASSOC);
        if($resultPost===false){
            throw new UserNotFoundException("статья $uuid ненайдена в базе данных");
        }
        //Получаем пользователя, написавшего пост
        $author = $this->getUserByUuid($resultPost['uuidAuthor']);

        return new Post(new UUID($resultPost['uuid']), $author, $resultPost['header'], $resultPost['text']);
    }

    public function delete (UUID $uuid):void
    {
        $statement = $this->connect->prepare("DELETE FROM likes WHERE likes.uuid = :likeUuid");
        $statement->execute([':likeUuid'=>$uuid]);
    }

}
