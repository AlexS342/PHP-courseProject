<?php

namespace Alexs\PhpAdvanced\Blog\Repositories\CommitRepository;

use Alexs\PhpAdvanced\Blog\Commit;
use Alexs\PhpAdvanced\Blog\Exceptions\AppException;
use Alexs\PhpAdvanced\Blog\Exceptions\CommitNotFoundException;
use Alexs\PhpAdvanced\Blog\Exceptions\ErrorRepository;
use Alexs\PhpAdvanced\Blog\Exceptions\InvalidArgumentException;
use Alexs\PhpAdvanced\Blog\Exceptions\PostNotFoundException;
use Alexs\PhpAdvanced\Blog\Exceptions\UserNotFoundException;
use Alexs\PhpAdvanced\Blog\Repositories\PostRepository\SQLitePostRepository;
use Alexs\PhpAdvanced\Blog\Repositories\UserRepository\SQLiteUserRepository;
use Alexs\PhpAdvanced\Blog\UUID;
use PDO;
use PDOStatement;
use Psr\Log\LoggerInterface;

class SQLiteCommitRepository implements CommitRepositoryInterface
{
    public function __construct(
        private PDO $connect,
        // Внедряем контракт логгера
        private LoggerInterface $logger
    )
    {
    }

    public function save(Commit $commit): void
    {
        try {
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
            $this->logger->info("Коммит " . $commit->getUuid() . " добавлен в базу данных");
        }catch (ErrorRepository $e){
            $this->logger->error($e->getMessage(), ['exception' => $e]);
        }
    }

    /**
     * @throws CommitNotFoundException
     * @throws InvalidArgumentException
     * @throws AppException
     */
    public function get(UUID $uuid):Commit
    {
        $statement = $this->connect->prepare("SELECT * FROM comments WHERE uuid = :uuid");
        $statement->execute(['uuid' => (string)$uuid]);

        try {
            return $this->createCommit($statement, $uuid);
        } catch (CommitNotFoundException | InvalidArgumentException | PostNotFoundException |UserNotFoundException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            throw new AppException($e->getMessage());
        }
    }

    /**
     * @throws CommitNotFoundException
     * @throws InvalidArgumentException
     * @throws UserNotFoundException
     * @throws PostNotFoundException
     */
    private function createCommit(PDOStatement $statement, $data):Commit
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if($result===false){
            $this->logger->warning("Коммит $data нет в базе данных");
            throw new CommitNotFoundException("Комментария $data нет в базе данных");
        }

        $userRepository = new SQLiteUserRepository($this->connect, $this->logger);
        $user = $userRepository->get(new UUID($result['uuidAuthor']));

        $postRepository = new SQLitePostRepository($this->connect, $this->logger);
        $post = $postRepository->get(new UUID($result['uuidPost']));

        return new Commit(new UUID($result['uuid']), $user, $post, $result['text']);
    }

    public function delete (string $uuid):void
    {
        $statement = $this->connect->prepare("DELETE FROM comments WHERE comments.uuid = :commitUuid");
        $statement->execute([':commitUuid' => $uuid]);
        $this->logger->info("Post $uuid deleted");
    }
}