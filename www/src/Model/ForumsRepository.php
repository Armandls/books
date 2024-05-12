<?php

namespace Project\Bookworm\Model;

interface ForumsRepository
{
    public function createForum($data): bool;

    public function findForumByTitle(string $title): ?Forum;

    public function fetchAllForums();

    public function findForumByID($forumId);

    public function generateNewForum($data);

    public function deleteForum(mixed $forum_id);
}