<?php
session_start();

// ログインチェック
if (!isset($_SESSION['user_id'])) {
    header('Location: /auth/login.php');
    exit;
}

require_once '../dbconnect.php';

$error = '';
$result_card = null;

// ガチャを引く処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['draw_gacha'])) {
    // ユーザーのコイン数を取得
    $stmt = $dbh->prepare('SELECT coins FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user['coins'] < 1) {
        $error = 'コインが足りません';
    } else {
        try {
            $dbh->beginTransaction();
            
            // コインを1枚減らす
            $stmt = $dbh->prepare('UPDATE users SET coins = coins - 1 WHERE id = ?');
            $stmt->execute([$_SESSION['user_id']]);
            $_SESSION['coins'] = $user['coins'] - 1;
            
            // ランダムにカードを選択
            $stmt = $dbh->query('SELECT * FROM cards ORDER BY RAND() LIMIT 1');
            $result_card = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // ユーザーのカード所持情報を更新
            $stmt = $dbh->prepare('
                INSERT INTO user_cards (user_id, card_id, count, first_obtained_at, last_obtained_at)
                VALUES (?, ?, 1, NOW(), NOW())
                ON DUPLICATE KEY UPDATE 
                    count = count + 1,
                    last_obtained_at = NOW()
            ');
            $stmt->execute([$_SESSION['user_id'], $result_card['id']]);
            
            $dbh->commit();
        } catch (Exception $e) {
            $dbh->rollBack();
            $error = 'ガチャの実行に失敗しました';
        }
    }
}

// 現在のコイン数を取得
$stmt = $dbh->prepare('SELECT coins FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$coins = $user['coins'];
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ガチャ - ③でPON</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Dela+Gothic+One&family=DotGothic16&family=M+PLUS+Rounded+1c:wght@700;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'M PLUS Rounded 1c', sans-serif;
            background: linear-gradient(135deg, #FFB6C1 0%, #87CEEB 50%, #98FB98 100%);
            min-height: 100vh;
        }
        .font-heavy { font-family: 'Dela Gothic One', sans-serif; }
        .toy-box { border: 6px solid #000; box-shadow: 12px 12px 0 #000; }
        
        @keyframes cardFlip {
            0% { transform: rotateY(0deg) scale(0.5); opacity: 0; }
            50% { transform: rotateY(180deg) scale(1.2); }
            100% { transform: rotateY(360deg) scale(1); opacity: 1; }
        }
        
        .card-animation {
            animation: cardFlip 1s ease-out;
        }
        
        @keyframes shine {
            0%, 100% { opacity: 0; }
            50% { opacity: 1; }
        }
        
        .shine {
            animation: shine 2s infinite;
        }
    </style>
</head>
<body class="p-4 md:p-8">

    <?php include '../components/header.php'; ?>

    <div class="max-w-4xl mx-auto mt-12 md:mt-16">
        
        <!-- ガチャセクション -->
        <section class="toy-box p-6 md:p-8 bg-white relative">
            <div class="absolute -top-6 left-1/2 transform -translate-x-1/2 bg-[#9370DB] border-4 border-black px-6 py-2 font-heavy text-xl shadow-[4px_4px_0_#000] rotate-2 z-10 text-white">
                ガチャガチャ 🎰
            </div>
            
            <div class="mt-8">
                <!-- コイン表示 -->
                <div class="text-center mb-8">
                    <div class="inline-block toy-box p-4 bg-[#FFD700] transform -rotate-1">
                        <p class="text-sm font-bold text-gray-700">所持コイン</p>
                        <p class="text-4xl font-heavy">🪙 <?php echo $coins; ?></p>
                    </div>
                </div>
                
                <?php if ($error): ?>
                    <div class="bg-red-100 border-4 border-red-500 text-red-700 px-4 py-3 mb-6 font-bold transform -rotate-1 text-center">
                        ⚠️ <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($result_card): ?>
                    <!-- 結果表示 -->
                    <div class="text-center mb-8 card-animation">
                        <div class="inline-block toy-box p-6 bg-gradient-to-br from-[#FFD700] to-[#FFA500] relative">
                            <div class="absolute inset-0 shine bg-white opacity-0"></div>
                            <p class="text-2xl font-heavy mb-4 text-white [text-shadow:2px_2px_0_#000]">
                                🎉 おめでとう！ 🎉
                            </p>
                            <div class="bg-white border-4 border-black p-4 mb-4">
                                <img 
                                    src="/assets/img/gacha_img/<?php echo htmlspecialchars($result_card['image']); ?>" 
                                    alt="<?php echo htmlspecialchars($result_card['name']); ?>"
                                    class="w-64 h-auto mx-auto"
                                >
                            </div>
                            <p class="text-xl font-heavy"><?php echo htmlspecialchars($result_card['name']); ?></p>
                        </div>
                    </div>
                    
                    <div class="text-center mb-6">
                        <a href="/cardbook/cardbook.php" class="inline-block bg-[#87CEEB] text-white font-heavy text-lg py-3 px-6 border-4 border-black shadow-[6px_6px_0_#000] hover:translate-y-2 hover:shadow-[3px_3px_0_#000] transition">
                            カードブックを見る 📖
                        </a>
                    </div>
                <?php endif; ?>
                
                <!-- ガチャボタン -->
                <form method="POST" action="" class="text-center">
                    <button 
                        type="submit" 
                        name="draw_gacha"
                        <?php echo $coins < 1 ? 'disabled' : ''; ?>
                        class="bg-[#FF69B4] text-white font-heavy text-2xl md:text-3xl py-6 px-12 border-6 border-black shadow-[12px_12px_0_#000] hover:translate-y-2 hover:shadow-[6px_6px_0_#000] transition-all transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        ガチャを引く！<br>
                        <span class="text-lg">(コイン1枚使用)</span>
                    </button>
                </form>
                
                <div class="mt-8 text-center text-sm text-gray-600">
                    <p>※ コインを使ってガチャを引くと、ランダムでメンバーカードが手に入ります</p>
                    <p>※ 同じカードを引くと、所持枚数が増えます</p>
                </div>
            </div>
        </section>
        
    </div>

    <?php include '../components/footer.php'; ?>

</body>
</html>
