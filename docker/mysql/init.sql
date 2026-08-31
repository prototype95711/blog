CREATE DATABASE IF NOT EXISTS `blog` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `blog`.`categories` (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    descr TEXT NOT NULL,
    parent_id INT UNSIGNED NOT NULL DEFAULT 0,
    KEY parent_id (parent_id)
);

CREATE TABLE IF NOT EXISTS `blog`.`images` (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    filepath VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS `blog`.`posts` (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    descr TEXT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    image_id INT UNSIGNED NOT NULL DEFAULT 0,
    views INT UNSIGNED NOT NULL DEFAULT 0,
    KEY created_at (created_at),
    KEY image_id (image_id),
    KEY views (views)
);

CREATE TABLE IF NOT EXISTS `blog`.`categories_links` (
    category_id INT UNSIGNED NOT NULL,
    post_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (category_id, post_id)
);

