<?php

class Article
{
    public function __construct(
        private int $id,
        private string $idAuthor,
        private int $header,
        private string $text
    )
    {
    }
    private function getId()
    {
        return $this->id;
    }
    private function getIdAuthor()
    {
        return $this->idAuthor;
    }
    private function getHeader()
    {
        return $this->header;
    }
    private function getText()
    {
        return $this->text;
    }
}