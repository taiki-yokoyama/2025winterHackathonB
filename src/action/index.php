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
<body>

    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex items-center space-x-3 mb-2">
            <div class="p-2 bg-teal-100 rounded-lg text-teal-600">
                <i class="fa-solid fa-bullseye text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-800">Action - 次の一手フェーズ</h2>
                <p class="text-sm text-gray-500">チームメンバーそれぞれに対して、次に取るべきアクションを提案しましょう。</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 rounded-full bg-teal-400 text-white flex items-center justify-center font-bold mr-3">ぼ</div>
                <div>
                    <h3 class="font-bold text-gray-800">ぼーちゃん</h3>
                    <p class="text-xs text-gray-400">チームメンバー</p>
                </div>
            </div>

            <div class="mb-4">
                <label class="text-xs text-gray-500 mb-2 block">このメンバーへの次の一手</label>
                <textarea class="w-full p-3 border border-gray-200 rounded-lg text-sm h-24 focus:ring-2 focus:ring-teal-400 focus:outline-none" placeholder="例：ペアプログラミングで一緒にリファクタリングをやろう"></textarea>
            </div>

            <button class="w-full bg-teal-500 hover:bg-teal-600 text-white font-bold py-3 rounded-lg shadow-md mb-6 transition">
                <i class="fa-regular fa-paper-plane mr-2"></i> アクションを送信
            </button>

            <div>
                <h4 class="text-sm font-bold text-gray-700 flex items-center mb-3">
                    <i class="fa-solid fa-bullseye text-teal-500 mr-2"></i> 受け取ったアクション
                </h4>
                
                <div class="space-y-3">
                    <div class="bg-teal-50 rounded-lg p-3 border border-teal-100">
                        <div class="flex justify-between items-center mb-1">
                            <div class="flex items-center">
                                <div class="w-5 h-5 rounded-full bg-green-400 text-white text-[10px] flex items-center justify-center mr-2">ち</div>
                                <span class="text-xs font-bold text-gray-700">ちゃんり</span>
                            </div>
                            <span class="text-[10px] text-gray-400">2025/12/6</span>
                        </div>
                        <div class="bg-white p-2 rounded text-sm text-gray-600">
                            またわからないところあったら聞きにいきます！
                        </div>
                    </div>
                    <div class="bg-teal-50 rounded-lg p-3 border border-teal-100">
                        <div class="flex justify-between items-center mb-1">
                            <div class="flex items-center">
                                <div class="w-5 h-5 rounded-full bg-green-400 text-white text-[10px] flex items-center justify-center mr-2">ち</div>
                                <span class="text-xs font-bold text-gray-700">ちゃんり</span>
                            </div>
                            <span class="text-[10px] text-gray-400">2025/12/6</span>
                        </div>
                        <div class="bg-white p-2 rounded text-sm text-gray-600">
                            ふー
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 h-full">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 rounded-full bg-green-400 text-white flex items-center justify-center font-bold mr-3">ち</div>
                <div>
                    <h3 class="font-bold text-gray-800">ちゃんり</h3>
                    <p class="text-xs text-gray-400">(あなた)</p>
                </div>
            </div>

            <h4 class="text-sm font-bold text-gray-700 flex items-center mb-3">
                <i class="fa-solid fa-bullseye text-teal-500 mr-2"></i> 受け取ったアクション
            </h4>

            <div class="flex flex-col items-center justify-center h-48 text-gray-400 text-sm">
                まだアクションがありません
            </div>
        </div>
    </div>
    
</body>
</html>