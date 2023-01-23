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

    public function getId()
    {
        return $this->id;
    }
    public function getIdAuthor()
    {
        return $this->idAuthor;
    }
    public function getIdArticle()
    {
        return $this->idArticle;
    }
    public function getText()
    {
        return $this->text;
    }

}