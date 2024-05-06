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
    private User $user;

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

    public function setUser(User $user) {
        $this->user = $user;
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

    public function getUpdatedAtToString(): string
    {
        return $this->updated_at->format('Y-m-d H:i:s');
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

    public function getUserImage(): string
    {
        return $this->user->profile_picture();
    }
    public function getUserName(): string
    {
        return $this->user->username();
    }
}