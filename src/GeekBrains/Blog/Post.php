<?php

namespace GeekBrains\Blog;

class Post
{
    public function __construct(
        private int $id,
        private int $author_id,
        private string $title,
        private string $text
    ) {}

    public function getId() {
        return $this->id;
    }

    public function getAuthorId() {
        return $this->author_id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getText() {
        return $this->text;
    }

    public function __toString()
    {
        return sprintf('[%d]. Автор [%d] пишет в статье "%s" — %s', 
            $this->id, $this->author_id, $this->title, $this->text);
    }
}