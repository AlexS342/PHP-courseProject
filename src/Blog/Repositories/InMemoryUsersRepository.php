<?php

namespace Alexs\PhpAdvanced\Blog\Repositories;

use Alexs\PhpAdvanced\Blog\Exceptions\UserNotFoundException;
use Alexs\PhpAdvanced\Blog\User;

class InMemoryUsersRepository
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
     * @param int $id
     * @return User
     * @throws UserNotFoundException
     */
    public function get(int $id = 1): User
    {
        foreach ($this->users as $user){
            if($user->getId() === $id){
                return $user;
            }
        }
        throw new UserNotFoundException("User not found $id");
    }
}