<?php

namespace GeekBrains\Blog\Commands;

use GeekBrains\Blog\Exceptions\ArgumentsException;
use GeekBrains\Blog\Repositories\CommentsRepositoryInterface;
use GeekBrains\Blog\Comment;

class CreateCommentCommand implements CommandInterface
{
    public function __construct(
        private CommentsRepositoryInterface $commentsRepository
    ) {}

    /**
     * @throws ArgumentsException
     */
    public function handle(Arguments $arguments): void
    { 
        $this->commentsRepository->save(new Comment(
            0,
            $arguments->get('post_id'), 
            $arguments->get('author_id'), 
            $arguments->get('text')
        ));
    }
}