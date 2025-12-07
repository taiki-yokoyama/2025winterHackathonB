<?php
// セッションが開始されていない場合は開始
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$is_logged_in = isset($_SESSION['user_id']);

// ログイン済みの場合、ユーザー情報を取得
if ($is_logged_in) {
    require_once __DIR__ . '/../dbconnect.php';
    $stmt = $dbh->prepare('SELECT name, yokomoku, tatemoku, current_mode, coins FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $user_info = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user_info) {
        $_SESSION['current_mode'] = $user_info['current_mode'] ?? 'yokomoku';
        $_SESSION['coins'] = $user_info['coins'];
        $current_team = $user_info['current_mode'] === 'yokomoku' ? $user_info['yokomoku'] : $user_info['tatemoku'];
    }
}
?>
<header class="mb-8 mt-4">
    <div class="max-w-7xl mx-auto px-4">
        <div class="toy-box p-4 bg-[#FFD700] transform -rotate-1 relative">
            <div class="absolute -top-3 -left-3 w-8 h-8 bg-red-500 rounded-full border-4 border-black"></div>
            <div class="absolute -top-3 -right-3 w-8 h-8 bg-blue-500 rounded-full border-4 border-black"></div>
            
            <div class="flex flex-col gap-4">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <!-- ロゴ -->
                    <a href="/" class="flex items-center gap-3 hover:scale-105 transition">
                        <img src="/assets/img/logo.png" alt="③でPON" class="w-16 h-16 md:w-20 md:h-20 object-contain">
                        <h1 class="font-heavy text-3xl md:text-4xl text-black [text-shadow:2px_2px_0_#fff]">
                            ③でPON
                        </h1>
                    </a>
                    
                    <!-- ナビゲーション -->
                    <nav class="flex flex-wrap items-center justify-center gap-2">
                    <?php if ($is_logged_in): ?>
                        <!-- ログイン済み -->
                        <a href="/plan/index.php" class="px-3 py-2 bg-[#FF69B4] text-white font-bold border-4 border-black shadow-[4px_4px_0_#000] hover:translate-y-1 hover:shadow-[2px_2px_0_#000] transition">
                            Plan
                        </a>
                        <a href="/check/check.php" class="px-3 py-2 bg-[#87CEEB] text-white font-bold border-4 border-black shadow-[4px_4px_0_#000] hover:translate-y-1 hover:shadow-[2px_2px_0_#000] transition">
                            Check
                        </a>
                        <a href="/action/index.php" class="px-3 py-2 bg-[#32CD32] text-white font-bold border-4 border-black shadow-[4px_4px_0_#000] hover:translate-y-1 hover:shadow-[2px_2px_0_#000] transition">
                            Action
                        </a>
                        <a href="/gacha/gacha.php" class="px-3 py-2 bg-[#9370DB] text-white font-bold border-4 border-black shadow-[4px_4px_0_#000] hover:translate-y-1 hover:shadow-[2px_2px_0_#000] transition">
                            Gacha
                        </a>
                        
                        <!-- コイン表示 -->
                        <div class="px-3 py-2 bg-white border-4 border-black font-bold shadow-[4px_4px_0_#000]">
                            🪙 <?php echo $_SESSION['coins'] ?? 0; ?>
                        </div>
                        
                        <!-- マイページ -->
                        <a href="/mypage/index.php" class="px-3 py-2 bg-[#FFB6C1] text-white font-bold border-4 border-black shadow-[4px_4px_0_#000] hover:translate-y-1 hover:shadow-[2px_2px_0_#000] transition">
                            👤 マイページ
                        </a>
                        
                        <!-- ログアウト -->
                        <a href="/auth/logout.php" class="px-3 py-2 bg-gray-600 text-white font-bold border-4 border-black shadow-[4px_4px_0_#000] hover:translate-y-1 hover:shadow-[2px_2px_0_#000] transition">
                            ログアウト
                        </a>
                    <?php else: ?>
                        <!-- 未ログイン -->
                        <a href="/auth/login.php" class="px-4 py-2 bg-[#87CEEB] text-white font-bold border-4 border-black shadow-[4px_4px_0_#000] hover:translate-y-1 hover:shadow-[2px_2px_0_#000] transition">
                            ログイン 🔑
                        </a>
                        <a href="/auth/register.php" class="px-4 py-2 bg-[#FF69B4] text-white font-bold border-4 border-black shadow-[4px_4px_0_#000] hover:translate-y-1 hover:shadow-[2px_2px_0_#000] transition">
                            新規登録 ✨
                        </a>
                    <?php endif; ?>
                </nav>
                </div>
                
                <?php if ($is_logged_in && isset($user_info)): ?>
                <!-- チーム切り替えバー -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 bg-white border-4 border-black p-3 shadow-[4px_4px_0_#000]">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-sm">現在のチーム:</span>
                        <span class="px-3 py-1 bg-gradient-to-r from-pink-400 to-purple-400 text-white font-heavy border-2 border-black shadow-sm">
                            <?php echo htmlspecialchars($current_team ?? '未設定'); ?>
                        </span>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-xs">切り替え:</span>
                        <a href="/switch_mode.php?mode=yokomoku" class="px-3 py-1 <?php echo $_SESSION['current_mode'] === 'yokomoku' ? 'bg-pink-500 text-white' : 'bg-white text-black'; ?> font-bold border-2 border-black hover:scale-105 transition text-sm">
                            横もく
                        </a>
                        <a href="/switch_mode.php?mode=tatemoku" class="px-3 py-1 <?php echo $_SESSION['current_mode'] === 'tatemoku' ? 'bg-purple-500 text-white' : 'bg-white text-black'; ?> font-bold border-2 border-black hover:scale-105 transition text-sm">
                            縦もく
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<style>
    .font-heavy {
        font-family: 'Dela Gothic One', sans-serif;
    }
    .toy-box {
        border: 6px solid #000;
        box-shadow: 12px 12px 0 #000;
    }
</style>
