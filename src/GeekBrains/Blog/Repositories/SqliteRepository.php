<?php

namespace GeekBrains\Blog\Repositories;

use PDO;
use GeekBrains\Blog\config\SqliteConfig;

class SqliteRepository implements RepositoryInterface
{
    protected PDO $connection;

    public function __construct() 
    {
        $this->connection = new PDO(SqliteConfig::DSN);
    }    
}