<?php
session_start();

// ログインチェック
if (!isset($_SESSION['user_id'])) {
    header('Location: /');
    exit;
}

require_once '../dbconnect.php';

$error = '';
$success = '';
$is_first = isset($_GET['first']);

// ユーザー情報取得
$stmt = $dbh->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $generation = $_POST['generation'] ?? '';
    $yokomoku = $_POST['yokomoku'] ?? '';
    $tatemoku = $_POST['tatemoku'] ?? '';
    
    // バリデーション
    if (empty($name) || empty($generation) || empty($yokomoku) || empty($tatemoku)) {
        $error = 'すべての項目を入力してください';
    } else {
        try {
            // アイコン画像のアップロード処理
            $icon_filename = $user['icon'];
            if (isset($_FILES['icon']) && $_FILES['icon']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../assets/img/';
                $file_extension = pathinfo($_FILES['icon']['name'], PATHINFO_EXTENSION);
                $icon_filename = 'user_' . $_SESSION['user_id'] . '_' . time() . '.' . $file_extension;
                
                if (move_uploaded_file($_FILES['icon']['tmp_name'], $upload_dir . $icon_filename)) {
                    // 古いアイコンを削除
                    if ($user['icon'] && file_exists($upload_dir . $user['icon'])) {
                        unlink($upload_dir . $user['icon']);
                    }
                }
            }
            
            // ユーザー情報更新
            $stmt = $dbh->prepare('UPDATE users SET name = ?, generation = ?, icon = ?, yokomoku = ?, tatemoku = ? WHERE id = ?');
            $stmt->execute([$name, $generation, $icon_filename, $yokomoku, $tatemoku, $_SESSION['user_id']]);
            
            // マイページへリダイレクト
            header('Location: /mypage/');
            exit;
            
        } catch (PDOException $e) {
            $error = '更新に失敗しました。もう一度お試しください。';
        }
    }
}

// 横もくと縦もくの選択肢
$yokomoku_options = [
    '横もく5A', '横もく5B', '横もく5C', '横もく5D', '横もく5E', '横もく5F', '横もく5G', '横もく5H',
    '横もく6A', '横もく6B', '横もく6C', '横もく6D', '横もく6E', '横もく6F', '横もく6G', '横もく6H'
];
$tatemoku_options = ['縦もくA', '縦もくB', '縦もくC', '縦もくD', '縦もくE', '縦もくF', '縦もくG', '縦もくH', '縦もくI'];
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_first ? 'プロフィール登録' : 'プロフィール編集'; ?> - ③でPON</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Dela+Gothic+One&family=DotGothic16&family=M+PLUS+Rounded+1c:wght@700;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'M PLUS Rounded 1c', sans-serif;
            background: linear-gradient(135deg, #FFB6C1 0%, #87CEEB 50%, #98FB98 100%);
            min-height: 100vh;
        }
        .font-heavy { font-family: 'Dela Gothic One', sans-serif; }
        .toy-box { border: 6px solid #000; box-shadow: 12px 12px 0 #000; }
    </style>
</head>
<body class="p-4 md:p-8">
    
    <?php include '../components/header.php'; ?>
    
    <div class="max-w-2xl mx-auto">

        <!-- 編集フォーム -->
        <section class="toy-box p-6 md:p-8 bg-white relative">
            <div class="absolute -top-6 left-1/2 transform -translate-x-1/2 bg-[#FF69B4] border-4 border-black px-6 py-2 font-heavy text-xl shadow-[4px_4px_0_#000] rotate-2 z-10 text-white">
                <?php echo $is_first ? 'プロフィール登録 ✨' : 'プロフィール編集 ✏️'; ?>
            </div>
            
            <div class="mt-8">
                <?php if ($error): ?>
                    <div class="bg-red-100 border-4 border-red-500 text-red-700 px-4 py-3 mb-6 font-bold transform -rotate-1">
                        ⚠️ <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="bg-green-100 border-4 border-green-500 text-green-700 px-4 py-3 mb-6 font-bold transform rotate-1">
                        ✅ <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" enctype="multipart/form-data" class="space-y-6">
                    <!-- 名前 -->
                    <div>
                        <label for="name" class="block font-bold text-lg mb-2">👤 名前（ユーザー名）</label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            required
                            value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>"
                            class="w-full px-4 py-3 border-4 border-black text-lg focus:outline-none focus:border-[#FF69B4] transition"
                            placeholder="山田太郎"
                        >
                    </div>
                    
                    <!-- 期生 -->
                    <div>
                        <label for="generation" class="block font-bold text-lg mb-2">🎓 期生</label>
                        <select 
                            id="generation" 
                            name="generation" 
                            required
                            class="w-full px-4 py-3 border-4 border-black text-lg focus:outline-none focus:border-[#FF69B4] transition"
                        >
                            <option value="">選択してください</option>
                            <option value="5" <?php echo ($user['generation'] ?? '') === '5' ? 'selected' : ''; ?>>5期</option>
                            <option value="5.5" <?php echo ($user['generation'] ?? '') === '5.5' ? 'selected' : ''; ?>>5.5期</option>
                            <option value="6" <?php echo ($user['generation'] ?? '') === '6' ? 'selected' : ''; ?>>6期</option>
                            <option value="6.5" <?php echo ($user['generation'] ?? '') === '6.5' ? 'selected' : ''; ?>>6.5期</option>
                        </select>
                    </div>
                    
                    <!-- アイコン -->
                    <div>
                        <label for="icon" class="block font-bold text-lg mb-2">📷 アイコン画像</label>
                        <?php if ($user['icon']): ?>
                            <div class="mb-2">
                                <img src="/assets/img/<?php echo htmlspecialchars($user['icon']); ?>" alt="現在のアイコン" class="w-20 h-20 object-cover border-4 border-black">
                            </div>
                        <?php endif; ?>
                        <input 
                            type="file" 
                            id="icon" 
                            name="icon" 
                            accept="image/*"
                            class="w-full px-4 py-3 border-4 border-black text-lg focus:outline-none focus:border-[#FF69B4] transition"
                        >
                        <p class="text-sm text-gray-600 mt-1">※ 画像を選択しない場合は現在のアイコンが維持されます</p>
                    </div>
                    
                    <!-- 横もく -->
                    <div>
                        <label for="yokomoku" class="block font-bold text-lg mb-2">➡️ 横もく</label>
                        <select 
                            id="yokomoku" 
                            name="yokomoku" 
                            required
                            class="w-full px-4 py-3 border-4 border-black text-lg focus:outline-none focus:border-[#FF69B4] transition"
                        >
                            <option value="">選択してください</option>
                            <?php foreach ($yokomoku_options as $option): ?>
                                <option value="<?php echo $option; ?>" <?php echo ($user['yokomoku'] ?? '') === $option ? 'selected' : ''; ?>>
                                    <?php echo $option; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- 縦もく -->
                    <div>
                        <label for="tatemoku" class="block font-bold text-lg mb-2">⬇️ 縦もく</label>
                        <select 
                            id="tatemoku" 
                            name="tatemoku" 
                            required
                            class="w-full px-4 py-3 border-4 border-black text-lg focus:outline-none focus:border-[#FF69B4] transition"
                        >
                            <option value="">選択してください</option>
                            <?php foreach ($tatemoku_options as $option): ?>
                                <option value="<?php echo $option; ?>" <?php echo ($user['tatemoku'] ?? '') === $option ? 'selected' : ''; ?>>
                                    <?php echo $option; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- 保存ボタン -->
                    <button 
                        type="submit"
                        class="w-full bg-[#FF69B4] text-white font-heavy text-2xl py-4 border-4 border-black shadow-[8px_8px_0_#000] hover:translate-y-2 hover:shadow-[4px_4px_0_#000] transition-all transform hover:scale-105"
                    >
                        <?php echo $is_first ? '登録する 🚀' : '更新する 💾'; ?>
                    </button>
                </form>
                
                <?php if (!$is_first): ?>
                    <!-- キャンセルボタン -->
                    <div class="mt-4 text-center">
                        <a href="/mypage/" class="text-gray-600 hover:underline">← マイページに戻る</a>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        
    </div>

</body>
</html>
