<?php

namespace Salle\LSCryptoNews\Model;

interface UserRepository
{
    public function registerNewUser($email, $password, $numBitcoins): void;
    public function userRegistered($email):bool;
    public function userValidation($email, $password):bool;
    public function emailValidation($email):bool;
    public function getUser($email): ?User;
}