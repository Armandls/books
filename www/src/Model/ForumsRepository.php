<?php

namespace Project\Bookworm\Model;

interface ForumsRepository
{
    public function createForum(Forum $forum): bool;

    public function findForumByTitle(string $title): ?Forum;

    public function fetchAllForums();

    public function findForumByID($bookId);

    public function generateNewForum(array $data);
}