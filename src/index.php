<?php
session_start();

// 簡易的なデータ保持（DBの代わり）
if (!isset($_SESSION['coins'])) $_SESSION['coins'] = 0;
if (!isset($_SESSION['pdca_count'])) $_SESSION['pdca_count'] = 2;

// 現在のページを取得 (デフォルトは plan)
$page = isset($_GET['page']) ? $_GET['page'] : 'plan';

// タブのアクティブスタイル判定関数
function getTabStyle($currentPage, $targetPage) {
    $base = "flex items-center space-x-2 py-4 px-2 border-b-2 font-medium transition-colors duration-200 ";
    if ($currentPage === $targetPage) {
        $colors = [
            'check' => 'border-purple-500 text-purple-600',
            'action' => 'border-teal-500 text-teal-600',
            'plan' => 'border-red-500 text-red-500',
            'gacha' => 'border-yellow-500 text-yellow-600',
            'zukan' => 'border-blue-500 text-blue-600',
        ];
        return $base . ($colors[$targetPage] ?? 'border-gray-800 text-gray-800');
    }
    return $base . "border-transparent text-gray-500 hover:text-gray-700";
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDCAガチャ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Noto Sans JP', sans-serif; }
    </style>
</head>
<body class="bg-purple-50 min-h-screen flex flex-col">

    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <div class="flex items-center text-2xl font-bold text-gray-800 mr-2">
                        <span class="text-pink-500 text-3xl mr-1">{3}</span>
                        <div class="flex flex-col leading-none">
                            <span class="text-lg">PDCAガチャ</span>
                            <span class="text-xs text-gray-400 font-normal">2025初級チーム開発B</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-6">
                    <div class="bg-yellow-100 border border-yellow-300 rounded-full px-4 py-1 flex items-center space-x-2">
                        <i class="fa-solid fa-coins text-yellow-500"></i>
                        <span class="font-bold text-gray-700"><?php echo $_SESSION['coins']; ?> コイン</span>
                    </div>
                    
                    <div class="text-right">
                        <div class="text-sm font-bold text-gray-700">ちゃんり</div>
                        <div class="text-xs text-gray-500">PDCA完了: <?php echo $_SESSION['pdca_count']; ?>回</div>
                    </div>
                    
                    <button class="text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    </button>
                </div>
            </div>

            <nav class="flex space-x-8 overflow-x-auto">
                <a href="?page=check" class="<?php echo getTabStyle($page, 'check'); ?>">
                    <i class="fa-regular fa-circle-check"></i>
                    <span>Check - 評価</span>
                </a>
                <a href="?page=action" class="<?php echo getTabStyle($page, 'action'); ?>">
                    <i class="fa-solid fa-bullseye"></i>
                    <span>Action - 次の一手</span>
                </a>
                <a href="?page=plan" class="<?php echo getTabStyle($page, 'plan'); ?>">
                    <i class="fa-regular fa-file-lines"></i>
                    <span>Plan - 計画</span>
                </a>
                <a href="?page=gacha" class="<?php echo getTabStyle($page, 'gacha'); ?>">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                    <span>ガチャ</span>
                </a>
                <a href="?page=zukan" class="<?php echo getTabStyle($page, 'zukan'); ?>">
                    <i class="fa-solid fa-book-open"></i>
                    <span>図鑑</span>
                </a>
            </nav>
        </div>
    </header>

    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
        <?php
        // 簡易ルーティング
        switch($page) {
            case 'plan': include './plan/index.php'; break;
            case 'check': include 'check.php'; break;
            case 'action': include 'action.php'; break;
            case 'gacha': include 'gacha.php'; break;
            case 'zukan': include 'zukan.php'; break;
            default: include './plan/index.php'; break;
        }
        ?>
    </main>

    <div class="fixed bottom-6 right-6">
        <button class="bg-gray-800 text-white rounded-full w-10 h-10 flex items-center justify-center shadow-lg hover:bg-gray-700">
            <i class="fa-solid fa-question"></i>
        </button>
    </div>

</body>
</html>