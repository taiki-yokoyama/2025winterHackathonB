<?php
/**
 * ユニットテスト: バリデーション関数
 * 
 * このファイルには、入力バリデーション関数のユニットテストが含まれています。
 * 要件 1.4, 2.4, 8.1 を検証します。
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../evaluation_functions.php';

class ValidationFunctionsTest extends TestCase
{
    /**
     * validateNotEmpty() の基本的な動作をテスト
     * 要件 1.4, 2.4: 空入力のバリデーション
     */
    public function testValidateNotEmptyWithValidInput()
    {
        // 有効な入力
        $this->assertTrue(validateNotEmpty('Hello World'));
        $this->assertTrue(validateNotEmpty('a'));
        $this->assertTrue(validateNotEmpty('123'));
        $this->assertTrue(validateNotEmpty('  text  ')); // 前後に空白があっても中身があればOK
    }
    
    /**
     * validateNotEmpty() の空入力処理をテスト
     * 要件 1.4, 2.4: 空文字列と空白のみの文字列をチェック
     */
    public function testValidateNotEmptyWithInvalidInput()
    {
        // 無効な入力
        $this->assertFalse(validateNotEmpty(''));
        $this->assertFalse(validateNotEmpty('   ')); // 空白のみ
        $this->assertFalse(validateNotEmpty("\t")); // タブのみ
        $this->assertFalse(validateNotEmpty("\n")); // 改行のみ
        $this->assertFalse(validateNotEmpty("  \t\n  ")); // 空白文字の組み合わせ
        $this->assertFalse(validateNotEmpty(null)); // null
    }
    
    /**
     * validateRating() の有効な評価値をテスト
     * 要件 8.1: 評価が1-4の範囲内かチェック
     */
    public function testValidateRatingWithValidInput()
    {
        // 有効な評価値（1-4）
        $this->assertTrue(validateRating(1));
        $this->assertTrue(validateRating(2));
        $this->assertTrue(validateRating(3));
        $this->assertTrue(validateRating(4));
        
        // 文字列形式でも有効
        $this->assertTrue(validateRating('1'));
        $this->assertTrue(validateRating('2'));
        $this->assertTrue(validateRating('3'));
        $this->assertTrue(validateRating('4'));
    }
    
    /**
     * validateRating() の無効な評価値をテスト
     * 要件 8.1: 範囲外の値を拒否
     */
    public function testValidateRatingWithInvalidInput()
    {
        // 範囲外の値
        $this->assertFalse(validateRating(0));
        $this->assertFalse(validateRating(5));
        $this->assertFalse(validateRating(-1));
        $this->assertFalse(validateRating(100));
        
        // 非数値
        $this->assertFalse(validateRating('abc'));
        $this->assertFalse(validateRating(''));
        $this->assertFalse(validateRating(null));
        $this->assertFalse(validateRating([]));
    }
    
    /**
     * validateEvaluationForm() の有効なフォームデータをテスト
     * 要件 8.1: 評価フォーム全体のバリデーション
     */
    public function testValidateEvaluationFormWithValidData()
    {
        // 完全な有効データ
        $formData = [
            'target_user_id' => 1,
            'code_rating' => 3,
            'code_comment' => 'Good code quality',
            'personality_rating' => 4,
            'personality_comment' => 'Great teamwork',
            'action_proposal' => 'Continue the good work'
        ];
        
        $result = validateEvaluationForm($formData);
        
        $this->assertTrue($result['valid']);
        $this->assertEmpty($result['errors']);
    }
    
    /**
     * validateEvaluationForm() の必須フィールドのみのテスト
     * 要件 8.1: オプションフィールドは空でも許可
     */
    public function testValidateEvaluationFormWithRequiredFieldsOnly()
    {
        // 必須フィールドのみ（オプションフィールドなし）
        $formData = [
            'target_user_id' => 2,
            'code_rating' => 2,
            'personality_rating' => 3
        ];
        
        $result = validateEvaluationForm($formData);
        
        $this->assertTrue($result['valid']);
        $this->assertEmpty($result['errors']);
    }
    
    /**
     * validateEvaluationForm() の対象ユーザーID欠落をテスト
     * 要件 8.1: 必須フィールドのバリデーション
     */
    public function testValidateEvaluationFormMissingTargetUser()
    {
        $formData = [
            'code_rating' => 3,
            'personality_rating' => 4
        ];
        
        $result = validateEvaluationForm($formData);
        
        $this->assertFalse($result['valid']);
        $this->assertArrayHasKey('target_user_id', $result['errors']);
    }
    
    /**
     * validateEvaluationForm() のコード評価欠落をテスト
     * 要件 8.1: 必須フィールドのバリデーション
     */
    public function testValidateEvaluationFormMissingCodeRating()
    {
        $formData = [
            'target_user_id' => 1,
            'personality_rating' => 4
        ];
        
        $result = validateEvaluationForm($formData);
        
        $this->assertFalse($result['valid']);
        $this->assertArrayHasKey('code_rating', $result['errors']);
    }
    
    /**
     * validateEvaluationForm() の人格評価欠落をテスト
     * 要件 8.1: 必須フィールドのバリデーション
     */
    public function testValidateEvaluationFormMissingPersonalityRating()
    {
        $formData = [
            'target_user_id' => 1,
            'code_rating' => 3
        ];
        
        $result = validateEvaluationForm($formData);
        
        $this->assertFalse($result['valid']);
        $this->assertArrayHasKey('personality_rating', $result['errors']);
    }
    
    /**
     * validateEvaluationForm() の無効な評価値をテスト
     * 要件 8.1: 評価範囲のバリデーション
     */
    public function testValidateEvaluationFormInvalidRatings()
    {
        // コード評価が範囲外
        $formData = [
            'target_user_id' => 1,
            'code_rating' => 5, // 無効（1-4の範囲外）
            'personality_rating' => 3
        ];
        
        $result = validateEvaluationForm($formData);
        
        $this->assertFalse($result['valid']);
        $this->assertArrayHasKey('code_rating', $result['errors']);
        
        // 人格評価が範囲外
        $formData = [
            'target_user_id' => 1,
            'code_rating' => 3,
            'personality_rating' => 0 // 無効（1-4の範囲外）
        ];
        
        $result = validateEvaluationForm($formData);
        
        $this->assertFalse($result['valid']);
        $this->assertArrayHasKey('personality_rating', $result['errors']);
    }
    
    /**
     * validateEvaluationForm() の複数エラーをテスト
     * 要件 8.1: 全てのバリデーションエラーを返す
     */
    public function testValidateEvaluationFormMultipleErrors()
    {
        // 複数のフィールドが無効
        $formData = [
            'target_user_id' => -1, // 無効
            'code_rating' => 10, // 無効
            'personality_rating' => 'invalid' // 無効
        ];
        
        $result = validateEvaluationForm($formData);
        
        $this->assertFalse($result['valid']);
        $this->assertCount(3, $result['errors']);
        $this->assertArrayHasKey('target_user_id', $result['errors']);
        $this->assertArrayHasKey('code_rating', $result['errors']);
        $this->assertArrayHasKey('personality_rating', $result['errors']);
    }
    
    /**
     * validateEvaluationForm() の空のオプションフィールドをテスト
     * 要件 8.1: オプションフィールドは空でも許可
     */
    public function testValidateEvaluationFormEmptyOptionalFields()
    {
        $formData = [
            'target_user_id' => 1,
            'code_rating' => 3,
            'code_comment' => '', // 空でもOK
            'personality_rating' => 4,
            'personality_comment' => '', // 空でもOK
            'action_proposal' => '' // 空でもOK
        ];
        
        $result = validateEvaluationForm($formData);
        
        $this->assertTrue($result['valid']);
        $this->assertEmpty($result['errors']);
    }
}
