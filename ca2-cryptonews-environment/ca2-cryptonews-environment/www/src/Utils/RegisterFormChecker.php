<?php

namespace Salle\LSCryptoNews\Utils;

use Salle\LSCryptoNews\Model\UserRepository;

class RegisterFormChecker
{
    public static function checkCorrectForm($data, UserRepository $repository): array
    {
        $foundErrors = [];

        $emailError = self::checkEmail($data, $repository);
        if ($emailError !== null) {
            $foundErrors["email"] = $emailError;
        }

        $passwordError = self::checkPassword($data["password"]);
        if ($passwordError !== null) {
            $foundErrors["password"] = $passwordError;
        }

        $passwordError = self::checkPassword($data["repeatPassword"]);
        if ($passwordError !== null) {
            $foundErrors["repeatPassword"] = $passwordError;
        }

        $passwordConfirmError = self::checkEqualPasswords($data);
        if ($passwordConfirmError !== null) {
            $foundErrors["repeatPassword"] = $passwordConfirmError;
        }

        $bitcoinsError = self::checkBitcoins($data);
        if ($bitcoinsError !== null) {
            $foundErrors["numBitcoins"] = $bitcoinsError;
        }

        return $foundErrors;
    }

    private static function checkEmail($data, UserRepository $repository): ?string
    {
        if (empty($data["email"]) || !filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
            return "The email address is not valid.";
        }

        if (!str_ends_with($data["email"], '@salle.url.edu')) {
            return "Only emails from the domain @salle.url.edu are accepted.";
        }

        if ($repository->userRegistered($data["email"])) {
            return "The email address is already registered.";
        }

        return null;
    }

    private static function checkPassword($password): ?string
    {
        if (empty($password)) {
            return "The password must contain at least 7 characters.";
        }

        if (strlen($password) < 7) {
            return "The password must contain at least 7 characters.";
        }

        $regex = '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{7,}$/';
        if (!preg_match($regex, $password)) {
            return "The password must contain both upper and lower case letters and numbers.";
        }

        return null;
    }

    private static function checkEqualPasswords($data): ?string
    {
        if ($data["password"] !== $data["repeatPassword"]) {
            return "Passwords do not match.";
        }
        return null;
    }

    private static function checkBitcoins($data): ?string
    {
        // Check if the field is empty
        if (empty($data["numBitcoins"])) {
            // If it's empty, return null indicating no error
            return null;
        }

        // Validation regex pattern
        $regex = '/^-?[0-9]+$/';

        // Check if the input matches the regex pattern
        if (!preg_match($regex, $data["numBitcoins"])) {
            return "The number of Bitcoins is not a valid number.";
        }

        // Check if the number of Bitcoins is within the allowed range
        if ($data["numBitcoins"] > 40000 || $data["numBitcoins"] < 0) {
            return "Sorry, the number of Bitcoins is either below or above the limits.";
        }

        // If all validations pass, return null indicating no error
        return null;
    }

}
