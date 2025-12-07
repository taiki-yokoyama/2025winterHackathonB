<?php
/**
 * Plan機能のヘルパー関数
 */

/**
 * 自分のプラン一覧を取得（フィルター対応）
 */
function getMyPlans($dbh, $user_id, $period = 'all', $status = 'all') {
    try {
        $sql = "SELECT * FROM plans WHERE user_id = ?";
        $params = [$user_id];
        
        // 期間フィルター
        if ($period === 'this_week') {
            $sql .= " AND start_date >= DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY)";
        } elseif ($period === 'last_week') {
            $sql .= " AND start_date >= DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) + 7 DAY)
                      AND start_date < DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY)";
        }
        
        // ステータスフィルター
        if ($status !== 'all') {
            $sql .= " AND status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        $stmt = $dbh->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("getMyPlans Error: " . $e->getMessage());
        return [];
    }
}

/**
 * チームメンバーのプラン一覧を取得
 */
function getTeamPlans($dbh, $current_user_id, $filter_user_id = 'all') {
    try {
        $sql = "SELECT p.*, u.name, u.avatar, u.avatar_color 
                FROM plans p 
                JOIN users u ON p.user_id = u.id 
                WHERE p.user_id != ?";
        $params = [$current_user_id];
        
        if ($filter_user_id !== 'all') {
            $sql .= " AND p.user_id = ?";
            $params[] = $filter_user_id;
        }
        
        $sql .= " ORDER BY p.created_at DESC";
        
        $stmt = $dbh->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("getTeamPlans Error: " . $e->getMessage());
        return [];
    }
}

/**
 * すべてのユーザーを取得
 */
function getAllUsers($dbh) {
    try {
        $stmt = $dbh->query("SELECT id, name, avatar, avatar_color FROM users ORDER BY id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("getAllUsers Error: " . $e->getMessage());
        return [];
    }
}

/**
 * 現在のユーザー情報を取得
 */
function getCurrentUser($dbh, $user_id) {
    try {
        $stmt = $dbh->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    } catch (PDOException $e) {
        error_log("getCurrentUser Error: " . $e->getMessage());
        return null;
    }
}
