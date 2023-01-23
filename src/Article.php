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

    public function getId()
    {
        return $this->id;
    }
    public function getIdAuthor()
    {
        return $this->idAuthor;
    }
    public function getHeader()
    {
        return $this->header;
    }
    public function getText()
    {
        return $this->text;
    }
}