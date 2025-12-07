<?php
session_start();

// セッションを破棄
$_SESSION = array();

if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
}

session_destroy();

// トップページにリダイレクト
header('Location: /');
exit;
?>
