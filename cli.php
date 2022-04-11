<?php

require_once __DIR__ . '/vendor/autoload.php';

use GeekBrains\Blog\Repositories\RepositoryFactory;
use GeekBrains\Blog\Commands\CommandFactory;
use GeekBrains\Blog\Commands\Arguments;

try {
    $repositoryFactory = new RepositoryFactory();
    $repository = $repositoryFactory->create($argv[1]);
    $commandFactory = new CommandFactory();
    $command = $commandFactory->create($argv[1], $repository);
    $command->handle(Arguments::fromArgv($argv));
} catch (\Throwable $th) {
    echo "{$th->getMessage()}\n";
}