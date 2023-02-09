<?php

namespace Alexs\PhpAdvanced\Blog\Repositories\UserRepository;

use Alexs\PhpAdvanced\Blog\Exceptions\InvalidArgumentException;
use Alexs\PhpAdvanced\Blog\Exceptions\UserNotFoundException;
use Alexs\PhpAdvanced\Blog\User;
use Alexs\PhpAdvanced\Blog\UUID;
use PDO;
use PDOStatement;

class SQLiteUserRepository implements UserRepositoryInterface
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

    /**
     * @param string $username
     * @return User
     * @throws InvalidArgumentException
     * @throws UserNotFoundException
     */
    public function getByUsername(string $username):User
    {
        $statement = $this->connect->prepare(
            "SELECT * FROM users WHERE username = :username"
        );
        $statement->execute(['username' => $username]);

        return $this->createUser($statement, $username);
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

    public function delete (string $uuid):void
    {
        $statement = $this->connect->prepare("DELETE FROM users WHERE 'uuid' = :userUuid");
//        $statement->bindValue(':userUuid', $uuid);
//        $count = $statement->execute();
        $count = $statement->execute([':userUuid'=>$uuid]);
        echo 'Удалено строк: ' . $count . PHP_EOL;
    }
}
