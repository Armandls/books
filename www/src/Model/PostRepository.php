<?php

namespace Project\Bookworm\Model;

interface PostRepository
{
    public function getForumPosts($forum_id);

    public function createPost(Post $post);
}