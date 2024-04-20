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
        INSERT INTO users(email, password, username, profile_picture, created_at, updated_at) VALUES(?, ?, ?, ?, ?, ?)
QUERY;
        $statement = $this->database->connection()->prepare($query);

        $email = $user->email();
        $password = $user->password();
        $username = $user->username();
        $profile_picture = $user->profile_picture();
        $created_at = $user->createdAt()->format('Y-m-d H:i:s');
        $updated_at = $user->updatedAt()->format('Y-m-d H:i:s');


        $statement->bindParam(1, $email, PDO::PARAM_STR);
        $statement->bindParam(2, $password, PDO::PARAM_STR);
        $statement->bindParam(3, $username, PDO::PARAM_STR);
        $statement->bindParam(4, $profile_picture, PDO::PARAM_STR);
        $statement->bindParam(5, $created_at, PDO::PARAM_STR);
        $statement->bindParam(6, $updated_at, PDO::PARAM_STR);

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

        $created_at = new DateTime($userData['created_at']);
        $updated_at = new DateTime($userData['updated_at']);

        return new User(
            (int)$userData['id'],
            $userData['email'],
            $userData['password'],
            $userData['username'],
            $userData['profile_picture'],
            $created_at,
            $updated_at
        );
    }

}