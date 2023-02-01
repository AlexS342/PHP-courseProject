<?php

namespace Alexs\PhpAdvanced\Blog;

use Alexs\PhpAdvanced\Blog\Exceptions\InvalidArgumentException;

class UUID
{

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(
        private string $uuidString
    )
    {
        if(!uuid_is_valid($this->uuidString)){
            throw new InvalidArgumentException("Получен некоректный UUID" . $this->uuidString);
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    public static function random():self
    {
        return new self(uuid_create(UUID_TYPE_RANDOM));
    }
    public function __toString(): string
    {
        return $this->uuidString;
    }

    /**
     * @return string
     */
    public function getUuidString(): string
    {
        return $this->uuidString;
    }

    /**
     * @param string $uuidString
     */
    public function setUuidString(string $uuidString): void
    {
        $this->uuidString = $uuidString;
    }

}