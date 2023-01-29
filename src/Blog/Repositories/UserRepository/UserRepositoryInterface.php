<?php

namespace Alexs\PhpAdvanced\Blog\Repositories\UserRepository;

use Alexs\PhpAdvanced\Blog\User;
use Alexs\PhpAdvanced\Blog\UUID;

interface UserRepositoryInterface
{
    public function save(User $user) : void;
    public function get(UUID $uuid) : User;
    public function getByUsername(string $username):User;
}