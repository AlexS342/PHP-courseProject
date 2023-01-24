<?php

namespace Alexs\PhpCourseProject;
class User
{
    public function __construct(
        private int $id,
        private string $name,
        private string $surname
    )
    {
    }

    public function __toString()
    {
        return $this->name . ' ' . $this->surname;
    }

    public function getId() :int
    {
        return $this->id;
    }
    public function getName() :string
    {
        return $this->name;
    }
    public function getSurname() :string
    {
        return $this->surname;
    }
}