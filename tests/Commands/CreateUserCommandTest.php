<?php

namespace GeekBrains\Blog\UnitTests\Commands;

use GeekBrains\Blog\Commands\Arguments;
use GeekBrains\Blog\Exceptions\CommandException;
use GeekBrains\Blog\Exceptions\UserNotFoundException;
use GeekBrains\Blog\Exceptions\ArgumentsException;
use GeekBrains\Blog\Commands\CreateUserCommand;
use GeekBrains\Blog\Repositories\UsersRepositoryInterface;
use GeekBrains\Blog\Repositories\DummyUsersRepository;
use GeekBrains\Blog\User;
use PHPUnit\Framework\TestCase;

class CreateUserCommandTest extends TestCase
{
    private function getAnonymUsersRepository(): UsersRepositoryInterface {
        return new class implements UsersRepositoryInterface 
        {
            public function save(User $user): void {}

            public function get(int $id): User
            {
                throw new UserNotFoundException("User not found");
            }

            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException("User not found");
            }
        };
    }

    // Проверяем, что команда создания пользователя бросает исключение,
    // если пользователь с таким именем уже существует
    public function testItThrowsAnExceptionWhenUserAlreadyExists(): void
    {
        // Создаём объект команды
        // У команды одна зависимость - UsersRepositoryInterface
        $command = new CreateUserCommand(
            // Передаём наш стаб в качестве реализации UsersRepositoryInterface
            new DummyUsersRepository()
        );

        // Описываем тип ожидаемого исключения
        $this->expectException(CommandException::class);
        // и его сообщение
        $this->expectExceptionMessage('User already exists: Ivan');

        // Запускаем команду с аргументами
        $command->handle(new Arguments(['username' => 'Ivan']));
    }

    // Тест проверяет, что команда действительно требует имя пользователя

    /**
     * @throws CommandException
     */
    public function testItRequiresFirstName(): void
    {
        // $usersRepository - это объект анонимного класса,
        // реализующего контракт UsersRepositoryInterface
        $usersRepository = $this->getAnonymUsersRepository();

        // Передаём объект анонимного класса
        // в качестве реализации UsersRepositoryInterface
        $command = new CreateUserCommand($usersRepository);

        // Ожидаем, что будет брошено исключение
        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: first_name');

        // Запускаем команду
        $command->handle(new Arguments(['username' => 'Ivan']));
    }

    // Тест проверяет, что команда действительно требует фамилию пользователя

    /**
     * @throws CommandException
     */
    public function testItRequiresLastName(): void
    {
        // Передаём в конструктор команды объект, возвращаемый нашей функцией
        $command = new CreateUserCommand($this->getAnonymUsersRepository());
        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: last_name');
        $command->handle(new Arguments([
            'username' => 'Ivan',
            // Нам нужно передать имя пользователя,
            // чтобы дойти до проверки наличия фамилии
            'first_name' => 'Ivan',
        ]));
    }

    // Тест, проверяющий, что команда сохраняет пользователя в репозитории

    /**
     * @throws CommandException
     */
    public function testItSavesUserToRepository(): void
    {
        // Создаём объект анонимного класса
        $usersRepository = new class implements UsersRepositoryInterface {
            // В этом свойстве мы храним информацию о том,
            // был ли вызван метод save
            private bool $called = false;

            public function save(User $user): void
            {
                // Запоминаем, что метод save был вызван
                $this->called = true;
            }

            public function get(int $id): User
            {
                throw new UserNotFoundException("User not found");
            }

            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException("User not found");
            }

            // Этого метода нет в контракте UsersRepositoryInterface,
            // но ничто не мешает его добавить.
            // С помощью этого метода мы можем узнать,
            // был ли вызван метод save
            public function wasCalled(): bool
            {
                return $this->called;
            }
        };

        // Передаём наш мок в команду
        $command = new CreateUserCommand($usersRepository);

        // Запускаем команду
        $command->handle(new Arguments([
            'username' => 'Ivan',
            'first_name' => 'Ivan',
            'last_name' => 'Nikitin',
        ]));

        // Проверяем утверждение относительно мока,
        // а не утверждение относительно команды
        $this->assertTrue($usersRepository->wasCalled());
    }
}