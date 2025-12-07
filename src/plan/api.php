<?php
/**
 * Plan API - プラン作成・更新・削除・ステータス切替
 */

// セッション開始
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// データベース接続
require_once __DIR__ . '/../dbconnect.php';

// JSON レスポンスヘッダー
header('Content-Type: application/json; charset=utf-8');

// ログインユーザー取得
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; // デフォルト
}
$current_user_id = (int)$_SESSION['user_id'];

/**
 * JSON レスポンスを返して終了
 */
function sendJson($data) {
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    // POSTリクエストのみ受付
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendJson(['success' => false, 'message' => 'POSTリクエストのみ対応しています']);
    }

    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'create':
            // プラン作成
            $content = trim($_POST['content'] ?? '');
            $start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
            $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;

            if (empty($content)) {
                sendJson(['success' => false, 'message' => '内容を入力してください']);
            }

            $stmt = $dbh->prepare("
                INSERT INTO plans (user_id, content, start_date, end_date, status, created_at) 
                VALUES (?, ?, ?, ?, 'running', NOW())
            ");
            $stmt->execute([$current_user_id, $content, $start_date, $end_date]);

            sendJson(['success' => true, 'message' => 'プランを作成しました！']);
            break;

        case 'update':
            // プラン更新
            $plan_id = (int)($_POST['plan_id'] ?? 0);
            $content = trim($_POST['content'] ?? '');
            $start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
            $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
            $status = $_POST['status'] ?? 'running';

            if (empty($content) || $plan_id <= 0) {
                sendJson(['success' => false, 'message' => '無効なデータです']);
            }

            $stmt = $dbh->prepare("
                UPDATE plans 
                SET content = ?, start_date = ?, end_date = ?, status = ?, updated_at = NOW()
                WHERE id = ? AND user_id = ?
            ");
            $stmt->execute([$content, $start_date, $end_date, $status, $plan_id, $current_user_id]);

            if ($stmt->rowCount() === 0) {
                sendJson(['success' => false, 'message' => '対象のプランが見つかりません']);
            }

            sendJson(['success' => true, 'message' => 'プランを更新しました']);
            break;

        case 'delete':
            // プラン削除
            $plan_id = (int)($_POST['plan_id'] ?? 0);

            if ($plan_id <= 0) {
                sendJson(['success' => false, 'message' => '無効なIDです']);
            }

            $stmt = $dbh->prepare("DELETE FROM plans WHERE id = ? AND user_id = ?");
            $stmt->execute([$plan_id, $current_user_id]);

            if ($stmt->rowCount() === 0) {
                sendJson(['success' => false, 'message' => '対象のプランが見つかりません']);
            }

            sendJson(['success' => true, 'message' => 'プランを削除しました']);
            break;

        case 'toggle_status':
            // ステータス切り替え（running ⇔ completed）
            $plan_id = (int)($_POST['plan_id'] ?? 0);

            if ($plan_id <= 0) {
                sendJson(['success' => false, 'message' => '無効なIDです']);
            }

            $stmt = $dbh->prepare("
                UPDATE plans 
                SET status = IF(status = 'running', 'completed', 'running'),
                    updated_at = NOW()
                WHERE id = ? AND user_id = ?
            ");
            $stmt->execute([$plan_id, $current_user_id]);

            if ($stmt->rowCount() === 0) {
                sendJson(['success' => false, 'message' => '対象のプランが見つかりません']);
            }

            sendJson(['success' => true, 'message' => 'ステータスを更新しました']);
            break;

        default:
            sendJson(['success' => false, 'message' => '無効なアクションです']);
    }
} catch (Exception $e) {
    error_log('Plan API Error: ' . $e->getMessage());
    sendJson(['success' => false, 'message' => 'サーバーエラーが発生しました']);
}
