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
            // Condición para verificar si la descripción es mayor a 100 caracteres

            if (strlen($bookData['description']) > 100) {
                $truncatedDescription = substr($bookData['description'], 0, 100) . "...";
            } else {
                // Si la descripción es menor o igual a 100 caracteres, no se trunca
                $truncatedDescription = $bookData['description'];
            }

            // Crear el objeto Book con la descripción (truncada o no truncada)
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

    public function countRatings($bookId): int
    {
        $query = <<<'QUERY'
            SELECT COUNT(*) FROM ratings WHERE book_id = ?
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

    public function deleteReviewById($userId, $bookId): void
    {

        $query = <<<'QUERY'
        DELETE FROM reviews
        WHERE book_id = :book_id AND user_id = :user_id
        QUERY;

        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam(':book_id', $bookId, PDO::PARAM_INT);
        $statement->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $statement->execute();
    }

    public function deleteRatingById($userId, $bookId): void
    {

        $query = <<<'QUERY'
        DELETE FROM ratings
        WHERE book_id = :book_id AND user_id = :user_id
        QUERY;

        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam(':book_id', $bookId, PDO::PARAM_INT);
        $statement->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $statement->execute();
    }

    public function addReview($userId, $bookId, $reviewText): void
    {
        $query = <<<'QUERY'
        INSERT INTO reviews (user_id, book_id, review_text, created_at, updated_at)
        VALUES (:user_id, :book_id, :review_text, NOW(), NOW())
    QUERY;

        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $statement->bindParam(':book_id', $bookId, PDO::PARAM_INT);
        $statement->bindParam(':review_text', $reviewText, PDO::PARAM_STR);
        $statement->execute();
    }

    public function addRatingToBook($user_id, $bookId, $rating): void
    {

        $existingRating = $this->getRatingForBook($bookId);
        $bookId = (int)$bookId; // Convertir a entero

        $statement = $this->database->connection()->prepare("INSERT INTO ratings (user_id, book_id, rating) VALUES (:user_id, :book_id, :rating)");
        $statement->bindParam(':user_id', $user_id, PDO::PARAM_INT); // Corregido a $user_id
        $statement->bindParam(':book_id', $bookId, PDO::PARAM_INT); // Corregido a $bookId
        $statement->bindParam(':rating', $rating, PDO::PARAM_INT);
        $statement->execute();
    }





    private function getRatingForBook($bookId): ?int
    {

        $statement = $this->database->connection()->prepare("SELECT rating FROM ratings WHERE book_id = :bookId");
        $statement->execute(['bookId' => $bookId]);
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        return $result ? (int)$result['rating'] : null;
    }


}