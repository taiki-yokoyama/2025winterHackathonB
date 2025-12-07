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

-- チーム計画テーブル
CREATE TABLE team_plans (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    plan_text TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_created_at (created_at)
);

-- チームチャットテーブル
CREATE TABLE team_chats (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_created_at (created_at)
);

-- 個人評価テーブル
CREATE TABLE individual_evaluations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    evaluator_id INT NOT NULL,
    target_user_id INT NOT NULL,
    code_rating INT NOT NULL CHECK (code_rating BETWEEN 1 AND 4),
    code_comment TEXT,
    personality_rating INT NOT NULL CHECK (personality_rating BETWEEN 1 AND 4),
    personality_comment TEXT,
    action_proposal TEXT,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (evaluator_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (target_user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_target_user (target_user_id, created_at),
    INDEX idx_evaluator (evaluator_id, created_at)
);
