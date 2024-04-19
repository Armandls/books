<?php

declare(strict_types=1);

namespace Project\Bookworm\Model\Repository;

use DateTime;
use PDO;
use Project\Bookworm\Model\User;
use Project\Bookworm\Model\UserRepository;


final class MySQLUserRepository implements UserRepository
{

    private PDOSingleton $database;

    public function __construct(PDOSingleton $database)
    {
        $this->database = $database;
    }

    public function save(User $user): void
    {
        $query = <<<'QUERY'
        INSERT INTO users(email, password, numBitcoins, createdAt, updatedAt) VALUES(?, ?, ?, ?, ?)
QUERY;
        $statement = $this->database->connection()->prepare($query);

        $email = $user->email();
        $password = $user->password();
        $numBitcoins = $user->numBitcoins();
        $createdAt = $user->createdAt()->format('Y-m-d H:i:s');
        $updatedAt = $user->updatedAt()->format('Y-m-d H:i:s');


        $statement->bindParam(1, $email, PDO::PARAM_STR);
        $statement->bindParam(2, $password, PDO::PARAM_STR);
        $statement->bindParam(3, $numBitcoins, PDO::PARAM_INT);
        $statement->bindParam(4, $createdAt, PDO::PARAM_STR);
        $statement->bindParam(5, $updatedAt, PDO::PARAM_STR);

        $statement->execute();
    }

    public function findByEmail(string $email): ?User
    {
        $query = <<<'QUERY'
        SELECT * FROM users WHERE email = ?
    QUERY;

        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam(1, $email, PDO::PARAM_STR);
        $statement->execute();

        $userData = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$userData) {
            return null; // No se encontró ningún usuario con ese correo electrónico
        }

        $createdAt = new DateTime($userData['createdAt']);
        $updatedAt = new DateTime($userData['updatedAt']);

        return new User(
            (int)$userData['id'],
            $userData['email'],
            $userData['password'],
            (int)$userData['numBitcoins'],
            $createdAt,
            $updatedAt
        );
    }

}