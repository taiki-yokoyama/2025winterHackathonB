<?php
/**
 * ユニットテスト: 計算関数
 * 
 * このファイルには、平均スコア計算関数のユニットテストが含まれています。
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../evaluation_functions.php';

class CalculationFunctionsTest extends TestCase
{
    /**
     * calculateAverageRating() の基本的な動作をテスト
     */
    public function testCalculateAverageRatingBasic()
    {
        // 基本的な平均計算
        $ratings = [1, 2, 3, 4];
        $average = calculateAverageRating($ratings);
        $this->assertEquals(2.5, $average);
        
        // 同じ値の配列
        $ratings = [3, 3, 3, 3];
        $average = calculateAverageRating($ratings);
        $this->assertEquals(3.0, $average);
        
        // 単一の値
        $ratings = [4];
        $average = calculateAverageRating($ratings);
        $this->assertEquals(4.0, $average);
    }
    
    /**
     * calculateAverageRating() の空配列処理をテスト
     */
    public function testCalculateAverageRatingEmpty()
    {
        // 空の配列
        $ratings = [];
        $average = calculateAverageRating($ratings);
        $this->assertNull($average);
    }
    
    /**
     * calculateAverageRating() の数値フィルタリングをテスト
     */
    public function testCalculateAverageRatingFiltering()
    {
        // 数値と非数値が混在
        $ratings = [1, 2, 'invalid', 3, null, 4];
        $average = calculateAverageRating($ratings);
        $this->assertEquals(2.5, $average); // (1+2+3+4)/4 = 2.5
    }
    
    /**
     * calculateSummaryScores() の基本的な動作をテスト
     */
    public function testCalculateSummaryScoresBasic()
    {
        $evaluations = [
            ['code_rating' => 3, 'personality_rating' => 4],
            ['code_rating' => 2, 'personality_rating' => 3],
            ['code_rating' => 4, 'personality_rating' => 4],
        ];
        
        $summary = calculateSummaryScores($evaluations);
        
        // コード評価の平均: (3+2+4)/3 = 3.0
        $this->assertEquals(3.0, $summary['code_average']);
        
        // 人格評価の平均: (4+3+4)/3 = 3.666...
        $this->assertEqualsWithDelta(3.666, $summary['personality_average'], 0.01);
        
        // カウント
        $this->assertEquals(3, $summary['code_count']);
        $this->assertEquals(3, $summary['personality_count']);
    }
    
    /**
     * calculateSummaryScores() の空配列処理をテスト
     */
    public function testCalculateSummaryScoresEmpty()
    {
        $evaluations = [];
        $summary = calculateSummaryScores($evaluations);
        
        $this->assertNull($summary['code_average']);
        $this->assertNull($summary['personality_average']);
        $this->assertEquals(0, $summary['code_count']);
        $this->assertEquals(0, $summary['personality_count']);
    }
    
    /**
     * calculateSummaryScores() の不完全なデータ処理をテスト
     */
    public function testCalculateSummaryScoresIncompleteData()
    {
        $evaluations = [
            ['code_rating' => 3, 'personality_rating' => 4],
            ['code_rating' => 2], // personality_rating がない
            ['personality_rating' => 3], // code_rating がない
        ];
        
        $summary = calculateSummaryScores($evaluations);
        
        // コード評価の平均: (3+2)/2 = 2.5
        $this->assertEquals(2.5, $summary['code_average']);
        
        // 人格評価の平均: (4+3)/2 = 3.5
        $this->assertEquals(3.5, $summary['personality_average']);
        
        // カウント
        $this->assertEquals(2, $summary['code_count']);
        $this->assertEquals(2, $summary['personality_count']);
    }
    
    /**
     * calculateSummaryScores() の要件7.3の検証
     * 全てのコード評価の平均と全ての人格評価の平均を別々に計算
     */
    public function testCalculateSummaryScoresRequirement73()
    {
        // 実際の評価データに近い形式
        $evaluations = [
            [
                'id' => 1,
                'evaluator_id' => 1,
                'target_user_id' => 2,
                'code_rating' => 4,
                'code_comment' => 'Great code!',
                'personality_rating' => 3,
                'personality_comment' => 'Good teamwork',
                'created_at' => '2024-01-01 10:00:00'
            ],
            [
                'id' => 2,
                'evaluator_id' => 3,
                'target_user_id' => 2,
                'code_rating' => 3,
                'code_comment' => 'Nice work',
                'personality_rating' => 4,
                'personality_comment' => 'Excellent communication',
                'created_at' => '2024-01-02 11:00:00'
            ],
        ];
        
        $summary = calculateSummaryScores($evaluations);
        
        // コード評価の平均: (4+3)/2 = 3.5
        $this->assertEquals(3.5, $summary['code_average']);
        
        // 人格評価の平均: (3+4)/2 = 3.5
        $this->assertEquals(3.5, $summary['personality_average']);
        
        // 両方とも2つの評価
        $this->assertEquals(2, $summary['code_count']);
        $this->assertEquals(2, $summary['personality_count']);
    }
}
