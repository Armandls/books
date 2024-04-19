<?php

declare(strict_types=1);

namespace Project\Bookworm\Model;

use DateTime;

final class User
{
    private int $id;
    private string $email;
    private string $password;
    private int $numBitcoins;
    private DateTime $createdAt;
    private DateTime $updatedAt;

    public function __construct(
        int $id,
        string $email,
        string $password,
        int $numBitcoins,
        DateTime $createdAt,
        DateTime $updatedAt
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->numBitcoins = $numBitcoins;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function createdAt(): DateTime
    {
        return $this->createdAt;
    }

    public function updatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function numBitcoins(): int
    {
        return $this->numBitcoins;
    }
}