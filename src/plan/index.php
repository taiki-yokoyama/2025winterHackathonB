<?php
session_start();
require_once '../dbconnect.php';

// ユーザー情報（仮）
$current_user = [
    'id' => 1,
    'name' => 'ちーちゃん',
    'avatar' => 'ち',
    'avatar_color' => 'bg-orange-400'
];

// POSTリクエスト処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['plan_content'])) {
    $plan_content = trim($_POST['plan_content']);
    $user_id = $current_user['id'];
    
    if (!empty($plan_content)) {
        $stmt = $dbh->prepare("INSERT INTO plans (user_id, content, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$user_id, $plan_content]);
        header('Location: index.php');
        exit;
    }
}

// 自分の最新の計画を取得
$stmt = $dbh->prepare("SELECT * FROM plans WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
$stmt->execute([$current_user['id']]);
$my_plan = $stmt->fetch(PDO::FETCH_ASSOC);

// チームメンバーの計画を取得（自分以外）
$stmt = $dbh->prepare("SELECT p.*, u.name, u.avatar, u.avatar_color FROM plans p JOIN users u ON p.user_id = u.id WHERE p.user_id != ? ORDER BY p.created_at DESC");
$stmt->execute([$current_user['id']]);
$team_plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plan - 計画フェーズ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <!-- ヘッダー -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <div class="flex items-center space-x-3 mb-2">
                <div class="p-2 bg-red-100 rounded-lg text-red-500">
                    <i class="fa-regular fa-file-lines text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Plan - 計画フェーズ</h2>
                    <p class="text-sm text-gray-500">次のサイクルに向けた計画を立てましょう。計画はチーム全員に共有されます。</p>
                </div>
            </div>
        </div>

        <!-- メインコンテンツ -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- 左側：あなたの計画 -->
            <div class="bg-white rounded-xl shadow-sm p-6 h-full flex flex-col">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 rounded-full <?php echo $current_user['avatar_color']; ?> text-white flex items-center justify-center font-bold mr-3">
                        <?php echo htmlspecialchars($current_user['avatar']); ?>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800">あなたの計画</h3>
                        <p class="text-xs text-gray-400">チーム全員に公開されます</p>
                    </div>
                </div>

                <form action="" method="POST" class="flex-grow flex flex-col">
                    <label class="text-sm text-gray-600 mb-2">次にやること</label>
                    <textarea 
                        name="plan_content" 
                        id="planContent"
                        class="w-full flex-grow p-4 border border-gray-200 rounded-lg resize-none focus:outline-none focus:ring-2 focus:ring-red-400 bg-gray-50" 
                        rows="8" 
                        placeholder="例：フレンチトースト作る"
                        oninput="updateCharCount()"
                    ><?php echo $my_plan ? htmlspecialchars($my_plan['content']) : ''; ?></textarea>
                    <div class="text-right text-xs text-gray-400 mt-1 mb-4">
                        <span id="charCount">0</span>文字
                    </div>
                    <button 
                        type="submit" 
                        class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-3 rounded-lg shadow-md transition duration-200"
                    >
                        <i class="fa-regular fa-paper-plane mr-2"></i> 計画を保存 & 共有
                    </button>
                </form>

                <div class="mt-4 bg-yellow-50 border border-yellow-100 rounded-lg p-3 flex items-center text-sm text-gray-600">
                    <i class="fa-regular fa-circle-check text-green-500 mr-2"></i>
                    Check → Action → Plan を完了すると、コインが1枚もらえます！
                </div>
            </div>

            <!-- 右側：チームメンバーの計画 -->
            <div class="bg-white rounded-xl shadow-sm p-6 h-full">
                <div class="flex items-center mb-4">
                    <i class="fa-regular fa-file-lines text-red-500 mr-2"></i>
                    <h3 class="font-bold text-gray-800">チームメンバーの計画</h3>
                </div>

                <?php if (empty($team_plans)): ?>
                <div class="bg-orange-50 rounded-lg p-6 border border-orange-100 flex flex-col items-center justify-center h-64">
                    <div class="w-full flex items-start mb-2">
                        <div class="w-8 h-8 rounded-full bg-orange-400 text-white flex items-center justify-center font-bold mr-3">ぼ</div>
                        <span class="font-bold text-gray-700">ぼーちゃん</span>
                    </div>
                    <p class="text-gray-400 text-sm">まだ計画が登録されていません</p>
                </div>
                <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($team_plans as $plan): ?>
                    <div class="bg-orange-50 rounded-lg p-4 border border-orange-100">
                        <div class="flex items-start mb-2">
                            <div class="w-8 h-8 rounded-full <?php echo htmlspecialchars($plan['avatar_color']); ?> text-white flex items-center justify-center font-bold mr-3">
                                <?php echo htmlspecialchars($plan['avatar']); ?>
                            </div>
                            <div class="flex-grow">
                                <span class="font-bold text-gray-700"><?php echo htmlspecialchars($plan['name']); ?></span>
                                <p class="text-gray-700 mt-2"><?php echo nl2br(htmlspecialchars($plan['content'])); ?></p>
                                <p class="text-xs text-gray-400 mt-2"><?php echo date('Y/m/d H:i', strtotime($plan['created_at'])); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function updateCharCount() {
            const textarea = document.getElementById('planContent');
            const charCount = document.getElementById('charCount');
            charCount.textContent = textarea.value.length;
        }
        
        // ページ読み込み時に文字数を更新
        window.addEventListener('DOMContentLoaded', updateCharCount);
    </script>
</body>
</html>
