<?php

namespace Project\Bookworm\Utils;

use Project\Bookworm\Model\BookRepository;

class BookCreationChecker
{
    private const MEDIUM_TEXT_LENGTH = 16777215;

    private const TEXT_LENGTH = 65535;

    public static function checkCorrectForm(array $data, BookRepository $repository, $errors): array
    {

        $authorError = self::checkAuthor($data['author']);
        if ($authorError != null) {
            $errors["author"] = $authorError;
        }
        else {
            $pageNumberError = self::checkPageNumber($data['page_number']);
            if ($pageNumberError != null) {
                $errors["page_number"] = $pageNumberError;
            }
            else {
                $titleError = self::checkTitle($data['title'], $repository);
                if ($titleError != null) {
                    $errors["title"] = $titleError;
                }
                else {
                    $descriptionError = self::checkDescription($data['description']);
                    if ($descriptionError != null) {
                        $errors["description"] = $descriptionError;
                    }
                }
            }
        }
        return $errors;
    }

    private static function checkAuthor(string $author): ?string
    {
        // Check if the author is empty
        if (empty($author)) {
            return "The author's field cannot be empty.";
        }

        if (!preg_match('/^[a-zA-Z\s]+$/', $author)) {
            return "The author's name should contain only letters and spaces.";
        }

        if (strlen($author) > self::MEDIUM_TEXT_LENGTH) {
            return "The author's name should contain less than 16777215 characters.";
        }

        return null;
    }


    private static function checkTitle(string $title, BookRepository $bookRepository): ?string
    {
        // Check if the $title is empty
        if (empty($title)) {
            return "The title's field cannot be empty.";
        }

        // Check if the title contains only letters and spaces
        if (!preg_match('/^[a-zA-Z\s]+$/', $title)) {
            return "The title should contain only letters and spaces.";
        }

        if (strlen($title) > self::MEDIUM_TEXT_LENGTH) {
            return "The title should contain less than 16777215 characters.";
        }

        $book = $bookRepository->findBookByTitle($title);
        if ($book !== null) {
            return "The title of this book is already in use!";
        }

        return null;
    }


    private static function checkPageNumber(string $pageNumber): ?string
    {
        // Check if the page number is numeric
        if (!is_numeric($pageNumber)) {
            return "Page number must be numeric.";
        }

        // Check if the page number is less than 1
        if ($pageNumber < 1) {
            return "Page number cannot be less than 1.";
        }

        return null;
    }


    private static function checkDescription(string $description): ?string
    {
        // Check if the description is empty
        if (empty($description)) {
            return "The description cannot be empty.";
        }

        if (strlen($description) > self::TEXT_LENGTH) {
            return "The description should contain less than 65535 characters.";
        }

        return null;
    }
}