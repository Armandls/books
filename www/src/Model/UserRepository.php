<?php

declare(strict_types=1);

namespace Project\Bookworm\Model;

use Project\Bookworm\Model\User;

interface UserRepository
{
    public function save(User $user): void;

    public function findByEmail(string $email): ?User;

    public function findByUsername(string $username): ?User;

    public function updateProfilePicture(string $email, string $string): void;

    public function updateUsername(string $email, string $username): void;

    public function findById(int $user_id): ?User;
}