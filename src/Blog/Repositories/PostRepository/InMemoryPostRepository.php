<?php

namespace Alexs\PhpAdvanced\Blog\Repositories\PostRepository;

use Alexs\PhpAdvanced\Blog\Exceptions\PostNotFoundException;
use Alexs\PhpAdvanced\Blog\Post;
use Alexs\PhpAdvanced\Blog\UUID;

class InMemoryPostRepository implements PostRepositoryInterface
{
    /**
     * @var array
     */
    private array $posts = [];

    /**
     * @param Post $post
     * @return void
     */
    public function save(Post $post):void
    {
        $this->posts[] = $post;
    }

    /**
     * @param UUID $uuid
     * @return Post
     * @throws PostNotFoundException
     */
    public function get(UUID $uuid): Post
    {
        foreach ($this->posts as $post){
            if($post->getUuid() === $uuid){
                return $post;
            }
        }
        throw new PostNotFoundException("Error: Post not found $uuid");
    }
}