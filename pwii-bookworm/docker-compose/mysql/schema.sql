SET NAMES utf8;
SET
time_zone = '+00:00';
SET
foreign_key_checks = 0;
SET
sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `users`;
-- Create the users table
CREATE TABLE users
(
    id              INT AUTO_INCREMENT PRIMARY KEY,
    email           VARCHAR(255) NOT NULL UNIQUE,
    password        VARCHAR(255) NOT NULL,
    username        VARCHAR(255) DEFAULT NULL UNIQUE,
    profile_picture VARCHAR(255) DEFAULT NULL,
    created_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

DROP TABLE IF EXISTS `books`;
-- Create the books table
CREATE TABLE books
(
    id          INT AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(65535) NOT NULL,
    author      VARCHAR(65535) NOT NULL,
    description TEXT,
    page_number INT            NOT NULL,
    cover_image VARCHAR(65535),
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

DROP TABLE IF EXISTS `ratings`;
-- Create the ratings table
CREATE TABLE ratings
(
    user_id    INT NOT NULL,
    book_id    INT NOT NULL,
    rating     INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id),
    FOREIGN KEY (book_id) REFERENCES books (id),
    PRIMARY KEY (user_id, book_id)
);

DROP TABLE IF EXISTS `reviews`;
-- Create the reviews table
CREATE TABLE reviews
(
    user_id     INT NOT NULL,
    book_id     INT NOT NULL,
    review_text TEXT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id),
    FOREIGN KEY (book_id) REFERENCES books (id),
    PRIMARY KEY (user_id, book_id)
);

DROP TABLE IF EXISTS `forums`;
-- Create the forums table
CREATE TABLE forums
(
    id          INT AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(255) NOT NULL,
    description TEXT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

DROP TABLE IF EXISTS `posts`;
-- Create the posts table
CREATE TABLE posts
(
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT          NOT NULL,
    forum_id   INT          NOT NULL,
    title      VARCHAR(255) NOT NULL,
    contents   TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id),
    FOREIGN KEY (forum_id) REFERENCES forums (id) ON DELETE CASCADE
);
