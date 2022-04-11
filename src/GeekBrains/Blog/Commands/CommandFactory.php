<?php

namespace GeekBrains\Blog\Commands;

use GeekBrains\Blog\Enums\Argument;
use GeekBrains\Blog\Exceptions\MatchException;
use GeekBrains\Blog\Repositories\RepositoryInterface;

class CommandFactory
{
    /**
     * @throws MatchException
     */
    public function create(string $type, RepositoryInterface $repository): CommandInterface
    {
        return match ($type)
        {
            Argument::USER => new CreateUserCommand($repository),
            Argument::POST => new CreatePostCommand($repository),
            Argument::COMMENT => new CreateCommentCommand($repository),
            default => throw new MatchException(
                sprintf(
                    "Первый аргумент должен содержать одно из перечисленных значений: '%s'.",
                    implode("', '", Argument::$ARGUMENTS)
                )
            )
        };
    }  
}