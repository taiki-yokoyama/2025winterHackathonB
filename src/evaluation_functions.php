<?php
/**
 * 評価・フィードバックシステム データベースアクセス関数
 * 
 * このファイルには、チーム計画、チームチャット、個人評価に関する
 * データベース操作関数が含まれています。
 */

require_once __DIR__ . '/dbconnect.php';

/**
 * チーム計画をデータベースに保存
 * 
 * @param PDO $dbh データベース接続
 * @param int $userId ユーザーID
 * @param string $planText 計画テキスト
 * @return int|false 保存された計画のID、失敗時はfalse
 * @throws PDOException データベースエラー
 */
function saveTeamPlan($dbh, $userId, $planText) {
    $sql = "INSERT INTO team_plans (user_id, plan_text, created_at) VALUES (:user_id, :plan_text, NOW())";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':plan_text', $planText, PDO::PARAM_STR);
    
    if ($stmt->execute()) {
        return $dbh->lastInsertId();
    }
    return false;
}

/**
 * 全てのチーム計画を取得
 * 
 * @param PDO $dbh データベース接続
 * @return array チーム計画の配列（作成日時の昇順）
 * @throws PDOException データベースエラー
 */
function getTeamPlans($dbh) {
    $sql = "SELECT tp.id, tp.user_id, tp.plan_text, tp.created_at, tp.updated_at, 
                   u.name as user_name, u.icon as user_icon
            FROM team_plans tp
            LEFT JOIN users u ON tp.user_id = u.id
            ORDER BY tp.created_at ASC";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * 特定のチーム計画をIDで取得
 * 
 * @param PDO $dbh データベース接続
 * @param int $planId 計画ID
 * @return array|false チーム計画の配列、見つからない場合はfalse
 * @throws PDOException データベースエラー
 */
function getTeamPlanById($dbh, $planId) {
    $sql = "SELECT id, user_id, plan_text, created_at, updated_at
            FROM team_plans
            WHERE id = :plan_id";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':plan_id', $planId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * チャットメッセージをデータベースに保存
 * 
 * @param PDO $dbh データベース接続
 * @param int $userId ユーザーID
 * @param string $message メッセージテキスト
 * @return int|false 保存されたメッセージのID、失敗時はfalse
 * @throws PDOException データベースエラー
 */
function saveChatMessage($dbh, $userId, $message) {
    $sql = "INSERT INTO team_chats (user_id, message, created_at) VALUES (:user_id, :message, NOW())";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':message', $message, PDO::PARAM_STR);
    
    if ($stmt->execute()) {
        return $dbh->lastInsertId();
    }
    return false;
}

/**
 * 全てのチャットメッセージをタイムスタンプ順に取得
 * 
 * @param PDO $dbh データベース接続
 * @return array チャットメッセージの配列（作成日時の昇順）
 * @throws PDOException データベースエラー
 */
function getChatMessages($dbh) {
    $sql = "SELECT tc.id, tc.user_id, tc.message, tc.created_at,
                   u.name as user_name, u.icon as user_icon
            FROM team_chats tc
            LEFT JOIN users u ON tc.user_id = u.id
            ORDER BY tc.created_at ASC";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * 個人評価をデータベースに保存
 * 
 * @param PDO $dbh データベース接続
 * @param int $evaluatorId 評価者のユーザーID
 * @param int $targetUserId 対象ユーザーのID
 * @param int $codeRating コード評価（1-4）
 * @param string|null $codeComment コードコメント（任意）
 * @param int $personalityRating 人格評価（1-4）
 * @param string|null $personalityComment 人格コメント（任意）
 * @param string|null $actionProposal アクション提案（任意）
 * @return int|false 保存された評価のID、失敗時はfalse
 * @throws PDOException データベースエラー
 */
function saveEvaluation($dbh, $evaluatorId, $targetUserId, $codeRating, $codeComment, 
                       $personalityRating, $personalityComment, $actionProposal) {
    $sql = "INSERT INTO individual_evaluations 
            (evaluator_id, target_user_id, code_rating, code_comment, 
             personality_rating, personality_comment, action_proposal, created_at) 
            VALUES (:evaluator_id, :target_user_id, :code_rating, :code_comment, 
                    :personality_rating, :personality_comment, :action_proposal, NOW())";
    
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':evaluator_id', $evaluatorId, PDO::PARAM_INT);
    $stmt->bindParam(':target_user_id', $targetUserId, PDO::PARAM_INT);
    $stmt->bindParam(':code_rating', $codeRating, PDO::PARAM_INT);
    $stmt->bindParam(':code_comment', $codeComment, PDO::PARAM_STR);
    $stmt->bindParam(':personality_rating', $personalityRating, PDO::PARAM_INT);
    $stmt->bindParam(':personality_comment', $personalityComment, PDO::PARAM_STR);
    $stmt->bindParam(':action_proposal', $actionProposal, PDO::PARAM_STR);
    
    if ($stmt->execute()) {
        return $dbh->lastInsertId();
    }
    return false;
}

/**
 * 特定ユーザーへの評価を取得
 * 
 * @param PDO $dbh データベース接続
 * @param int $targetUserId 対象ユーザーのID
 * @return array 評価の配列（作成日時の降順）
 * @throws PDOException データベースエラー
 */
function getEvaluationsByTargetUser($dbh, $targetUserId) {
    $sql = "SELECT ie.id, ie.evaluator_id, ie.target_user_id, 
                   ie.code_rating, ie.code_comment, 
                   ie.personality_rating, ie.personality_comment, 
                   ie.action_proposal, ie.created_at,
                   u.name as evaluator_name, u.icon as evaluator_icon
            FROM individual_evaluations ie
            LEFT JOIN users u ON ie.evaluator_id = u.id
            WHERE ie.target_user_id = :target_user_id
            ORDER BY ie.created_at DESC";
    
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':target_user_id', $targetUserId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * 特定ユーザーへのアクション提案を取得
 * 
 * @param PDO $dbh データベース接続
 * @param int $targetUserId 対象ユーザーのID
 * @return array アクション提案の配列（作成日時の降順）
 * @throws PDOException データベースエラー
 */
function getActionProposalsByTargetUser($dbh, $targetUserId) {
    $sql = "SELECT ie.id, ie.evaluator_id, ie.action_proposal, ie.created_at,
                   u.name as evaluator_name, u.icon as evaluator_icon
            FROM individual_evaluations ie
            LEFT JOIN users u ON ie.evaluator_id = u.id
            WHERE ie.target_user_id = :target_user_id 
            AND ie.action_proposal IS NOT NULL 
            AND ie.action_proposal != ''
            ORDER BY ie.created_at DESC";
    
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':target_user_id', $targetUserId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ============================================================================
// バリデーション関数
// ============================================================================

/**
 * 空文字列と空白のみの文字列をチェック
 * 
 * 要件 1.4, 2.4: チーム計画とチャットメッセージの空入力を防止
 * 
 * @param string $input チェックする入力文字列
 * @return bool 有効な入力の場合true、空または空白のみの場合false
 */
function validateNotEmpty($input) {
    // nullチェック
    if ($input === null) {
        return false;
    }
    
    // 文字列に変換
    $input = (string)$input;
    
    // 空白文字を削除して空かチェック
    return trim($input) !== '';
}

/**
 * 評価が1-4の範囲内かチェック
 * 
 * 要件 8.1: 評価フォームの必須フィールドバリデーション
 * 
 * @param mixed $rating チェックする評価値
 * @return bool 1-4の範囲内の場合true、それ以外はfalse
 */
function validateRating($rating) {
    // 数値に変換可能かチェック
    if (!is_numeric($rating)) {
        return false;
    }
    
    // 整数に変換
    $ratingInt = (int)$rating;
    
    // 1-4の範囲内かチェック
    return $ratingInt >= 1 && $ratingInt <= 4;
}

/**
 * 評価フォーム全体のバリデーション
 * 
 * 要件 8.1: 評価フォームの全フィールドを検証
 * 
 * @param array $formData フォームデータの連想配列
 *                        - target_user_id: 対象ユーザーID（必須）
 *                        - code_rating: コード評価（必須、1-4）
 *                        - code_comment: コードコメント（任意）
 *                        - personality_rating: 人格評価（必須、1-4）
 *                        - personality_comment: 人格コメント（任意）
 *                        - action_proposal: アクション提案（任意）
 * @return array バリデーション結果
 *               - 'valid': bool バリデーション成功の場合true
 *               - 'errors': array エラーメッセージの連想配列（フィールド名 => エラーメッセージ）
 */
function validateEvaluationForm($formData) {
    $errors = [];
    
    // 対象ユーザーIDのバリデーション
    if (!isset($formData['target_user_id']) || empty($formData['target_user_id'])) {
        $errors['target_user_id'] = '対象ユーザーを選択してください';
    } elseif (!is_numeric($formData['target_user_id']) || (int)$formData['target_user_id'] <= 0) {
        $errors['target_user_id'] = '有効な対象ユーザーを選択してください';
    }
    
    // コード評価のバリデーション
    if (!isset($formData['code_rating']) || $formData['code_rating'] === '') {
        $errors['code_rating'] = 'コード評価を選択してください';
    } elseif (!validateRating($formData['code_rating'])) {
        $errors['code_rating'] = 'コード評価は1から4の範囲で選択してください';
    }
    
    // 人格評価のバリデーション
    if (!isset($formData['personality_rating']) || $formData['personality_rating'] === '') {
        $errors['personality_rating'] = '人格評価を選択してください';
    } elseif (!validateRating($formData['personality_rating'])) {
        $errors['personality_rating'] = '人格評価は1から4の範囲で選択してください';
    }
    
    // オプションフィールドは空でも許可されるが、提供された場合は文字列であることを確認
    // コードコメント（任意）
    if (isset($formData['code_comment']) && $formData['code_comment'] !== null && $formData['code_comment'] !== '') {
        // コメントが提供された場合、文字列であることを確認
        $formData['code_comment'] = (string)$formData['code_comment'];
    }
    
    // 人格コメント（任意）
    if (isset($formData['personality_comment']) && $formData['personality_comment'] !== null && $formData['personality_comment'] !== '') {
        // コメントが提供された場合、文字列であることを確認
        $formData['personality_comment'] = (string)$formData['personality_comment'];
    }
    
    // アクション提案（任意）
    if (isset($formData['action_proposal']) && $formData['action_proposal'] !== null && $formData['action_proposal'] !== '') {
        // 提案が提供された場合、文字列であることを確認
        $formData['action_proposal'] = (string)$formData['action_proposal'];
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors
    ];
}

// ============================================================================
// 計算関数
// ============================================================================

/**
 * 評価配列から平均を計算
 * 
 * 要件 7.3: サマリースコアの計算
 * 
 * @param array $ratings 評価値の配列（1-4の整数）
 * @return float|null 平均値、配列が空の場合はnull
 */
function calculateAverageRating($ratings) {
    // 空の配列チェック
    if (empty($ratings)) {
        return null;
    }
    
    // 数値のみをフィルタリング
    $validRatings = array_filter($ratings, function($rating) {
        return is_numeric($rating);
    });
    
    // フィルタリング後に空になった場合
    if (empty($validRatings)) {
        return null;
    }
    
    // 平均を計算
    $sum = array_sum($validRatings);
    $count = count($validRatings);
    
    return $sum / $count;
}

/**
 * コードと人格の平均スコアを計算
 * 
 * 要件 7.3: 全てのコード評価の平均と全ての人格評価の平均を別々に計算
 * 
 * @param array $evaluations 評価データの配列
 *                          各要素は連想配列で以下のキーを含む:
 *                          - code_rating: コード評価（1-4）
 *                          - personality_rating: 人格評価（1-4）
 * @return array サマリースコアの連想配列
 *               - 'code_average': float|null コード評価の平均
 *               - 'personality_average': float|null 人格評価の平均
 *               - 'code_count': int コード評価の数
 *               - 'personality_count': int 人格評価の数
 */
function calculateSummaryScores($evaluations) {
    // 空の配列チェック
    if (empty($evaluations)) {
        return [
            'code_average' => null,
            'personality_average' => null,
            'code_count' => 0,
            'personality_count' => 0
        ];
    }
    
    // コード評価と人格評価を抽出
    $codeRatings = [];
    $personalityRatings = [];
    
    foreach ($evaluations as $evaluation) {
        // コード評価を抽出
        if (isset($evaluation['code_rating']) && is_numeric($evaluation['code_rating'])) {
            $codeRatings[] = $evaluation['code_rating'];
        }
        
        // 人格評価を抽出
        if (isset($evaluation['personality_rating']) && is_numeric($evaluation['personality_rating'])) {
            $personalityRatings[] = $evaluation['personality_rating'];
        }
    }
    
    // 平均を計算
    $codeAverage = calculateAverageRating($codeRatings);
    $personalityAverage = calculateAverageRating($personalityRatings);
    
    return [
        'code_average' => $codeAverage,
        'personality_average' => $personalityAverage,
        'code_count' => count($codeRatings),
        'personality_count' => count($personalityRatings)
    ];
}
?>
