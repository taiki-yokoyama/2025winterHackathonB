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
    current_mode ENUM('yokomoku', 'tatemoku') DEFAULT 'yokomoku',
    coins INT DEFAULT 3,
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

-- サンプルユーザーデータ（パスワードは全て "password"）
-- 横もく6A（フロントエンド重視チーム）
INSERT INTO users (id, email, password, name, generation, icon, yokomoku, tatemoku, current_mode, coins, created_at) VALUES
(1, 'yuuki@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ゆうき', '6', 'yuuki.jpg', '横もく6A', '縦もくA', 'yokomoku', 5, NOW()),
(2, 'haruto@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'はると', '6', 'haruto.jpg', '横もく6A', '縦もくB', 'yokomoku', 3, NOW()),
(3, 'shu@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'しゅう', '5.5', 'shu.jpg', '横もく5A', '縦もくA', 'yokomoku', 7, NOW()),
(4, 'maho@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'まほ', '6', 'maho.jpg', '横もく6A', '縦もくC', 'yokomoku', 4, NOW()),

-- 横もく6B（バックエンド重視チーム）
(5, 'daichi@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'だいち', '6.5', 'daichi.jpg', '横もく6B', '縦もくB', 'yokomoku', 6, NOW()),
(6, 'rin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'りん', '6', 'rin.jpg', '横もく6B', '縦もくA', 'yokomoku', 3, NOW()),
(7, 'kouki@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'こうき', '5.5', 'kouki.png', '横もく5B', '縦もくC', 'yokomoku', 8, NOW()),
(8, 'yuina@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ゆいな', '6', 'yuina.jpg', '横もく6B', '縦もくB', 'yokomoku', 2, NOW()),

-- 横もく6C（フルスタックチーム）
(9, 'kai@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'かい', '6', 'kai.JPG', '横もく6C', '縦もくA', 'yokomoku', 5, NOW()),
(10, 'akari@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'あかり', '5.5', 'akari.jpg', '横もく5C', '縦もくC', 'yokomoku', 4, NOW()),
(11, 'tetsurou@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'てつろう', '6.5', 'tetsurou.JPG', '横もく6C', '縦もくB', 'yokomoku', 6, NOW()),
(12, 'mayu@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'まゆ', '6', 'mayu.jpg', '横もく6C', '縦もくA', 'yokomoku', 3, NOW());

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

-- サンプルプランデータ（横もくA）
INSERT INTO plans (user_id, content, start_date, end_date, status, created_at) VALUES
(1, 'ボタンのホバーエフェクトを実装して、UIをもっと分かりやすくする', '2024-12-02', '2024-12-08', 'running', NOW()),
(2, 'レスポンシブ対応を完成させる（スマホで見た時にレイアウトが崩れないようにする）', '2024-12-02', '2024-12-08', 'running', NOW()),
(3, 'JavaScriptのエラーを全部直して、コンソールにエラーが出ないようにする', '2024-12-02', '2024-12-08', 'running', NOW()),
(4, 'CSSのクラス名を分かりやすく統一する（今バラバラで混乱してる）', '2024-12-02', '2024-12-08', 'running', NOW()),

-- サンプルプランデータ（横もくB）
(5, 'データベースのテーブル設計を完成させる（まだ足りないカラムがある）', '2024-12-02', '2024-12-08', 'running', NOW()),
(6, 'ログイン機能のバグを修正する（たまにセッションが切れる問題）', '2024-12-02', '2024-12-08', 'running', NOW()),
(7, 'フォームのバリデーションを追加する（空欄チェックとか）', '2024-12-02', '2024-12-08', 'running', NOW()),
(8, 'SQLインジェクション対策を全部のクエリに適用する', '2024-12-01', '2024-12-07', 'completed', '2024-12-01 09:00:00'),

-- サンプルプランデータ（横もくC）
(9, 'APIのエンドポイントを整理する（今ごちゃごちゃしてて分かりにくい）', '2024-12-02', '2024-12-08', 'running', NOW()),
(10, 'エラーハンドリングをちゃんと実装する（エラーメッセージが出ないことがある）', '2024-12-02', '2024-12-08', 'running', NOW()),
(11, 'コードにコメントを追加して、後で見ても分かるようにする', '2024-12-02', '2024-12-08', 'running', NOW()),
(12, 'GitHubのコミットメッセージを分かりやすく書く習慣をつける', '2024-11-28', '2024-12-05', 'completed', '2024-11-28 10:00:00');

-- サンプルActionデータ（横もくA - 技術面とチームワーク）
INSERT INTO actions (from_user_id, to_user_id, content, created_at) VALUES
(2, 1, 'CSSのレイアウト、すごく綺麗になってる！もう少しスマホサイズでも確認してみるといいかも', '2024-12-05 10:30:00'),
(3, 1, 'JavaScriptのエラー直してくれてありがとう！動きがスムーズになりました', '2024-12-05 14:20:00'),
(4, 1, 'ボタンのデザイン可愛くなってて良い感じです。色の組み合わせも見やすいです', '2024-12-04 16:45:00'),
(1, 2, 'HTMLの構造が分かりやすくなってて、コード読みやすかったです！', '2024-12-05 11:00:00'),

-- サンプルActionデータ（横もくB - バックエンド重視）
(6, 5, 'データベースのテーブル、ちゃんと設計できてますね。外部キーの使い方も理解できました', '2024-12-05 09:15:00'),
(7, 5, 'ログイン機能のバグ修正お疲れ様！セッション周りの説明も分かりやすかったです', '2024-12-05 13:45:00'),
(8, 6, 'フォームのバリデーション追加してくれてありがとう。エラーメッセージも親切で良いです', '2024-12-04 15:30:00'),
(5, 7, 'SQLインジェクション対策、しっかり実装できてますね。セキュリティ意識高くて素晴らしいです', '2024-12-05 16:20:00'),

-- サンプルActionデータ（横もくC - フルスタック）
(10, 9, 'APIの使い方教えてくれてありがとう！フロントから呼び出せるようになりました', '2024-12-05 10:00:00'),
(11, 9, 'エラーハンドリングの実装、参考になりました。try-catchの使い方が分かってきました', '2024-12-05 12:30:00'),
(12, 10, 'コメント追加してくれて助かります。コードの意図が分かりやすくなりました', '2024-12-04 17:00:00'),
(9, 11, 'Gitのコミットメッセージ、すごく分かりやすくなってる！何を変更したか一目で分かります', '2024-12-05 14:50:00'),

-- 人格面を重視したActionデータ
(3, 2, '分からないところ丁寧に教えてくれてありがとう。質問しやすい雰囲気作ってくれて嬉しいです', '2024-12-05 18:00:00'),
(4, 3, 'いつも明るくて、作業中も楽しく開発できてます。ポジティブな雰囲気ありがとう！', '2024-12-05 11:30:00'),
(7, 8, '進捗報告マメにしてくれて助かってます。チーム全体の状況が把握しやすいです', '2024-12-05 15:00:00'),
(10, 12, '困ってる時にすぐ気づいて声かけてくれるの、本当に助かってます。優しさに感謝です', '2024-12-05 16:45:00'),
(2, 4, '夜遅くまで一緒に頑張ってくれてありがとう。チームワークの良さを感じます', '2024-12-05 19:30:00'),
(6, 7, 'ミーティングでの意見、いつも的確で参考になります。発言してくれて助かってます', '2024-12-05 10:45:00'),
(8, 5, 'タスク管理しっかりしてくれて、チーム全体が動きやすいです。リーダーシップありがとう', '2024-12-05 17:15:00'),
(11, 10, 'デバッグ一緒に付き合ってくれて感謝です。諦めずに解決できて良かったです', '2024-12-05 20:00:00');
