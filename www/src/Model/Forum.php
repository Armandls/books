<?php

namespace Project\Bookworm\Model;

use DateTime;

class Forum
{

    private int $id;
    private string $title;
    private string $description;
    private DateTime $created_at;
    private DateTime $updated_at;

    public function __construct(
        int $id,
        string $title,
        string $description,
        DateTime $created_at,
        DateTime $updated_at)
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
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
    public function getTitle()
    {
        return $this->title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function getUpdatedAt()
    {
        return $this->updated_at;
    }
}
