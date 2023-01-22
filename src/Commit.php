<?php

class Commit
{
    public function __construct(
        private int $id,
        private string $idAuthor,
        private int $idArticle,
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
    private function getIdArticle()
    {
        return $this->idArticle;
    }
    private function getText()
    {
        return $this->text;
    }
}