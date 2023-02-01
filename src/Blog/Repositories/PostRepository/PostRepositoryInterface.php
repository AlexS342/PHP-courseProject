<?php

namespace Alexs\PhpAdvanced\Blog\Repositories\PostRepository;

use Alexs\PhpAdvanced\Blog\Post;
use Alexs\PhpAdvanced\Blog\UUID;

interface PostRepositoryInterface
{
    public function save(Post $post) : void;
    public function get(UUID $uuid) : Post;
}