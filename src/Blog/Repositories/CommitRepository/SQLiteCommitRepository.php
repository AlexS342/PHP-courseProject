<?php

namespace Alexs\PhpAdvanced\Blog\Repositories\CommitRepository;

use Alexs\PhpAdvanced\Blog\Commit;
use Alexs\PhpAdvanced\Blog\Exceptions\CommitNotFoundException;
use Alexs\PhpAdvanced\Blog\Exceptions\InvalidArgumentException;
use Alexs\PhpAdvanced\Blog\Exceptions\PostNotFoundException;
use Alexs\PhpAdvanced\Blog\Exceptions\UserNotFoundException;
use Alexs\PhpAdvanced\Blog\Repositories\PostRepository\SQLitePostRepository;
use Alexs\PhpAdvanced\Blog\Repositories\UserRepository\SQLiteUserRepository;
use Alexs\PhpAdvanced\Blog\UUID;
use PDO;
use PDOStatement;

class SQLiteCommitRepository implements CommitRepositoryInterface
{
    public function __construct(
        private PDO $connect
    )
    {
    }

    public function save(Commit $commit): void
    {
        $statement = $this->connect->prepare(
            "INSERT INTO comments (uuid, uuidAuthor, uuidPost, text) 
            VALUES (:uuid, :uuidAuthor, :uuidPost, :text)"
        );
        $statement->execute([
            'uuid' => (string)$commit->getUuid(),
            'uuidAuthor' => $commit->getAuthor()->getUuid(),
            'uuidPost' => $commit->getPost()->getUuid(),
            'text' => $commit->getText()
        ]);
    }

    /**
     * @throws CommitNotFoundException
     * @throws InvalidArgumentException
     */
    public function get(UUID $uuid):Commit
    {
        $statement = $this->connect->prepare(
            "SELECT * FROM comments WHERE uuid = :uuid"
        );
        $statement->execute(['uuid' => (string)$uuid]);

        return $this->createUser($statement, $uuid);
    }

    /**
     * @throws CommitNotFoundException
     * @throws InvalidArgumentException
     * @throws UserNotFoundException
     * @throws PostNotFoundException
     */
    private function createUser(PDOStatement $statement, $data):Commit
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if($result===false){
            throw new CommitNotFoundException("Пользователя $data нет в базе данных");
        }

        $userRepository = new SQLiteUserRepository($this->connect);
        $user = $userRepository->get(new UUID($result['uuidAuthor']));

        $postRepository = new SQLitePostRepository($this->connect);
        $post = $postRepository->get(new UUID($result['uuidPost']));

        return new Commit(new UUID($result['uuid']), $user, $post, $result['text']);
    }
}