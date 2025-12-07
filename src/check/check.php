<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POCAガチャ - 評価</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Noto Sans JP', sans-serif;
            background-color: #f3f4f6;
            background-image: radial-gradient(#cbd5e1 2px, transparent 2px);
            background-size: 20px 20px;
        }
        /* カスタムシャドウ */
        .shadow-hard {
            box-shadow: 6px 6px 0 #000;
        }
        .shadow-hard-sm {
            box-shadow: 3px 3px 0 #000;
        }
        .shadow-hard-active:active {
            box-shadow: none;
            transform: translate(3px, 3px);
        }
        /* タブのアクティブスタイル */
        .tab-active {
            background-color: #FFD700; /* Yellow */
            color: black;
            font-weight: bold;
            transform: translateY(-4px);
            box-shadow: 4px 4px 0 #000;
            z-index: 10;
        }
        .tab-inactive {
            background-color: #e5e7eb;
            color: #6b7280;
            box-shadow: inset 2px 2px 0 rgba(0,0,0,0.1);
        }
        /* スクロールバー装飾 */
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-left: 1px solid #ddd;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #999;
        }
    </style>
</head>
<body class="p-4 md:p-8 min-h-screen text-gray-800">

    <?php include '../components/header.php'; ?>

    <div class="max-w-5xl mx-auto relative">
        
        <div class="bg-white border-4 border-black p-4 mb-8 shadow-hard relative z-10">
            <h2 class="font-bold text-sm mb-3 bg-gray-200 inline-block px-3 py-1 border-2 border-black rounded">メンバー一覧</h2>
            <div class="flex justify-start gap-4 items-center overflow-x-auto pb-2">
                <?php $members = ['ゆ', 'え', 'ま', 'ぼ', 'た', 'く']; ?>
                <?php foreach($members as $m): ?>
                <div class="w-14 h-14 rounded-full border-4 border-black bg-gray-100 flex items-center justify-center font-black text-xl shadow-sm flex-shrink-0">
                    <?php echo $m; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="flex space-x-2 pl-4 relative z-20 top-1">
            <button onclick="switchTab('team')" id="btn-team" class="w-40 py-3 border-4 border-b-0 border-black rounded-t-xl text-lg transition-all duration-200 tab-active">
                チーム
            </button>
            <button onclick="switchTab('personal')" id="btn-personal" class="w-40 py-3 border-4 border-b-0 border-black rounded-t-xl text-lg transition-all duration-200 tab-inactive bg-gray-200">
                個人
            </button>
        </div>

        <div class="bg-white border-4 border-black p-6 md:p-8 shadow-[8px_8px_0_#000] relative min-h-[600px]">
            
            <div id="tab-content-team" class="block">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    
                    <div class="space-y-2">
                        <div class="bg-yellow-50 border-4 border-black p-5 rounded-lg shadow-hard-sm relative h-full">
                            <div class="flex justify-between items-center mb-4">
                                <label class="font-bold text-lg border-b-4 border-yellow-400 inline-block">チームの今後の計画</label>
                                <button class="text-xs font-bold text-black border-2 border-black px-3 py-1 bg-white hover:bg-black hover:text-white transition-colors shadow-sm">
                                    <i class="fa-solid fa-pen mr-1"></i>編集
                                </button>
                            </div>
                            <textarea class="w-full h-80 bg-white border-2 border-black border-dashed rounded p-4 outline-none text-base resize-none focus:bg-yellow-100 transition-colors leading-relaxed" placeholder="ここをクリックして計画を入力してください...&#13;&#10;例：&#13;&#10;・来週までにプロトタイプ完成&#13;&#10;・DB設計の見直し"></textarea>
                            <button class="w-full mt-4 py-3 bg-[#FFD700] border-2 border-black rounded font-bold text-base shadow-[3px_3px_0_#000] hover:bg-yellow-300 active:shadow-none active:translate-y-1 transition-all flex items-center justify-center">
                                <i class="fa-solid fa-paper-plane mr-2"></i>送信
                            </button>
                        </div>
                    </div>

                    <div class="bg-blue-50 border-4 border-black p-5 rounded-lg shadow-hard-sm relative flex flex-col h-[500px] lg:h-auto">
                        <label class="font-bold text-lg border-b-4 border-blue-400 inline-block mb-4 self-start bg-white px-2">チーム全体への意見</label>
                        
                        <div class="flex-1 overflow-y-auto space-y-4 p-4 bg-white/50 border-2 border-black border-dashed rounded mb-4 custom-scrollbar">
                            <div class="flex items-start">
                                <div class="w-10 h-10 rounded-full border-2 border-black bg-pink-300 flex-shrink-0 mr-3 shadow-sm flex items-center justify-center font-bold text-xs">ゆ</div>
                                <div class="bg-white border-2 border-black px-4 py-2 rounded-2xl rounded-tl-none shadow-sm text-sm relative max-w-[80%]">
                                    <p>デザインの方向性はこれでOK？</p>
                                </div>
                            </div>
                            <div class="flex items-start justify-end">
                                <div class="bg-blue-100 border-2 border-black px-4 py-2 rounded-2xl rounded-tr-none shadow-sm text-sm relative max-w-[80%]">
                                    <p>OK！実装進めますー</p>
                                </div>
                                <div class="w-10 h-10 rounded-full border-2 border-black bg-green-300 flex-shrink-0 ml-3 shadow-sm flex items-center justify-center font-bold text-xs">自</div>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <input type="text" class="flex-1 h-12 bg-white border-2 border-black rounded p-3 outline-none text-sm focus:border-blue-500 transition-colors" placeholder="メッセージを入力...">
                            <button class="w-12 h-12 bg-[#00FFFF] border-2 border-black rounded flex items-center justify-center shadow-[2px_2px_0_#000] active:shadow-none active:translate-y-1 transition-all hover:bg-cyan-200">
                                <i class="fa-solid fa-paper-plane text-lg"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="tab-content-personal" class="hidden">
                
                <form action="" method="POST">
                    <div class="bg-gray-100 border-4 border-black p-6 rounded-lg mb-8">
                        <label class="font-bold text-lg block mb-4 border-l-4 border-black pl-3">誰に対して送信する？</label>
                        <div class="flex flex-wrap gap-6">
                            <?php 
                            $users = [
                                ['name'=>'えび', 'color'=>'bg-pink-400', 'char'=>'え'],
                                ['name'=>'まお', 'color'=>'bg-blue-400', 'char'=>'ま'],
                                ['name'=>'ぼー', 'color'=>'bg-green-400', 'char'=>'ぼ']
                            ];
                            foreach($users as $index => $u): 
                            ?>
                            <label class="cursor-pointer group relative">
                                <input type="radio" name="target_user" class="peer hidden" <?php echo $index === 0 ? 'checked' : ''; ?>>
                                <div class="flex flex-col items-center gap-2 transition-transform hover:-translate-y-1">
                                    <div class="w-16 h-16 rounded-full border-4 border-gray-300 bg-white flex items-center justify-center text-gray-300 peer-checked:border-black peer-checked:<?php echo $u['color']; ?> peer-checked:text-white peer-checked:shadow-hard transition-all">
                                        <span class="font-black text-2xl"><?php echo $u['char']; ?></span>
                                    </div>
                                    <span class="font-bold text-sm text-gray-400 peer-checked:text-black"><?php echo $u['name']; ?></span>
                                </div>
                                <div class="absolute top-0 right-0 bg-black text-white rounded-full w-6 h-6 flex items-center justify-center border-2 border-white scale-0 peer-checked:scale-100 transition-transform z-10">
                                    <i class="fa-solid fa-check text-xs"></i>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                        
                        <div class="bg-yellow-50 border-4 border-black p-6 rounded-lg shadow-hard-sm flex flex-col">
                            <div class="mb-4">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="font-bold text-lg flex items-center">
                                        <i class="fa-solid fa-code mr-2"></i>コード面の評価
                                    </label>
                                    <span class="text-xs font-bold bg-white border border-black px-2 py-1 rounded">1〜4で選択</span>
                                </div>
                                <div class="grid grid-cols-4 gap-3">
                                    <?php for($i=1; $i<=4; $i++): ?>
                                    <label class="cursor-pointer w-full">
                                        <input type="radio" name="score_code" value="<?php echo $i; ?>" class="peer hidden">
                                        <div class="w-full aspect-square bg-white border-2 border-black rounded-lg flex items-center justify-center font-black text-xl shadow-[2px_2px_0_#000] peer-checked:bg-yellow-400 peer-checked:translate-y-1 peer-checked:shadow-none transition-all hover:bg-yellow-100">
                                            <?php echo $i; ?>
                                        </div>
                                    </label>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            
                            <div class="mt-auto">
                                <label class="text-sm font-bold block mb-1 text-gray-600">コメント（任意）</label>
                                <input type="text" class="w-full h-12 bg-white border-2 border-black rounded px-3 outline-none text-base focus:border-yellow-500 transition-colors shadow-sm" placeholder="技術的なフィードバックを入力...">
                            </div>
                        </div>

                        <div class="bg-pink-50 border-4 border-black p-6 rounded-lg shadow-hard-sm flex flex-col">
                            <div class="mb-4">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="font-bold text-lg flex items-center">
                                        <i class="fa-solid fa-smile mr-2"></i>人格面の評価
                                    </label>
                                    <span class="text-xs font-bold bg-white border border-black px-2 py-1 rounded">1〜4で選択</span>
                                </div>
                                <div class="grid grid-cols-4 gap-3">
                                    <?php for($i=1; $i<=4; $i++): ?>
                                    <label class="cursor-pointer w-full">
                                        <input type="radio" name="score_human" value="<?php echo $i; ?>" class="peer hidden">
                                        <div class="w-full aspect-square bg-white border-2 border-black rounded-lg flex items-center justify-center font-black text-xl shadow-[2px_2px_0_#000] peer-checked:bg-pink-400 peer-checked:text-white peer-checked:translate-y-1 peer-checked:shadow-none transition-all hover:bg-pink-100">
                                            <?php echo $i; ?>
                                        </div>
                                    </label>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            
                            <div class="mt-auto">
                                <label class="text-sm font-bold block mb-1 text-gray-600">コメント（任意）</label>
                                <input type="text" class="w-full h-12 bg-white border-2 border-black rounded px-3 outline-none text-base focus:border-pink-500 transition-colors shadow-sm" placeholder="感謝や励ましの言葉を入力...">
                            </div>
                        </div>
                    </div>

                    <div class="bg-green-50 border-4 border-black p-6 rounded-lg shadow-hard-sm mb-8">
                        <label class="font-bold text-lg block mb-3 border-l-4 border-green-500 pl-3">次のplanの提案</label>
                        <input type="text" class="w-full h-14 bg-white border-2 border-black rounded px-4 outline-none text-base shadow-sm focus:bg-green-100 transition-colors" placeholder="次はこんなことをしてみよう！">
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="w-full md:w-auto md:px-12 py-4 bg-black text-white font-bold text-xl rounded-lg border-4 border-black shadow-hard hover:bg-gray-800 hover:translate-y-1 hover:shadow-none transition-all flex items-center justify-center">
                            <i class="fa-solid fa-paper-plane mr-3"></i>送 信
                        </button>
                    </div>
                </form>
            </div>

        </div>

        <div class="text-right mt-6 mb-12">
            <a href="/check/result.php" class="inline-flex items-center font-bold text-lg text-black hover:text-gray-600 border-b-2 border-black hover:border-gray-600 transition-colors">
                あなたへの評価を見る <i class="fa-solid fa-arrow-right ml-2"></i>
            </a>
        </div>

    </div>

    <script>
        function switchTab(tabName) {
            // コンテンツの切り替え
            const contentTeam = document.getElementById('tab-content-team');
            const contentPersonal = document.getElementById('tab-content-personal');
            
            // タブボタンスタイルの切り替え
            const btnTeam = document.getElementById('btn-team');
            const btnPersonal = document.getElementById('btn-personal');

            if (tabName === 'team') {
                contentTeam.classList.remove('hidden');
                contentPersonal.classList.add('hidden');

                btnTeam.classList.add('tab-active');
                btnTeam.classList.remove('tab-inactive', 'bg-gray-200');
                
                btnPersonal.classList.remove('tab-active');
                btnPersonal.classList.add('tab-inactive', 'bg-gray-200');
            } else {
                contentTeam.classList.add('hidden');
                contentPersonal.classList.remove('hidden');

                btnPersonal.classList.add('tab-active');
                btnPersonal.classList.remove('tab-inactive', 'bg-gray-200');

                btnTeam.classList.remove('tab-active');
                btnTeam.classList.add('tab-inactive', 'bg-gray-200');
            }
        }
    </script>
</body>
</html>