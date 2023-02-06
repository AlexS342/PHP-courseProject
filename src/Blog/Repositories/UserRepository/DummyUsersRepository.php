<?php

namespace Alexs\PhpAdvanced\Blog\Repositories\UserRepository;

use Alexs\PhpAdvanced\Blog\Exceptions\InvalidArgumentException;
use Alexs\PhpAdvanced\Blog\Exceptions\UserNotFoundException;
use Alexs\PhpAdvanced\Blog\User;
use Alexs\PhpAdvanced\Blog\UUID;

class DummyUsersRepository implements UserRepositoryInterface
{

    public function save(User $user): void
    {
        // Ничего не делаем
    }

    /**
     * @throws UserNotFoundException
     */
    public function get(UUID $uuid): User
    {
        // И здесь ничего не делаем
        throw new UserNotFoundException("Not found");
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getByUsername(string $username): User
    {
        // Нас интересует реализация только этого метода
        // Для нашего теста не важно, что это будет за пользователь,
        // поэтому возвращаем совершенно произвольного
        return new User(UUID::random(), 'Василий', 'Ромашев', 'DomRoy', 'vFv456NgN');
    }
}