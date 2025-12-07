<?php
/**
 * 評価入力ページ
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
    // セッションが無効な場合はログインページにリダイレクト
    if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (strpos($contentType, 'application/json') !== false || isset($_GET['action'])) {
            // API リクエストの場合はJSONエラーを返す
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'message' => 'セッションが無効です。再度ログインしてください。'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }
    header('Location: ../auth/login.php');
    exit;
}

// データベース接続と関数をインクルード
require_once __DIR__ . '/../dbconnect.php';
require_once __DIR__ . '/../evaluation_functions.php';

// JSONレスポンスを返すヘルパー関数
function sendJsonResponse($success, $message = '', $data = [], $errors = []) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data,
        'errors' => $errors
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// POSTリクエストの処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Content-Typeをチェック（JSON APIリクエストの場合）
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    
    if (strpos($contentType, 'application/json') !== false) {
        // JSON形式のリクエストを処理
        $jsonInput = file_get_contents('php://input');
        $postData = json_decode($jsonInput, true);
        
        if ($postData === null) {
            sendJsonResponse(false, 'Invalid JSON format');
        }
    } else {
        // 通常のフォームデータ
        $postData = $_POST;
    }
    
    // CSRF トークンの検証（要件 9.5）
    $csrfToken = $postData['csrf_token'] ?? '';
    if (!validateCsrfToken($csrfToken)) {
        error_log("CSRF token validation failed in check.php POST | User ID: " . ($_SESSION['user_id'] ?? 'unknown'));
        sendJsonResponse(false, 'セキュリティトークンが無効です。ページを再読み込みしてください。');
    }
    
    // レート制限チェック（要件 9.5）
    $action = $postData['action'] ?? '';
    if (!checkRateLimit('check_post_' . $action, 20, 60)) {
        error_log("Rate limit exceeded in check.php POST | Action: $action | User ID: " . ($_SESSION['user_id'] ?? 'unknown'));
        sendJsonResponse(false, 'リクエストが多すぎます。しばらく待ってから再度お試しください。');
    }
    
    try {
        // チーム計画の送信処理
        if ($action === 'submit_team_plan') {
            // セッションチェック
            if (!isset($_SESSION['user_id'])) {
                sendJsonResponse(false, 'ログインが必要です');
            }
            
            $userId = $_SESSION['user_id'];
            $planText = $postData['plan_text'] ?? '';
            
            // バリデーション: 空入力チェック（要件 1.4）
            if (!validateNotEmpty($planText)) {
                sendJsonResponse(false, 'チーム計画を入力してください', [], ['plan_text' => 'チーム計画を入力してください']);
            }
            
            // データベースに保存（要件 1.1）
            $planId = saveTeamPlan($dbh, $userId, $planText);
            
            if ($planId === false) {
                // データベースエラー（要件 9.4）
                error_log("Failed to save team plan | User ID: $userId | Plan text length: " . strlen($planText));
                sendJsonResponse(false, 'データベースエラーが発生しました。しばらくしてから再度お試しください。');
            }
            
            // 成功レスポンス（要件 1.5）
            sendJsonResponse(true, 'チーム計画を送信しました', ['plan_id' => $planId]);
        }
        
        // チャットメッセージの送信処理（要件 2.1, 2.4）
        if ($action === 'send_chat_message') {
            // セッションチェック
            if (!isset($_SESSION['user_id'])) {
                sendJsonResponse(false, 'ログインが必要です');
            }
            
            $userId = $_SESSION['user_id'];
            $message = $postData['message'] ?? '';
            
            // バリデーション: 空入力チェック（要件 2.4）
            if (!validateNotEmpty($message)) {
                sendJsonResponse(false, 'メッセージを入力してください', [], ['message' => 'メッセージを入力してください']);
            }
            
            // データベースに保存（要件 2.1）
            $messageId = saveChatMessage($dbh, $userId, $message);
            
            if ($messageId === false) {
                // データベースエラー（要件 9.4）
                error_log("Failed to save chat message | User ID: $userId | Message length: " . strlen($message));
                sendJsonResponse(false, 'データベースエラーが発生しました。しばらくしてから再度お試しください。');
            }
            
            // 成功レスポンス（要件 2.2）
            sendJsonResponse(true, 'メッセージを送信しました', ['message_id' => $messageId]);
        }
        
        // 個人評価の送信処理（要件 3.4, 4.4, 5.4, 6.2, 8.1, 8.2）
        if ($action === 'submit_evaluation') {
            // セッションチェック
            if (!isset($_SESSION['user_id'])) {
                sendJsonResponse(false, 'ログインが必要です');
            }
            
            $evaluatorId = $_SESSION['user_id'];
            
            // フォームデータを取得
            $formData = [
                'target_user_id' => $postData['target_user_id'] ?? null,
                'code_rating' => $postData['code_rating'] ?? null,
                'code_comment' => $postData['code_comment'] ?? null,
                'personality_rating' => $postData['personality_rating'] ?? null,
                'personality_comment' => $postData['personality_comment'] ?? null,
                'action_proposal' => $postData['action_proposal'] ?? null
            ];
            
            // 全フィールドのバリデーション実行（要件 8.1）
            $validation = validateEvaluationForm($formData);
            
            if (!$validation['valid']) {
                // バリデーションエラー（要件 8.1）
                sendJsonResponse(false, 'フォームに不備があります', [], $validation['errors']);
            }
            
            // データベースに単一レコードとして保存（要件 8.2）
            $evaluationId = saveEvaluation(
                $dbh,
                $evaluatorId,
                (int)$formData['target_user_id'],
                (int)$formData['code_rating'],
                $formData['code_comment'],
                (int)$formData['personality_rating'],
                $formData['personality_comment'],
                $formData['action_proposal']
            );
            
            if ($evaluationId === false) {
                // データベースエラー（要件 9.4）
                error_log("Failed to save evaluation | Evaluator ID: $evaluatorId | Target User ID: " . $formData['target_user_id'] . " | Code Rating: " . $formData['code_rating'] . " | Personality Rating: " . $formData['personality_rating']);
                sendJsonResponse(false, 'データベースエラーが発生しました。しばらくしてから再度お試しください。');
            }
            
            // 成功レスポンス（要件 8.3）
            sendJsonResponse(true, '評価を送信しました', ['evaluation_id' => $evaluationId]);
        }
        
    } catch (PDOException $e) {
        // データベースエラーのログ記録（要件 9.4）
        error_log("Database error in check.php POST | Action: " . ($action ?? 'unknown') . " | Error: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        sendJsonResponse(false, 'データベースエラーが発生しました。しばらくしてから再度お試しください。');
    } catch (Exception $e) {
        // その他のエラー
        error_log("Error in check.php POST | Action: " . ($action ?? 'unknown') . " | Error: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        sendJsonResponse(false, 'エラーが発生しました。しばらくしてから再度お試しください。');
    }
}

// GETリクエストの処理
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // アクションタイプを取得
    $action = $_GET['action'] ?? '';
    
    try {
        // チャットメッセージの取得処理（要件 2.3）
        if ($action === 'get_chat_messages') {
            // セッションチェック
            if (!isset($_SESSION['user_id'])) {
                sendJsonResponse(false, 'ログインが必要です');
            }
            
            // データベースから全てのチャットメッセージを取得（要件 2.3）
            $messages = getChatMessages($dbh);
            
            // 成功レスポンス
            sendJsonResponse(true, '', ['messages' => $messages]);
        }
        
    } catch (PDOException $e) {
        // データベースエラーのログ記録（要件 9.4）
        error_log("Database error in check.php GET | Action: " . ($action ?? 'unknown') . " | Error: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        sendJsonResponse(false, 'データベースエラーが発生しました。しばらくしてから再度お試しください。');
    } catch (Exception $e) {
        // その他のエラー
        error_log("Error in check.php GET | Action: " . ($action ?? 'unknown') . " | Error: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        sendJsonResponse(false, 'エラーが発生しました。しばらくしてから再度お試しください。');
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POCAガチャ - 評価</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Noto Sans JP', sans-serif;
            background-color: #f3f4f6;
            background-image: radial-gradient(#cbd5e1 2px, transparent 2px);
            background-size: 20px 20px;
        }
        /* カスタムシャドウ */
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
        /* タブのアクティブスタイル（要件 10.4） */
        .tab-active {
            background-color: #FFD700; /* Yellow */
            color: black;
            font-weight: bold;
            transform: translateY(-4px);
            box-shadow: 4px 4px 0 #000;
            z-index: 10;
            transition: all 0.2s ease-out;
        }
        .tab-inactive {
            background-color: #e5e7eb;
            color: #6b7280;
            box-shadow: inset 2px 2px 0 rgba(0,0,0,0.1);
            transition: all 0.2s ease-out;
        }
        .tab-inactive:hover {
            background-color: #d1d5db;
            transform: translateY(-2px);
            box-shadow: 2px 2px 0 rgba(0,0,0,0.2);
        }
        /* タブコンテンツのアニメーション（要件 10.2, 10.3） */
        .tab-content-enter {
            animation: fadeSlideIn 0.3s ease-out;
        }
        .tab-content-exit {
            animation: fadeSlideOut 0.2s ease-in;
        }
        @keyframes fadeSlideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes fadeSlideOut {
            from {
                opacity: 1;
                transform: translateY(0);
            }
            to {
                opacity: 0;
                transform: translateY(-10px);
            }
        }
        /* フォームデータ保存インジケーター */
        .data-saved-indicator {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #10b981;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            border: 2px solid #000;
            box-shadow: 3px 3px 0 #000;
            font-weight: bold;
            font-size: 14px;
            z-index: 1000;
            animation: slideInRight 0.3s ease-out;
        }
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        @keyframes fadeOut {
            from {
                opacity: 1;
            }
            to {
                opacity: 0;
            }
        }
        /* スクロールバー装飾 */
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-left: 1px solid #ddd;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #999;
        }
    </style>
</head>
<body class="p-4 md:p-8 min-h-screen text-gray-800">

    <?php include '../components/header.php'; ?>

    <div class="max-w-5xl mx-auto relative">
        
        <?php
        // 現在のユーザー情報を取得
        $currentUserId = $_SESSION['user_id'] ?? 0;
        $stmt = $dbh->prepare('SELECT yokomoku, tatemoku, current_mode FROM users WHERE id = ?');
        $stmt->execute([$currentUserId]);
        $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);
        $currentMode = $currentUser['current_mode'] ?? 'yokomoku';
        $teamValue = $currentMode === 'yokomoku' ? $currentUser['yokomoku'] : $currentUser['tatemoku'];
        
        // 同じチームのメンバーを取得
        $stmt = $dbh->prepare('SELECT id, name, icon, generation, yokomoku, tatemoku FROM users WHERE id != ? AND ' . $currentMode . ' = ? ORDER BY name');
        $stmt->execute([$currentUserId, $teamValue]);
        $teamMembers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
        
        <div class="bg-white border-4 border-black p-4 mb-8 shadow-hard relative z-10">
            <h2 class="font-bold text-sm mb-3 bg-gray-200 inline-block px-3 py-1 border-2 border-black rounded">メンバー一覧（<?php echo htmlspecialchars($teamValue); ?>）</h2>
            <div class="flex justify-start gap-4 items-center overflow-x-auto pb-2">
                <?php foreach($teamMembers as $member): ?>
                <div onclick="openMemberModal(<?php echo $member['id']; ?>, '<?php echo htmlspecialchars($member['name'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($member['icon'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($member['generation'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($member['yokomoku'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($member['tatemoku'], ENT_QUOTES); ?>')" class="w-14 h-14 rounded-full border-4 border-black bg-gray-200 overflow-hidden shadow-sm flex-shrink-0 cursor-pointer hover:scale-110 transition-transform flex items-center justify-center" title="<?php echo htmlspecialchars($member['name']); ?>">
                    <?php if (!empty($member['icon'])): ?>
                        <img src="/assets/img/gacha_img/<?php echo htmlspecialchars($member['icon']); ?>" alt="<?php echo htmlspecialchars($member['name']); ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                        <span class="text-3xl">👤</span>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- タブボタン（要件 10.1, 10.4） -->
        <div class="flex space-x-2 pl-4 relative z-20 top-1">
            <button onclick="switchTab('team')" id="btn-team" class="w-40 py-3 border-4 border-b-0 border-black rounded-t-xl text-lg transition-all duration-200 tab-active hover:brightness-95" aria-label="チームタブ" aria-selected="true">
                <i class="fa-solid fa-users mr-2"></i>チーム
            </button>
            <button onclick="switchTab('personal')" id="btn-personal" class="w-40 py-3 border-4 border-b-0 border-black rounded-t-xl text-lg transition-all duration-200 tab-inactive bg-gray-200 hover:bg-gray-300" aria-label="個人タブ" aria-selected="false">
                <i class="fa-solid fa-user mr-2"></i>個人
            </button>
        </div>

        <div class="bg-white border-4 border-black p-6 md:p-8 shadow-[8px_8px_0_#000] relative min-h-[600px]">
            
            <div id="tab-content-team" class="block">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    
                    <div class="space-y-2">
                        <div class="bg-yellow-50 border-4 border-black p-5 rounded-lg shadow-hard-sm relative h-full">
                            <div class="flex justify-between items-center mb-4">
                                <label class="font-bold text-lg border-b-4 border-yellow-400 inline-block">チームの今後の計画</label>
                                <button class="text-xs font-bold text-black border-2 border-black px-3 py-1 bg-white hover:bg-black hover:text-white transition-colors shadow-sm">
                                    <i class="fa-solid fa-pen mr-1"></i>編集
                                </button>
                            </div>
                            <textarea id="team-plan-textarea" class="w-full h-80 bg-white border-2 border-black border-dashed rounded p-4 outline-none text-base resize-none focus:bg-yellow-100 transition-colors leading-relaxed" placeholder="ここをクリックして計画を入力してください...&#13;&#10;例：&#13;&#10;・来週までにプロトタイプ完成&#13;&#10;・DB設計の見直し"></textarea>
                            <div id="team-plan-error" class="text-red-600 text-sm font-bold mt-2 hidden"></div>
                            <div id="team-plan-success" class="text-green-600 text-sm font-bold mt-2 hidden"></div>
                            <button id="team-plan-submit-btn" class="w-full mt-4 py-3 bg-[#FFD700] border-2 border-black rounded font-bold text-base shadow-[3px_3px_0_#000] hover:bg-yellow-300 active:shadow-none active:translate-y-1 transition-all flex items-center justify-center">
                                <i class="fa-solid fa-paper-plane mr-2"></i>送信
                            </button>
                        </div>
                    </div>

                    <div class="bg-blue-50 border-4 border-black p-5 rounded-lg shadow-hard-sm relative flex flex-col h-[500px] lg:h-auto">
                        <label class="font-bold text-lg border-b-4 border-blue-400 inline-block mb-4 self-start bg-white px-2">チーム全体への意見</label>
                        
                        <div id="chat-messages-container" class="flex-1 overflow-y-auto space-y-4 p-4 bg-white/50 border-2 border-black border-dashed rounded mb-4 custom-scrollbar">
                            <!-- チャットメッセージがここに動的に追加されます -->
                        </div>

                        <div class="flex gap-2">
                            <input type="text" id="chat-message-input" class="flex-1 h-12 bg-white border-2 border-black rounded p-3 outline-none text-sm focus:border-blue-500 transition-colors" placeholder="メッセージを入力...">
                            <button id="chat-send-btn" class="w-12 h-12 bg-[#00FFFF] border-2 border-black rounded flex items-center justify-center shadow-[2px_2px_0_#000] active:shadow-none active:translate-y-1 transition-all hover:bg-cyan-200">
                                <i class="fa-solid fa-paper-plane text-lg"></i>
                            </button>
                        </div>
                        <div id="chat-error" class="text-red-600 text-sm font-bold mt-2 hidden"></div>
                        <div id="chat-success" class="text-green-600 text-sm font-bold mt-2 hidden"></div>
                    </div>
                </div>
            </div>

            <div id="tab-content-personal" class="hidden">
                
                <form id="evaluation-form" action="" method="POST">
                    <div class="bg-gray-100 border-4 border-black p-6 rounded-lg mb-8">
                        <label class="font-bold text-lg block mb-4 border-l-4 border-black pl-3">誰に対して送信する？</label>
                        <div class="flex flex-wrap gap-6">
                            <?php 
                            // 同じチームのメンバーを取得（既に上で取得済み）
                            $availableUsers = $teamMembers;
                            
                            $colors = ['bg-pink-400', 'bg-blue-400', 'bg-green-400', 'bg-purple-400', 'bg-yellow-400', 'bg-red-400'];
                            
                            foreach($availableUsers as $index => $user): 
                                $colorClass = $colors[$index % count($colors)];
                                $userInitial = mb_substr($user['name'], 0, 1);
                                // XSS対策: 全ての出力をエスケープ（要件 9.5）
                                $userId = escapeHtml($user['id']);
                                $userName = escapeHtml($user['name']);
                                $userInitialEscaped = escapeHtml($userInitial);
                            ?>
                            <label class="cursor-pointer group relative">
                                <input type="radio" name="target_user_id" value="<?php echo $userId; ?>" class="peer hidden" <?php echo $index === 0 ? 'checked' : ''; ?>>
                                <div class="flex flex-col items-center gap-2 transition-transform hover:-translate-y-1">
                                    <div class="w-16 h-16 rounded-full border-4 border-gray-300 bg-white flex items-center justify-center text-gray-300 peer-checked:border-black peer-checked:<?php echo $colorClass; ?> peer-checked:text-white peer-checked:shadow-hard transition-all">
                                        <span class="font-black text-2xl"><?php echo $userInitialEscaped; ?></span>
                                    </div>
                                    <span class="font-bold text-sm text-gray-400 peer-checked:text-black"><?php echo $userName; ?></span>
                                </div>
                                <div class="absolute top-0 right-0 bg-black text-white rounded-full w-6 h-6 flex items-center justify-center border-2 border-white scale-0 peer-checked:scale-100 transition-transform z-10">
                                    <i class="fa-solid fa-check text-xs"></i>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                        
                        <div class="bg-yellow-50 border-4 border-black p-6 rounded-lg shadow-hard-sm flex flex-col">
                            <div class="mb-4">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="font-bold text-lg flex items-center">
                                        <i class="fa-solid fa-code mr-2"></i>コード面の評価
                                    </label>
                                    <span class="text-xs font-bold bg-white border border-black px-2 py-1 rounded">1〜4で選択</span>
                                </div>
                                <div class="grid grid-cols-4 gap-3">
                                    <?php for($i=1; $i<=4; $i++): ?>
                                    <label class="cursor-pointer w-full">
                                        <input type="radio" name="code_rating" value="<?php echo $i; ?>" class="peer hidden">
                                        <div class="w-full aspect-square bg-white border-2 border-black rounded-lg flex items-center justify-center font-black text-xl shadow-[2px_2px_0_#000] peer-checked:bg-yellow-400 peer-checked:translate-y-1 peer-checked:shadow-none transition-all hover:bg-yellow-100">
                                            <?php echo $i; ?>
                                        </div>
                                    </label>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            
                            <div class="mt-auto">
                                <label class="text-sm font-bold block mb-1 text-gray-600">コメント（任意）</label>
                                <input type="text" name="code_comment" id="code-comment" class="w-full h-12 bg-white border-2 border-black rounded px-3 outline-none text-base focus:border-yellow-500 transition-colors shadow-sm" placeholder="技術的なフィードバックを入力...">
                            </div>
                        </div>

                        <div class="bg-pink-50 border-4 border-black p-6 rounded-lg shadow-hard-sm flex flex-col">
                            <div class="mb-4">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="font-bold text-lg flex items-center">
                                        <i class="fa-solid fa-smile mr-2"></i>人格面の評価
                                    </label>
                                    <span class="text-xs font-bold bg-white border border-black px-2 py-1 rounded">1〜4で選択</span>
                                </div>
                                <div class="grid grid-cols-4 gap-3">
                                    <?php for($i=1; $i<=4; $i++): ?>
                                    <label class="cursor-pointer w-full">
                                        <input type="radio" name="personality_rating" value="<?php echo $i; ?>" class="peer hidden">
                                        <div class="w-full aspect-square bg-white border-2 border-black rounded-lg flex items-center justify-center font-black text-xl shadow-[2px_2px_0_#000] peer-checked:bg-pink-400 peer-checked:text-white peer-checked:translate-y-1 peer-checked:shadow-none transition-all hover:bg-pink-100">
                                            <?php echo $i; ?>
                                        </div>
                                    </label>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            
                            <div class="mt-auto">
                                <label class="text-sm font-bold block mb-1 text-gray-600">コメント（任意）</label>
                                <input type="text" name="personality_comment" id="personality-comment" class="w-full h-12 bg-white border-2 border-black rounded px-3 outline-none text-base focus:border-pink-500 transition-colors shadow-sm" placeholder="感謝や励ましの言葉を入力...">
                            </div>
                        </div>
                    </div>

                    <div class="bg-green-50 border-4 border-black p-6 rounded-lg shadow-hard-sm mb-8">
                        <label class="font-bold text-lg block mb-3 border-l-4 border-green-500 pl-3">次のplanの提案</label>
                        <input type="text" name="action_proposal" id="action-proposal" class="w-full h-14 bg-white border-2 border-black rounded px-4 outline-none text-base shadow-sm focus:bg-green-100 transition-colors" placeholder="次はこんなことをしてみよう！">
                    </div>

                    <div id="evaluation-error" class="text-red-600 text-sm font-bold mb-4 hidden"></div>
                    <div id="evaluation-success" class="text-green-600 text-sm font-bold mb-4 hidden"></div>

                    <div class="flex justify-end">
                        <button type="submit" id="evaluation-submit-btn" class="w-full md:w-auto md:px-12 py-4 bg-black text-white font-bold text-xl rounded-lg border-4 border-black shadow-hard hover:bg-gray-800 hover:translate-y-1 hover:shadow-none transition-all flex items-center justify-center">
                            <i class="fa-solid fa-paper-plane mr-3"></i>送 信
                        </button>
                    </div>
                </form>
            </div>

        </div>

        <div class="text-right mt-6 mb-12">
            <a href="/check/result.php" class="inline-flex items-center font-bold text-lg text-black hover:text-gray-600 border-b-2 border-black hover:border-gray-600 transition-colors">
                あなたへの評価を見る <i class="fa-solid fa-arrow-right ml-2"></i>
            </a>
        </div>

    </div>

    <script>
        // フォームデータを保持するためのグローバル変数（要件 10.5）
        let formDataStore = {
            team: {
                planText: '',
                chatMessage: ''
            },
            personal: {
                targetUserId: null,
                codeRating: null,
                codeComment: '',
                personalityRating: null,
                personalityComment: '',
                actionProposal: ''
            }
        };

        // データ保存通知を表示する関数
        function showDataSavedNotification() {
            // 既存の通知があれば削除
            const existingNotification = document.querySelector('.data-saved-indicator');
            if (existingNotification) {
                existingNotification.remove();
            }
            
            // 新しい通知を作成
            const notification = document.createElement('div');
            notification.className = 'data-saved-indicator';
            notification.innerHTML = '<i class="fa-solid fa-check-circle mr-2"></i>フォームデータを保存しました';
            document.body.appendChild(notification);
            
            // 2秒後に削除
            setTimeout(() => {
                notification.style.animation = 'fadeOut 0.3s ease-out';
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 2000);
        }

        // 現在のタブのフォームデータを保存する関数（要件 10.5）
        function saveCurrentTabData(currentTab) {
            let hasData = false;
            
            if (currentTab === 'team') {
                // チームタブのデータを保存
                const teamPlanTextarea = document.getElementById('team-plan-textarea');
                const chatMessageInput = document.getElementById('chat-message-input');
                
                if (teamPlanTextarea) {
                    formDataStore.team.planText = teamPlanTextarea.value;
                    if (teamPlanTextarea.value.trim()) hasData = true;
                }
                if (chatMessageInput) {
                    formDataStore.team.chatMessage = chatMessageInput.value;
                    if (chatMessageInput.value.trim()) hasData = true;
                }
            } else if (currentTab === 'personal') {
                // 個人タブのデータを保存
                const targetUserRadio = document.querySelector('input[name="target_user_id"]:checked');
                const codeRatingRadio = document.querySelector('input[name="code_rating"]:checked');
                const codeCommentInput = document.getElementById('code-comment');
                const personalityRatingRadio = document.querySelector('input[name="personality_rating"]:checked');
                const personalityCommentInput = document.getElementById('personality-comment');
                const actionProposalInput = document.getElementById('action-proposal');
                
                formDataStore.personal.targetUserId = targetUserRadio ? targetUserRadio.value : null;
                formDataStore.personal.codeRating = codeRatingRadio ? codeRatingRadio.value : null;
                formDataStore.personal.codeComment = codeCommentInput ? codeCommentInput.value : '';
                formDataStore.personal.personalityRating = personalityRatingRadio ? personalityRatingRadio.value : null;
                formDataStore.personal.personalityComment = personalityCommentInput ? personalityCommentInput.value : '';
                formDataStore.personal.actionProposal = actionProposalInput ? actionProposalInput.value : '';
                
                // データがあるかチェック
                if (codeRatingRadio || personalityRatingRadio || 
                    (codeCommentInput && codeCommentInput.value.trim()) ||
                    (personalityCommentInput && personalityCommentInput.value.trim()) ||
                    (actionProposalInput && actionProposalInput.value.trim())) {
                    hasData = true;
                }
            }
            
            // データがある場合のみ通知を表示
            if (hasData) {
                showDataSavedNotification();
            }
        }

        // タブのフォームデータを復元する関数（要件 10.5）
        function restoreTabData(targetTab) {
            if (targetTab === 'team') {
                // チームタブのデータを復元
                const teamPlanTextarea = document.getElementById('team-plan-textarea');
                const chatMessageInput = document.getElementById('chat-message-input');
                
                if (teamPlanTextarea && formDataStore.team.planText) {
                    teamPlanTextarea.value = formDataStore.team.planText;
                }
                if (chatMessageInput && formDataStore.team.chatMessage) {
                    chatMessageInput.value = formDataStore.team.chatMessage;
                }
            } else if (targetTab === 'personal') {
                // 個人タブのデータを復元
                if (formDataStore.personal.targetUserId) {
                    const targetUserRadio = document.querySelector(`input[name="target_user_id"][value="${formDataStore.personal.targetUserId}"]`);
                    if (targetUserRadio) {
                        targetUserRadio.checked = true;
                    }
                }
                
                if (formDataStore.personal.codeRating) {
                    const codeRatingRadio = document.querySelector(`input[name="code_rating"][value="${formDataStore.personal.codeRating}"]`);
                    if (codeRatingRadio) {
                        codeRatingRadio.checked = true;
                    }
                }
                
                const codeCommentInput = document.getElementById('code-comment');
                if (codeCommentInput && formDataStore.personal.codeComment) {
                    codeCommentInput.value = formDataStore.personal.codeComment;
                }
                
                if (formDataStore.personal.personalityRating) {
                    const personalityRatingRadio = document.querySelector(`input[name="personality_rating"][value="${formDataStore.personal.personalityRating}"]`);
                    if (personalityRatingRadio) {
                        personalityRatingRadio.checked = true;
                    }
                }
                
                const personalityCommentInput = document.getElementById('personality-comment');
                if (personalityCommentInput && formDataStore.personal.personalityComment) {
                    personalityCommentInput.value = formDataStore.personal.personalityComment;
                }
                
                const actionProposalInput = document.getElementById('action-proposal');
                if (actionProposalInput && formDataStore.personal.actionProposal) {
                    actionProposalInput.value = formDataStore.personal.actionProposal;
                }
            }
        }

        // タブ切り替え関数（要件 10.1, 10.2, 10.3, 10.4, 10.5）
        function switchTab(tabName) {
            // 現在のアクティブタブを特定
            const btnTeam = document.getElementById('btn-team');
            const btnPersonal = document.getElementById('btn-personal');
            const currentTab = btnTeam.classList.contains('tab-active') ? 'team' : 'personal';
            
            // 同じタブをクリックした場合は何もしない
            if (currentTab === tabName) {
                return;
            }
            
            // 現在のタブのフォームデータを保存（要件 10.5）
            saveCurrentTabData(currentTab);
            
            // コンテンツの切り替え
            const contentTeam = document.getElementById('tab-content-team');
            const contentPersonal = document.getElementById('tab-content-personal');

            if (tabName === 'team') {
                // チームタブに切り替え（要件 10.3）
                // タブボタンのスタイルを即座に更新（要件 10.4）
                btnTeam.classList.add('tab-active');
                btnTeam.classList.remove('tab-inactive', 'bg-gray-200');
                btnTeam.setAttribute('aria-selected', 'true');
                
                btnPersonal.classList.remove('tab-active');
                btnPersonal.classList.add('tab-inactive', 'bg-gray-200');
                btnPersonal.setAttribute('aria-selected', 'false');
                
                // 退出アニメーションを追加
                contentPersonal.classList.add('tab-content-exit');
                
                // アニメーション完了後にコンテンツを切り替え
                setTimeout(() => {
                    contentPersonal.classList.add('hidden');
                    contentPersonal.classList.remove('tab-content-exit');
                    
                    contentTeam.classList.remove('hidden');
                    contentTeam.classList.add('tab-content-enter');
                    
                    // フォームデータを復元（要件 10.5）
                    restoreTabData('team');
                    
                    // アニメーションクラスを削除（次回のアニメーションのため）
                    setTimeout(() => {
                        contentTeam.classList.remove('tab-content-enter');
                    }, 300);
                }, 200);
            } else {
                // 個人タブに切り替え（要件 10.2）
                // タブボタンのスタイルを即座に更新（要件 10.4）
                btnPersonal.classList.add('tab-active');
                btnPersonal.classList.remove('tab-inactive', 'bg-gray-200');
                btnPersonal.setAttribute('aria-selected', 'true');

                btnTeam.classList.remove('tab-active');
                btnTeam.classList.add('tab-inactive', 'bg-gray-200');
                btnTeam.setAttribute('aria-selected', 'false');
                
                // 退出アニメーションを追加
                contentTeam.classList.add('tab-content-exit');
                
                // アニメーション完了後にコンテンツを切り替え
                setTimeout(() => {
                    contentTeam.classList.add('hidden');
                    contentTeam.classList.remove('tab-content-exit');
                    
                    contentPersonal.classList.remove('hidden');
                    contentPersonal.classList.add('tab-content-enter');
                    
                    // フォームデータを復元（要件 10.5）
                    restoreTabData('personal');
                    
                    // アニメーションクラスを削除（次回のアニメーションのため）
                    setTimeout(() => {
                        contentPersonal.classList.remove('tab-content-enter');
                    }, 300);
                }, 200);
            }
        }

        // チーム計画送信処理（要件 1.1, 1.4, 1.5）
        document.addEventListener('DOMContentLoaded', function() {
            const teamPlanTextarea = document.getElementById('team-plan-textarea');
            const teamPlanSubmitBtn = document.getElementById('team-plan-submit-btn');
            const teamPlanError = document.getElementById('team-plan-error');
            const teamPlanSuccess = document.getElementById('team-plan-success');

            if (teamPlanSubmitBtn) {
                teamPlanSubmitBtn.addEventListener('click', async function(e) {
                    e.preventDefault();
                    
                    // エラーと成功メッセージをクリア
                    teamPlanError.classList.add('hidden');
                    teamPlanSuccess.classList.add('hidden');
                    
                    const planText = teamPlanTextarea.value;
                    
                    // クライアントサイドバリデーション（要件 1.4）
                    if (!planText || planText.trim() === '') {
                        teamPlanError.textContent = 'チーム計画を入力してください';
                        teamPlanError.classList.remove('hidden');
                        return;
                    }
                    
                    // ボタンを無効化
                    teamPlanSubmitBtn.disabled = true;
                    teamPlanSubmitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i>送信中...';
                    
                    try {
                        // POSTリクエストを送信（要件 1.1）
                        const response = await fetch('/check/check.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                action: 'submit_team_plan',
                                plan_text: planText,
                                csrf_token: '<?php echo getCsrfToken(); ?>' // CSRF対策（要件 9.5）
                            })
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            // 成功時の処理（要件 1.5）
                            teamPlanSuccess.textContent = result.message || 'チーム計画を送信しました';
                            teamPlanSuccess.classList.remove('hidden');
                            
                            // テキストエリアをクリア（要件 1.5）
                            teamPlanTextarea.value = '';
                        } else {
                            // エラー時の処理
                            teamPlanError.textContent = result.message || 'エラーが発生しました';
                            teamPlanError.classList.remove('hidden');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        teamPlanError.textContent = 'ネットワークエラーが発生しました';
                        teamPlanError.classList.remove('hidden');
                    } finally {
                        // ボタンを再度有効化
                        teamPlanSubmitBtn.disabled = false;
                        teamPlanSubmitBtn.innerHTML = '<i class="fa-solid fa-paper-plane mr-2"></i>送信';
                    }
                });
            }

            // チャットメッセージ送信処理（要件 2.1, 2.4）
            const chatMessageInput = document.getElementById('chat-message-input');
            const chatSendBtn = document.getElementById('chat-send-btn');
            const chatError = document.getElementById('chat-error');
            const chatSuccess = document.getElementById('chat-success');
            const chatMessagesContainer = document.getElementById('chat-messages-container');

            // チャットメッセージを送信する関数
            async function sendChatMessage() {
                // エラーと成功メッセージをクリア
                chatError.classList.add('hidden');
                chatSuccess.classList.add('hidden');
                
                const message = chatMessageInput.value;
                
                // クライアントサイドバリデーション（要件 2.4）
                if (!message || message.trim() === '') {
                    chatError.textContent = 'メッセージを入力してください';
                    chatError.classList.remove('hidden');
                    return;
                }
                
                // ボタンを無効化
                chatSendBtn.disabled = true;
                const originalBtnContent = chatSendBtn.innerHTML;
                chatSendBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-lg"></i>';
                
                try {
                    // POSTリクエストを送信（要件 2.1）
                    const response = await fetch('/check/check.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            action: 'send_chat_message',
                            message: message,
                            csrf_token: '<?php echo getCsrfToken(); ?>' // CSRF対策（要件 9.5）
                        })
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        // 成功時の処理（要件 2.2）
                        chatMessageInput.value = '';
                        
                        // メッセージを再読み込み
                        await loadChatMessages();
                    } else {
                        // エラー時の処理
                        chatError.textContent = result.message || 'エラーが発生しました';
                        chatError.classList.remove('hidden');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    chatError.textContent = 'ネットワークエラーが発生しました';
                    chatError.classList.remove('hidden');
                } finally {
                    // ボタンを再度有効化
                    chatSendBtn.disabled = false;
                    chatSendBtn.innerHTML = originalBtnContent;
                }
            }

            // チャットメッセージを読み込む関数（要件 2.3）
            async function loadChatMessages() {
                try {
                    const response = await fetch('/check/check.php?action=get_chat_messages', {
                        method: 'GET'
                    });
                    
                    const result = await response.json();
                    
                    if (result.success && result.data.messages) {
                        // メッセージコンテナをクリア
                        chatMessagesContainer.innerHTML = '';
                        
                        // 各メッセージを表示（要件 2.3, 2.5）
                        result.data.messages.forEach(msg => {
                            const messageDiv = createChatMessageElement(msg);
                            chatMessagesContainer.appendChild(messageDiv);
                        });
                        
                        // 最新メッセージまでスクロール
                        chatMessagesContainer.scrollTop = chatMessagesContainer.scrollHeight;
                    }
                } catch (error) {
                    console.error('Error loading chat messages:', error);
                }
            }

            // チャットメッセージ要素を作成する関数（要件 2.5）
            function createChatMessageElement(msg) {
                const messageDiv = document.createElement('div');
                
                // ユーザーアイコンの色を決定（簡易的な実装）
                const colors = ['bg-pink-300', 'bg-blue-300', 'bg-green-300', 'bg-yellow-300', 'bg-purple-300', 'bg-red-300'];
                const colorIndex = (msg.user_id || 0) % colors.length;
                const avatarColor = colors[colorIndex];
                
                // ユーザー名の最初の文字を取得
                const userInitial = msg.user_name ? msg.user_name.charAt(0) : '?';
                
                messageDiv.className = 'flex items-start';
                messageDiv.innerHTML = `
                    <div class="w-10 h-10 rounded-full border-2 border-black ${avatarColor} flex-shrink-0 mr-3 shadow-sm flex items-center justify-center font-bold text-xs">
                        ${userInitial}
                    </div>
                    <div class="bg-white border-2 border-black px-4 py-2 rounded-2xl rounded-tl-none shadow-sm text-sm relative max-w-[80%]">
                        <p>${escapeHtml(msg.message)}</p>
                    </div>
                `;
                
                return messageDiv;
            }

            // HTMLエスケープ関数（XSS対策）
            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            // イベントリスナーを設定
            if (chatSendBtn) {
                chatSendBtn.addEventListener('click', sendChatMessage);
            }

            // Enterキーでメッセージ送信
            if (chatMessageInput) {
                chatMessageInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        sendChatMessage();
                    }
                });
            }

            // ページ読み込み時にチャットメッセージを取得（要件 2.3）
            loadChatMessages();

            // 個人評価フォーム送信処理（要件 3.4, 4.4, 5.4, 6.2, 8.1, 8.2）
            const evaluationForm = document.getElementById('evaluation-form');
            const evaluationSubmitBtn = document.getElementById('evaluation-submit-btn');
            const evaluationError = document.getElementById('evaluation-error');
            const evaluationSuccess = document.getElementById('evaluation-success');

            if (evaluationForm) {
                evaluationForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    // エラーと成功メッセージをクリア
                    evaluationError.classList.add('hidden');
                    evaluationSuccess.classList.add('hidden');
                    
                    // フォームデータを取得
                    const formData = new FormData(evaluationForm);
                    
                    // フォームデータをJSONに変換
                    const data = {
                        action: 'submit_evaluation',
                        target_user_id: formData.get('target_user_id'),
                        code_rating: formData.get('code_rating'),
                        code_comment: formData.get('code_comment') || null,
                        personality_rating: formData.get('personality_rating'),
                        personality_comment: formData.get('personality_comment') || null,
                        action_proposal: formData.get('action_proposal') || null
                    };
                    
                    // クライアントサイドバリデーション（要件 8.1）
                    const validationErrors = [];
                    
                    if (!data.target_user_id) {
                        validationErrors.push('対象ユーザーを選択してください');
                    }
                    
                    if (!data.code_rating) {
                        validationErrors.push('コード評価を選択してください');
                    }
                    
                    if (!data.personality_rating) {
                        validationErrors.push('人格評価を選択してください');
                    }
                    
                    if (validationErrors.length > 0) {
                        evaluationError.textContent = validationErrors.join('、');
                        evaluationError.classList.remove('hidden');
                        return;
                    }
                    
                    // ボタンを無効化
                    evaluationSubmitBtn.disabled = true;
                    evaluationSubmitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-3"></i>送信中...';
                    
                    try {
                        // CSRF トークンを追加（要件 9.5）
                        data.csrf_token = '<?php echo getCsrfToken(); ?>';
                        
                        // POSTリクエストを送信（要件 8.1）
                        const response = await fetch('/check/check.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify(data)
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            // 成功時の処理（要件 8.3, 8.4）
                            evaluationSuccess.textContent = result.message || '評価を送信しました';
                            evaluationSuccess.classList.remove('hidden');
                            
                            // フォームをリセット（要件 8.4）
                            evaluationForm.reset();
                            
                            // 最初のユーザーを選択状態に戻す
                            const firstUserRadio = evaluationForm.querySelector('input[name="target_user_id"]');
                            if (firstUserRadio) {
                                firstUserRadio.checked = true;
                            }
                        } else {
                            // エラー時の処理（要件 8.5）
                            if (result.errors && Object.keys(result.errors).length > 0) {
                                // 具体的なフィールドエラーを表示
                                const errorMessages = Object.values(result.errors).join('、');
                                evaluationError.textContent = errorMessages;
                            } else {
                                evaluationError.textContent = result.message || 'エラーが発生しました';
                            }
                            evaluationError.classList.remove('hidden');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        evaluationError.textContent = 'ネットワークエラーが発生しました';
                        evaluationError.classList.remove('hidden');
                    } finally {
                        // ボタンを再度有効化
                        evaluationSubmitBtn.disabled = false;
                        evaluationSubmitBtn.innerHTML = '<i class="fa-solid fa-paper-plane mr-3"></i>送 信';
                    }
                });
            }
        });
    </script>

    <!-- メンバープロフィールモーダル -->
    <div id="memberModal" class="fixed inset-0 bg-black bg-opacity-80 hidden items-center justify-center z-50" onclick="closeMemberModal()">
        <div class="bg-white border-6 border-black p-6 shadow-[12px_12px_0_#000] relative max-w-md mx-4" onclick="event.stopPropagation()">
            <button onclick="closeMemberModal()" class="absolute -top-4 -right-4 w-12 h-12 bg-red-500 text-white rounded-full border-4 border-black shadow-[4px_4px_0_#000] hover:bg-red-600 font-heavy text-2xl">
                ×
            </button>
            
            <div class="flex flex-col items-center">
                <div class="w-32 h-32 rounded-full border-4 border-black overflow-hidden mb-4 shadow-hard">
                    <img id="modalIcon" src="" alt="" class="w-full h-full object-cover">
                </div>
                
                <h3 id="modalName" class="text-2xl font-heavy mb-4 text-gray-800"></h3>
                
                <div class="w-full space-y-3">
                    <div class="bg-yellow-100 border-4 border-black p-3">
                        <p class="text-sm font-bold text-gray-600 mb-1">期生</p>
                        <p class="text-xl font-heavy" id="modalGeneration"></p>
                    </div>
                    
                    <div class="bg-pink-100 border-4 border-black p-3">
                        <p class="text-sm font-bold text-gray-600 mb-1">横もく</p>
                        <p class="text-lg font-bold" id="modalYokomoku"></p>
                    </div>
                    
                    <div class="bg-purple-100 border-4 border-black p-3">
                        <p class="text-sm font-bold text-gray-600 mb-1">縦もく</p>
                        <p class="text-lg font-bold" id="modalTatemoku"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openMemberModal(id, name, icon, generation, yokomoku, tatemoku) {
            const modalIconContainer = document.getElementById('modalIcon').parentElement;
            
            if (icon && icon.trim() !== '') {
                // アイコン画像がある場合
                modalIconContainer.innerHTML = '<img id="modalIcon" src="/assets/img/gacha_img/' + icon + '" alt="' + name + '" class="w-full h-full object-cover">';
            } else {
                // アイコン画像がない場合はデフォルトアイコン
                modalIconContainer.innerHTML = '<div id="modalIcon" class="w-full h-full flex items-center justify-center bg-gray-200"><span class="text-6xl">👤</span></div>';
            }
            
            document.getElementById('modalName').textContent = name;
            document.getElementById('modalGeneration').textContent = generation + '期生';
            document.getElementById('modalYokomoku').textContent = yokomoku;
            document.getElementById('modalTatemoku').textContent = tatemoku;
            document.getElementById('memberModal').classList.remove('hidden');
            document.getElementById('memberModal').classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeMemberModal() {
            document.getElementById('memberModal').classList.add('hidden');
            document.getElementById('memberModal').classList.remove('flex');
            document.body.style.overflow = 'auto';
        }

        // ESCキーでモーダルを閉じる
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeMemberModal();
            }
        });
    </script>
</body>
</html>