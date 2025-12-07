<?php
session_start();

// データベース接続
require_once __DIR__ . '/../dbconnect.php';

// 現在のユーザーID（仮で1を使用、本来はセッションから取得）
$current_user_id = $_SESSION['user_id'] ?? 1;

// 自分宛のActionを取得
$stmt = $dbh->prepare('
    SELECT a.*, u.name as from_user_name, u.avatar, u.avatar_color
    FROM actions a
    JOIN users u ON a.from_user_id = u.id
    WHERE a.to_user_id = ?
    ORDER BY a.created_at DESC
');
$stmt->execute([$current_user_id]);
$actions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Action - ③でPON</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Bangers&display=swap" rel="stylesheet">
    <style>
        .toy-title { font-family: 'Bangers', cursive; letter-spacing: 2px; }
    </style>
</head>
<body class="bg-purple-50 min-h-screen p-8">

<div class="bg-white rounded-xl shadow-sm p-6 mb-6 border-4 border-black" style="background: linear-gradient(135deg, #FF6B9D 0%, #FEC163 100%);">
    <div class="flex items-center space-x-3 mb-2">
        <div class="p-3 bg-yellow-300 rounded-full border-4 border-black shadow-[4px_4px_0_#000]">
            <i class="fa-solid fa-bullseye text-2xl text-red-600"></i>
        </div>
        <div class="text-white">
            <h2 class="text-3xl font-black drop-shadow-[3px_3px_0_#000] toy-title" style="text-shadow: 3px 3px 0 #000;">ACTION!</h2>
            <p class="text-sm font-bold text-yellow-100">次の一手を送ろう！</p>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto">
    <h3 class="text-2xl font-black text-gray-800 mb-6 toy-title">あなたへのActionの提案</h3>
    
    <div class="space-y-6">
        <?php if (empty($actions)): ?>
            <div class="bg-white rounded-3xl p-8 border-6 border-gray-300 shadow-[8px_8px_0_#ccc] text-center">
                <p class="text-gray-500 text-lg font-bold">まだActionがありません 📭</p>
            </div>
        <?php else: ?>
            <?php foreach ($actions as $action): ?>
                <div class="bg-white rounded-3xl p-6 border-6 border-yellow-400 shadow-[8px_8px_0_#000] transform hover:translate-y-1 hover:shadow-[4px_4px_0_#000] transition-all">
                    <div class="flex items-start gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-12 h-12 rounded-full border-4 border-black flex items-center justify-center font-black text-xl shadow-lg <?php echo htmlspecialchars($action['avatar_color']); ?> text-white">
                                    <?php echo htmlspecialchars($action['avatar']); ?>
                                </div>
                                <div>
                                    <h4 class="font-black text-lg text-gray-800"><?php echo htmlspecialchars($action['from_user_name']); ?></h4>
                                    <p class="text-xs text-gray-500 flex items-center">
                                        <i class="fa-regular fa-calendar mr-1"></i> 
                                        <?php echo date('Y/m/d', strtotime($action['created_at'])); ?>
                                    </p>
                                </div>
                            </div>
                            <div class="bg-yellow-100 rounded-2xl p-4 border-4 border-yellow-300 shadow-inner">
                                <p class="text-gray-800 font-bold flex items-start gap-2">
                                    <i class="fa-solid fa-lightbulb text-yellow-600 mt-1"></i> 
                                    <?php echo nl2br(htmlspecialchars($action['content'])); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="mt-12 relative">
        <div class="absolute -inset-1 bg-gradient-to-r from-red-500 to-orange-500 rounded-3xl blur opacity-30"></div>
        <div class="relative bg-gradient-to-r from-yellow-400 to-orange-400 rounded-3xl p-8 border-6 border-black shadow-[8px_8px_0_#000] text-center">
            <div class="absolute -top-6 left-1/2 transform -translate-x-1/2 w-16 h-16 bg-green-500 rounded-full border-4 border-black flex items-center justify-center text-white font-black text-2xl shadow-lg animate-bounce">
                <i class="fa-solid fa-arrow-down"></i>
            </div>
            <h3 class="text-2xl font-black text-white mb-3 toy-title drop-shadow-[2px_2px_0_#000]">これを見てプランを作ろう！</h3>
            <p class="text-white font-bold mb-6">受け取ったActionをもとに、次のサイクルの計画を立てましょう 🎯</p>
            <a href="../plan/" class="inline-block bg-red-500 hover:bg-red-600 text-white font-black py-4 px-8 rounded-2xl shadow-[6px_6px_0_#000] border-4 border-black transform hover:translate-y-1 hover:shadow-[3px_3px_0_#000] transition-all text-lg uppercase toy-title">
                <i class="fa-solid fa-file-lines mr-2"></i> プラン作成へGO!
            </a>
        </div>
    </div>
</div>

</body>
</html>
