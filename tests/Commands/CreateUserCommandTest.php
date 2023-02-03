<?php

namespace Alexs\PhpAdvanced\UnitTest\Commands;

use Alexs\PhpAdvanced\Blog\Exceptions\ArgumentsException;
use Alexs\PhpAdvanced\Blog\Exceptions\InvalidArgumentException;
use Alexs\PhpAdvanced\Blog\Exceptions\UserNotFoundException;
use Alexs\PhpAdvanced\Blog\Repositories\UserRepository\DummyUsersRepository;
use PHPUnit\Framework\TestCase;
use Alexs\PhpAdvanced\Blog\Repositories\UserRepository\UserRepositoryInterface;
use Alexs\PhpAdvanced\Blog\Commands\Arguments;
use Alexs\PhpAdvanced\Blog\Exceptions\CommandException;
use Alexs\PhpAdvanced\Blog\Commands\CreateUserCommand;
use Alexs\PhpAdvanced\Blog\User;
use Alexs\PhpAdvanced\Blog\UUID;


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
        // Вызываем ту же функцию
        $command = new CreateUserCommand($this->makeUsersRepository());
        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: firstName');
        $command->handle(new Arguments(['username' => 'Ivan']));
    }

    // Тест проверяет, что команда действительно требует фамилию пользователя

    /**
     * @throws InvalidArgumentException
     * @throws CommandException
     */
    public function testItRequiresLastName(): void
    {
        // Передаём в конструктор команды объект, возвращаемый нашей функцией
        $command = new CreateUserCommand($this->makeUsersRepository());
        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: lastName');
        $command->handle(new Arguments([
            'username' => 'Ivan',
            // Нам нужно передать имя пользователя,
            // чтобы дойти до проверки наличия фамилии
            'firstName' => 'Ivan',
        ]));
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
// В этом свойстве мы храним информацию о том,
// был ли вызван метод save
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
// Этого метода нет в контракте UsersRepositoryInterface,
// но ничто не мешает его добавить.
// С помощью этого метода мы можем узнать,
// был ли вызван метод save
            public function wasCalled(): bool
            {
                return $this->called;
            }
        };
// Передаём наш мок в команду
        $command = new CreateUserCommand($usersRepository);
// Запускаем команду
        $command->handle(new Arguments([
            'username' => 'Ivan',
            'firstName' => 'Ivan',
            'lastName' => 'Nikitin',
            'password' => '12345sdfg'
        ]));
// Проверяем утверждение относительно мока,
// а не утверждение относительно команды
        $this->assertTrue($usersRepository->wasCalled());
    }

}

