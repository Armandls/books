<?php

namespace Salle\LSCryptoNews\Utils;

use Salle\LSCryptoNews\Model\UserRepository;

class LoginFormChecker
{

    public static function checkLoginForm ($data, $repository): array {
        $foundErrors = [];

        $emailError = self::checkEmail($data["email"]);
        if ($emailError !== null) {
            $foundErrors["email"] = $emailError;
        }

        if (empty($foundErrors["email"])) {
            $validationError = self::checkCorrectUser($data["email"], $data["password"], $repository);
            if ($validationError !== null) {
                $foundErrors["email"] = $validationError;
            }
        }

        $passwordError = self::checkPassword($data["password"]);
        if ($passwordError !== null) {
            $foundErrors["password"] = $passwordError;
        }

        if (empty($foundErrors["password"])) {
            $validationError = self::checkCorrectUser($data["email"], $data["password"], $repository);
            if ($validationError !== null) {
                $foundErrors["password"] = $validationError;
            }
        }


        return $foundErrors;
    }

    private static function checkEmail($email): ?string
    {
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "The email address is not valid.";
        }

        if (!str_ends_with($email, '@salle.url.edu')) {
            return "Only emails from the domain @salle.url.edu are accepted.";
        }

        return null;
    }

    private static function checkPassword($password): ?string
    {
        if (empty($password) || strlen($password) < 7) {
            return "The password must contain at least 7 characters.";
        } else {
            $regex = '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{7,}$/';
            if (!preg_match($regex, $password)) {
                return "The password must contain both upper and lower case letters and numbers.";
            }
        }

        return null;
    }

    private static function checkCorrectUser($email, $password, $repository)
    {
        $errorEmail = $repository->emailValidation($email);
        if ($errorEmail) {
            return "User with this email address does not exist.";
        }

        $errorPassword = $repository->userValidation($email, $password);
        if ($errorPassword) {
            return "Your email and/or password are incorrect.";
        }

        return null;
    }

    public static function getUser($formData, $repository)
    {
        return $repository->getUser($formData["email"]);
    }
}
