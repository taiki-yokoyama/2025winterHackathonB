<?php
session_start();

// ログインチェック
if (!isset($_SESSION['user_id'])) {
    header('Location: /auth/login.php');
    exit;
}

require_once '../dbconnect.php';

$user_id = $_SESSION['user_id'];
$plan_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$error = '';

// Plan取得
$stmt = $dbh->prepare('SELECT * FROM plans WHERE id = ? AND user_id = ?');
$stmt->execute([$plan_id, $user_id]);
$plan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$plan) {
    header('Location: index.php?sub=my');
    exit;
}

// Plan更新処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = trim($_POST['content'] ?? '');
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $status = $_POST['status'] ?? 'running';
    
    if (empty($content)) {
        $error = '行動計画を入力してください';
    } elseif (empty($start_date) || empty($end_date)) {
        $error = '開始日と終了日を入力してください';
    } else {
        $stmt = $dbh->prepare('UPDATE plans SET content = ?, start_date = ?, end_date = ?, status = ? WHERE id = ? AND user_id = ?');
        if ($stmt->execute([$content, $start_date, $end_date, $status, $plan_id, $user_id])) {
            header('Location: index.php?sub=my');
            exit;
        } else {
            $error = 'Planの更新に失敗しました';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plan編集 - ③でPON</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Dela+Gothic+One&family=M+PLUS+Rounded+1c:wght@700;900&display=swap" rel="stylesheet">
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
    
    <div class="max-w-4xl mx-auto mt-12 md:mt-16">

        <?php if ($error): ?>
            <div class="bg-red-100 border-4 border-red-500 text-red-700 px-4 py-3 mb-6 font-bold transform -rotate-1 text-center">
                ⚠️ <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="toy-box p-6 bg-[#87CEEB] relative">
            <div class="absolute -top-5 left-1/2 transform -translate-x-1/2 bg-yellow-400 border-4 border-black px-6 py-2 font-heavy text-xl shadow-[4px_4px_0_#000] rotate-2 z-10 whitespace-nowrap">
                EDIT PLAN ✏️
            </div>

            <form action="" method="POST" class="mt-8 space-y-6">
                <div>
                    <label class="font-heavy text-white text-lg drop-shadow-md mb-2 block">
                        <i class="fa-solid fa-bullseye"></i> 具体的な行動計画
                    </label>
                    <div class="relative p-2 bg-white border-4 border-black shadow-inner">
                        <textarea name="content" required class="w-full h-40 bg-transparent resize-none focus:outline-none font-heavy text-gray-800 text-xl leading-relaxed p-2"><?php echo htmlspecialchars($plan['content']); ?></textarea>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="font-heavy text-white text-sm drop-shadow-md mb-1 block">START DATE</label>
                        <input type="date" name="start_date" required value="<?php echo htmlspecialchars($plan['start_date']); ?>" class="w-full bg-white border-4 border-black p-2 font-bold shadow-[4px_4px_0_#000] focus:translate-y-1 focus:shadow-none outline-none">
                    </div>
                    <div>
                        <label class="font-heavy text-white text-sm drop-shadow-md mb-1 block">END DATE</label>
                        <input type="date" name="end_date" required value="<?php echo htmlspecialchars($plan['end_date']); ?>" class="w-full bg-white border-4 border-black p-2 font-bold shadow-[4px_4px_0_#000] focus:translate-y-1 focus:shadow-none outline-none">
                    </div>
                </div>

                <div>
                    <label class="font-heavy text-white text-sm drop-shadow-md mb-1 block">ステータス</label>
                    <select name="status" class="w-full bg-white border-4 border-black p-2 font-bold shadow-[4px_4px_0_#000] focus:translate-y-1 focus:shadow-none outline-none">
                        <option value="running" <?php echo $plan['status'] === 'running' ? 'selected' : ''; ?>>進行中</option>
                        <option value="completed" <?php echo $plan['status'] === 'completed' ? 'selected' : ''; ?>>完了</option>
                        <option value="cancelled" <?php echo $plan['status'] === 'cancelled' ? 'selected' : ''; ?>>キャンセル</option>
                    </select>
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="flex-1 bg-[#00FFFF] text-black font-heavy text-xl py-4 border-4 border-black shadow-[8px_8px_0_#000] hover:translate-y-2 hover:shadow-[4px_4px_0_#000] transition-all">
                        <i class="fa-solid fa-save mr-2"></i> 更新する
                    </button>
                    <a href="index.php?sub=my" class="flex-1 bg-gray-400 text-white font-heavy text-xl py-4 border-4 border-black shadow-[8px_8px_0_#000] hover:translate-y-2 hover:shadow-[4px_4px_0_#000] transition-all text-center">
                        <i class="fa-solid fa-times mr-2"></i> キャンセル
                    </a>
                </div>
            </form>
        </div>
    </div>

    <?php include '../components/footer.php'; ?>

</body>
</html>
