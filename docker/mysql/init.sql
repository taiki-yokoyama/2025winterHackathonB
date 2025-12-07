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

-- Planテーブル
CREATE TABLE plans (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    start_date DATE,
    end_date DATE,
    status ENUM('running', 'completed', 'cancelled') DEFAULT 'running',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Actionテーブル
CREATE TABLE actions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    from_user_id INT NOT NULL,
    to_user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (from_user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (to_user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- サンプルユーザーデータ
INSERT INTO users (id, email, password, name, generation, icon, yokomoku, tatemoku, coins, created_at) VALUES
(1, 'user1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ユーザー1', '6', NULL, '横もくA', '縦もくA', 3, NOW()),
(2, 'user2@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ユーザー2', '6', NULL, '横もくB', '縦もくB', 3, NOW()),
(3, 'user3@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ユーザー3', '5.5', NULL, '横もくC', '縦もくC', 3, NOW()),
(4, 'user4@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ユーザー4', '6.5', NULL, '横もくD', '縦もくD', 3, NOW());

-- カードデータ
INSERT INTO cards (name, image) VALUES
('UT', 'UT.jpg'),
('あかぎ', 'akagi.jpg'),
('あかり', 'akari.jpg'),
('あおい', 'aoi.jpg'),
('だいぼう', 'daibou.jpeg'),
('だいち', 'daichi.jpg'),
('えび', 'ebi.JPG'),
('ギャラドス', 'gyaradosu.jpg'),
('はると', 'haruto.jpg'),
('ひなり', 'hinari.jpg'),
('かほこ', 'kahoko.jpg'),
('かい', 'kai.JPG'),
('かの', 'kano.jpg'),
('これちか', 'korechika.jpg'),
('ことね', 'kotone.jpg'),
('こつこ', 'kotuko.jpg'),
('こういちろう', 'kouichirou.jpg'),
('こうき', 'kouki.png'),
('こうしろう', 'koushirou.jpg'),
('まほ', 'maho.jpg'),
('まゆ', 'mayu.jpg'),
('みづき', 'miduki.jpg'),
('みきゆう', 'mikiyuu.jpg'),
('もりん', 'morin.jpg'),
('おにいちゃん', 'oniichan.jpg'),
('ぱなえ', 'panae.jpg'),
('ぴま', 'pima.jpg'),
('りん', 'rin.jpg'),
('さつき', 'satsuki.jpg'),
('しまむー', 'shimamu-.jpg'),
('しゅう', 'shu.jpg'),
('しょうこん', 'syoukon.jpg'),
('たからだ', 'takarada.jpg'),
('てつろう', 'tetsurou.JPG'),
('つぼ', 'tubo.jpg'),
('よこ', 'yoko.JPG'),
('よなみね', 'yonamine.JPG'),
('ゆいな', 'yuina.jpg'),
('ゆりな', 'yurina.jpg'),
('ゆうき', 'yuuki.jpg'),
('ちゃんり', 'tyannri.jpg');

-- サンプルプランデータ
INSERT INTO plans (user_id, content, start_date, end_date, status, created_at) VALUES
(2, '毎日30分、ペアプロの時間を設ける', '2024-12-02', '2024-12-08', 'running', NOW()),
(2, 'レビューのフィードバックを24時間以内に返す', '2024-12-02', '2024-12-08', 'running', NOW()),
(3, 'テストコードのカバレッジを80%以上にする', '2024-12-02', '2024-12-08', 'running', NOW()),
(1, 'APIドキュメントを詳しく記述する', '2024-11-25', '2024-12-01', 'completed', '2024-11-25 10:00:00');

-- サンプルActionデータ
INSERT INTO actions (from_user_id, to_user_id, content, created_at) VALUES
(2, 1, 'もう少しペアプロの時間を増やせるといいかも', '2024-12-05 10:30:00'),
(3, 1, 'テストコードをもう少し充実させると完璧です', '2024-12-05 14:20:00'),
(4, 1, '引き続きこの調子で！レビューのスピードが早くて助かってます', '2024-12-04 16:45:00');
