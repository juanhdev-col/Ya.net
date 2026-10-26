<?php

namespace Config;

use PDO;
use PDOException;

class Database 
{
    private static $instance = null;

    public static function getConnection() 
    {
        if (self::$instance === null) {
            $host = '127.0.0.1';
            $port = '3306';
            $db   = 'yaconecta_db';  // <-- Nombre exacto de tu base de datos
            $user = 'root';
            $pass = '';
            $charset = 'utf8mb4';

            // Incluimos port=$port en el DSN
            $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                die("Error de conexión a la base de datos: " . $e->getMessage());
            }
        }

        return self::$instance;
    }
}
