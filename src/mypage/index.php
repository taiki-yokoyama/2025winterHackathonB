<?php
session_start();

// ログインチェック
if (!isset($_SESSION['user_id'])) {
    header('Location: /auth/login.php');
    exit;
}

require_once '../dbconnect.php';

// ユーザー情報取得
$stmt = $dbh->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// プロフィール未設定の場合は編集画面へ
if (empty($user['name'])) {
    header('Location: /mypage/edit.php?first=1');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>マイページ - ③でPON</title>
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
    </style>
</head>
<body class="">

    <?php include '../components/header.php'; ?>

    <div class="max-w-4xl mx-auto">


        <!-- マイページ -->
        <section class="toy-box p-6 md:p-8 bg-white relative">
            <div class="absolute -top-6 left-1/2 transform -translate-x-1/2 bg-[#FF69B4] border-4 border-black px-6 py-2 font-heavy text-xl shadow-[4px_4px_0_#000] rotate-2 z-10 text-white">
                マイページ 👤
            </div>
            
            <div class="mt-8">
                <!-- プロフィール表示 -->
                <div class="flex flex-col md:flex-row gap-8 items-center md:items-start">
                    <!-- アイコン -->
                    <div class="flex-shrink-0">
                        <div class="w-32 h-32 md:w-40 md:h-40 border-4 border-black bg-gray-200 flex items-center justify-center overflow-hidden">
                            <?php if ($user['icon']): ?>
                                <img src="/assets/img/<?php echo htmlspecialchars($user['icon']); ?>" alt="アイコン" class="w-full h-full object-cover">
                            <?php else: ?>
                                <span class="text-6xl">👤</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- ユーザー情報 -->
                    <div class="flex-grow">
                        <div class="space-y-4">
                            <div class="toy-box p-4 bg-[#FFD700] transform -rotate-1">
                                <p class="text-sm font-bold text-gray-700">名前</p>
                                <p class="text-2xl font-heavy"><?php echo htmlspecialchars($user['name']); ?></p>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="toy-box p-4 bg-[#87CEEB] transform rotate-1">
                                    <p class="text-sm font-bold text-gray-700">期生</p>
                                    <p class="text-xl font-bold"><?php echo htmlspecialchars($user['generation']); ?>期</p>
                                </div>
                                
                                <div class="toy-box p-4 bg-[#98FB98] transform -rotate-1">
                                    <p class="text-sm font-bold text-gray-700">コイン</p>
                                    <p class="text-xl font-bold">🪙 <?php echo htmlspecialchars($user['coins']); ?></p>
                                </div>
                            </div>
                            
                            <div class="toy-box p-4 bg-[#FFB6C1] transform rotate-1">
                                <p class="text-sm font-bold text-gray-700">横もく</p>
                                <p class="text-xl font-bold"><?php echo htmlspecialchars($user['yokomoku']); ?></p>
                            </div>
                            
                            <div class="toy-box p-4 bg-[#DDA0DD] transform -rotate-1">
                                <p class="text-sm font-bold text-gray-700">縦もく</p>
                                <p class="text-xl font-bold"><?php echo htmlspecialchars($user['tatemoku']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- ボタン -->
                <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
                    <a 
                        href="/mypage/edit.php" 
                        class="inline-block bg-[#FF69B4] text-white font-heavy text-xl py-3 px-8 border-4 border-black shadow-[6px_6px_0_#000] hover:translate-y-2 hover:shadow-[3px_3px_0_#000] transition text-center"
                    >
                        プロフィールを編集 ✏️
                    </a>
                    <a 
                        href="/cardbook/cardbook.php" 
                        class="inline-block bg-[#FFD700] text-black font-heavy text-xl py-3 px-8 border-4 border-black shadow-[6px_6px_0_#000] hover:translate-y-2 hover:shadow-[3px_3px_0_#000] transition text-center"
                    >
                        今までに集めたカードを見る 🎴
                    </a>
                </div>
            </div>
        </section>
        
        <!-- ナビゲーション -->
        <div class="mt-8 flex flex-wrap gap-4 justify-center">
            <a href="/plan/" class="toy-box p-3 bg-[#FF69B4] font-bold hover:translate-y-1 transition">Plan</a>
            <a href="/check/" class="toy-box p-3 bg-[#87CEEB] font-bold hover:translate-y-1 transition">Check</a>
            <a href="/action/" class="toy-box p-3 bg-[#32CD32] font-bold hover:translate-y-1 transition">Action</a>
            <a href="/gacha/" class="toy-box p-3 bg-[#FFD700] font-bold hover:translate-y-1 transition">Gacha</a>
        </div>
        
    </div>

</body>
</html>
