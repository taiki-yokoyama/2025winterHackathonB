<?php
/**
 * セキュリティ関数
 * 
 * 要件 9.5: セキュリティ対策
 * - XSS対策（htmlspecialchars）
 * - CSRF対策（トークン）
 * - セッション検証
 */

/**
 * XSS対策: HTMLエスケープ
 * 
 * @param string $string エスケープする文字列
 * @param int $flags htmlspecialcharsのフラグ（デフォルト: ENT_QUOTES）
 * @param string $encoding 文字エンコーディング（デフォルト: UTF-8）
 * @return string エスケープされた文字列
 */
function escapeHtml($string, $flags = ENT_QUOTES, $encoding = 'UTF-8') {
    if ($string === null) {
        return '';
    }
    return htmlspecialchars((string)$string, $flags, $encoding);
}

/**
 * CSRFトークンを生成してセッションに保存
 * 
 * @return string 生成されたCSRFトークン
 */
function generateCsrfToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // トークンを生成
    $token = bin2hex(random_bytes(32));
    
    // セッションに保存
    $_SESSION['csrf_token'] = $token;
    $_SESSION['csrf_token_time'] = time();
    
    return $token;
}

/**
 * CSRFトークンを取得（存在しない場合は生成）
 * 
 * @return string CSRFトークン
 */
function getCsrfToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // トークンが存在しない、または有効期限切れの場合は新規生成
    if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
        return generateCsrfToken();
    }
    
    // トークンの有効期限チェック（1時間）
    $tokenAge = time() - $_SESSION['csrf_token_time'];
    if ($tokenAge > 3600) {
        return generateCsrfToken();
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * CSRFトークンを検証
 * 
 * @param string $token 検証するトークン
 * @return bool トークンが有効な場合true、無効な場合false
 */
function validateCsrfToken($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // セッションにトークンが存在しない
    if (!isset($_SESSION['csrf_token'])) {
        error_log("CSRF validation failed: No token in session");
        return false;
    }
    
    // トークンの有効期限チェック（1時間）
    if (!isset($_SESSION['csrf_token_time'])) {
        error_log("CSRF validation failed: No token time in session");
        return false;
    }
    
    $tokenAge = time() - $_SESSION['csrf_token_time'];
    if ($tokenAge > 3600) {
        error_log("CSRF validation failed: Token expired (age: {$tokenAge}s)");
        return false;
    }
    
    // トークンの比較（タイミング攻撃対策のためhash_equals使用）
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        error_log("CSRF validation failed: Token mismatch");
        return false;
    }
    
    return true;
}

/**
 * セッションの検証とセキュリティ強化
 * 
 * @return bool セッションが有効な場合true、無効な場合false
 */
function validateSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // ユーザーIDが設定されているか確認
    if (!isset($_SESSION['user_id'])) {
        error_log("Session validation failed: No user_id in session");
        return false;
    }
    
    // セッションハイジャック対策: IPアドレスのチェック（オプション）
    // 注意: プロキシ環境では問題が発生する可能性があるため、必要に応じて有効化
    /*
    if (isset($_SESSION['user_ip'])) {
        if ($_SESSION['user_ip'] !== $_SERVER['REMOTE_ADDR']) {
            error_log("Session validation failed: IP address mismatch");
            return false;
        }
    } else {
        $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
    }
    */
    
    // セッションハイジャック対策: User-Agentのチェック
    if (isset($_SESSION['user_agent'])) {
        if ($_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
            error_log("Session validation failed: User-Agent mismatch");
            return false;
        }
    } else {
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
    }
    
    // セッションの最終アクセス時刻をチェック（30分でタイムアウト）
    if (isset($_SESSION['last_activity'])) {
        $inactiveTime = time() - $_SESSION['last_activity'];
        if ($inactiveTime > 1800) { // 30分
            error_log("Session validation failed: Session timeout (inactive: {$inactiveTime}s)");
            session_unset();
            session_destroy();
            return false;
        }
    }
    
    // 最終アクセス時刻を更新
    $_SESSION['last_activity'] = time();
    
    // セッション固定攻撃対策: 定期的にセッションIDを再生成（5分ごと）
    if (!isset($_SESSION['session_regenerate_time'])) {
        $_SESSION['session_regenerate_time'] = time();
    } else {
        $regenerateAge = time() - $_SESSION['session_regenerate_time'];
        if ($regenerateAge > 300) { // 5分
            session_regenerate_id(true);
            $_SESSION['session_regenerate_time'] = time();
        }
    }
    
    return true;
}

/**
 * セッションを安全に開始
 * 
 * @return void
 */
function startSecureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        // セッションのセキュリティ設定
        ini_set('session.cookie_httponly', 1); // JavaScriptからのアクセスを防止
        ini_set('session.cookie_secure', 0); // HTTPS環境では1に設定すべき
        ini_set('session.use_strict_mode', 1); // 厳格なセッションID管理
        ini_set('session.use_only_cookies', 1); // URLパラメータでのセッションID送信を無効化
        
        session_start();
        
        // 初回アクセス時の設定
        if (!isset($_SESSION['initiated'])) {
            session_regenerate_id(true);
            $_SESSION['initiated'] = true;
            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
            $_SESSION['last_activity'] = time();
            $_SESSION['session_regenerate_time'] = time();
        }
    }
}

/**
 * 入力値のサニタイゼーション
 * 
 * @param string $input サニタイズする入力値
 * @param string $type サニタイズのタイプ（'string', 'email', 'int', 'url'）
 * @return mixed サニタイズされた値
 */
function sanitizeInput($input, $type = 'string') {
    switch ($type) {
        case 'email':
            return filter_var($input, FILTER_SANITIZE_EMAIL);
        
        case 'int':
            return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
        
        case 'url':
            return filter_var($input, FILTER_SANITIZE_URL);
        
        case 'string':
        default:
            // 基本的な文字列サニタイゼーション
            return trim(strip_tags($input));
    }
}

/**
 * SQLインジェクション対策のためのプリペアドステートメント実行ヘルパー
 * 
 * 注意: この関数は既にPDOのプリペアドステートメントを使用している場合は不要です。
 * 要件 9.5 では、全てのデータベースクエリでプリペアドステートメントを使用することが求められています。
 * 
 * @param PDO $dbh データベース接続
 * @param string $sql SQLクエリ
 * @param array $params パラメータ配列
 * @return PDOStatement|false 実行されたステートメント、失敗時はfalse
 */
function executePreparedStatement($dbh, $sql, $params = []) {
    try {
        $stmt = $dbh->prepare($sql);
        
        // パラメータをバインド
        foreach ($params as $key => $value) {
            if (is_int($key)) {
                // 位置パラメータ（?）の場合
                $stmt->bindValue($key + 1, $value);
            } else {
                // 名前付きパラメータ（:name）の場合
                $stmt->bindValue($key, $value);
            }
        }
        
        $stmt->execute();
        return $stmt;
        
    } catch (PDOException $e) {
        error_log("Prepared statement execution failed: " . $e->getMessage());
        return false;
    }
}

/**
 * レート制限チェック（簡易版）
 * 
 * @param string $action アクション名
 * @param int $maxAttempts 最大試行回数
 * @param int $timeWindow 時間ウィンドウ（秒）
 * @return bool 制限内の場合true、制限超過の場合false
 */
function checkRateLimit($action, $maxAttempts = 10, $timeWindow = 60) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $key = "rate_limit_{$action}";
    $now = time();
    
    // レート制限データの初期化
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = [
            'attempts' => 0,
            'first_attempt' => $now
        ];
    }
    
    $rateData = $_SESSION[$key];
    
    // 時間ウィンドウをリセット
    if ($now - $rateData['first_attempt'] > $timeWindow) {
        $_SESSION[$key] = [
            'attempts' => 1,
            'first_attempt' => $now
        ];
        return true;
    }
    
    // 試行回数をチェック
    if ($rateData['attempts'] >= $maxAttempts) {
        error_log("Rate limit exceeded for action: {$action} | Attempts: {$rateData['attempts']}");
        return false;
    }
    
    // 試行回数を増やす
    $_SESSION[$key]['attempts']++;
    
    return true;
}
?>
