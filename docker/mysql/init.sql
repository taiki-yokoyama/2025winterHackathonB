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
    coins INT DEFAULT 3,
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- カードテーブル
CREATE TABLE cards (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    image VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ユーザーカード所持テーブル
CREATE TABLE user_cards (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    card_id INT NOT NULL,
    count INT DEFAULT 1,
    first_obtained_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_obtained_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (card_id) REFERENCES cards(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_card (user_id, card_id)
);

-- サンプルカードデータ（5枚）
INSERT INTO cards (name, image) VALUES
('だいぼう', 'daibou.jpeg'),
('えび', 'ebi.JPG'),
('かい', 'kai.JPG'),
('てつろう', 'tetsurou.JPG'),
('よこ', 'yoko.JPG');
