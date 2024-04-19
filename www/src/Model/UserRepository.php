<?php

declare(strict_types=1);

namespace Project\Bookworm\Model;

use Project\Bookworm\Model\User;

interface UserRepository
{
    public function save(User $user): void;

    public function findByEmail(string $email): ?User;
}