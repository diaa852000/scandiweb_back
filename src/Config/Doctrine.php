<?php

namespace App\Config;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

class Doctrine
{
    public static function createEntityManager(): EntityManager
    {
        $config = ORMSetup::createAttributeMetadataConfiguration(
            paths: [__DIR__ . '/../Entities'],
            isDevMode: true
        );

        $connection = DriverManager::getConnection([
            'dbname'   => 'ecommerce_db',
            'user'     => 'ecommerce_user',
            'password' => 'userpassword',
            'host'     => 'localhost', //local
            // 'host'     => '136.112.103.156', live
            'driver'   => 'pdo_mysql',
            'charset'  => 'utf8mb4',
            'port'     => 3306
        ], $config);

        return new EntityManager($connection, $config);
    }
}
