<?php
declare(strict_types=1);

namespace Salle\LSCryptoNews\Model;

use DateTime;
use PDO;

final class UserSQL implements UserRepository
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';
    private PDOSingleton $database;

    public function __construct(PDOSingleton $database)
    {
        $this->database = $database;
    }

    public function registerNewUser($email, $password, $numBitcoins): void
    {
        $hashedPassword = $this->encodePassword($password);
        //$hashedPassword = $password;
        $date = date("Y-m-d H:i:s");

        $query = "INSERT INTO users (email, password, numBitcoins, createdAt, updatedAt) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->database->connection()->prepare($query);
        $stmt->execute([$email, $hashedPassword, $numBitcoins, $date, $date]);
    }

    public function userRegistered($email): bool
    {
        $query = "SELECT email FROM users WHERE email = ?";
        $stmt = $this->database->connection()->prepare($query);
        $stmt->execute([$email]);

        return !empty($stmt->fetch());
    }

    public function userValidation($email, $password): bool
    {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->database->connection()->prepare($sql);
        $stmt->execute([$email]);

        $credentials = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($credentials && password_verify($password, $credentials['password'])) {
            return false; // No Error
        }

        return true; // error
    }

    public function emailValidation($email): bool
    {
        $query = "SELECT email FROM users WHERE email = ?";
        $stmt = $this->database->connection()->prepare($query);
        $stmt->execute([$email]);

        return empty($stmt->fetch());
    }

    private function encodePassword($password)
    {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        return $hashed_password;
    }

    public function getUser($email): ?User
    {
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->database->connection()->prepare($query);
        $stmt->execute([$email]);

        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$userData) {
            return null; // User not found
        }

        return new User(
            $userData['email'],
            $userData['password'],
            $userData['numBitcoins'],
            new DateTime($userData['createdAt']),
            new DateTime($userData['updatedAt'])
        );
    }

}
