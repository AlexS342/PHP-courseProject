<?php
namespace Alexs\PhpCourseProject;
class Article
{
    public function __construct(
        private int $id,
        private int $idAuthor,
        private string $header,
        private string $text
    )
    {
    }

    public function __toString()
    {
        return $this->header . ' >>> ' . $this->text;
    }

    public function getId() :int
    {
        return $this->id;
    }
    public function getIdAuthor() :int
    {
        return $this->idAuthor;
    }
    public function getHeader() :string
    {
        return $this->header;
    }
    public function getText() :string
    {
        return $this->text;
    }
}