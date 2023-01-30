<?php

namespace Alexs\PhpAdvanced\Blog;

class User
{
    public function __construct(
        private int $id,
        private string $username,
        private string $login
    )
    {

    }
    public function __toString(): string
    {
        return 'Пользователь ' . $this->id . ' с именем ' . $this->username . ' и логином ' . $this->login;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @param string $login
     */
    public function setLogin(string $login): void
    {
        $this->login = $login;
    }

}