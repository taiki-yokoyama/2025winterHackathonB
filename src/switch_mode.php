<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /');
    exit;
}

require_once 'dbconnect.php';

$mode = isset($_GET['mode']) ? $_GET['mode'] : 'yokomoku';

if ($mode !== 'yokomoku' && $mode !== 'tatemoku') {
    $mode = 'yokomoku';
}

// モードを更新
$stmt = $dbh->prepare('UPDATE users SET current_mode = ? WHERE id = ?');
$stmt->execute([$mode, $_SESSION['user_id']]);

// セッションに保存
$_SESSION['current_mode'] = $mode;

// 元のページに戻る
$referer = $_SERVER['HTTP_REFERER'] ?? '/mypage/index.php';
header('Location: ' . $referer);
exit;
