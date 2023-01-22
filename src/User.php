<?php

class user
{
    public function __construct(
        private int $id,
        private string $name,
        private string $surname
    )
    {
    }

    private function getId()
    {
        return $this->id;
    }
    private function getName()
    {
        return $this->name;
    }
    private function getSurname()
    {
        return $this->surname;
    }
}