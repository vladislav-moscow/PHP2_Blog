<?php

require_once __DIR__ . '/vendor/autoload.php';

use GeekBrains\Blog\Repositories\SqliteUsersRepository;
use GeekBrains\Blog\User;

//Создаём объект подключения к SQLite
$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');

//Создаём объект репозитория
$usersRepository = new SqliteUsersRepository($connection);

//Добавляем в репозиторий несколько пользователей
$usersRepository->save(new User(1, 'IvanNikitin@ya.ru', 'Ivan', 'Nikitin'));
$usersRepository->save(new User(2, 'AnnaPetrova@ya.ru', 'Anna', 'Petrova'));

echo $usersRepository->get(1);
echo $usersRepository->getByUsername('AnnaPetrova@ya.ru');