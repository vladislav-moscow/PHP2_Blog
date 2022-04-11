<?php

namespace GeekBrains\Blog\UnitTests\Commands;

use GeekBrains\Blog\Post;
use GeekBrains\Blog\Commands\Arguments;
use GeekBrains\Blog\Commands\CreatePostCommand;
use GeekBrains\Blog\Exceptions\PostNotFoundException;
use GeekBrains\Blog\Repositories\PostsRepositoryInterface;
use GeekBrains\Blog\Repositories\SqlitePostsRepository;
use PHPUnit\Framework\TestCase;

class PostRepositoryTest extends TestCase
{
    // Тест, проверяющий, что команда сохраняет статью в репозитории
    public function testItSavesPostToRepository(): void
    {
        // Создаём объект анонимного класса
        $postsRepository = new class implements PostsRepositoryInterface {
            // В этом свойстве мы храним информацию о том,
            // был ли вызван метод save
            private bool $called = false;

            public function save(Post $post): void
            {
                // Запоминаем, что метод save был вызван
                $this->called = true;
            }

            public function get(int $id): Post
            {
                throw new PostNotFoundException("Post not found");
            }

            // Этого метода нет в контракте PostsRepositoryInterface,
            // но ничто не мешает его добавить.
            // С помощью этого метода мы можем узнать,
            // был ли вызван метод save
            public function wasCalled(): bool
            {
                return $this->called;
            }
        };

        // Передаём наш мок в команду
        $command = new CreatePostCommand($postsRepository);

        // Запускаем команду
        $command->handle(new Arguments([
            'author_id' => '1',
            'title' => 'title',
            'text' => 'text',
        ]));

        // Проверяем утверждение относительно мока,
        // а не утверждение относительно команды
        $this->assertTrue($postsRepository->wasCalled());
    }

    // Тест, проверяющий, что репозиторий бросает исключение, если статья не найдена.
    public function testItThrowsAnExceptionWhenPostNotFound(): void
    {
        $repository = new SqlitePostsRepository();
        $id = 0;

        // Описываем тип ожидаемого исключения
        $this->expectException(PostNotFoundException::class);
        // и его сообщение
        $this->expectExceptionMessage("Cannot find post: $id");

        // Проверяем утверждение
        $repository->get($id);
    }
}