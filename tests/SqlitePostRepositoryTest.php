<?php

namespace Alexs\PhpAdvanced\UnitTest;

use Alexs\PhpAdvanced\Blog\Exceptions\InvalidArgumentException;
use Alexs\PhpAdvanced\Blog\Exceptions\PostNotFoundException;
use Alexs\PhpAdvanced\Blog\Exceptions\UserNotFoundException;
use Alexs\PhpAdvanced\Blog\Repositories\PostRepository\SQLitePostRepository;
use Alexs\PhpAdvanced\Blog\User;
use Alexs\PhpAdvanced\Blog\UUID;
use Alexs\PhpAdvanced\Blog\Post;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class SqlitePostRepositoryTest extends TestCase
{
    // Тест, проверяющий, что SQLite-репозиторий бросает исключение,
    // когда запрашиваемый пост не найден
    /**
     * @throws InvalidArgumentException
     * @throws PostNotFoundException|UserNotFoundException
     */
    public function testItThrowsAnExceptionWhenPostNotFound(): void
    {
        // Сначала нам нужно подготовить все стабы
        // 2. Создаём стаб подключения
        $connectionStub = $this->createStub(PDO::class);
        // 4. Стаб запроса
        $statementStub = $this->createStub(PDOStatement::class);
        // 5. Стаб запроса будет возвращать false
        // при вызове метода fetch
        $statementStub->method('fetch')->willReturn(false);
        // 3. Стаб подключения будет возвращать другой стаб -
        // стаб запроса - при вызове метода prepare
        $connectionStub->method('prepare')->willReturn($statementStub);
        // 1. Передаём в репозиторий стаб подключения
        $repository = new SQLitePostRepository($connectionStub);
        // Ожидаем, что будет брошено исключение
        $this->expectException(PostNotFoundException::class);
        $this->expectExceptionMessage("Пост " . new UUID("123e4567-e89b-12d3-a456-426614174050") . " нет в базе данных");
        // Вызываем метод получения пользователя
        $repository->get(new UUID("123e4567-e89b-12d3-a456-426614174050"));
    }

    // Тест, проверяющий, что репозиторий сохраняет данные в БД
    public function testItSavesPostToDatabase(): void
    {
        // 2. Создаём стаб подключения
        $connectionStub = $this->createStub(PDO::class);
        // 4. Создаём мок запроса, возвращаемый стабом подключения
        $statementMock = $this->createMock(PDOStatement::class);
        // 5. Описываем ожидаемое взаимодействие
        // нашего репозитория с моком запроса
        $statementMock
            ->expects($this->once()) // Ожидаем, что будет вызван один раз
            ->method('execute') // метод execute
            ->with([ // с единственным аргументом - массивом
                'uuid' => '123e4567-e89b-12d3-a456-426614174123',
                'uuidAuthor' => '123e4567-e89b-12d3-a456-426614174000',
                'header' => 'Случайный заголовок',
                'text' => 'Случайный текст какого-то поста'
            ]);
        // 3. При вызове метода prepare стаб подключения
        // возвращает мок запроса
        $connectionStub->method('prepare')->willReturn($statementMock);
        // 1. Передаём в репозиторий стаб подключения
        $repository = new SQLitePostRepository($connectionStub);
        // Вызываем метод сохранения пользователя
        $repository->save(
            new Post(
                new UUID('123e4567-e89b-12d3-a456-426614174123'),
                new User(
                    new UUID('123e4567-e89b-12d3-a456-426614174000'),
                    'Андрей',
                    'Чилябин',
                    'AndChi345',
                    'ergg2345'
                ),
                'Случайный заголовок',
                'Случайный текст какого-то поста'
            )
        );
    }
}