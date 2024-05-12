<?php

namespace Project\Bookworm\Model;

interface PostRepository
{
    public function getForumPosts($forum_id);

    public function createPost(Post $post);

    public function generateNewPost(array $data, int $forum_id, int $user_id);
}