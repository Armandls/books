<?php

namespace Project\Bookworm\Model;

use DateTime;

class Post
{
    private int $id;
    private int $user_id;
    private int $forum_id;
    private string $title;
    private string $contents;
    private DateTime $created_at;
    private DateTime $updated_at;

    public function __construct(
        int $id,
        int $user_id,
        int $forum_id,
        string $title,
        string $contents,
        DateTime $created_at,
        DateTime $updated_at
    )
    {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->forum_id = $forum_id;
        $this->title = $title;
        $this->contents = $contents;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updated_at;
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
    public function getCreatedAt(): DateTime
    {
        return $this->created_at;
    }

    /**
     * @return string
     */
    public function getContents(): string
    {
        return $this->contents;
    }

    /**
     * @return int
     */
    public function getForumId(): int
    {
        return $this->forum_id;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }
}