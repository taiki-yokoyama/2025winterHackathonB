<?php
session_start();

// ログインチェック
if (!isset($_SESSION['user_id'])) {
    header('Location: /');
    exit;
}

require_once '../dbconnect.php';

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

// Plan作成処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_plan'])) {
    $content = trim($_POST['content'] ?? '');
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    
    if (empty($content)) {
        $error = '行動計画を入力してください';
    } elseif (empty($start_date) || empty($end_date)) {
        $error = '開始日と終了日を入力してください';
    } else {
        $stmt = $dbh->prepare('INSERT INTO plans (user_id, content, start_date, end_date, status, created_at) VALUES (?, ?, ?, ?, "running", NOW())');
        if ($stmt->execute([$user_id, $content, $start_date, $end_date])) {
            $message = 'Planを追加しました！';
            header('Location: ?sub=my');
            exit;
        } else {
            $error = 'Planの追加に失敗しました';
        }
    }
}

// Plan編集処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_plan'])) {
    $plan_id = $_POST['plan_id'] ?? 0;
    $content = trim($_POST['content'] ?? '');
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $status = $_POST['status'] ?? 'running';
    
    $stmt = $dbh->prepare('UPDATE plans SET content = ?, start_date = ?, end_date = ?, status = ? WHERE id = ? AND user_id = ?');
    if ($stmt->execute([$content, $start_date, $end_date, $status, $plan_id, $user_id])) {
        $message = 'Planを更新しました！';
        header('Location: ?sub=my');
        exit;
    } else {
        $error = 'Planの更新に失敗しました';
    }
}

// サブページ判定
$sub = isset($_GET['sub']) ? $_GET['sub'] : 'create';
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$member_filter = isset($_GET['member']) ? (int)$_GET['member'] : 0;

// 自分のPlan取得
if ($sub === 'my') {
    $query = 'SELECT * FROM plans WHERE user_id = ?';
    $params = [$user_id];
    
    if ($status_filter === 'completed') {
        $query .= ' AND status = "completed"';
    } elseif ($status_filter === 'running') {
        $query .= ' AND status = "running"';
    }
    
    $query .= ' ORDER BY created_at DESC';
    $stmt = $dbh->prepare($query);
    $stmt->execute($params);
    $my_plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// チームのPlan取得
if ($sub === 'team') {
    $query = 'SELECT p.*, u.name as user_name FROM plans p JOIN users u ON p.user_id = u.id WHERE p.user_id != ?';
    $params = [$user_id];
    
    if ($member_filter > 0) {
        $query .= ' AND p.user_id = ?';
        $params[] = $member_filter;
    }
    
    $query .= ' ORDER BY p.created_at DESC';
    $stmt = $dbh->prepare($query);
    $stmt->execute($params);
    $team_plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // チームメンバー取得
    $stmt = $dbh->prepare('SELECT id, name FROM users WHERE id != ? ORDER BY name');
    $stmt->execute([$user_id]);
    $team_members = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// トイボックス風のサブメニューボタン関数
function getToySubNav($current, $target, $label, $color) {
    $isActive = $current === $target;
    $base = "flex-1 py-3 text-center font-heavy text-lg border-4 border-black transition-all transform ";
    
    if ($isActive) {
        return $base . "bg-{$color}-500 text-white shadow-none translate-y-2 scale-95 cursor-default relative z-0";
    }
    return $base . "bg-white text-black shadow-[4px_4px_0_#000] hover:-translate-y-1 hover:shadow-[6px_6px_0_#000] hover:bg-{$color}-100 cursor-pointer relative z-10";
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plan - ③でPON</title>
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
    
    <div class="max-w-6xl mx-auto mt-12 md:mt-16">

        <?php if ($message): ?>
            <div class="bg-green-100 border-4 border-green-500 text-green-700 px-4 py-3 mb-6 font-bold transform -rotate-1 text-center">
                ✅ <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-red-100 border-4 border-red-500 text-red-700 px-4 py-3 mb-6 font-bold transform -rotate-1 text-center">
                ⚠️ <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
            <!-- エラーメッセージ表示（要件 9.4） -->
            <div class="bg-red-100 border-4 border-red-500 p-4 rounded-lg shadow-[4px_4px_0_#000] mb-6">
                <div class="flex items-center">
                    <i class="fa-solid fa-exclamation-triangle text-2xl text-red-500 mr-3"></i>
                    <p class="text-red-700 font-bold"><?php echo escapeHtml($errorMessage); ?></p>
                </div>
            </div>
        <?php endif; ?>

        <div class="flex gap-4 mb-6">
            <a href="?sub=create" class="<?php echo getToySubNav($sub, 'create', '作成', 'pink'); ?>">
                <i class="fa-solid fa-pen-nib mr-1"></i> MAKE
            </a>
            <a href="?sub=my" class="<?php echo getToySubNav($sub, 'my', '自分', 'yellow'); ?>">
                <i class="fa-solid fa-user mr-1"></i> MINE
            </a>
            <a href="?sub=team" class="<?php echo getToySubNav($sub, 'team', 'みんな', 'blue'); ?>">
                <i class="fa-solid fa-users mr-1"></i> TEAM
            </a>
        </div>

        <?php if ($sub === 'create'): ?>
        <!-- MAKE: Plan作成 -->
        <div class="toy-box p-6 bg-[#FF69B4] relative">
            <div class="absolute -top-5 left-1/2 transform -translate-x-1/2 bg-yellow-400 border-4 border-black px-6 py-2 font-heavy text-xl shadow-[4px_4px_0_#000] rotate-2 z-10 whitespace-nowrap">
                NEW MISSION 📝
            </div>

            <form action="" method="POST" class="mt-8 space-y-6">
                <div>
                    <label class="font-heavy text-white text-lg drop-shadow-md mb-2 block">
                        <i class="fa-solid fa-bullseye"></i> 具体的な行動計画
                    </label>
                    <div class="relative p-2 bg-white border-4 border-black shadow-inner">
                        <textarea name="content" required class="w-full h-40 bg-transparent resize-none focus:outline-none font-heavy text-gray-800 text-xl leading-relaxed placeholder-pink-200 p-2" 
                            style="background-image: repeating-linear-gradient(transparent, transparent 38px, #ffb6c1 39px, #ffb6c1 40px); line-height: 40px;"
                            placeholder="例：&#13;&#10;ペアプロの時間を増やす！"></textarea>
                        <div class="absolute bottom-2 right-2 text-3xl transform rotate-12">🖍️</div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="font-heavy text-white text-sm drop-shadow-md mb-1 block">START DATE</label>
                        <input type="date" name="start_date" required class="w-full bg-white border-4 border-black p-2 font-bold shadow-[4px_4px_0_#000] focus:translate-y-1 focus:shadow-none outline-none">
                    </div>
                    <div>
                        <label class="font-heavy text-white text-sm drop-shadow-md mb-1 block">END DATE</label>
                        <input type="date" name="end_date" required class="w-full bg-white border-4 border-black p-2 font-bold shadow-[4px_4px_0_#000] focus:translate-y-1 focus:shadow-none outline-none">
                    </div>
                </div>

                <button type="submit" name="create_plan" class="w-full bg-[#00FFFF] text-black font-heavy text-2xl py-4 border-4 border-black shadow-[8px_8px_0_#000] hover:translate-y-2 hover:shadow-[4px_4px_0_#000] transition-all group relative overflow-hidden">
                    <span class="relative z-10 group-hover:scale-110 inline-block transition"><i class="fa-solid fa-plus mr-2"></i> PLANを追加！</span>
                </button>
            </form>
        </div>

        <?php elseif ($sub === 'my'): ?>
        <!-- MINE: 自分のPlan一覧 -->
        <div class="toy-box p-6 bg-[#FFD700] relative">
            <div class="bg-white border-4 border-black p-4 mb-6 shadow-[4px_4px_0_rgba(0,0,0,0.2)]">
                <div>
                    <div class="text-xs font-heavy mb-2">▼ ステータス</div>
                    <div class="flex flex-wrap gap-2">
                        <a href="?sub=my&status=all" class="<?php echo $status_filter === 'all' ? 'bg-blue-500 text-white' : 'bg-white text-black hover:bg-gray-100'; ?> px-3 py-1 font-bold border-2 border-black">すべて</a>
                        <a href="?sub=my&status=running" class="<?php echo $status_filter === 'running' ? 'bg-blue-500 text-white' : 'bg-white text-black hover:bg-gray-100'; ?> px-3 py-1 font-bold border-2 border-black">進行中</a>
                        <a href="?sub=my&status=completed" class="<?php echo $status_filter === 'completed' ? 'bg-blue-500 text-white' : 'bg-white text-black hover:bg-gray-100'; ?> px-3 py-1 font-bold border-2 border-black">完了</a>
                    </div>
                </div>
            </div>

            <div class="space-y-4 max-h-[600px] overflow-y-auto pr-2">
                <?php if (empty($my_plans)): ?>
                    <div class="bg-white border-4 border-gray-300 p-8 text-center">
                        <p class="text-gray-500 font-bold">まだPlanがありません</p>
                        <a href="?sub=create" class="inline-block mt-4 bg-pink-500 text-white px-6 py-2 font-bold border-2 border-black hover:bg-pink-600">
                            Planを作成する
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($my_plans as $plan): ?>
                        <div class="bg-white border-4 border-black p-4 shadow-[6px_6px_0_#FFA500] relative group hover:scale-[1.02] transition-transform cursor-pointer" onclick="location.href='detail.php?id=<?php echo $plan['id']; ?>'">
                            <div class="flex justify-between items-start mb-2">
                                <?php if ($plan['status'] === 'running'): ?>
                                    <span class="bg-yellow-400 text-black text-xs font-heavy px-2 py-1 border-2 border-black">
                                        <i class="fa-solid fa-person-running"></i> RUNNING!
                                    </span>
                                <?php elseif ($plan['status'] === 'completed'): ?>
                                    <span class="bg-green-500 text-white text-xs font-heavy px-2 py-1 border-2 border-black">
                                        <i class="fa-solid fa-check"></i> CLEAR!!
                                    </span>
                                <?php else: ?>
                                    <span class="bg-gray-400 text-white text-xs font-heavy px-2 py-1 border-2 border-black">
                                        <i class="fa-solid fa-ban"></i> CANCELLED
                                    </span>
                                <?php endif; ?>
                                <a href="edit.php?id=<?php echo $plan['id']; ?>" onclick="event.stopPropagation()" class="w-8 h-8 bg-gray-200 border-2 border-black flex items-center justify-center hover:bg-blue-300">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                            </div>
                            <h3 class="font-heavy text-lg leading-tight mb-2 <?php echo $plan['status'] === 'completed' ? 'line-through text-gray-500' : ''; ?>">
                                <?php echo htmlspecialchars($plan['content']); ?>
                            </h3>
                            <div class="text-xs font-bold text-gray-500">
                                <i class="fa-regular fa-calendar"></i> 
                                <?php echo date('Y/m/d', strtotime($plan['start_date'])); ?> ～ 
                                <?php echo date('Y/m/d', strtotime($plan['end_date'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <?php elseif ($sub === 'team'): ?>
        <!-- TEAM: チームのPlan一覧 -->
        <div class="bg-[#32CD32] border-4 border-black p-6 shadow-[12px_12px_0_#006400] relative rounded-[1rem]">
            <div class="bg-white border-4 border-black p-4 mb-6 relative z-10">
                <div class="text-xs font-heavy mb-2 text-center">▼ メンバーで絞り込み</div>
                <div class="flex justify-center flex-wrap gap-3">
                    <a href="?sub=team&member=0" class="w-10 h-10 rounded-full <?php echo $member_filter === 0 ? 'bg-blue-500' : 'bg-gray-300'; ?> text-white font-heavy border-2 border-black hover:scale-110 transition shadow-[2px_2px_0_#000] flex items-center justify-center">全</a>
                    <?php foreach ($team_members as $member): ?>
                        <a href="?sub=team&member=<?php echo $member['id']; ?>" class="w-10 h-10 rounded-full <?php echo $member_filter === $member['id'] ? 'bg-pink-400' : 'bg-gray-300'; ?> text-white font-heavy border-2 border-black hover:scale-110 transition shadow-[2px_2px_0_#000] flex items-center justify-center text-xs" title="<?php echo htmlspecialchars($member['name']); ?>">
                            <?php echo mb_substr($member['name'], 0, 1); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="space-y-6 max-h-[600px] overflow-y-auto pr-2">
                <?php if (empty($team_plans)): ?>
                    <div class="bg-white border-4 border-black p-8 text-center">
                        <p class="text-gray-700 font-bold">チームのPlanがありません</p>
                    </div>
                <?php else: ?>
                    <?php 
                    $grouped_plans = [];
                    foreach ($team_plans as $plan) {
                        $grouped_plans[$plan['user_id']][] = $plan;
                    }
                    ?>
                    <?php foreach ($grouped_plans as $uid => $plans): ?>
                        <div class="relative pl-4 border-l-4 border-dashed border-black/30">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-8 h-8 rounded-full bg-pink-400 border-2 border-black text-white flex items-center justify-center font-heavy text-xs">
                                    <?php echo mb_substr($plans[0]['user_name'], 0, 1); ?>
                                </div>
                                <span class="font-heavy bg-white px-2 border-2 border-black shadow-[2px_2px_0_#000]">
                                    <?php echo htmlspecialchars($plans[0]['user_name']); ?>
                                </span>
                            </div>

                            <?php foreach ($plans as $plan): ?>
                                <div class="bg-white border-4 border-black p-3 mb-3 shadow-[4px_4px_0_#FF69B4] transition cursor-pointer hover:scale-[1.02]" onclick="location.href='detail.php?id=<?php echo $plan['id']; ?>'">
                                    <div class="flex justify-between items-start">
                                        <?php if ($plan['status'] === 'running'): ?>
                                            <span class="bg-yellow-400 text-[10px] font-heavy px-1 border border-black mb-1 inline-block">RUNNING</span>
                                        <?php elseif ($plan['status'] === 'completed'): ?>
                                            <span class="bg-green-500 text-white text-[10px] font-heavy px-1 border border-black mb-1 inline-block">CLEAR</span>
                                        <?php endif; ?>
                                        <i class="fa-regular fa-comment text-gray-400 hover:text-blue-500 cursor-pointer"></i>
                                    </div>
                                    <p class="font-bold text-sm leading-tight"><?php echo htmlspecialchars($plan['content']); ?></p>
                                    <div class="text-[10px] text-gray-500 mt-1">
                                        <?php echo date('Y/m/d', strtotime($plan['start_date'])); ?> - <?php echo date('m/d', strtotime($plan['end_date'])); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="absolute inset-0 bg-[radial-gradient(#000_2px,transparent_2px)] bg-[size:20px_20px] opacity-10 pointer-events-none z-0"></div>
        </div>
        <?php endif; ?>

    </div>

    <?php include '../components/footer.php'; ?>

</body>
</html>
