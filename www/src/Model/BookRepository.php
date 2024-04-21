<?php

declare(strict_types=1);

namespace Project\Bookworm\Model;

use Project\Bookworm\Model\Book;

interface BookRepository
{
    public function createBook(Book $book): void;

    public function findBookByTitle(string $title): ?Book;
}