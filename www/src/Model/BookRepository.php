<?php

declare(strict_types=1);

namespace Project\Bookworm\Model;

use Project\Bookworm\Model\Book;

interface BookRepository
{
    public function createBook(Book $book): bool;

    public function findBookByTitle(string $title): ?Book;

    public function fetchAllBooks();

    public function generateBook(array $data): Book;

    public function findBookById($bookId);

    public function getAverageRating($bookId);

    public function getBookReviews($bookId);
}