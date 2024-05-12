<?php

namespace Project\Bookworm\Model\Repository;

use DateTime;
use PDO;
use Project\Bookworm\Model\Forum;
use Project\Bookworm\Model\Post;
use Project\Bookworm\Model\PostRepository;

class MySQLPostRepository implements PostRepository
{
    private PDOSingleton $database;

    public function __construct(PDOSingleton $database)
    {
        $this->database = $database;
    }

    public function getForumPosts($forum_id)
    {
        $query = <<<'QUERY'
        SELECT * FROM posts WHERE forum_id = ?
        QUERY;

        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam(1, $forum_id, PDO::PARAM_INT);
        $statement->execute();

        $forumData = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (!$forumData) {
            return null; // No se encontró ningún foro con ese ID
        }

        $posts = [];

        foreach ($forumData as $data) {
            $post = new Post(
                $data['id'],
                $data['user_id'],
                $data['forum_id'],
                $data['title'],
                $data['contents'],
                new DateTime($data['created_at']),
                new DateTime($data['updated_at'])
            );

            $posts[] = $post;
        }

        return $posts;

    }

    public function createPost(Post $post): bool
    {
        $query = <<<'QUERY'
        INSERT INTO posts (user_id, forum_id, title, contents, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)
QUERY;
        try {
            $statement = $this->database->connection()->prepare($query);

            $user_id = $post->getUserId();
            $forum_id = $post->getForumId();
            $title = $post->getTitle();
            $contents = $post->getContents();
            $created_at = $post->getCreatedAt()->format('Y-m-d H:i:s');
            $updated_at = $post->getUpdatedAt()->format('Y-m-d H:i:s');


            $statement->bindParam(1, $user_id, PDO::PARAM_STR);
            $statement->bindParam(2, $forum_id, PDO::PARAM_STR);
            $statement->bindParam(3, $title, PDO::PARAM_STR);
            $statement->bindParam(4, $contents, PDO::PARAM_STR);
            $statement->bindParam(5, $created_at, PDO::PARAM_STR);
            $statement->bindParam(6, $updated_at, PDO::PARAM_STR);

            $result = $statement->execute();

            return $result;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function generateNewPost(array $data, int $forum_id, int $user_id)
    {
        return new Post(0,
            $user_id,
            $forum_id,
            $data['title'],
            $data['description'],
            new DateTime(),
            new DateTime()
        );
    }
}