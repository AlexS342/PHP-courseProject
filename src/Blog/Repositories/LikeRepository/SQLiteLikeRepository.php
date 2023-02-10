<?php

namespace Alexs\PhpAdvanced\Blog\Repositories\LikeRepository;

use Alexs\PhpAdvanced\Blog\Exceptions\InvalidArgumentException;
use Alexs\PhpAdvanced\Blog\Exceptions\UserNotFoundException;
use Alexs\PhpAdvanced\Blog\Like;
use Alexs\PhpAdvanced\Blog\Repositories\UserRepository\UserRepositoryInterface;
use Alexs\PhpAdvanced\Blog\User;
use Alexs\PhpAdvanced\Blog\UUID;
use Alexs\PhpAdvanced\Http\ErrorResponse;
use Alexs\PhpAdvanced\Http\Response;
use PDO;
use PDOStatement;

class SQLiteLikeRepository implements LikeRepositoryInterface
{

    public function __construct(
        private PDO $connect
    )
    {
    }

    public function save(User $user): void
    {
        $statement = $this->connect->prepare(
            "INSERT INTO users (uuid, firstName, lastName, username, password) 
            VALUES (:uuid, :firstName, :lastName, :username, :password)"
        );
        $statement->execute([
            'uuid' => (string)$user->getUuid(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'username' => $user->getUsername(),
            'password' => $user->getPassword()
        ]);
    }


    /**
     * @throws UserNotFoundException
     * @throws InvalidArgumentException
     */
    public function get(UUID $uuid):User
    {
        $statement = $this->connect->prepare(
            "SELECT * FROM users WHERE uuid = :uuid"
        );
        $statement->execute(['uuid' => (string)$uuid]);
//        $result = $statement->fetch(PDO::FETCH_ASSOC);
//        if($result===false){
//            throw new UserNotFoundException("Такого пользователя нет в базе данных");
//        }
//        return new User(new UUID($result['uuid']), $result['firstName'], $result['lastName'], $result['username'], $result['password'], );
        return $this->createUser($statement, $uuid);
    }


    public function getLikesByPostUuid(string $uuidPost):array
    {
        $statement = $this->connect->prepare(
            "SELECT * FROM users WHERE username = :username"
        );
        $statement->execute(['username' => $uuidPost]);

        return $this->createUser($statement, $uuidPost);
    }

    /**
     * @param PDOStatement $statement
     * @param $data
     * @return User
     * @throws InvalidArgumentException
     * @throws UserNotFoundException
     */
    private function createUser(PDOStatement $statement, $data):User
        {
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            if($result===false){
                throw new UserNotFoundException("Пользователя $data нет в базе данных");
            }
            return new User(new UUID($result['uuid']), $result['firstName'], $result['lastName'], $result['username'], $result['password'], );
        }

    public function delete (UUID $uuid):void
    {
        $statement = $this->connect->prepare("DELETE FROM users WHERE users.uuid = :userUuid");
        $statement->execute([':userUuid'=>$uuid]);
    }

}
