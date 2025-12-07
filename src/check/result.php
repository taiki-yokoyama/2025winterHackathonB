<?php
/**
 * 評価閲覧ページ
 * 
 * 要件 4.5, 5.5, 7.1, 7.2, 7.3, 7.4, 7.5:
 * - 現在のユーザーIDを取得
 * - データベースから評価を取得
 * - サマリースコアを計算
 * - 評価データを表示用に整形
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
require_once __DIR__ . '/../security_functions.php';

// セキュアなセッション開始（要件 9.5）
startSecureSession();

// セッション検証（要件 9.5）
if (!validateSession()) {
    // セッションがない場合はログインページにリダイレクト
    error_log("Unauthorized access attempt to result.php - invalid session");
    header('Location: ../auth/login.php');
    exit;
}

// エラーハンドリング
$errorMessage = null;
$evaluations = [];
$summaryScores = [
    'code_average' => null,
    'personality_average' => null,
    'code_count' => 0,
    'personality_count' => 0
];

try {
    // データベース接続ファイルをインクルード
    require_once __DIR__ . '/../dbconnect.php';
    require_once __DIR__ . '/../evaluation_functions.php';
    
    // データベース接続の確認
    if (!isset($dbh) || !($dbh instanceof PDO)) {
        throw new Exception("Database connection not available");
    }
    
    // 現在のユーザーIDを取得
    $currentUserId = $_SESSION['user_id'];
    
    // ユーザーIDのバリデーション
    if (!is_numeric($currentUserId) || $currentUserId <= 0) {
        throw new Exception("Invalid user ID in session");
    }
    
    // データベースから評価を取得（要件 7.1）
    $evaluations = getEvaluationsByTargetUser($dbh, $currentUserId);
    
    // サマリースコアを計算（要件 7.3）
    $summaryScores = calculateSummaryScores($evaluations);
    
} catch (PDOException $e) {
    // データベースエラーのログ記録（要件 9.4）
    error_log("Database error in result.php: " . $e->getMessage() . " | User ID: " . ($currentUserId ?? 'unknown'));
    error_log("Stack trace: " . $e->getTraceAsString());
    $errorMessage = 'データベースエラーが発生しました。しばらくしてから再度お試しください。';
} catch (Exception $e) {
    // その他のエラーのログ記録（要件 9.4）
    error_log("Error in result.php: " . $e->getMessage() . " | User ID: " . ($currentUserId ?? 'unknown'));
    error_log("Stack trace: " . $e->getTraceAsString());
    $errorMessage = 'エラーが発生しました。しばらくしてから再度お試しください。';
}

// 評価が存在しない場合のメッセージ（要件 7.4）
$hasEvaluations = !empty($evaluations);

// パーセンテージ計算用のヘルパー関数
function calculatePercentage($rating) {
    if ($rating === null) {
        return 0;
    }
    return ($rating / 4.0) * 100;
}

// アバター色の配列（ランダムな色を割り当て）
$avatarColors = ['purple', 'green', 'blue', 'pink', 'yellow', 'red', 'indigo', 'orange'];

// 評価者のアバター色を取得する関数
function getAvatarColor($evaluatorId, $avatarColors) {
    return $avatarColors[$evaluatorId % count($avatarColors)];
}

// アバターの最初の文字を取得する関数
function getAvatarInitial($name) {
    if (empty($name)) {
        return '?';
    }
    // 日本語の場合は最初の1文字、英語の場合は最初の1文字を大文字で
    // XSS対策: エスケープして返す（要件 9.5）
    return escapeHtml(mb_substr($name, 0, 1));
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POCAガチャ - あなたへの評価</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Mochiy+Pop+One&family=Noto+Sans+JP:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Noto Sans JP', sans-serif;
            background-color: #f3f4f6;
            background-image: radial-gradient(#cbd5e1 2px, transparent 2px);
            background-size: 20px 20px;
        }
        .font-pop {
            font-family: 'Mochiy Pop One', sans-serif;
        }
        .shadow-hard {
            box-shadow: 6px 6px 0 #000;
        }
        .shadow-hard-sm {
            box-shadow: 3px 3px 0 #000;
        }
        .shadow-hard-active:active {
            box-shadow: none;
            transform: translate(3px, 3px);
        }
        /* ストライプ背景のアニメーション */
        @keyframes slide {
            0% { background-position: 0 0; }
            100% { background-position: 40px 40px; }
        }
        .bg-stripe-anim {
            background-image: linear-gradient(45deg, rgba(255,255,255,0.2) 25%, transparent 25%, transparent 50%, rgba(255,255,255,0.2) 50%, rgba(255,255,255,0.2) 75%, transparent 75%, transparent);
            background-size: 40px 40px;
            animation: slide 2s linear infinite;
        }
    </style>
</head>
<body class="p-4 md:p-8 min-h-screen text-gray-800">

    <div class="max-w-4xl mx-auto relative">

        <div class="flex justify-center mb-10 relative z-20">
            <div class="bg-white border-4 border-black px-8 py-3 transform -rotate-2 shadow-hard flex items-center gap-3 relative overflow-hidden">
                <div class="absolute inset-0 bg-yellow-300 opacity-20 bg-stripe-anim"></div>
                <i class="fa-solid fa-crown text-3xl text-yellow-500 drop-shadow-md"></i>
                <h1 class="text-2xl md:text-3xl font-pop text-black tracking-widest mt-1 relative z-10">あなたへの評価</h1>
            </div>
            <div class="absolute -top-4 -right-4 text-4xl transform rotate-12">✨</div>
            <div class="absolute -bottom-4 -left-4 text-4xl transform -rotate-12">🎉</div>
        </div>

        <?php if ($errorMessage): ?>
            <!-- エラーメッセージ表示 -->
            <div class="bg-red-100 border-4 border-red-500 p-6 rounded-xl shadow-[8px_8px_0_#000] mb-12">
                <div class="flex items-center">
                    <i class="fa-solid fa-exclamation-triangle text-3xl text-red-500 mr-4"></i>
                    <p class="text-red-700 font-bold"><?php echo escapeHtml($errorMessage); ?></p>
                </div>
            </div>
        <?php endif; ?>

        <div class="bg-white border-4 border-black p-6 md:p-8 rounded-xl shadow-[8px_8px_0_#000] mb-12 relative z-10">
            <div class="absolute -top-5 left-8">
                <span class="bg-blue-500 text-white font-pop text-lg px-4 py-1 border-4 border-black shadow-hard transform -rotate-2 inline-block">
                    <i class="fa-solid fa-chart-line mr-2"></i>今週のサマリー
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                
                <div class="bg-pink-50 border-4 border-black rounded-lg p-5 shadow-hard-sm relative overflow-hidden group hover:-translate-y-1 transition-transform">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-lg flex items-center">
                            <div class="w-8 h-8 bg-black text-white rounded flex items-center justify-center mr-2">
                                <i class="fa-solid fa-code text-sm"></i>
                            </div>
                            コード面
                        </h3>
                        <span class="text-xs font-bold text-gray-500 bg-white border-2 border-black px-2 py-0.5 rounded-full">MAX 4.0</span>
                    </div>
                    <div class="flex items-baseline mb-2">
                        <?php if ($summaryScores['code_average'] !== null): ?>
                            <span class="text-5xl font-black text-pink-500 drop-shadow-[2px_2px_0_#fff]" style="-webkit-text-stroke: 1px black;">
                                <?php echo number_format($summaryScores['code_average'], 1); ?>
                            </span>
                            <span class="text-sm font-bold ml-2 text-gray-600">/ 4.0</span>
                        <?php else: ?>
                            <span class="text-3xl font-black text-gray-400">評価なし</span>
                        <?php endif; ?>
                    </div>
                    <div class="w-full bg-white border-2 border-black h-4 rounded-full overflow-hidden">
                        <div class="bg-pink-400 h-full border-r-2 border-black" 
                             style="width: <?php echo calculatePercentage($summaryScores['code_average']); ?>%"></div>
                    </div>
                </div>

                <div class="bg-blue-50 border-4 border-black rounded-lg p-5 shadow-hard-sm relative overflow-hidden group hover:-translate-y-1 transition-transform">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-lg flex items-center">
                            <div class="w-8 h-8 bg-black text-white rounded flex items-center justify-center mr-2">
                                <i class="fa-solid fa-smile text-sm"></i>
                            </div>
                            人格面
                        </h3>
                        <span class="text-xs font-bold text-gray-500 bg-white border-2 border-black px-2 py-0.5 rounded-full">MAX 4.0</span>
                    </div>
                    <div class="flex items-baseline mb-2">
                        <?php if ($summaryScores['personality_average'] !== null): ?>
                            <span class="text-5xl font-black text-blue-500 drop-shadow-[2px_2px_0_#fff]" style="-webkit-text-stroke: 1px black;">
                                <?php echo number_format($summaryScores['personality_average'], 1); ?>
                            </span>
                            <span class="text-sm font-bold ml-2 text-gray-600">/ 4.0</span>
                        <?php else: ?>
                            <span class="text-3xl font-black text-gray-400">評価なし</span>
                        <?php endif; ?>
                    </div>
                    <div class="w-full bg-white border-2 border-black h-4 rounded-full overflow-hidden">
                        <div class="bg-blue-400 h-full border-r-2 border-black" 
                             style="width: <?php echo calculatePercentage($summaryScores['personality_average']); ?>%"></div>
                    </div>
                </div>

            </div>
        </div>

        <div class="space-y-8 mb-16">
            <h2 class="text-center font-pop text-2xl mb-6 relative inline-block w-full">
                <span class="relative z-10 bg-yellow-300 px-4 py-1 border-4 border-black transform rotate-1 inline-block shadow-hard">メンバーからの評価</span>
                <div class="absolute top-1/2 left-0 w-full h-1 bg-black -z-0"></div>
            </h2>

            <?php if (!$hasEvaluations): ?>
                <!-- 評価が存在しない場合のメッセージ（要件 7.4） -->
                <div class="bg-white border-4 border-black p-8 rounded-xl shadow-[6px_6px_0_#000] text-center">
                    <i class="fa-solid fa-inbox text-6xl text-gray-300 mb-4"></i>
                    <p class="text-xl font-bold text-gray-600 mb-2">まだ評価が届いていません</p>
                    <p class="text-gray-500">チームメンバーからの評価をお待ちください</p>
                </div>
            <?php else: ?>
                <!-- 評価一覧を表示（要件 7.2, 7.5: タイムスタンプ順に最新のものを最初に） -->
                <?php foreach ($evaluations as $index => $evaluation): ?>
                    <?php 
                        $avatarColor = getAvatarColor($evaluation['evaluator_id'], $avatarColors);
                        $avatarInitial = getAvatarInitial($evaluation['evaluator_name']);
                        $hasCodeComment = !empty($evaluation['code_comment']);
                        $hasPersonalityComment = !empty($evaluation['personality_comment']);
                        
                        // XSS対策: 全ての出力をエスケープ（要件 9.5）
                        $evaluatorName = escapeHtml($evaluation['evaluator_name']);
                        $codeRating = escapeHtml($evaluation['code_rating']);
                        $personalityRating = escapeHtml($evaluation['personality_rating']);
                        $codeComment = escapeHtml($evaluation['code_comment']);
                        $personalityComment = escapeHtml($evaluation['personality_comment']);
                        $createdAt = escapeHtml(date('Y/m/d H:i', strtotime($evaluation['created_at'])));
                    ?>
                    <div class="bg-white border-4 border-black p-6 rounded-xl shadow-[6px_6px_0_#000] transition-transform duration-300 relative">
                        <?php if ($index === 0): ?>
                            <!-- 最新の評価にマーカーを表示 -->
                            <div class="absolute -top-3 left-1/2 -translate-x-1/2 w-32 h-8 bg-red-400 opacity-80 border-2 border-black transform -rotate-1 z-20"></div>
                        <?php endif; ?>

                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 pb-4 border-b-2 border-dashed border-gray-300">
                            <div class="flex items-center mb-3 sm:mb-0">
                                <div class="w-12 h-12 rounded-full border-2 border-black bg-<?php echo $avatarColor; ?>-400 flex items-center justify-center text-white font-pop text-xl mr-3 shadow-[2px_2px_0_#000]">
                                    <?php echo $avatarInitial; ?>
                                </div>
                                <div>
                                    <div class="font-bold text-lg"><?php echo $evaluatorName; ?></div>
                                    <div class="text-xs text-gray-500 font-bold bg-gray-100 border border-black inline-block px-1">からの評価</div>
                                    <div class="text-xs text-gray-400 mt-1">
                                        <?php echo $createdAt; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="flex gap-3">
                                <div class="flex items-center bg-pink-100 border-2 border-black px-3 py-1 rounded shadow-sm">
                                    <i class="fa-solid fa-code mr-2 text-xs"></i>
                                    <span class="font-black text-lg"><?php echo $codeRating; ?></span>
                                </div>
                                <div class="flex items-center bg-blue-100 border-2 border-black px-3 py-1 rounded shadow-sm">
                                    <i class="fa-solid fa-smile mr-2 text-xs"></i>
                                    <span class="font-black text-lg"><?php echo $personalityRating; ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <?php if ($hasCodeComment): ?>
                                <div class="bg-pink-50 border-2 border-pink-200 rounded-lg p-4 relative">
                                    <div class="absolute -top-2 left-3 bg-pink-500 text-white text-[10px] font-bold px-2 py-0.5 rounded border border-black">
                                        コード面
                                    </div>
                                    <p class="text-gray-700 font-medium mt-1"><?php echo nl2br($codeComment); ?></p>
                                </div>
                            <?php endif; ?>

                            <?php if ($hasPersonalityComment): ?>
                                <div class="bg-blue-50 border-2 border-blue-200 rounded-lg p-4 relative">
                                    <div class="absolute -top-2 left-3 bg-blue-500 text-white text-[10px] font-bold px-2 py-0.5 rounded border border-black">
                                        人格面
                                    </div>
                                    <p class="text-gray-700 font-medium mt-1"><?php echo nl2br($personalityComment); ?></p>
                                </div>
                            <?php endif; ?>

                            <?php if (!$hasCodeComment && !$hasPersonalityComment): ?>
                                <div class="text-center text-gray-400 py-4">
                                    <i class="fa-solid fa-comment-slash mr-2"></i>
                                    コメントはありません
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>

        <div class="text-center pb-12">
            <a href="/action/index.php" class="inline-flex flex-col items-center group">
                <div class="relative">
                    <button class="bg-[#00FFFF] border-4 border-black px-12 py-4 rounded-full font-pop text-2xl shadow-hard group-hover:translate-y-1 group-hover:shadow-none transition-all duration-200 relative overflow-hidden">
                        <span class="relative z-10 text-black">Actionを決める！</span>
                        <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity"></div>
                    </button>
                    <div class="absolute -top-10 -right-10 bg-yellow-300 border-2 border-black px-3 py-1 rounded-lg text-xs font-bold transform rotate-12 animate-bounce">
                        Next Stage!
                    </div>
                </div>
                <span class="mt-4 font-bold text-gray-500 border-b-2 border-gray-400 group-hover:text-black group-hover:border-black transition-colors">
                    PDCAサイクルを回そう <i class="fa-solid fa-rotate-right ml-1"></i>
                </span>
            </a>
        </div>

    </div>
</body>
</html>