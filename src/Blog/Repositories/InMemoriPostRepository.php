<?php

namespace Alexs\PhpAdvanced\Blog\Repositories;

use Alexs\PhpAdvanced\Blog\Exceptions\PostNotFoundException;
use Alexs\PhpAdvanced\Blog\Post;

class InMemoriPostRepository
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
     * @param int $id
     * @return Post
     * @throws PostNotFoundException
     */
    public function get(int $id): Post
    {
        foreach ($this->posts as $post){
            if($post->getId() === $id){
                return $post;
            }
        }
        throw new PostNotFoundException("Error: Post not found $id");
    }
}