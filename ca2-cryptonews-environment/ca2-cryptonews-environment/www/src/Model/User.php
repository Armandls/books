<?php
declare(strict_types=1);

namespace Salle\LSCryptoNews\Model;

use DateTime;

class User {
    private int $id;
    private int $coins;
    private string $email;
    private string $password;
    private DateTime $creationDate;
    private DateTime $updateDate;

    public function __construct(
        string $email,
        string $password,
        int $coins,
        DateTime $creationDate,
        DateTime $updateDate
    ) {
        $this->email = $email;
        $this->password = $password;
        $this->coins = $coins;
        $this->creationDate = $creationDate;
        $this->updateDate = $updateDate;
    }

    public function getCoins(): int
    {
        return $this->coins;
    }

    public function setCoins(int $coins): void
    {
        $this->coins = $coins;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getCreationDate(): DateTime
    {
        return $this->creationDate;
    }

    public function getUpdateDate(): DateTime
    {
        return $this->updateDate;
    }
}