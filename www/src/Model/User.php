<?php

declare(strict_types=1);

namespace Project\Bookworm\Model;

use DateTime;

final class User
{
    private int $id;
    private string $email;
    private string $password;
    private ?string $username;
    private ?string $profile_picture;
    private DateTime $created_at;
    private DateTime $updated_at;

    public function __construct(
        int $id,
        string $email,
        string $password,
        ?string $username,
        ?string $profile_picture,
        DateTime $created_at,
        DateTime $updated_at
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->username = $username;
        $this->profile_picture = $profile_picture;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
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

    public function username(): string
    {
        return $this->username;
    }

    public function profile_picture(): string
    {
        return $this->profile_picture;
    }

    public function createdAt(): DateTime
    {
        return $this->created_at;
    }

    public function updatedAt(): DateTime
    {
        return $this->updated_at;
    }
}