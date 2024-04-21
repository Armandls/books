<?php

namespace Project\Bookworm\Model;

use DateTime;

final class Book
{
    private int $id;
   private string $title;
   private string $author;
   private string $description;
   private int $num_pages;
   private string $image_url;
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
        $this->num_pages = $num_pages;
        $this->image_url = $image_url;
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
    public function getImageUrl(): string
    {
        return $this->image_url;
    }

    /**
     * @return int
     */
    public function getNumPages(): int
    {
        return $this->num_pages;
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