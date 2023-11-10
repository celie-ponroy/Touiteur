<?php
declare(strict_types=1);

namespace iutnc\touiteur\bd;

/**
 * Class ConnectionFactory
 */
class ConnectionFactory
{
    private static $config;
    private static $pdo;

    /**
     * Méthode setConfig qui initialise la configuration
     * @param $file string fichier de configuration
     * @return void
     */
    public static function setConfig ($file) {
        self::$config = parse_ini_file($file);
    }

    /**
     * Méthode makeConnection qui crée la connexion à la base de données
     * @return \PDO
     */
    public static function makeConnection(){

        //on vérifie que la configuration est bien initialisée
        if(!isset(self::$pdo)) {
            //on récupère les paramètres de configuration
            $driver = self::$config['driver'];
            $username = self::$config['username'];
            $password = self::$config['password'];
            $host = self::$config['host'];
            $dataB = self::$config['database'];

         //on crée la connexion
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