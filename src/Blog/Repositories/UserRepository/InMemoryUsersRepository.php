<?php

namespace Alexs\PhpAdvanced\Blog\Repositories\UserRepository;

use Alexs\PhpAdvanced\Blog\Exceptions\UserNotFoundException;
use Alexs\PhpAdvanced\Blog\User;
use Alexs\PhpAdvanced\Blog\UUID;

class InMemoryUsersRepository implements UserRepositoryInterface
{
    /**
     * @var array
     */
    private array $users = [];

    /**
     * @param User $user
     * @return void
     */
    public function save(User $user):void
    {
        $this->users[] = $user;
    }

    /**
     * @param UUID $uuid
     * @return User
     * @throws UserNotFoundException
     */
    public function get(UUID $uuid): User
    {
        foreach ($this->users as $user){
            if((string)$user->getUuid() === (string)$uuid){
                return $user;
            }
        }
        throw new UserNotFoundException("Error: User not found $uuid");
    }


    /**
     * @param string $username
     * @return User
     * @throws UserNotFoundException
     */
    public function getByUsername(string $username): User
    {
        foreach ($this->users as $user){
            if($user->getUsername() === $username){
                return $user;
            }
        }
        throw new UserNotFoundException("Error: User not found $username");
    }
}