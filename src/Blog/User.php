<?php

namespace Alexs\PhpAdvanced\Blog;

class User
{
    public function __construct(
        private UUID $uuid,
        private string $firstName,
        private string $lastName,
        private string $username,
//        private string $password
        // Переименовали поле password
        private string $hashedPassword,
    )
    {

    }
    public function __toString(): string
    {
        return 'Пользователь ' . $this->uuid . ' с именем ' . $this->firstName . ' ' . $this->lastName . ' и логином ' . $this->username;
    }

    // Переименовали функцию
    public function hashedPassword(): string
    {
        return $this->hashedPassword;
    }

    // Функция для вычисления хеша
    private static function hash(string $password, $uuid): string
    {
        return hash('sha256', $uuid . $password);
    }
// Функция для проверки предъявленного пароля
    public function checkPassword(string $password): bool
    {
        // Передаём UUID пользователя
        // в функцию хеширования пароля
        return $this->hashedPassword === self::hash($password, $this->uuid);
    }

    // Функция для создания нового пользователя
    public static function createFrom(string $firstName, string $lastName, string $username, string $password, ): self
    {
        $uuid = UUID::random();
        return new self(
            $uuid,
            $firstName,
            $lastName,
            $username,
            self::hash($password, $uuid),
        );
    }

    /**
     * @return UUID
     */
    public function getUuid(): UUID
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->hashedPassword;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->hashedPassword = $password;
    }


}