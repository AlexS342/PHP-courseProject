<?php

namespace Alexs\PhpAdvanced\Blog\Repositories\PostRepository;

use Alexs\PhpAdvanced\Blog\Exceptions\InvalidArgumentException;
use Alexs\PhpAdvanced\Blog\Exceptions\PostNotFoundException;
use Alexs\PhpAdvanced\Blog\Exceptions\UserNotFoundException;
use Alexs\PhpAdvanced\Blog\Post;
use Alexs\PhpAdvanced\Blog\Repositories\UserRepository\SQLiteUserRepository;
use Alexs\PhpAdvanced\Blog\UUID;
use PDO;
use PDOStatement;

class SQLitePostRepository implements PostRepositoryInterface
{
    public function __construct(
        private PDO $connect
    )
    {
    }
    public function save(Post $post): void
    {
        $statement = $this->connect->prepare(
            "INSERT INTO posts (uuid, uuidAuthor, header, text) 
            VALUES (:uuid, :uuidAuthor, :header, :text)"
        );
        $statement->execute([
            'uuid' => $post->getUuid(),
            'uuidAuthor' => $post->getAuthor()->getUuid(),
            'header' => $post->getHeader(),
            'text' => $post->getText()
        ]);
    }

    /**
     * @throws InvalidArgumentException
     * @throws PostNotFoundException
     * @throws UserNotFoundException
     */
    public function get(UUID $uuid):Post
    {
        $statement = $this->connect->prepare(
            "SELECT * FROM posts WHERE uuid = :uuid"
        );
        $statement->execute(['uuid' => (string)$uuid]);

        return $this->createPost($statement, $uuid);
    }

    /**
     * @throws PostNotFoundException
     * @throws InvalidArgumentException|UserNotFoundException
     */
    private function createPost(PDOStatement $statement, $data):Post
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if($result===false){
            throw new PostNotFoundException("Пост $data нет в базе данных");
        }

        $userRepository = new SQLiteUserRepository($this->connect);
        $user = $userRepository->get(new UUID($result['uuidAuthor']));

        return new Post(new UUID($result['uuid']), $user, $result['header'], $result['text'] );
    }

    public function delete (string $uuid):void
    {
        $statement = $this->connect->prepare("DELETE FROM posts WHERE 'uuid' = :postUuid");
        $statement->execute([':postUuid' => $uuid]);
    }
}