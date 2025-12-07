<?php
/**
 * プロパティベーステスト: チーム計画機能
 * 
 * このファイルには、チーム計画の保存と取得に関する
 * プロパティベーステストが含まれています。
 */

use PHPUnit\Framework\TestCase;
use Eris\Generator;

require_once __DIR__ . '/../evaluation_functions.php';
require_once __DIR__ . '/../dbconnect.php';

class TeamPlanPropertyTest extends TestCase
{
    use Eris\TestTrait;
    
    private $dbh;
    private $testUserId;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // データベース接続を取得
        $dsn = 'mysql:host=db;dbname=posse;charset=utf8';
        $user = 'root';
        $password = 'root';
        $this->dbh = new PDO($dsn, $user, $password);
        $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // テスト用ユーザーを作成
        $this->createTestUser();
    }
    
    protected function tearDown(): void
    {
        // テストデータをクリーンアップ
        $this->cleanupTestData();
        parent::tearDown();
    }
    
    /**
     * テスト用ユーザーを作成
     */
    private function createTestUser()
    {
        // 既存のテストユーザーを削除
        $sql = "DELETE FROM users WHERE email = 'test_property@example.com'";
        $this->dbh->exec($sql);
        
        // 新しいテストユーザーを作成
        $sql = "INSERT INTO users (email, password, created_at) 
                VALUES ('test_property@example.com', 'test_password', NOW())";
        $this->dbh->exec($sql);
        $this->testUserId = $this->dbh->lastInsertId();
    }
    
    /**
     * テストデータをクリーンアップ
     */
    private function cleanupTestData()
    {
        // テストで作成されたチーム計画を削除
        $sql = "DELETE FROM team_plans WHERE user_id = :user_id";
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':user_id', $this->testUserId, PDO::PARAM_INT);
        $stmt->execute();
        
        // テストユーザーを削除
        $sql = "DELETE FROM users WHERE id = :user_id";
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':user_id', $this->testUserId, PDO::PARAM_INT);
        $stmt->execute();
    }
    
    /**
     * 非空の文字列を生成するカスタムジェネレーター
     */
    private function nonEmptyString()
    {
        return Generator\bind(
            Generator\choose(1, 1000),
            function($length) {
                return Generator\bind(
                    Generator\seq(Generator\char(), $length),
                    function($chars) {
                        $str = implode('', $chars);
                        // 空白のみの文字列を避けるため、少なくとも1文字は非空白にする
                        if (trim($str) === '') {
                            $str = 'a' . $str;
                        }
                        return Generator\constant($str);
                    }
                );
            }
        );
    }
    
    /**
     * Feature: evaluation-feedback-system, Property 1: チーム計画の保存整合性
     * 
     * 任意の有効なチーム計画テキストとユーザーIDに対して、
     * 計画を保存した後にデータベースから取得すると、
     * 元のテキスト、ユーザーID、タイムスタンプが正しく保存されていなければならない
     * 
     * **検証: 要件 1.1, 9.1**
     */
    public function testTeamPlanStorageConsistency()
    {
        $this->forAll(
            $this->nonEmptyString()
        )
        ->withMaxSize(100) // 最大100回の反復
        ->then(function ($planText) {
            // 計画を保存
            $planId = saveTeamPlan($this->dbh, $this->testUserId, $planText);
            
            // 保存が成功したことを確認
            $this->assertNotFalse($planId, "チーム計画の保存に失敗しました");
            $this->assertIsNumeric($planId, "保存されたIDは数値でなければなりません");
            $planId = (int)$planId; // 整数に変換
            
            // データベースから取得
            $retrieved = getTeamPlanById($this->dbh, $planId);
            
            // 取得が成功したことを確認
            $this->assertNotFalse($retrieved, "チーム計画の取得に失敗しました");
            $this->assertIsArray($retrieved, "取得されたデータは配列でなければなりません");
            
            // 元のデータと一致することを確認
            $this->assertEquals(
                $planText, 
                $retrieved['plan_text'],
                "保存されたテキストが元のテキストと一致しません"
            );
            
            $this->assertEquals(
                $this->testUserId, 
                $retrieved['user_id'],
                "保存されたユーザーIDが元のユーザーIDと一致しません"
            );
            
            // タイムスタンプが設定されていることを確認
            $this->assertNotNull(
                $retrieved['created_at'],
                "作成タイムスタンプが設定されていません"
            );
            
            // タイムスタンプが妥当な形式であることを確認
            $this->assertMatchesRegularExpression(
                '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/',
                $retrieved['created_at'],
                "作成タイムスタンプの形式が不正です"
            );
            
            // クリーンアップ: このテストで作成した計画を削除
            $sql = "DELETE FROM team_plans WHERE id = :plan_id";
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindParam(':plan_id', $planId, PDO::PARAM_INT);
            $stmt->execute();
        });
    }
}
