<?php

namespace Alexs\PhpAdvanced\Blog\Repositories\CommitRepository;

use Alexs\PhpAdvanced\Blog\Commit;
use Alexs\PhpAdvanced\Blog\Exceptions\AppException;
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
     * @throws AppException
     */
    public function get(UUID $uuid):Commit
    {
        $statement = $this->connect->prepare(
            "SELECT * FROM comments WHERE uuid = :uuid"
        );
        $statement->execute(['uuid' => (string)$uuid]);

        try {
            return $this->createUser($statement, $uuid);
        } catch (CommitNotFoundException | InvalidArgumentException | PostNotFoundException |UserNotFoundException $e) {
            throw new AppException($e->getMessage());
        }
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

    public function delete (string $uuid):void
    {
        $statement = $this->connect->prepare("DELETE FROM comments WHERE 'uuid' = :commitUuid");
        $x = $statement->execute([':commitUuid' => $uuid]);
    }
}