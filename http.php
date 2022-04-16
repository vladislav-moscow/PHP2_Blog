<?php

use GeekBrains\Blog\Exceptions\AppException;
use GeekBrains\Blog\Http\Actions\Comments\CreateComment;
use GeekBrains\Blog\Http\Actions\Likes\CreateLike;
use GeekBrains\Blog\Http\Actions\Posts\CreatePost;
use GeekBrains\Blog\Http\Actions\Posts\DeletePost;
use GeekBrains\Blog\Http\Actions\Posts\FindById;
use GeekBrains\Blog\Http\Actions\Users\FindByUsername;
use GeekBrains\Blog\Http\ErrorResponse;
use GeekBrains\Blog\Http\HttpException;
use GeekBrains\Blog\Http\Request;

// Подключаем файл bootstrap.php и получаем настроенный контейнер
$container = require __DIR__ . '/bootstrap.php';

// Создаём объект запроса из суперглобальных переменных
$request = new Request(
    $_GET,
    $_SERVER,
    // Читаем поток, содержащий тело запроса
    file_get_contents('php://input'),
);

try {
    $path = $request->path();
} catch (HttpException) {
    (new ErrorResponse)->send();
    return;
}

try {
    $method = $request->method();
} catch (HttpException) {
    (new ErrorResponse)->send();
    return;
}

// Ассоциируем маршруты с именами классов действий,
// вместо готовых объектов
$routes = [
    'GET' => [
        '/users/show' => FindByUsername::class,
        '/posts/show' => FindById::class,
    ],
    'POST' => [
        '/posts/create' => CreatePost::class,
        '/posts/comment' => CreateComment::class,
        '/likes/create' => CreateLike::class,
    ],
    'DELETE' => [
        '/posts' => DeletePost::class,
    ],
];

// Если у нас нет маршрутов для метода запроса -
// возвращаем неуспешный ответ
if (!array_key_exists($method, $routes)) {
    (new ErrorResponse("Route not found: $method $path"))->send();
    return;
}

// Ищем маршрут среди маршрутов для этого метода
if (!array_key_exists($path, $routes[$method])) {
    (new ErrorResponse("Route not found: $method $path"))->send();
    return;
}

// Получаем имя класса действия для маршрута
$actionClassName = $routes[$method][$path];

// С помощью контейнера создаём объект нужного действия
$action = $container->get($actionClassName);

try {
    $response = $action->handle($request);
    $response->send();
} catch (AppException $e) {
    (new ErrorResponse($e->getMessage()))->send();
}