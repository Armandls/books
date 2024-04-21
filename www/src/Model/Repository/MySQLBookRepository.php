<?php

declare(strict_types=1);

namespace Project\Bookworm\Model\Repository;

use DateTime;
use PDO;
use Project\Bookworm\Model\Book;
use Project\Bookworm\Model\BookRepository;


final class MySQLBookRepository implements BookRepository
{

    private PDOSingleton $database;

    public function __construct(PDOSingleton $database)
    {
        $this->database = $database;
    }

    public function createBook(Book $book): void
    {
        $query = <<<'QUERY'
        INSERT INTO books(title, author, description, page_number, cover_image, created_at, updated_at) VALUES(?, ?, ?, ?, ?, ?, ?)
QUERY;
        $statement = $this->database->connection()->prepare($query);

        $title = $book->getTitle();
        $author = $book->getAuthor();
        $description = $book->getDescription();
        $page_number = $book->getNumPages();
        $cover_image = $book->getImageUrl();
        $created_at = $book->getCreatedAt()->format('Y-m-d H:i:s');
        $updated_at = $book->getUpdatedAt()->format('Y-m-d H:i:s');


        $statement->bindParam(1, $title, PDO::PARAM_STR);
        $statement->bindParam(2, $author, PDO::PARAM_STR);
        $statement->bindParam(3, $description, PDO::PARAM_STR);
        $statement->bindParam(4, $page_number, PDO::PARAM_STR);
        $statement->bindParam(5, $cover_image, PDO::PARAM_STR);
        $statement->bindParam(6, $created_at, PDO::PARAM_STR);
        $statement->bindParam(7, $updated_at, PDO::PARAM_STR);

        $statement->execute();
    }

    public function findBookByTitle(string $title): ?Book
    {
        $query = <<<'QUERY'
        SELECT * FROM books WHERE title = ?
    QUERY;

        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam(1, $title, PDO::PARAM_STR);
        $statement->execute();

        $bookData = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$bookData) {
            return null; // No se encontró ningún usuario con ese correo electrónico
        }

        $created_at = new DateTime($bookData['created_at']);
        $updated_at = new DateTime($bookData['updated_at']);

        return new Book(
            (int)$bookData['id'],
            $bookData['title'],
            $bookData['author'],
            $bookData['description'],
            $bookData['page_number'],
            $bookData['cover_image'],
            $created_at,
            $updated_at
        );
    }

}