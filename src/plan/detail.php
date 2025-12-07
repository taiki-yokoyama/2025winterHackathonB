<?php
session_start();

// ログインチェック
if (!isset($_SESSION['user_id'])) {
    header('Location: /');
    exit;
}

require_once '../dbconnect.php';

$user_id = $_SESSION['user_id'];
$plan_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Plan取得
$stmt = $dbh->prepare('SELECT p.*, u.name as user_name FROM plans p JOIN users u ON p.user_id = u.id WHERE p.id = ?');
$stmt->execute([$plan_id]);
$plan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$plan) {
    header('Location: index.php');
    exit;
}

$is_owner = ($plan['user_id'] == $user_id);

// コメント投稿処理（将来的に実装）
// 現在はプレースホルダーとして表示のみ
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plan詳細 - ③でPON</title>
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

        <!-- Plan詳細 -->
        <div class="toy-box p-6 md:p-8 bg-white relative mb-6">
            <div class="absolute -top-5 left-1/2 transform -translate-x-1/2 bg-[#9370DB] border-4 border-black px-6 py-2 font-heavy text-xl shadow-[4px_4px_0_#000] rotate-2 z-10 text-white">
                PLAN DETAIL 📋
            </div>

            <div class="mt-8">
                <!-- ユーザー情報 -->
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 rounded-full bg-pink-400 border-4 border-black text-white flex items-center justify-center font-heavy text-xl">
                        <?php echo mb_substr($plan['user_name'], 0, 1); ?>
                    </div>
                    <div>
                        <p class="font-bold text-lg"><?php echo htmlspecialchars($plan['user_name']); ?></p>
                        <p class="text-xs text-gray-500">
                            <i class="fa-regular fa-clock"></i> 
                            <?php echo date('Y/m/d H:i', strtotime($plan['created_at'])); ?>
                        </p>
                    </div>
                    <?php if ($is_owner): ?>
                        <a href="edit.php?id=<?php echo $plan['id']; ?>" class="ml-auto bg-blue-500 text-white px-4 py-2 font-bold border-2 border-black hover:bg-blue-600">
                            <i class="fa-solid fa-pen mr-1"></i> 編集
                        </a>
                    <?php endif; ?>
                </div>

                <!-- ステータス -->
                <div class="mb-4">
                    <?php if ($plan['status'] === 'running'): ?>
                        <span class="bg-yellow-400 text-black text-sm font-heavy px-3 py-1 border-2 border-black">
                            <i class="fa-solid fa-person-running"></i> RUNNING!
                        </span>
                    <?php elseif ($plan['status'] === 'completed'): ?>
                        <span class="bg-green-500 text-white text-sm font-heavy px-3 py-1 border-2 border-black">
                            <i class="fa-solid fa-check"></i> CLEAR!!
                        </span>
                    <?php else: ?>
                        <span class="bg-gray-400 text-white text-sm font-heavy px-3 py-1 border-2 border-black">
                            <i class="fa-solid fa-ban"></i> CANCELLED
                        </span>
                    <?php endif; ?>
                </div>

                <!-- 内容 -->
                <div class="bg-yellow-50 border-4 border-black p-6 mb-6">
                    <h2 class="font-heavy text-2xl leading-tight mb-4">
                        <?php echo nl2br(htmlspecialchars($plan['content'])); ?>
                    </h2>
                    <div class="text-sm font-bold text-gray-600">
                        <i class="fa-regular fa-calendar"></i> 
                        <?php echo date('Y年m月d日', strtotime($plan['start_date'])); ?> ～ 
                        <?php echo date('Y年m月d日', strtotime($plan['end_date'])); ?>
                    </div>
                </div>

                <!-- コメントセクション（将来実装） -->
                <div class="bg-blue-50 border-4 border-black p-6">
                    <h3 class="font-heavy text-lg mb-4">
                        <i class="fa-regular fa-comments"></i> コメント
                    </h3>
                    <div class="bg-white border-2 border-dashed border-gray-300 p-8 text-center text-gray-400">
                        <i class="fa-regular fa-comment-dots text-4xl mb-2"></i>
                        <p class="font-bold">コメント機能は近日実装予定です</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 戻るボタン -->
        <div class="text-center">
            <a href="index.php?sub=<?php echo $is_owner ? 'my' : 'team'; ?>" class="inline-block bg-gray-600 text-white font-heavy text-lg py-3 px-8 border-4 border-black shadow-[6px_6px_0_#000] hover:translate-y-2 hover:shadow-[3px_3px_0_#000] transition">
                <i class="fa-solid fa-arrow-left mr-2"></i> 戻る
            </a>
        </div>
    </div>

    <?php include '../components/footer.php'; ?>

</body>
</html>
