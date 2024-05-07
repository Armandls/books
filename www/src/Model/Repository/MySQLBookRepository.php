<?php

declare(strict_types=1);

namespace Project\Bookworm\Model\Repository;

use DateTime;
use PDO;
use PDOException;
use Project\Bookworm\Model\Book;
use Project\Bookworm\Model\BookRepository;


final class MySQLBookRepository implements BookRepository
{

    private PDOSingleton $database;

    public function __construct(PDOSingleton $database)
    {
        $this->database = $database;
    }

    public function createBook(Book $book): bool
    {
        $query = <<<'QUERY'
        INSERT INTO books(title, author, description, page_number, cover_image, created_at, updated_at) VALUES(?, ?, ?, ?, ?, ?, ?)
QUERY;
        try {
            $statement = $this->database->connection()->prepare($query);

            $title = $book->getTitle();
            $author = $book->getAuthor();
            $description = $book->getDescription();
            $page_number = $book->getPagenumber();
            $cover_image = $book->getCoverImage();
            $created_at = $book->getCreatedAt()->format('Y-m-d H:i:s');
            $updated_at = $book->getUpdatedAt()->format('Y-m-d H:i:s');


            $statement->bindParam(1, $title, PDO::PARAM_STR);
            $statement->bindParam(2, $author, PDO::PARAM_STR);
            $statement->bindParam(3, $description, PDO::PARAM_STR);
            $statement->bindParam(4, $page_number, PDO::PARAM_STR);
            $statement->bindParam(5, $cover_image, PDO::PARAM_STR);
            $statement->bindParam(6, $created_at, PDO::PARAM_STR);
            $statement->bindParam(7, $updated_at, PDO::PARAM_STR);

            $result = $statement->execute();
        return $result; // Return the result of the execution (true if successful, false otherwise)
        } catch (PDOException $e) {
            return false; // Return false if an exception occurs
        }
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

    public function fetchAllBooks()
    {
        $query = <<<'QUERY'
    SELECT * FROM books
QUERY;

        $statement = $this->database->connection()->prepare($query);
        $statement->execute();

        $books = [];

        while ($bookData = $statement->fetch(PDO::FETCH_ASSOC)) {
            // Acortar la descripción a 100 caracteres
            $truncatedDescription = substr($bookData['description'], 0, 100);
            $truncatedDescription = $truncatedDescription . "...";
            // Crear el objeto Book con la descripción truncada
            $book = new Book(
                $bookData['id'],
                $bookData['title'],
                $bookData['author'],
                $truncatedDescription,
                $bookData['page_number'],
                $bookData['cover_image'],
                new DateTime($bookData['created_at']),
                new DateTime($bookData['updated_at'])
            );

            $books[] = $book;
        }

        return $books;
    }



    public function generateBook(array $data): Book
    {
        return new Book(0,
            $data['title'],
            $data['author'],
            $data['description'],
            (int)$data['page_number'],
            $data['cover_image'],
            new DateTime(),
            new DateTime()
        );
    }

    public function findBookById($bookId)
    {
        $query = <<<'QUERY'
        SELECT * FROM books WHERE id = ?
    QUERY;

        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam(1, $bookId, PDO::PARAM_STR);
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

    public function getAverageRating($bookId)
    {
        // TODO: Implement getAverageRating() method.
        return 0;
    }


    public function countRaiting($bookId): int
    {
        $query = <<<'QUERY'
            SELECT COUNT(*) FROM ratings WHERE book_id = ?
        QUERY;

        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam(1, $bookId, PDO::PARAM_INT);
        $statement->execute();

        return (int)$statement->fetchColumn();
    }

    // Implementación del método averageRating
    public function averageRating($bookId): float
    {
        $query = <<<'QUERY'
            SELECT AVG(rating) FROM ratings WHERE book_id = ?
        QUERY;

        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam(1, $bookId, PDO::PARAM_INT);
        $statement->execute();

        return (float)$statement->fetchColumn();
    }

    public function countReviews($bookId): int
    {
        $query = <<<'QUERY'
            SELECT COUNT(*) FROM reviews WHERE book_id = ?
        QUERY;

        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam(1, $bookId, PDO::PARAM_INT);
        $statement->execute();

        return (int)$statement->fetchColumn();
    }

    public function getBookReviews($bookId): array
    {
        $query = <<<'QUERY'
        SELECT reviews.review_text, users.username FROM reviews JOIN users ON reviews.user_id = users.id WHERE reviews.book_id = ?
    QUERY;

        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam(1, $bookId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}