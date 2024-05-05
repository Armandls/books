<?php

namespace Project\Bookworm\Model;

class Forum
{

    private int $id;
    private string $title;
    private string $description;
    private DateTime $created_at;
    private DateTime $updated_at;

    public function __construct($id, $title, $description, $created_at, $updated_at)
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $this->description;
        $this->created_at = $this->created_at;
        $this->updated_at = $this->updated_at;
    }
}
