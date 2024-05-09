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


    public function countRaiting($bookId);

    public function averageRating($bookId);
    public function countReviews($bookId);

    public function getBookReviews($bookId);

    public function deleteReviewById($userId, $bookId);

    public function countRatings($bookId);

    public function addReview($userId,$bookId,$reviewText);

    public function addRatingToBook($bookId, $rating);




}