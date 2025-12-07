<?php
session_start();

// ログインチェック
if (!isset($_SESSION['user_id'])) {
    header('Location: /auth/login.php');
    exit;
}

require_once '../dbconnect.php';

// 全カードを取得
$stmt = $dbh->query('SELECT * FROM cards ORDER BY id ASC');
$all_cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ユーザーが所持しているカードを取得
$stmt = $dbh->prepare('
    SELECT c.*, uc.count, uc.first_obtained_at 
    FROM cards c
    INNER JOIN user_cards uc ON c.id = uc.card_id
    WHERE uc.user_id = ?
    ORDER BY c.id ASC
');
$stmt->execute([$_SESSION['user_id']]);
$user_cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 所持カードのIDをマップ化
$user_card_map = [];
foreach ($user_cards as $card) {
    $user_card_map[$card['id']] = $card;
}

// コレクション率を計算
$total_cards = count($all_cards);
$obtained_cards = count($user_cards);
$collection_rate = $total_cards > 0 ? ($obtained_cards / $total_cards) * 100 : 0;
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>カードブック - ③でPON</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Dela+Gothic+One&family=DotGothic16&family=M+PLUS+Rounded+1c:wght@700;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'M PLUS Rounded 1c', sans-serif;
            background: linear-gradient(135deg, #FFB6C1 0%, #87CEEB 50%, #98FB98 100%);
            min-height: 100vh;
        }
        .font-heavy { font-family: 'Dela Gothic One', sans-serif; }
        .font-pop { font-family: 'Dela Gothic One', sans-serif; }
        .toy-box { border: 6px solid #000; box-shadow: 12px 12px 0 #000; }
        .shadow-hard { box-shadow: 6px 6px 0 #000; }
        .shadow-hard-sm { box-shadow: 3px 3px 0 #000; }
        .inset-deep { box-shadow: inset 3px 3px 8px rgba(0,0,0,0.15), inset -1px -1px 2px rgba(255,255,255,0.5); }
        .inset-highlight { box-shadow: inset 2px 2px 4px rgba(255,255,255,0.9), inset -2px -2px 4px rgba(0,0,0,0.1); }
        
        /* モーダル */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.8);
            animation: fadeIn 0.3s;
        }
        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            position: relative;
            max-width: 90%;
            max-height: 90%;
            animation: zoomIn 0.3s;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes zoomIn {
            from { transform: scale(0.5); }
            to { transform: scale(1); }
        }
    </style>
</head>
<body class="p-4 md:p-8">

    <?php include '../components/header.php'; ?>

    <div class="max-w-6xl mx-auto mt-12 md:mt-16">
        
        <div class="bg-white border-6 border-black p-6 md:p-10 shadow-hard relative overflow-hidden toy-box">
            <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#000 2px, transparent 2px); background-size: 20px 20px;"></div>

            <!-- タイトル -->
            <div class="flex flex-col items-center justify-center mb-12 relative z-10">
                <div class="bg-[#FFD700] border-4 border-black px-10 py-4 transform -rotate-2 shadow-hard flex items-center gap-4">
                    <i class="fa-solid fa-book-open text-3xl"></i>
                    <h2 class="text-3xl md:text-4xl font-pop text-black tracking-widest mt-1">メンバー図鑑</h2>
                </div>
                <div class="mt-6 bg-white border-4 border-black rounded-2xl px-8 py-3 transform rotate-1 shadow-hard-sm relative">
                    <p class="text-xl text-gray-900 font-bold">ガチャで獲得したPOSSEメンバーのコレクション！</p>
                </div>
            </div>
            
            <!-- コレクション率 -->
            <div class="mb-12 relative group z-10">
                <div class="bg-gradient-to-br from-green-100 to-green-200 border-4 border-black p-6 md:p-8 shadow-hard relative overflow-hidden rounded-xl inset-deep">
                    <div class="flex justify-between items-end mb-4 font-pop text-black relative z-10">
                        <span class="text-lg md:text-xl font-bold drop-shadow-sm">COLLECTION RATE</span>
                        <div class="flex items-baseline bg-gradient-to-b from-white to-gray-100 border-2 border-black px-4 py-2 rounded-lg shadow-hard-sm transform -rotate-1 inset-highlight">
                            <span class="text-4xl md:text-5xl font-black text-[#ec4899] mr-2 drop-shadow-sm" style="-webkit-text-stroke: 1px black;">
                                <?php echo number_format($collection_rate, 1); ?>
                            </span>
                            <span class="text-xl text-black font-bold">%</span>
                        </div>
                    </div>
                    <div class="w-full bg-white p-2 rounded-full border-2 border-black shadow-[inset_0_2px_4px_rgba(0,0,0,0.2)]">
                        <div class="h-6 bg-gradient-to-r from-yellow-300 via-orange-400 to-pink-500 rounded-full border-2 border-black relative overflow-hidden shadow-[0_2px_0_rgba(255,255,255,0.5)_inset]" style="width: <?php echo $collection_rate; ?>%">
                            <div class="absolute inset-0 bg-[linear-gradient(45deg,rgba(255,255,255,0.3)_25%,transparent_25%,transparent_50%,rgba(255,255,255,0.3)_50%,rgba(255,255,255,0.3)_75%,transparent_75%,transparent)] bg-[size:20px_20px]"></div>
                            <div class="absolute top-0 left-0 right-0 h-1/3 bg-white/40 rounded-full"></div>
                        </div>
                    </div>
                    <div class="text-right mt-3 font-bold text-black text-xl drop-shadow-sm">
                        <?php echo $obtained_cards; ?> / <?php echo $total_cards; ?> 人 ゲットだぜ！
                    </div>
                </div>
            </div>

            <!-- 全カード -->
            <div class="bg-cyan-100 border-4 border-black p-6 shadow-hard relative z-10">
                <div class="absolute -top-5 -left-4 bg-[#60a5fa] border-4 border-black px-4 py-1 shadow-hard transform -rotate-2">
                    <span class="font-pop text-xl text-white">📖 全カード</span>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-6 mt-4">
                    <?php foreach ($all_cards as $card): ?>
                        <?php if (isset($user_card_map[$card['id']])): ?>
                            <!-- 所持しているカード -->
                            <div onclick="openModal('<?php echo htmlspecialchars($card['image']); ?>', '<?php echo htmlspecialchars($card['name']); ?>', <?php echo $user_card_map[$card['id']]['count']; ?>, '<?php echo date('Y/m/d', strtotime($user_card_map[$card['id']]['first_obtained_at'])); ?>')" class="aspect-square bg-white border-4 border-black rounded-lg relative shadow-hard group cursor-pointer transition-all hover:-translate-y-1 hover:shadow-[8px_8px_0_#000]">
                                <img src="/assets/img/gacha_img/<?php echo htmlspecialchars($card['image']); ?>" alt="<?php echo htmlspecialchars($card['name']); ?>" class="w-full h-full object-cover rounded-sm">
                                <div class="absolute -bottom-3 -right-2 bg-[#67e8f9] border-2 border-black px-3 py-1 shadow-sm transform rotate-2 group-hover:rotate-0 transition-transform">
                                    <div class="font-pop text-sm text-black"><?php echo htmlspecialchars($card['name']); ?></div>
                                </div>
                                <div class="absolute top-2 right-2 bg-white border-2 border-black px-2 py-1 rounded-full font-bold text-xs">
                                    ×<?php echo $user_card_map[$card['id']]['count']; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- 未所持のカード -->
                            <div class="aspect-square bg-white border-4 border-black rounded-lg border-dashed flex flex-col items-center justify-center text-gray-400 opacity-60">
                                <i class="fa-solid fa-lock text-4xl mb-3"></i>
                                <span class="font-pop text-sm">LOCKED</span>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- ガチャに戻るボタン -->
            <div class="mt-12 text-center relative z-10">
                <a href="/gacha/gacha.php" class="inline-block bg-[#FF69B4] text-white font-heavy text-xl py-3 px-8 border-4 border-black shadow-[6px_6px_0_#000] hover:translate-y-2 hover:shadow-[3px_3px_0_#000] transition">
                    ガチャに戻る 🎰
                </a>
            </div>
        </div>
        
    </div>

    <?php include '../components/footer.php'; ?>

    <!-- モーダル -->
    <div id="cardModal" class="modal" onclick="closeModal()">
        <div class="modal-content" onclick="event.stopPropagation()">
            <div class="bg-white border-6 border-black p-6 shadow-[12px_12px_0_#000] relative max-w-2xl">
                <button onclick="closeModal()" class="absolute -top-4 -right-4 w-12 h-12 bg-red-500 text-white rounded-full border-4 border-black shadow-[4px_4px_0_#000] hover:bg-red-600 font-heavy text-2xl">
                    ×
                </button>
                
                <div class="flex flex-col md:flex-row gap-6">
                    <div class="flex-shrink-0">
                        <img id="modalImage" src="" alt="" class="w-full md:w-64 h-auto border-4 border-black shadow-hard">
                    </div>
                    
                    <div class="flex-1">
                        <h3 id="modalName" class="text-3xl font-heavy mb-4 text-gray-800"></h3>
                        
                        <div class="space-y-3">
                            <div class="bg-yellow-100 border-4 border-black p-3">
                                <p class="text-sm font-bold text-gray-600 mb-1">所持枚数</p>
                                <p class="text-2xl font-heavy">×<span id="modalCount"></span></p>
                            </div>
                            
                            <div class="bg-blue-100 border-4 border-black p-3">
                                <p class="text-sm font-bold text-gray-600 mb-1">初回獲得日</p>
                                <p class="text-xl font-bold">
                                    <i class="fa-regular fa-calendar mr-2"></i>
                                    <span id="modalDate"></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openModal(image, name, count, date) {
            document.getElementById('modalImage').src = '/assets/img/gacha_img/' + image;
            document.getElementById('modalImage').alt = name;
            document.getElementById('modalName').textContent = name;
            document.getElementById('modalCount').textContent = count;
            document.getElementById('modalDate').textContent = date;
            document.getElementById('cardModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('cardModal').classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        // ESCキーでモーダルを閉じる
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });
    </script>

</body>
</html>
