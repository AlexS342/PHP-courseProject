<?php

namespace Alexs\PhpAdvanced\Blog;

class Post
{
    /**
     * @param UUID $uuid
     * @param User $author
     * @param string $header
     * @param string $text
     */
    public function __construct(
        private UUID $uuid,
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
     * @return UUID
     */
    public function getUuid(): UUID
    {
        return $this->uuid;
    }

    /**
     * @param UUID $uuid
     */
    public function setUuid(UUID $uuid): void
    {
        $this->uuid = $uuid;
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
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }



}