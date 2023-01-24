<?php

namespace Alexs\PhpCourseProject;
class Commit
{
    public function __construct(
        private int $id,
        private int $idAuthor,
        private int $idArticle,
        private string $text
    )
    {
    }

    public function __toString()
    {
        return $this->text;
    }

    public function getId() :int
    {
        return $this->id;
    }
    public function getIdAuthor() :int
    {
        return $this->idAuthor;
    }
    public function getIdArticle() :int
    {
        return $this->idArticle;
    }
    public function getText() :string
    {
        return $this->text;
    }

}