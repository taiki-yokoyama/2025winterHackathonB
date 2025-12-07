<?php
session_start();

// ログインチェック
if (!isset($_SESSION['user_id'])) {
    header('Location: /auth/login.php');
    exit;
}

// データベース接続
require_once __DIR__ . '/../dbconnect.php';
require_once __DIR__ . '/../evaluation_functions.php';

// 現在のユーザーID
$current_user_id = $_SESSION['user_id'];

// 現在のユーザー情報を取得
$stmt = $dbh->prepare('SELECT yokomoku, tatemoku, current_mode FROM users WHERE id = ?');
$stmt->execute([$current_user_id]);
$current_user = $stmt->fetch(PDO::FETCH_ASSOC);
$current_mode = $current_user['current_mode'] ?? 'yokomoku';
$team_value = $current_mode === 'yokomoku' ? $current_user['yokomoku'] : $current_user['tatemoku'];

// 自分宛のAction提案を取得（individual_evaluationsテーブルから）
$action_proposals = getActionProposalsByTargetUser($dbh, $current_user_id);

// 同じチームのメンバーからの提案のみをフィルタリング
$filtered_proposals = [];
foreach ($action_proposals as $proposal) {
    // 提案者の情報を取得
    $stmt = $dbh->prepare('SELECT ' . $current_mode . ' FROM users WHERE id = ?');
    $stmt->execute([$proposal['evaluator_id']]);
    $evaluator = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // 同じチームの場合のみ追加
    if ($evaluator && $evaluator[$current_mode] === $team_value) {
        $filtered_proposals[] = $proposal;
    }
}

// アバター色の配列
$avatar_colors = ['bg-pink-400', 'bg-blue-400', 'bg-green-400', 'bg-purple-400', 'bg-yellow-400', 'bg-red-400'];
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
    <link href="https://fonts.googleapis.com/css2?family=Dela+Gothic+One&family=M+PLUS+Rounded+1c:wght@700;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'M PLUS Rounded 1c', sans-serif;
            background: linear-gradient(135deg, #FFB6C1 0%, #87CEEB 50%, #98FB98 100%);
            min-height: 100vh;
        }
        .toy-title { font-family: 'Bangers', cursive; letter-spacing: 2px; }
        .font-heavy { font-family: 'Dela Gothic One', sans-serif; }
        .toy-box { border: 6px solid #000; box-shadow: 12px 12px 0 #000; }
    </style>
</head>
<body class="p-4 md:p-8">

<?php include '../components/header.php'; ?>

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
        <?php if (empty($filtered_proposals)): ?>
            <div class="bg-white rounded-3xl p-8 border-6 border-gray-300 shadow-[8px_8px_0_#ccc] text-center">
                <p class="text-gray-500 text-lg font-bold">まだActionの提案がありません 📭</p>
                <p class="text-gray-400 text-sm mt-2">チームメンバーからの評価を待ちましょう</p>
            </div>
        <?php else: ?>
            <?php foreach ($filtered_proposals as $index => $proposal): ?>
                <?php 
                $avatar_color = $avatar_colors[$proposal['evaluator_id'] % count($avatar_colors)];
                ?>
                <div class="bg-white rounded-3xl p-6 border-6 border-yellow-400 shadow-[8px_8px_0_#000] transform hover:translate-y-1 hover:shadow-[4px_4px_0_#000] transition-all">
                    <div class="flex items-start gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-3">
                                <?php if (!empty($proposal['evaluator_icon'])): ?>
                                    <div class="w-12 h-12 rounded-full border-4 border-black overflow-hidden shadow-lg">
                                        <img src="/assets/img/gacha_img/<?php echo htmlspecialchars($proposal['evaluator_icon']); ?>" 
                                             alt="<?php echo htmlspecialchars($proposal['evaluator_name']); ?>" 
                                             class="w-full h-full object-cover">
                                    </div>
                                <?php else: ?>
                                    <div class="w-12 h-12 rounded-full border-4 border-black flex items-center justify-center font-black text-xl shadow-lg <?php echo $avatar_color; ?> text-white">
                                        <?php echo htmlspecialchars(mb_substr($proposal['evaluator_name'], 0, 1)); ?>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <h4 class="font-black text-lg text-gray-800"><?php echo htmlspecialchars($proposal['evaluator_name']); ?></h4>
                                    <p class="text-xs text-gray-500 flex items-center">
                                        <i class="fa-regular fa-calendar mr-1"></i> 
                                        <?php echo date('Y/m/d H:i', strtotime($proposal['created_at'])); ?>
                                    </p>
                                </div>
                            </div>
                            <div class="bg-gradient-to-r from-green-100 to-blue-100 rounded-2xl p-5 border-4 border-green-400 shadow-inner">
                                <div class="flex items-start gap-2 mb-2">
                                    <i class="fa-solid fa-lightbulb text-yellow-600 text-xl mt-1"></i>
                                    <span class="text-xs font-black text-green-700 uppercase tracking-wider">次のPlanの提案</span>
                                </div>
                                <p class="text-gray-800 font-bold text-lg pl-7">
                                    <?php echo nl2br(htmlspecialchars($proposal['action_proposal'])); ?>
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
