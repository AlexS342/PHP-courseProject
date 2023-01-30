<?php

namespace Alexs\PhpAdvanced\Blog;

class Post
{
    /**
     * @param int $id
     * @param User $author
     * @param string $header
     * @param string $text
     */
    public function __construct(
        private int $id,
        private User $author,
        private string $header,
        private string $text
    ) { }

    /**
     * @return string
     */
    public function __toString():string
    {
        return $this->author . ' ' . 'пишет: "' . $this->header . '" "'. $this->text . '"';
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }


    /**
     * @return User
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * @param User $author
     * @return void
     */
    public function setAuthor(User $author): void
    {
        $this->author = $author;
    }

    /**
     * @return string
     */
    public function getHeader(): string
    {
        return $this->header;
    }

    /**
     * @param string $header
     * @return void
     */
    public function setHeader(string $header): void
    {
        $this->header = $header;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return void
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }

}