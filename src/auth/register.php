<?php
session_start();

// 既にログイン済みの場合はリダイレクト
if (isset($_SESSION['user_id'])) {
    header('Location: /plan/');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    
    // バリデーション
    if (empty($email) || empty($password) || empty($password_confirm)) {
        $error = 'すべての項目を入力してください';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = '有効なメールアドレスを入力してください';
    } elseif ($password !== $password_confirm) {
        $error = 'パスワードが一致しません';
    } elseif (strlen($password) < 6) {
        $error = 'パスワードは6文字以上で入力してください';
    } else {
        // データベース接続
        require_once '../dbconnect.php';
        
        try {
            // メールアドレスの重複チェック
            $stmt = $dbh->prepare('SELECT id FROM users WHERE email = ?');
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                $error = 'このメールアドレスは既に登録されています';
            } else {
                // ユーザー登録
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $dbh->prepare('INSERT INTO users (email, password, created_at) VALUES (?, ?, NOW())');
                $stmt->execute([$email, $hashed_password]);
                
                // セッションに保存してログイン状態に
                $_SESSION['user_id'] = $dbh->lastInsertId();
                $_SESSION['email'] = $email;
                $_SESSION['coins'] = 3;
                
                // マイページにリダイレクト
                header('Location: /mypage/');
                exit;
            }
        } catch (PDOException $e) {
            $error = '登録に失敗しました。もう一度お試しください。';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新規登録 - ③でPON</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Dela+Gothic+One&family=DotGothic16&family=M+PLUS+Rounded+1c:wght@700;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'M PLUS Rounded 1c', sans-serif;
            background: linear-gradient(135deg, #FFB6C1 0%, #87CEEB 50%, #98FB98 100%);
            min-height: 100vh;
        }
        
        .font-heavy {
            font-family: 'Dela Gothic One', sans-serif;
        }
        
        .toy-box {
            border: 6px solid #000;
            box-shadow: 12px 12px 0 #000;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .float-animation {
            animation: float 3s ease-in-out infinite;
        }
    </style>
</head>
<body class="p-4 md:p-8">
    
    <?php include '../components/header.php'; ?>
    
    <div class="max-w-md mx-auto">

        <!-- 登録フォーム -->
        <section class="toy-box p-6 md:p-8 bg-white relative">
            <div class="absolute -top-6 left-1/2 transform -translate-x-1/2 bg-[#FF69B4] border-4 border-black px-6 py-2 font-heavy text-xl shadow-[4px_4px_0_#000] rotate-2 z-10 text-white">
                新規登録 ✨
            </div>
            
            <div class="mt-8">
                <?php if ($error): ?>
                    <div class="bg-red-100 border-4 border-red-500 text-red-700 px-4 py-3 mb-6 font-bold transform -rotate-1">
                        ⚠️ <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" class="space-y-6">
                    <!-- メールアドレス -->
                    <div>
                        <label for="email" class="block font-bold text-lg mb-2">
                            📧 メールアドレス
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            required
                            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                            class="w-full px-4 py-3 border-4 border-black text-lg focus:outline-none focus:border-[#FF69B4] transition"
                            placeholder="example@email.com"
                        >
                    </div>
                    
                    <!-- パスワード -->
                    <div>
                        <label for="password" class="block font-bold text-lg mb-2">
                            🔒 パスワード
                        </label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required
                            minlength="6"
                            class="w-full px-4 py-3 border-4 border-black text-lg focus:outline-none focus:border-[#FF69B4] transition"
                            placeholder="6文字以上"
                        >
                    </div>
                    
                    <!-- パスワード確認 -->
                    <div>
                        <label for="password_confirm" class="block font-bold text-lg mb-2">
                            🔒 パスワード（確認）
                        </label>
                        <input 
                            type="password" 
                            id="password_confirm" 
                            name="password_confirm" 
                            required
                            minlength="6"
                            class="w-full px-4 py-3 border-4 border-black text-lg focus:outline-none focus:border-[#FF69B4] transition"
                            placeholder="もう一度入力"
                        >
                    </div>
                    
                    <!-- 登録ボタン -->
                    <button 
                        type="submit"
                        class="w-full bg-[#FF69B4] text-white font-heavy text-2xl py-4 border-4 border-black shadow-[8px_8px_0_#000] hover:translate-y-2 hover:shadow-[4px_4px_0_#000] transition-all transform hover:scale-105"
                    >
                        登録する 🚀
                    </button>
                </form>
                
                <!-- ログインリンク -->
                <div class="mt-6 text-center">
                    <p class="text-gray-600 mb-2">既にアカウントをお持ちの方</p>
                    <a 
                        href="/auth/login.php" 
                        class="inline-block bg-[#87CEEB] text-black font-bold text-lg py-2 px-6 border-4 border-black shadow-[4px_4px_0_#000] hover:translate-y-1 hover:shadow-[2px_2px_0_#000] transition"
                    >
                        ログイン
                    </a>
                </div>
            </div>
            
            <!-- 装飾 -->
            <div class="absolute top-4 right-4 text-3xl opacity-30 float-animation">⭐</div>
            <div class="absolute bottom-4 left-4 text-3xl opacity-30 float-animation" style="animation-delay: 0.5s;">✨</div>
        </section>
        
        <!-- トップに戻る -->
        <div class="mt-8 text-center">
            <a href="/" class="text-black font-bold hover:underline">
                ← トップページに戻る
            </a>
        </div>
        
    </div>

</body>
</html>
