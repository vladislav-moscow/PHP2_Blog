<?php

namespace GeekBrains\Blog\Commands;

use GeekBrains\Blog\Exceptions\ArgumentsException;
use GeekBrains\Blog\Repositories\LikesRepositoryInterface;
use GeekBrains\Blog\Like;

class CreateLikeCommand implements CommandInterface
{
    public function __construct(
        private LikesRepositoryInterface $likesRepository
    ) {}

    /**
     * @throws ArgumentsException
     */
    public function handle(Arguments $arguments): void
    { 
        $this->likesRepository->save(new Like(
            0,
            $arguments->get('post_id'), 
            $arguments->get('user_id'),
        ));
    }
}