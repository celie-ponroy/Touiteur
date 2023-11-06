<?php
declare(strict_types=1);

namespace iutnc\touiteur\bd;


class ConnectionFactory
{
    private static $config;
    private static $pdo;


    public static function setConfig ($file) {
        self::$config = parse_ini_file($file);
    }

    public static function makeConnection(){

        if(!isset(self::$pdo)) {
            $driver = self::$config['driver'];
            $username = self::$config['username'];
            $password = self::$config['password'];
            $host = self::$config['host'];
            $dataB = self::$config['database'];


        $dsn = "$driver:host=$host;dbname=$dataB;";
            $db = new \PDO($dsn, $username, $password, [
                \PDO::ATTR_PERSISTENT => true,
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_EMULATE_PREPARES => false,
                \PDO::ATTR_STRINGIFY_FETCHES => false,
            ]);
            self::$pdo = $db;
        }
        return self::$pdo;
    }
}