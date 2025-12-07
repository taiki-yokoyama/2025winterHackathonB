<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Mochiy+Pop+One&family=Yomogi&display=swap" rel="stylesheet">
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Bangers&display=swap');
    .toy-title { font-family: 'Bangers', cursive; letter-spacing: 2px; }
    </style>
</head>

<body>

    <?php 
    /**
     * アクションページ
     * 
     * 要件 9.4: エラーハンドリング
     * - try-catchブロック
     * - エラーログ記録
     * - ユーザーフレンドリーなエラーメッセージ
     */
    
    // エラーレポートを設定（本番環境では無効化すべき）
    error_reporting(E_ALL);
    ini_set('display_errors', 0); // エラーを画面に表示しない
    ini_set('log_errors', 1); // エラーをログに記録
    
    // セキュリティ関数をインクルード（要件 9.5）
    require_once '../security_functions.php';
    
    // セキュアなセッション開始（要件 9.5）
    startSecureSession();
    
    include '../components/header.php';
    
    // エラーメッセージ
    $errorMessage = null;
    $actionProposals = [];
    
    try {
        require_once '../evaluation_functions.php';
        require_once '../dbconnect.php';
        
        // データベース接続の確認
        if (!isset($dbh) || !($dbh instanceof PDO)) {
            throw new Exception("Database connection not available");
        }
        
        // 現在のユーザーIDを取得
        $currentUserId = $_SESSION['user_id'] ?? null;
        
        // ユーザーIDのバリデーション
        if ($currentUserId && (!is_numeric($currentUserId) || $currentUserId <= 0)) {
            error_log("Invalid user ID in session for action page: " . $currentUserId);
            $currentUserId = null;
        }
        
        // アクション提案を取得
        if ($currentUserId) {
            try {
                $actionProposals = getActionProposalsByTargetUser($dbh, $currentUserId);
            } catch (PDOException $e) {
                error_log("Database error fetching action proposals: " . $e->getMessage() . " | User ID: " . $currentUserId);
                error_log("Stack trace: " . $e->getTraceAsString());
                $errorMessage = 'アクション提案の取得中にエラーが発生しました。';
                $actionProposals = [];
            }
        }
        
    } catch (PDOException $e) {
        // データベースエラーのログ記録（要件 9.4）
        error_log("Database error in action/index.php: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        $errorMessage = 'データベースエラーが発生しました。しばらくしてから再度お試しください。';
    } catch (Exception $e) {
        // その他のエラーのログ記録（要件 9.4）
        error_log("Error in action/index.php: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        $errorMessage = 'エラーが発生しました。しばらくしてから再度お試しください。';
    }
    
    // 日付フォーマット関数
    function formatDate($datetime) {
        $date = new DateTime($datetime);
        // XSS対策: エスケープして返す（要件 9.5）
        return escapeHtml($date->format('Y/m/d'));
    }
    
    // アバター表示用の最初の文字を取得
    function getFirstChar($name) {
        if (empty($name)) return '?';
        // XSS対策: エスケープして返す（要件 9.5）
        return escapeHtml(mb_substr($name, 0, 1));
    }
    
    // ランダムなグラデーション色を生成（一貫性のため、ユーザーIDベース）
    function getGradientColor($userId) {
        $gradients = [
            'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
            'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
            'linear-gradient(135deg, #4ade80 0%, #22c55e 100%)',
            'linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%)',
            'linear-gradient(135deg, #06b6d4 0%, #0891b2 100%)',
            'linear-gradient(135deg, #ec4899 0%, #db2777 100%)',
        ];
        return $gradients[$userId % count($gradients)];
    }
    ?>

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

    <?php if ($errorMessage): ?>
        <!-- エラーメッセージ表示（要件 9.4） -->
        <div class="max-w-4xl mx-auto mb-6">
            <div class="bg-red-100 border-4 border-red-500 p-4 rounded-lg shadow-[4px_4px_0_#000]">
                <div class="flex items-center">
                    <i class="fa-solid fa-exclamation-triangle text-2xl text-red-500 mr-3"></i>
                    <p class="text-red-700 font-bold"><?php echo escapeHtml($errorMessage); ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="max-w-4xl mx-auto">
        <h3 class="text-2xl font-black text-gray-800 mb-6 toy-title">あなたへのActionの提案</h3>
        
        <div class="space-y-6">
            <?php if (empty($actionProposals)): ?>
                <!-- アクション提案がない場合 -->
                <div class="bg-white rounded-3xl p-8 border-6 border-gray-300 shadow-[8px_8px_0_#000] text-center">
                    <div class="text-6xl mb-4">📭</div>
                    <h4 class="font-black text-xl text-gray-600 mb-2">まだアクション提案がありません</h4>
                    <p class="text-gray-500">チームメンバーからの提案が届くとここに表示されます</p>
                </div>
            <?php else: ?>
                <!-- アクション提案を表示 -->
                <?php foreach ($actionProposals as $proposal): 
                    // XSS対策: 全ての出力をエスケープ（要件 9.5）
                    $evaluatorIcon = escapeHtml($proposal['evaluator_icon'] ?? '');
                    $evaluatorName = escapeHtml($proposal['evaluator_name'] ?? '匿名');
                    $actionProposal = escapeHtml($proposal['action_proposal']);
                    $evaluatorId = (int)$proposal['evaluator_id'];
                ?>
                    <div class="bg-white rounded-3xl p-6 border-6 border-yellow-400 shadow-[8px_8px_0_#000] transform hover:translate-y-1 hover:shadow-[4px_4px_0_#000] transition-all">
                        <div class="flex items-start gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-3">
                                    <?php if (!empty($proposal['evaluator_icon'])): ?>
                                        <!-- アイコン画像がある場合 -->
                                        <img src="<?php echo $evaluatorIcon; ?>" 
                                             alt="<?php echo $evaluatorName; ?>" 
                                             class="w-12 h-12 rounded-full border-4 border-black object-cover shadow-lg">
                                    <?php else: ?>
                                        <!-- アイコン画像がない場合は名前の最初の文字を表示 -->
                                        <div class="w-12 h-12 rounded-full border-4 border-black flex items-center justify-center font-black text-xl shadow-lg" 
                                             style="background: <?php echo getGradientColor($evaluatorId); ?>; color: white;">
                                            <?php echo getFirstChar($proposal['evaluator_name']); ?>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <h4 class="font-black text-lg text-gray-800">
                                            <?php echo $evaluatorName; ?>
                                        </h4>
                                        <p class="text-xs text-gray-500 flex items-center">
                                            <i class="fa-regular fa-calendar mr-1"></i> 
                                            <?php echo formatDate($proposal['created_at']); ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="bg-yellow-100 rounded-2xl p-4 border-4 border-yellow-300 shadow-inner">
                                    <p class="text-gray-800 font-bold flex items-start gap-2">
                                        <i class="fa-solid fa-lightbulb text-yellow-600 mt-1"></i> 
                                        <?php echo nl2br($actionProposal); ?>
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
                <a href="?page=plan" class="inline-block bg-red-500 hover:bg-red-600 text-white font-black py-4 px-8 rounded-2xl shadow-[6px_6px_0_#000] border-4 border-black transform hover:translate-y-1 hover:shadow-[3px_3px_0_#000] transition-all text-lg uppercase toy-title">
                    <i class="fa-solid fa-file-lines mr-2"></i> プラン作成へGO!
                </a>
            </div>
        </div>
    </div>
    
</body>
</html>
