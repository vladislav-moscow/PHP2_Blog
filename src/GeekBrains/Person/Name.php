<?php

namespace GeekBrains\Person;

class Name
{
    public function __construct(
        private string $firstName,
        private string $lastName
    ) {}

    public function __toString()
    {
        return sprintf('%s %s', $this->firstName, $this->lastName);
    }
}