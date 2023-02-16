<?php

namespace Alexs\PhpAdvanced\Blog\Repositories\PostRepository;

use Alexs\PhpAdvanced\Blog\Exceptions\ErrorRepository;
use Alexs\PhpAdvanced\Blog\Exceptions\InvalidArgumentException;
use Alexs\PhpAdvanced\Blog\Exceptions\PostNotFoundException;
use Alexs\PhpAdvanced\Blog\Exceptions\UserNotFoundException;
use Alexs\PhpAdvanced\Blog\Post;
use Alexs\PhpAdvanced\Blog\Repositories\UserRepository\SQLiteUserRepository;
use Alexs\PhpAdvanced\Blog\UUID;
use PDO;
use PDOStatement;
use Psr\Log\LoggerInterface;

class SQLitePostRepository implements PostRepositoryInterface
{
    public function __construct(
        private PDO $connect,
        // Внедряем контракт логгера
        private LoggerInterface $logger
    )
    {
    }
    public function save(Post $post): void
    {
        try {
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
            $this->logger->info("Пост " . $post->getUuid() . " добавлен в базу данных");
        }catch (ErrorRepository $e){
            $this->logger->error($e->getMessage(), ['exception' => $e]);
        }

    }

    /**
     * @throws InvalidArgumentException
     * @throws PostNotFoundException
     * @throws UserNotFoundException
     */
    public function get(UUID $uuid):Post
    {
        $statement = $this->connect->prepare( "SELECT * FROM posts WHERE uuid = :uuid");
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
            $this->logger->warning("Поста $data нет в базе данных");
            throw new PostNotFoundException("Поста $data нет в базе данных");
        }

        $userRepository = new SQLiteUserRepository($this->connect, $this->logger);
        $user = $userRepository->get(new UUID($result['uuidAuthor']));

        return new Post(new UUID($result['uuid']), $user, $result['header'], $result['text'] );
    }

    public function delete (string $uuid):void
    {
        $statement = $this->connect->prepare("DELETE FROM posts WHERE posts.uuid = :postUuid");
        $statement->execute([':postUuid' => $uuid]);
        $this->logger->info("Post $uuid deleted");
    }
}