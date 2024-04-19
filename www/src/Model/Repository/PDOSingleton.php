<?php

declare(strict_types=1);

namespace Project\Bookworm\Model\Repository; //Take care if you added the Repository folder inside Model folder or outside that

use PDO;

final class PDOSingleton
{
    private const CONNECTION_STRING = 'mysql:host=%s;port=%s;dbname=%s';

    //Referncia a la instancia de la clase porque es un singleton y solo puede haber una instancia
    private static ?PDOSingleton $instance = null;

    private PDO $connection;

    private function __construct(
        string $username,
        string $password,
        string $host,
        string $port,
        string $database
    ) {
        $db = new PDO(
            sprintf(self::CONNECTION_STRING, $host, $port, $database),
            $username,
            $password
        );

        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->connection = $db;
    }

    public static function getInstance(
        string $username,
        string $password,
        string $host,
        string $port,
        string $database
    ): PDOSingleton {
        if (self::$instance === null) {
            self::$instance = new self(
                $username,
                $password,
                $host,
                $port,
                $database
            );
        }

        return self::$instance;
    }

    public function connection(): PDO
    {
        return $this->connection;
    }
}