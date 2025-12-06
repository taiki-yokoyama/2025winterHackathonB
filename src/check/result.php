<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POCAガチャ - あなたへの評価</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Mochiy+Pop+One&family=Noto+Sans+JP:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Noto Sans JP', sans-serif;
            background-color: #f3f4f6;
            background-image: radial-gradient(#cbd5e1 2px, transparent 2px);
            background-size: 20px 20px;
        }
        .font-pop {
            font-family: 'Mochiy Pop One', sans-serif;
        }
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
        /* ストライプ背景のアニメーション */
        @keyframes slide {
            0% { background-position: 0 0; }
            100% { background-position: 40px 40px; }
        }
        .bg-stripe-anim {
            background-image: linear-gradient(45deg, rgba(255,255,255,0.2) 25%, transparent 25%, transparent 50%, rgba(255,255,255,0.2) 50%, rgba(255,255,255,0.2) 75%, transparent 75%, transparent);
            background-size: 40px 40px;
            animation: slide 2s linear infinite;
        }
    </style>
</head>
<body class="p-4 md:p-8 min-h-screen text-gray-800">

    <div class="max-w-4xl mx-auto relative">

        <div class="flex justify-center mb-10 relative z-20">
            <div class="bg-white border-4 border-black px-8 py-3 transform -rotate-2 shadow-hard flex items-center gap-3 relative overflow-hidden">
                <div class="absolute inset-0 bg-yellow-300 opacity-20 bg-stripe-anim"></div>
                <i class="fa-solid fa-crown text-3xl text-yellow-500 drop-shadow-md"></i>
                <h1 class="text-2xl md:text-3xl font-pop text-black tracking-widest mt-1 relative z-10">あなたへの評価</h1>
            </div>
            <div class="absolute -top-4 -right-4 text-4xl transform rotate-12">✨</div>
            <div class="absolute -bottom-4 -left-4 text-4xl transform -rotate-12">🎉</div>
        </div>

        <div class="bg-white border-4 border-black p-6 md:p-8 rounded-xl shadow-[8px_8px_0_#000] mb-12 relative z-10">
            <div class="absolute -top-5 left-8">
                <span class="bg-blue-500 text-white font-pop text-lg px-4 py-1 border-4 border-black shadow-hard transform -rotate-2 inline-block">
                    <i class="fa-solid fa-chart-line mr-2"></i>今週のサマリー
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                
                <div class="bg-pink-50 border-4 border-black rounded-lg p-5 shadow-hard-sm relative overflow-hidden group hover:-translate-y-1 transition-transform">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-lg flex items-center">
                            <div class="w-8 h-8 bg-black text-white rounded flex items-center justify-center mr-2">
                                <i class="fa-solid fa-code text-sm"></i>
                            </div>
                            コード面
                        </h3>
                        <span class="text-xs font-bold text-gray-500 bg-white border-2 border-black px-2 py-0.5 rounded-full">MAX 4.0</span>
                    </div>
                    <div class="flex items-baseline mb-2">
                        <span class="text-5xl font-black text-pink-500 drop-shadow-[2px_2px_0_#fff]" style="-webkit-text-stroke: 1px black;">3.7</span>
                        <span class="text-sm font-bold ml-2 text-gray-600">/ 4.0</span>
                    </div>
                    <div class="w-full bg-white border-2 border-black h-4 rounded-full overflow-hidden">
                        <div class="bg-pink-400 h-full border-r-2 border-black" style="width: 92%"></div>
                    </div>
                </div>

                <div class="bg-blue-50 border-4 border-black rounded-lg p-5 shadow-hard-sm relative overflow-hidden group hover:-translate-y-1 transition-transform">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-lg flex items-center">
                            <div class="w-8 h-8 bg-black text-white rounded flex items-center justify-center mr-2">
                                <i class="fa-solid fa-smile text-sm"></i>
                            </div>
                            人格面
                        </h3>
                        <span class="text-xs font-bold text-gray-500 bg-white border-2 border-black px-2 py-0.5 rounded-full">MAX 4.0</span>
                    </div>
                    <div class="flex items-baseline mb-2">
                        <span class="text-5xl font-black text-blue-500 drop-shadow-[2px_2px_0_#fff]" style="-webkit-text-stroke: 1px black;">3.7</span>
                        <span class="text-sm font-bold ml-2 text-gray-600">/ 4.0</span>
                    </div>
                    <div class="w-full bg-white border-2 border-black h-4 rounded-full overflow-hidden">
                        <div class="bg-blue-400 h-full border-r-2 border-black" style="width: 92%"></div>
                    </div>
                </div>

            </div>
        </div>

        <div class="space-y-8 mb-16">
            <h2 class="text-center font-pop text-2xl mb-6 relative inline-block w-full">
                <span class="relative z-10 bg-yellow-300 px-4 py-1 border-4 border-black transform rotate-1 inline-block shadow-hard">メンバーからの評価</span>
                <div class="absolute top-1/2 left-0 w-full h-1 bg-black -z-0"></div>
            </h2>

            <div class="bg-white border-4 border-black p-6 rounded-xl shadow-[6px_6px_0_#000] transition-transform duration-300 relative">
                <div class="absolute -top-3 left-1/2 -translate-x-1/2 w-32 h-8 bg-red-400 opacity-80 border-2 border-black transform -rotate-1 z-20"></div>

                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 pb-4 border-b-2 border-dashed border-gray-300">
                    <div class="flex items-center mb-3 sm:mb-0">
                        <div class="w-12 h-12 rounded-full border-2 border-black bg-purple-400 flex items-center justify-center text-white font-pop text-xl mr-3 shadow-[2px_2px_0_#000]">
                            ゆ
                        </div>
                        <div>
                            <div class="font-bold text-lg">ゆいな</div>
                            <div class="text-xs text-gray-500 font-bold bg-gray-100 border border-black inline-block px-1">からの評価</div>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <div class="flex items-center bg-pink-100 border-2 border-black px-3 py-1 rounded shadow-sm">
                            <i class="fa-solid fa-code mr-2 text-xs"></i>
                            <span class="font-black text-lg">4</span>
                        </div>
                        <div class="flex items-center bg-blue-100 border-2 border-black px-3 py-1 rounded shadow-sm">
                            <i class="fa-solid fa-smile mr-2 text-xs"></i>
                            <span class="font-black text-lg">3</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="bg-pink-50 border-2 border-pink-200 rounded-lg p-4 relative">
                        <div class="absolute -top-2 left-3 bg-pink-500 text-white text-[10px] font-bold px-2 py-0.5 rounded border border-black">
                            コード面
                        </div>
                        <p class="text-gray-700 font-medium mt-1">レビューが的確で助かりました！特にSQLのクエリ最適化の部分は勉強になりました。</p>
                    </div>

                    <div class="bg-blue-50 border-2 border-blue-200 rounded-lg p-4 relative">
                        <div class="absolute -top-2 left-3 bg-blue-500 text-white text-[10px] font-bold px-2 py-0.5 rounded border border-black">
                            人格面
                        </div>
                        <p class="text-gray-700 font-medium mt-1">積極的にコミュニケーションを取ってくれて嬉しいです。ミーティングの進行もスムーズでした。</p>
                    </div>
                </div>
            </div>

            <div class="bg-white border-4 border-black p-6 rounded-xl shadow-[6px_6px_0_#000] transition-transform duration-300 relative">
                <div class="absolute -top-3 right-8 w-8 h-8 rounded-full bg-gray-200 border-2 border-black shadow-sm z-20 flex items-center justify-center text-gray-400">
                    <i class="fa-solid fa-thumbtack"></i>
                </div>

                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 pb-4 border-b-2 border-dashed border-gray-300">
                    <div class="flex items-center mb-3 sm:mb-0">
                        <div class="w-12 h-12 rounded-full border-2 border-black bg-green-400 flex items-center justify-center text-white font-pop text-xl mr-3 shadow-[2px_2px_0_#000]">
                            ぼ
                        </div>
                        <div>
                            <div class="font-bold text-lg">ぼーちゃん</div>
                            <div class="text-xs text-gray-500 font-bold bg-gray-100 border border-black inline-block px-1">からの評価</div>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <div class="flex items-center bg-pink-100 border-2 border-black px-3 py-1 rounded shadow-sm">
                            <i class="fa-solid fa-code mr-2 text-xs"></i>
                            <span class="font-black text-lg">3</span>
                        </div>
                        <div class="flex items-center bg-blue-100 border-2 border-black px-3 py-1 rounded shadow-sm">
                            <i class="fa-solid fa-smile mr-2 text-xs"></i>
                            <span class="font-black text-lg">4</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="bg-pink-50 border-2 border-pink-200 rounded-lg p-4 relative">
                        <div class="absolute -top-2 left-3 bg-pink-500 text-white text-[10px] font-bold px-2 py-0.5 rounded border border-black">
                            コード面
                        </div>
                        <p class="text-gray-700 font-medium mt-1">実装スピードが早くて頼りになります。命名規則だけ少し気をつけてもらえると嬉しいです！</p>
                    </div>

                    <div class="bg-blue-50 border-2 border-blue-200 rounded-lg p-4 relative">
                        <div class="absolute -top-2 left-3 bg-blue-500 text-white text-[10px] font-bold px-2 py-0.5 rounded border border-black">
                            人格面
                        </div>
                        <p class="text-gray-700 font-medium mt-1">いつも明るくてチームの雰囲気がよくなります！ムードメーカーですね。</p>
                    </div>
                </div>
            </div>

        </div>

        <div class="text-center pb-12">
            <a href="action.php" class="inline-flex flex-col items-center group">
                <div class="relative">
                    <button class="bg-[#00FFFF] border-4 border-black px-12 py-4 rounded-full font-pop text-2xl shadow-hard group-hover:translate-y-1 group-hover:shadow-none transition-all duration-200 relative overflow-hidden">
                        <span class="relative z-10 text-black">Actionを決める！</span>
                        <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity"></div>
                    </button>
                    <div class="absolute -top-10 -right-10 bg-yellow-300 border-2 border-black px-3 py-1 rounded-lg text-xs font-bold transform rotate-12 animate-bounce">
                        Next Stage!
                    </div>
                </div>
                <span class="mt-4 font-bold text-gray-500 border-b-2 border-gray-400 group-hover:text-black group-hover:border-black transition-colors">
                    PDCAサイクルを回そう <i class="fa-solid fa-rotate-right ml-1"></i>
                </span>
            </a>
        </div>

    </div>
</body>
</html>