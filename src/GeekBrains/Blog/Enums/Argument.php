<?php

namespace GeekBrains\Blog\Enums;

class Argument
{
    const USER = 'user';
    const POST = 'post';
    const COMMENT = 'comment';

    public static array $ARGUMENTS = [self::USER, self::POST, self::COMMENT];
}