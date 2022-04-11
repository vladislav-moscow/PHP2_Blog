<?php

use GeekBrains\Blog\Exceptions\AppException;
use GeekBrains\Blog\Http\Actions\Posts\CreatePost;
use GeekBrains\Blog\Http\Actions\Users\FindByUsername;
use GeekBrains\Blog\Http\Actions\Posts\FindById;
use GeekBrains\Blog\Http\ErrorResponse;
use GeekBrains\Blog\Http\HttpException;
use GeekBrains\Blog\Http\Request;
use GeekBrains\Blog\Repositories\SqliteUsersRepository;
use GeekBrains\Blog\Repositories\SqlitePostsRepository;

require_once __DIR__ . '/vendor/autoload.php';

// Создаём объект запроса из суперглобальных переменных
$request = new Request(
    $_GET,
    $_SERVER,
// Читаем поток, содержащий тело запроса
    file_get_contents('php://input'),
);

try {
    // Пытаемся получить путь из запроса
    $path = $request->path();
} catch (HttpException) {
    // Отправляем неудачный ответ,
    // если по какой-то причине
    // не можем получить путь
    try {
        (new ErrorResponse)->send();
    } catch (JsonException $e) {
    }
    // Выходим из программы
    return;
}

try {
    // Пытаемся получить HTTP-метод запроса
    $method = $request->method();
} catch (HttpException) {
    // Возвращаем неудачный ответ,
    // если по какой-то причине
    // не можем получить метод
    try {
        (new ErrorResponse)->send();
    } catch (JsonException $e) {
    }
    return;
}

$routes = [
    // Добавили ещё один уровень вложенности
    // для отделения маршрутов,
    // применяемых к запросам с разными методами
    'GET' => [
        '/users/show' => new FindByUsername(
            new SqliteUsersRepository()
        ),
        '/posts/show' => new FindById(
            new SqlitePostsRepository()
        ),
    ],
    'POST' => [
        // Добавили новый маршрут
        '/posts/create' => new CreatePost(
            new SqlitePostsRepository(),
            new SqliteUsersRepository()
        ),
    ],
];

// Если у нас нет маршрутов для метода запроса -
// возвращаем неуспешный ответ
if (!array_key_exists($method, $routes)) {
    try {
        (new ErrorResponse('Not found'))->send();
    } catch (JsonException $e) {
    }
    return;
}

// Ищем маршрут среди маршрутов для этого метода
if (!array_key_exists($path, $routes[$method])) {
    try {
        (new ErrorResponse('Not found'))->send();
    } catch (JsonException $e) {
    }
    return;
}

// Выбираем действие по методу и пути
$action = $routes[$method][$path];

try {
    // Пытаемся выполнить действие,
    // при этом результатом может быть
    // как успешный, так и неуспешный ответ
    $response = $action->handle($request);
    // Отправляем ответ
    try {
        $response->send();
    } catch (JsonException $e) {
    }
} catch (AppException $e) {
    // Отправляем неудачный ответ,
    // если что-то пошло не так
    try {
        (new ErrorResponse($e->getMessage()))->send();
    } catch (JsonException $e) {
    }
}