<?php

namespace Project\Bookworm\Model;

interface ForumsRepository
{
    public function createForum(array $data): bool;

    public function findForumByTitle(string $title): ?Forum;

    public function fetchAllForums();

    public function findForumByID($forumId);

    public function generateNewForum($data);
}