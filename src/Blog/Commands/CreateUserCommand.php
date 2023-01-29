<?php

namespace Alexs\PhpAdvanced\Blog\Commands;


use Alexs\PhpAdvanced\Blog\Exceptions\ArgumentsException;
use Alexs\PhpAdvanced\Blog\Exceptions\CommandException;
use Alexs\PhpAdvanced\Blog\Exceptions\InvalidArgumentException;
use Alexs\PhpAdvanced\Blog\Exceptions\UserNotFoundException;
use Alexs\PhpAdvanced\Blog\Repositories\UserRepository\UserRepositoryInterface;
use Alexs\PhpAdvanced\Blog\User;
use Alexs\PhpAdvanced\Blog\UUID;

final class CreateUserCommand
{
    public function __construct(
        private UserRepositoryInterface $usersRepository
    ) {
    }

    /**
     * @throws InvalidArgumentException
     * @throws CommandException
     * @throws ArgumentsException
     */
    public function handle(Arguments $arguments): void
    {
        $username = $arguments->get('username');
        if ($this->userExists($username)) {
            throw new CommandException("User already exists: $username");
        }
        $this->usersRepository->save(new User(
            UUID::random(),
            $arguments->get('firstName'),
            $arguments->get('lastName'),
            $username,
            $arguments->get('password')
        ));
    }

    private function userExists(string $username): bool
    {
        try {
            $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException) {
            return false;
        }
        return true;
    }
}