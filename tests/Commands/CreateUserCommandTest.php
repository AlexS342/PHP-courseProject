<?php

namespace Alexs\PhpAdvanced\UnitTest\Commands;

use Alexs\PhpAdvanced\Blog\Exceptions\ArgumentsException;
use Alexs\PhpAdvanced\Blog\Exceptions\InvalidArgumentException;
use Alexs\PhpAdvanced\Blog\Exceptions\UserNotFoundException;
use Alexs\PhpAdvanced\Blog\Repositories\UserRepository\DummyUsersRepository;
use Alexs\PhpAdvanced\Http\Actions\Users\CreateUser;
use PHPUnit\Framework\TestCase;
use Alexs\PhpAdvanced\Blog\Repositories\UserRepository\UserRepositoryInterface;
use Alexs\PhpAdvanced\Blog\Commands\Arguments;
use Alexs\PhpAdvanced\Blog\Exceptions\CommandException;
use Alexs\PhpAdvanced\Blog\Commands\CreateUserCommand;
use Alexs\PhpAdvanced\Blog\User;
use Alexs\PhpAdvanced\Blog\UUID;
use RuntimeException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;


class CreateUserCommandTest extends TestCase
{
    /**
     * @throws ArgumentsException
     * @throws InvalidArgumentException
     */
    public function testItThrowsAnExceptionWhenUserAlreadyExists(): void
    {
        // Создаём объект команды
        // У команды одна зависимость - UsersRepositoryInterface
        $command = new CreateUserCommand( new DummyUsersRepository() );
        // Описываем тип ожидаемого исключения
        $this->expectException(CommandException::class);
        // и его сообщение
        $this->expectExceptionMessage('User already exists: Ivan');
        // Запускаем команду с аргументами
        $command->handle(new Arguments(['username' => 'Ivan']));
    }

    // Тест проверяет, что команда действительно требует имя пользователя
    /**
     * @throws InvalidArgumentException
     * @throws CommandException
     */
    public function testItRequiresFirstName(): void
    {
        $command = new CreateUser(
            $this->makeUsersRepository()
        );
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Not enough arguments (missing: "first_name, last_name").'
        );
        $command->run(
            new ArrayInput([
                'username' => 'Ivan',
                'password' => 'some_password',
            ]),
            new NullOutput()
        );
    }

    // Тест проверяет, что команда действительно требует фамилию пользователя
    /**
     * @throws InvalidArgumentException
     * @throws CommandException
     */
    public function testItRequiresLastName(): void
    {
        // Тестируем новую команду
        $command = new CreateUser(
            $this->makeUsersRepository(),
        );
        // Меняем тип ожидаемого исключения ..
        $this->expectException(RuntimeException::class);
        // .. и его сообщение
        $this->expectExceptionMessage(
            'Not enough arguments (missing: "last_name").'
        );
        // Запускаем команду методом run вместо handle
        $command->run(
            // Передаём аргументы как ArrayInput, а не Arguments
            // Сами аргументы не меняются
            new ArrayInput([
                'username' => 'Ivan',
                'password' => 'some_password',
                'first_name' => 'Ivan',
            ]),
            // Передаём также объект, реализующий контракт OutputInterface
            // Нам подойдёт реализация, которая ничего не делает
            new NullOutput()
        );
    }

    public function testItRequiresPassword(): void
    {
        $command = new CreateUser(
            $this->makeUsersRepository()
        );
$this->expectException(RuntimeException::class);
$this->expectExceptionMessage(
    'Not enough arguments (missing: "first_name, last_name, password"'
);
$command->run(
    new ArrayInput([
        'username' => 'Ivan',
    ]),
    new NullOutput()
);
}

    // Функция возвращает объект типа UsersRepositoryInterface
    private function makeUsersRepository(): UserRepositoryInterface
    {
        return new class implements UserRepositoryInterface {
            public function save(User $user): void
            {
            }
            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }
            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException("Not found");
            }
        };
    }


// Тест, проверяющий, что команда сохраняет пользователя в репозитории
    public function testItSavesUserToRepository(): void
    {
        // Создаём объект анонимного класса
        $usersRepository = new class implements UserRepositoryInterface {
            // В этом свойстве мы храним информацию о том, был ли вызван метод save
            private bool $called = false;
            public function save(User $user): void
            {
                // Запоминаем, что метод save был вызван
                $this->called = true;
            }
            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }
            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException("Not found");
            }
            // Этого метода нет в контракте UsersRepositoryInterface, но ничто не мешает его добавить.
            // С помощью этого метода мы можем узнать, был ли вызван метод save
            public function wasCalled(): bool
            {
                return $this->called;
            }
        };

        $command = new CreateUser(
            $usersRepository
        );
        $command->run(
            new ArrayInput([
                'username' => 'Ivan',
                'password' => 'some_password',
                'first_name' => 'Ivan',
                'last_name' => 'Nikitin',
            ]),
            new NullOutput()
        );

        $this->assertTrue($usersRepository->wasCalled());
    }
}

