<?php

use GeekBrains\Blog\config\SqliteConfig;
use GeekBrains\Blog\Container\DIContainer;
use GeekBrains\Blog\Repositories\CommentsRepositoryInterface;
use GeekBrains\Blog\Repositories\LikesRepositoryInterface;
use GeekBrains\Blog\Repositories\PostsRepositoryInterface;
use GeekBrains\Blog\Repositories\SqliteCommentsRepository;
use GeekBrains\Blog\Repositories\SqliteLikesRepository;
use GeekBrains\Blog\Repositories\SqlitePostsRepository;
use GeekBrains\Blog\Repositories\SqliteUsersRepository;
use GeekBrains\Blog\Repositories\UsersRepositoryInterface;

// Подключаем автозагрузчик Composer
require_once __DIR__ . '/vendor/autoload.php';

// Создаём объект контейнера ..
$container = new DIContainer();

// .. и настраиваем его:
// 1. подключение к БД
$container->bind(
    PDO::class,
    new PDO(SqliteConfig::DSN)
);

// 2. репозиторий статей
$container->bind(
    PostsRepositoryInterface::class,
    SqlitePostsRepository::class
);

// 3. репозиторий пользователей
$container->bind(
    UsersRepositoryInterface::class,
    SqliteUsersRepository::class
);

// 4. репозиторий комментариев
$container->bind(
    CommentsRepositoryInterface::class,
    SqliteCommentsRepository::class
);

// 5. репозиторий комментариев
$container->bind(
    LikesRepositoryInterface::class,
    SqliteLikesRepository::class
);

// Возвращаем объект контейнера
return $container;
