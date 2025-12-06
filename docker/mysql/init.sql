DROP DATABASE IF EXISTS posse;
CREATE DATABASE posse;

USE posse;

-- ユーザーテーブル
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100),
    generation ENUM('5', '5.5', '6', '6.5'),
    icon VARCHAR(255),
    yokomoku VARCHAR(50),
    tatemoku VARCHAR(50),
    coins INT DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
