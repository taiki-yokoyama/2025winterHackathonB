DROP DATABASE IF EXISTS posse;
CREATE DATABASE posse;

USE posse;

-- ユーザーテーブル
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    avatar VARCHAR(10) DEFAULT '😊',
    avatar_color VARCHAR(50) DEFAULT 'bg-blue-400',
    coins INT DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- プランテーブル
CREATE TABLE plans (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    start_date DATE,
    end_date DATE,
    status ENUM('running', 'completed', 'pending') DEFAULT 'running',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- サンプルユーザーデータ
INSERT INTO users (name, email, password, avatar, avatar_color, coins, created_at) VALUES
('ちゃんり', 'chanri@example.com', 'password123', 'ち', 'bg-orange-400', 0, NOW()),
('メンバーA', 'memberA@example.com', 'password123', 'A', 'bg-pink-400', 0, NOW()),
('メンバーB', 'memberB@example.com', 'password123', 'B', 'bg-blue-400', 0, NOW()),
('メンバーC', 'memberC@example.com', 'password123', 'C', 'bg-purple-400', 0, NOW());

-- サンプルプランデータ
INSERT INTO plans (user_id, content, start_date, end_date, status, created_at) VALUES
(2, '毎日30分、ペアプロの時間を設ける', '2024-12-02', '2024-12-08', 'running', NOW()),
(2, 'レビューのフィードバックを24時間以内に返す', '2024-12-02', '2024-12-08', 'running', NOW()),
(3, 'テストコードのカバレッジを80%以上にする', '2024-12-02', '2024-12-08', 'running', NOW()),
(1, 'APIドキュメントを詳しく記述する', '2024-11-25', '2024-12-01', 'completed', '2024-11-25 10:00:00');
