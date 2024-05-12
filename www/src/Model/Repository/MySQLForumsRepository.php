<?php

namespace Project\Bookworm\Model\Repository;

use DateTime;
use PDO;
use PDOException;
use Project\Bookworm\Model\Book;
use Project\Bookworm\Model\Forum;
use Project\Bookworm\Model\ForumsRepository;

class MySQLForumsRepository implements ForumsRepository
{
    private PDOSingleton $database;

    public function __construct(PDOSingleton $database)
    {
        $this->database = $database;
    }

    public function createForum($data): bool
    {
        $forum = new Forum(0,
            $data['title'],
            $data['description'],
            new DateTime(),
            new DateTime());

        $query = <<<'QUERY'
        INSERT INTO forums (title, description, created_at, updated_at) VALUES (?, ?, ?, ?)
QUERY;
        try {
            $statement = $this->database->connection()->prepare($query);

            $title = $forum->getTitle();
            $description = $forum->getDescription();
            $created_at = $forum->getCreatedAt()->format('Y-m-d H:i:s');
            $updated_at = $forum->getUpdatedAt()->format('Y-m-d H:i:s');


            $statement->bindParam(1, $title, PDO::PARAM_STR);
            $statement->bindParam(2, $description, PDO::PARAM_STR);
            $statement->bindParam(3, $created_at, PDO::PARAM_STR);
            $statement->bindParam(4, $updated_at, PDO::PARAM_STR);

            $result = $statement->execute();

            return $result;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function fetchAllForums()
    {
        $query = <<<'QUERY'
        SELECT * FROM forums
QUERY;

        $statement = $this->database->connection()->prepare($query);
        $statement->execute();

        $forums = [];

        while ($forumData = $statement->fetch(PDO::FETCH_ASSOC)) {
            $forum = new Forum(
                $forumData['id'],
                $forumData['title'],
                $forumData['description'],
                new DateTime($forumData['created_at']),
                new DateTime($forumData['updated_at'])
            );

            $forums[] = $forum;
        }

        return $forums;
    }

    public function findForumByID($forumId)
    {
        $query = <<<'QUERY'
        SELECT * FROM forums WHERE id = ?
QUERY;

        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam(1, $forumId, PDO::PARAM_INT);
        $statement->execute();

        $forumData = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$forumData) {
            return null; // No se encontró ningún foro con ese ID
        }

        $forum = new Forum(
            $forumData['id'],
            $forumData['title'],
            $forumData['description'],
            new DateTime($forumData['created_at']),
            new DateTime($forumData['updated_at'])
        );

        return $forum;
    }

    public function findForumByTitle(string $title): ?Forum
    {
        $query = <<<'QUERY'
        SELECT * FROM forums WHERE title = ?
QUERY;

        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam(1, $title, PDO::PARAM_STR);
        $statement->execute();

        $forumData = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$forumData) {
            return null; // No se encontró ningún foro con ese título
        }

        $forum = new Forum(
            $forumData['id'],
            $forumData['title'],
            $forumData['description'],
            new DateTime($forumData['created_at']),
            new DateTime($forumData['updated_at'])
        );

        return $forum;
    }

    public function generateNewForum($data)
    {
        return new Forum(0,
            $data['title'],
            $data['description'],
            new DateTime(),
            new DateTime()
        );
    }

    public function deleteForum(mixed $forum_id)
    {
        $query = <<<'QUERY'
        DELETE FROM forums WHERE id = ?
QUERY;

        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam(1, $forum_id, PDO::PARAM_STR);
        $statement->execute();

       // $result = $statement->fetch(PDO::FETCH_ASSOC);

    }
}
