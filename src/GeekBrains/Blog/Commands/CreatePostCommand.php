<?php

namespace GeekBrains\Blog\Commands;

use GeekBrains\Blog\Exceptions\ArgumentsException;
use GeekBrains\Blog\Repositories\PostsRepositoryInterface;
use GeekBrains\Blog\Post;

class CreatePostCommand implements CommandInterface
{
    public function __construct(
        private PostsRepositoryInterface $postsRepository
    ) {}

    /**
     * @throws ArgumentsException
     */
    public function handle(Arguments $arguments): void
    { 
        $this->postsRepository->save(new Post(
            0,
            $arguments->get('author_id'), 
            $arguments->get('title'), 
            $arguments->get('text')
        ));
    }
}