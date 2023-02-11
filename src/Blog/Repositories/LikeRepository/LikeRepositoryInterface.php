<?php

namespace Alexs\PhpAdvanced\Blog\Repositories\LikeRepository;

use Alexs\PhpAdvanced\Blog\Like;
use Alexs\PhpAdvanced\Blog\UUID;

interface LikeRepositoryInterface
{
    public function save(Like $like) : void;
    public function getAllLikeByPostUuid(string $uuidPost):array;
}