<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../dbconnect.php';
require_once '../plan_helpers.php'; // ①のファイルパスに合わせて

// 仮ログイン
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
}
$current_user_id = (int)$_SESSION['user_id'];

// サブページ判定
$sub = $_GET['sub'] ?? 'create';

// フィルタ
$period        = $_GET['period'] ?? 'all';      // all / this_week / last_week
$status        = $_GET['status'] ?? 'all';      // all / running / completed
$filter_user_id = $_GET['user'] ?? 'all';

// データ取得
$myPlans   = getMyPlans($dbh, $current_user_id, $period, $status);
$teamPlans = getTeamPlans($dbh, $current_user_id, $filter_user_id);
$users     = getAllUsers($dbh);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Plan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Mochiy+Pop+One&family=Yomogi&display=swap" rel="stylesheet">
</head>
<body class="min-h-screen bg-slate-100">
<?php include '../components/header.php'; ?>

<div class="h-full flex flex-col px-4 py-4">

    <!-- サブナビ -->
    <div class="flex gap-4 mb-6">
        <?php
        // create
        $isActive = $sub === 'create';
        ?>
        <a href="?page=plan&sub=create"
           class="flex-1 py-3 text-center font-bold text-lg border-4 border-black transition-all transform
           <?php echo $isActive
               ? 'bg-pink-500 text-white shadow-none translate-y-2 scale-95 cursor-default'
               : 'bg-white text-black shadow-[4px_4px_0_#000] hover:-translate-y-1 hover:shadow-[6px_6px_0_#000] hover:bg-pink-100 cursor-pointer'; ?>">
            <i class="fa-solid fa-pen-nib mr-1"></i> MAKE
        </a>

        <?php $isActive = $sub === 'my'; ?>
        <a href="?page=plan&sub=my&period=<?php echo htmlspecialchars($period); ?>&status=<?php echo htmlspecialchars($status); ?>"
           class="flex-1 py-3 text-center font-bold text-lg border-4 border-black transition-all transform
           <?php echo $isActive
               ? 'bg-yellow-400 text-white shadow-none translate-y-2 scale-95 cursor-default'
               : 'bg-white text-black shadow-[4px_4px_0_#000] hover:-translate-y-1 hover:shadow-[6px_6px_0_#000] hover:bg-yellow-100 cursor-pointer'; ?>">
            <i class="fa-solid fa-user mr-1"></i> MINE
        </a>

        <?php $isActive = $sub === 'team'; ?>
        <a href="?page=plan&sub=team&user=<?php echo htmlspecialchars($filter_user_id); ?>"
           class="flex-1 py-3 text-center font-bold text-lg border-4 border-black transition-all transform
           <?php echo $isActive
               ? 'bg-blue-500 text-white shadow-none translate-y-2 scale-95 cursor-default'
               : 'bg-white text-black shadow-[4px_4px_0_#000] hover:-translate-y-1 hover:shadow-[6px_6px_0_#000] hover:bg-blue-100 cursor-pointer'; ?>">
            <i class="fa-solid fa-users mr-1"></i> TEAM
        </a>
    </div>

    <div class="flex-grow">
        <?php if ($sub === 'create'): ?>
            <!-- CREATE タブ -->
            <div class="toy-box p-6 bg-[#FF69B4] relative h-full flex flex-col rounded-xl border-4 border-black">
                <div class="absolute -top-5 left-1/2 transform -translate-x-1/2 bg-yellow-400 border-4 border-black px-6 py-2 font-bold text-xl shadow-[4px_4px_0_#000] rotate-2 z-10 whitespace-nowrap">
                    NEW MISSION 📝
                </div>

                <form id="planCreateForm" class="mt-8 flex-grow flex flex-col gap-6 relative z-0">
                    <input type="hidden" name="action" value="create">

                    <div class="flex-grow">
                        <label class="font-bold text-white text-lg drop-shadow-md mb-2 block">
                            <i class="fa-solid fa-bullseye"></i> 具体的な行動計画
                        </label>
                        <div class="relative p-2 bg-white border-4 border-black shadow-inner h-full">
                            <textarea
                                name="content"
                                class="w-full h-full bg-transparent resize-none focus:outline-none font-bold text-gray-800 text-xl leading-relaxed placeholder-pink-200 p-2"
                                style="background-image: repeating-linear-gradient(transparent, transparent 38px, #ffb6c1 39px, #ffb6c1 40px); line-height: 40px;"
                                placeholder="例：&#13;&#10;ペアプロの時間を増やす！"
                            ></textarea>
                            <div class="absolute bottom-2 right-2 text-3xl transform rotate-12">🖍️</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="font-bold text-white text-sm drop-shadow-md mb-1 block">START DATE</label>
                            <input type="date" name="start_date"
                                   class="w-full bg-white border-4 border-black p-2 font-bold shadow-[4px_4px_0_#000] focus:translate-y-1 focus:shadow-none outline-none">
                        </div>
                        <div>
                            <label class="font-bold text-white text-sm drop-shadow-md mb-1 block">END DATE</label>
                            <input type="date" name="end_date"
                                   class="w-full bg-white border-4 border-black p-2 font-bold shadow-[4px_4px_0_#000] focus:translate-y-1 focus:shadow-none outline-none">
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full mt-4 bg-[#00FFFF] text-black font-bold text-2xl py-4 border-4 border-black shadow-[8px_8px_0_#000] hover:translate-y-2 hover:shadow-[4px_4px_0_#000] transition-all group relative overflow-hidden">
                        <span class="relative z-10 group-hover:scale-110 inline-block transition">
                            <i class="fa-solid fa-plus mr-2"></i> PLANを追加！
                        </span>
                    </button>
                </form>
            </div>

        <?php elseif ($sub === 'my'): ?>
            <!-- MY タブ -->
            <div class="toy-box p-6 bg-[#FFD700] relative h-full flex flex-col rounded-xl border-4 border-black">
                <!-- フィルター -->
                <div class="bg-white border-4 border-black p-4 mb-6 shadow-[4px_4px_0_rgba(0,0,0,0.2)]">
                    <div class="mb-4">
                        <div class="text-xs font-bold mb-2">▼ 期間で絞り込み</div>
                        <div class="flex flex-wrap gap-2">
                            <?php
                            $periods = [
                                'all'       => 'すべて',
                                'this_week' => '今週',
                                'last_week' => '先週',
                            ];
                            foreach ($periods as $key => $label):
                                $active = $period === $key;
                                ?>
                                <a href="?page=plan&sub=my&period=<?php echo htmlspecialchars($key); ?>&status=<?php echo htmlspecialchars($status); ?>"
                                   class="px-3 py-1 font-bold border-2 border-black
                                   <?php echo $active
                                       ? 'bg-black text-white transform scale-105'
                                       : 'bg-white text-black hover:bg-gray-100'; ?>">
                                    <?php echo htmlspecialchars($label); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div>
                        <div class="text-xs font-bold mb-2">▼ ステータス</div>
                        <div class="flex flex-wrap gap-2">
                            <?php
                            $statuses = [
                                'all'       => 'すべて',
                                'completed' => '完了',
                                'running'   => '進行中',
                            ];
                            foreach ($statuses as $key => $label):
                                $active = $status === $key;
                                ?>
                                <a href="?page=plan&sub=my&period=<?php echo htmlspecialchars($period); ?>&status=<?php echo htmlspecialchars($key); ?>"
                                   class="px-3 py-1 font-bold border-2 border-black
                                   <?php echo $active
                                       ? 'bg-blue-500 text-white'
                                       : 'bg-white text-black hover:bg-gray-100'; ?>">
                                    <?php echo htmlspecialchars($label); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- リスト -->
                <div class="flex-grow overflow-y-auto space-y-4 pr-2">
                    <?php if (empty($myPlans)): ?>
                        <div class="text-center text-sm font-bold text-gray-600">
                            まだPLANがありません。作成してみましょう！✨
                        </div>
                    <?php else: ?>
                        <?php foreach ($myPlans as $plan): ?>
                            <?php $isRunning = $plan['status'] === 'running'; ?>
                            <div class="<?php echo $isRunning
                                ? 'bg-white border-4 border-black p-4 shadow-[6px_6px_0_#FFA500] relative group hover:scale-[1.02] transition-transform'
                                : 'bg-gray-100 border-4 border-gray-400 p-4 shadow-none relative opacity-80'; ?>">
                                <div class="flex justify-between items-start mb-2">
                                    <?php if ($isRunning): ?>
                                        <span class="bg-yellow-400 text-black text-xs font-bold px-2 py-1 border-2 border-black">
                                            <i class="fa-solid fa-person-running"></i> RUNNING!
                                        </span>
                                    <?php else: ?>
                                        <span class="bg-green-500 text-white text-xs font-bold px-2 py-1 border-2 border-black">
                                            <i class="fa-solid fa-check"></i> CLEAR!!
                                        </span>
                                    <?php endif; ?>

                                    <div class="flex gap-2">
                                        <button
                                            class="w-8 h-8 bg-gray-200 border-2 border-black flex items-center justify-center hover:bg-green-300 toggle-status-btn"
                                            data-plan-id="<?php echo (int)$plan['id']; ?>"
                                            title="ステータス切替">
                                            <i class="fa-solid fa-rotate"></i>
                                        </button>
                                        <button
                                            class="w-8 h-8 bg-gray-200 border-2 border-black flex items-center justify-center hover:bg-red-300 delete-plan-btn"
                                            data-plan-id="<?php echo (int)$plan['id']; ?>"
                                            title="削除">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </div>

                                <h3 class="font-bold text-lg leading-tight mb-2 <?php echo $isRunning ? '' : 'line-through text-gray-500'; ?>">
                                    <?php echo htmlspecialchars($plan['content']); ?>
                                </h3>

                                <div class="text-xs font-bold text-gray-500">
                                    <i class="fa-regular fa-calendar"></i>
                                    <?php echo htmlspecialchars($plan['start_date'] ?? ''); ?> ～ <?php echo htmlspecialchars($plan['end_date'] ?? ''); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

        <?php elseif ($sub === 'team'): ?>
            <!-- TEAM タブ -->
            <div class="bg-[#32CD32] border-4 border-black p-6 shadow-[12px_12px_0_#006400] h-full flex flex-col relative rounded-[1rem]">
                <div class="bg-white border-4 border-black p-4 mb-6 relative z-10 rounded-md">
                    <div class="text-xs font-bold mb-2 text-center">▼ メンバーで絞り込み</div>
                    <div class="flex justify-center flex-wrap gap-3">
                        <!-- 全員 -->
                        <a href="?page=plan&sub=team&user=all"
                           class="w-10 h-10 rounded-full <?php echo $filter_user_id === 'all' ? 'bg-blue-700' : 'bg-blue-500'; ?>
                           text-white font-bold border-2 border-black flex items-center justify-center hover:scale-110 transition shadow-[2px_2px_0_#000]">
                            全
                        </a>

                        <?php foreach ($users as $user): ?>
                            <?php $active = (string)$filter_user_id === (string)$user['id']; ?>
                            <a href="?page=plan&sub=team&user=<?php echo htmlspecialchars($user['id']); ?>"
                               class="w-10 h-10 rounded-full <?php echo $active ? 'bg-pink-600' : 'bg-pink-400'; ?>
                               text-white font-bold border-2 border-black flex items-center justify-center hover:scale-110 transition shadow-[2px_2px_0_#000]">
                                <?php echo htmlspecialchars(mb_substr($user['name'], 0, 1)); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="flex-grow overflow-y-auto space-y-6 pr-2 relative z-10">
                    <?php if (empty($teamPlans)): ?>
                        <div class="text-center text-sm font-bold text-white drop-shadow">
                            まだ他のメンバーのPLANがありません。
                        </div>
                    <?php else: ?>
                        <?php foreach ($teamPlans as $plan): ?>
                            <div class="relative pl-4 border-l-4 border-dashed border-black/30">
                                <div class="flex items-center gap-2 mb-3">
                                    <div class="w-8 h-8 rounded-full border-2 border-black text-white flex items-center justify-center font-bold"
                                         style="background-color: <?php echo htmlspecialchars($plan['avatar_color'] ?? '#FF69B4'); ?>">
                                        <?php echo htmlspecialchars(mb_substr($plan['name'], 0, 1)); ?>
                                    </div>
                                    <span class="font-bold bg-white px-2 border-2 border-black shadow-[2px_2px_0_#000]">
                                        <?php echo htmlspecialchars($plan['name']); ?>
                                    </span>
                                </div>

                                <div class="bg-white border-4 border-black p-3 mb-3 shadow-[4px_4px_0_#FF69B4] transition">
                                    <div class="flex justify-between items-start">
                                        <span class="bg-yellow-400 text-[10px] font-bold px-1 border border-black mb-1 inline-block">
                                            <?php echo $plan['status'] === 'running' ? 'RUNNING' : 'DONE'; ?>
                                        </span>
                                        <i class="fa-regular fa-comment text-gray-400 hover:text-blue-500 cursor-pointer"></i>
                                    </div>
                                    <p class="font-bold text-sm leading-tight">
                                        <?php echo htmlspecialchars($plan['content']); ?>
                                    </p>
                                    <div class="text-[10px] text-gray-500 mt-1">
                                        <?php echo htmlspecialchars($plan['start_date']); ?> - <?php echo htmlspecialchars($plan['end_date']); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="absolute inset-0 bg-[radial-gradient(#000_2px,transparent_2px)] bg-[size:20px_20px] opacity-10 pointer-events-none z-0"></div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- JS: API連携 -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const apiUrl = '../api/plan_api.php'; // ②のファイルパスに合わせて

    // 作成フォーム
    const form = document.getElementById('planCreateForm');
    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(form);
            const res = await fetch(apiUrl, { method: 'POST', body: formData });
            const json = await res.json();
            alert(json.message || '結果を取得できませんでした');
            if (json.success) {
                // 作成完了したら自分の一覧へ
                window.location.href = '?page=plan&sub=my';
            }
        });
    }

    // ステータス切替
    document.querySelectorAll('.toggle-status-btn').forEach(btn => {
        btn.addEventListener('click', async () => {
            if (!confirm('ステータスを切り替えますか？')) return;
            const id = btn.dataset.planId;
            const formData = new FormData();
            formData.append('action', 'toggle_status');
            formData.append('plan_id', id);

            const res = await fetch(apiUrl, { method: 'POST', body: formData });
            const json = await res.json();
            alert(json.message || '結果を取得できませんでした');
            if (json.success) {
                location.reload();
            }
        });
    });

    // 削除
    document.querySelectorAll('.delete-plan-btn').forEach(btn => {
        btn.addEventListener('click', async () => {
            if (!confirm('このPLANを削除しますか？')) return;
            const id = btn.dataset.planId;
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('plan_id', id);

            const res = await fetch(apiUrl, { method: 'POST', body: formData });
            const json = await res.json();
            alert(json.message || '結果を取得できませんでした');
            if (json.success) {
                location.reload();
            }
        });
    });
});
</script>
</body>
</html>
