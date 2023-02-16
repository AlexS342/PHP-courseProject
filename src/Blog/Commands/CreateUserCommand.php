<?php

namespace Alexs\PhpAdvanced\Blog\Commands;


use Alexs\PhpAdvanced\Blog\Exceptions\ArgumentsException;
use Alexs\PhpAdvanced\Blog\Exceptions\CommandException;
use Alexs\PhpAdvanced\Blog\Exceptions\InvalidArgumentException;
use Alexs\PhpAdvanced\Blog\Exceptions\UserNotFoundException;
use Alexs\PhpAdvanced\Blog\Repositories\UserRepository\UserRepositoryInterface;
use Alexs\PhpAdvanced\Blog\User;
use Alexs\PhpAdvanced\Blog\UUID;
use Psr\Log\LoggerInterface;

final class CreateUserCommand
{
    public function __construct(
        private UserRepositoryInterface $usersRepository,
        private LoggerInterface $logger
    ) {
    }

    /**
     * @throws InvalidArgumentException
     * @throws CommandException
     * @throws ArgumentsException
     */
    public function handle(Arguments $arguments): void
    {

        // Логируем информацию о том, что команда запущена
        // Уровень логирования – INFO
        $this->logger->info("Create user command started");

        $username = $arguments->get('username');

        if ($this->userExists($username)) {
            // Логируем сообщение с уровнем WARNING
            $this->logger->warning("User already exists: $username");
            // Вместо выбрасывания исключения просто выходим из функции
            return;
        }

        $uuid = UUID::random();

        $this->usersRepository->save(new User(
            $uuid,
            $arguments->get('firstName'),
            $arguments->get('lastName'),
            $username,
            $arguments->get('password')
        ));

        // Логируем информацию о новом пользователе
        $this->logger->info("User created: $uuid");
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