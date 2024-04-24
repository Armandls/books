<?php

namespace Project\Bookworm\Model;

use DateTime;

final class Book
{
    private int $id;
   private string $title;
   private string $author;
   private string $description;
   private int $page_number;
   private string $cover_image;
   private DateTime $created_at;
   private DateTime $updated_at;

    public function __construct(
        int $id,
        string $title,
        string $author,
        string $description,
        int $num_pages,
        string $image_url,
        DateTime $created_at,
        DateTime $updated_at
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->author = $author;
        $this->description = $description;
        $this->page_number = $num_pages;
        $this->cover_image = $image_url;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    /**
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->created_at;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCoverImage(): string
    {
        return $this->cover_image;
    }

    /**
     * @return int
     */
    public function getPagenumber(): int
    {
        return $this->page_number;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updated_at;
    }

}