<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>③でPON - PDCAガチャガチャ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'cyan-bright': '#00FFFF',
                        'gold-bright': '#FFD700',
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Dela+Gothic+One&family=DotGothic16&family=M+PLUS+Rounded+1c:wght@700;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'M PLUS Rounded 1c', sans-serif;
            background: linear-gradient(135deg, #FFB6C1 0%, #87CEEB 50%, #98FB98 100%);
            min-height: 100vh;
        }
        
        .font-heavy {
            font-family: 'Dela Gothic One', sans-serif;
        }
        
        .font-dot {
            font-family: 'DotGothic16', sans-serif;
        }
        
        .toy-box {
            border: 6px solid #000;
            box-shadow: 12px 12px 0 #000;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(-2deg); }
            50% { transform: translateY(-20px) rotate(-2deg); }
        }
        
        .float-animation {
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes spin-slow {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .spin-slow {
            animation: spin-slow 20s linear infinite;
        }
        
        @keyframes jitter {
            0% { transform: translate(0,0); }
            25% { transform: translate(2px,2px); }
            50% { transform: translate(-2px, -2px); }
            75% { transform: translate(2px, -2px); }
        }
        
        .jitter {
            animation: jitter 0.2s infinite steps(2);
        }
        
        @keyframes pdca-rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .pdca-circle {
            animation: pdca-rotate 8s linear infinite;
        }
    </style>
</head>
<body class="p-4 md:p-8">
    
    <!-- ヘッダー -->
    <header class="mb-20">
        <div class="toy-box p-4 bg-[#FFD700] transform -rotate-1 relative">
            <div class="absolute -top-3 -left-3 w-8 h-8 bg-red-500 rounded-full border-4 border-black"></div>
            <div class="absolute -top-3 -right-3 w-8 h-8 bg-blue-500 rounded-full border-4 border-black"></div>
            <div class="flex items-center justify-center gap-4">
                <img src="/assets/img/gacha.png" alt="ガチャガチャ" class="w-16 h-16 md:w-20 md:h-20 object-contain">
                <h1 class="text-center font-heavy text-4xl md:text-6xl text-black [text-shadow:4px_4px_0_#fff]">
                    ③でPON
                </h1>
                <img src="/assets/img/gacha.png" alt="ガチャガチャ" class="w-16 h-16 md:w-20 md:h-20 object-contain">
            </div>
            <p class="text-center font-bold text-lg md:text-xl mt-2">PDCAを回してガチャを回そう！</p>
        </div>
    </header>

    <div class="max-w-7xl mx-auto space-y-16">
        
        <!-- メインビジュアル -->
        <section class="toy-box p-8 md:p-12 bg-[#FF69B4] relative float-animation">
            <div class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-yellow-400 border-4 border-black px-8 py-3 font-heavy text-2xl md:text-3xl shadow-[6px_6px_0_#000] rotate-3 z-10">
                チーム開発をもっと楽しく！ 🎉
            </div>
            
            <div class="text-center mt-8">
                <!-- PDCAサイクルのビジュアル -->
                <div class="relative inline-block mb-6">
                    <div class="w-48 h-48 md:w-64 md:h-64 relative">
                        <!-- PDCA円（回転する） -->
                        <div class="absolute inset-0 pdca-circle">
                            <div class="absolute inset-0 border-8 border-black rounded-full bg-gradient-to-br from-[#FF69B4] via-[#87CEEB] to-[#98FB98]"></div>
                            
                            <!-- P -->
                            <div class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-12 h-12 bg-[#FF69B4] border-4 border-black rounded-full flex items-center justify-center font-heavy text-xl">
                                P
                            </div>
                            
                            <!-- D -->
                            <div class="absolute top-1/2 right-0 transform translate-x-1/2 -translate-y-1/2 w-12 h-12 bg-[#FFD700] border-4 border-black rounded-full flex items-center justify-center font-heavy text-xl">
                                D
                            </div>
                            
                            <!-- C -->
                            <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 translate-y-1/2 w-12 h-12 bg-[#87CEEB] border-4 border-black rounded-full flex items-center justify-center font-heavy text-xl">
                                C
                            </div>
                            
                            <!-- A -->
                            <div class="absolute top-1/2 left-0 transform -translate-x-1/2 -translate-y-1/2 w-12 h-12 bg-[#32CD32] border-4 border-black rounded-full flex items-center justify-center font-heavy text-xl">
                                A
                            </div>
                        </div>
                        
                        <!-- 中央の3（回転しない） -->
                        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-7xl md:text-9xl font-heavy text-black z-10">
                            3
                        </div>
                    </div>
                </div>
                
                <h2 class="font-heavy text-3xl md:text-5xl text-white mb-6 [text-shadow:4px_4px_0_#000]">
                    振り返りを<br>楽しく続けよう！
                </h2>
                <p class="text-xl md:text-2xl text-white font-bold mb-8 [text-shadow:2px_2px_0_#000]">
                    PDCAを回してコインを貯めて<br>ガチャガチャを回そう！
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <a href="/auth/login.php" class="inline-block bg-[#00FFFF] text-black font-heavy text-2xl md:text-3xl py-4 px-8 border-6 border-black shadow-[8px_8px_0_#000] hover:translate-y-2 hover:shadow-[4px_4px_0_#000] transition-all transform hover:scale-105">
                        <span class="inline-block">ログイン 🔑</span>
                    </a>
                    <a href="/auth/register.php" class="inline-block bg-[#FFD700] text-black font-heavy text-2xl md:text-3xl py-4 px-8 border-6 border-black shadow-[8px_8px_0_#000] hover:translate-y-2 hover:shadow-[4px_4px_0_#000] transition-all transform hover:scale-105">
                        <span class="inline-block">新規登録 ✨</span>
                    </a>
                </div>
            </div>
        </section>

        <!-- PDCAの説明 -->
        <section>
            <div class="text-center mb-8">
                <h2 class="inline-block bg-white border-4 border-black px-6 md:px-8 py-3 font-heavy text-2xl md:text-3xl shadow-[6px_6px_0_#000] transform -rotate-2">
                    🔄 PDCAって何？ 🔄
                </h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Plan -->
                <div class="toy-box p-6 bg-[#FF69B4] transform rotate-2 hover:rotate-0 transition relative">
                    <div class="absolute -top-4 -right-4 w-12 h-12 bg-white rounded-full border-4 border-black flex items-center justify-center font-heavy text-2xl shadow-[4px_4px_0_#000]">
                        P
                    </div>
                    <div class="text-center">
                        <div class="text-6xl mb-4">📝</div>
                        <h3 class="font-heavy text-2xl mb-4 text-white">Plan<br>計画</h3>
                        <p class="text-lg font-bold text-white">Actionを踏まえて<br>自分で計画を<br>決める！</p>
                    </div>
                </div>

                <!-- Do -->
                <div class="toy-box p-6 bg-[#FFD700] transform -rotate-1 hover:rotate-0 transition relative">
                    <div class="absolute -top-4 -right-4 w-12 h-12 bg-white rounded-full border-4 border-black flex items-center justify-center font-heavy text-2xl shadow-[4px_4px_0_#000]">
                        D
                    </div>
                    <div class="text-center">
                        <div class="text-6xl mb-4">🚀</div>
                        <h3 class="font-heavy text-2xl mb-4">Do<br>実行</h3>
                        <p class="text-lg font-bold">計画を<br>実際に<br>やってみる！</p>
                    </div>
                </div>

                <!-- Check -->
                <div class="toy-box p-6 bg-[#87CEEB] transform rotate-1 hover:rotate-0 transition relative">
                    <div class="absolute -top-4 -right-4 w-12 h-12 bg-white rounded-full border-4 border-black flex items-center justify-center font-heavy text-2xl shadow-[4px_4px_0_#000]">
                        C
                    </div>
                    <div class="text-center">
                        <div class="text-6xl mb-4">👀</div>
                        <h3 class="font-heavy text-2xl mb-4">Check<br>評価</h3>
                        <p class="text-lg font-bold">数値で<br>評価して<br>振り返る！</p>
                    </div>
                </div>

                <!-- Action -->
                <div class="toy-box p-6 bg-[#32CD32] transform -rotate-2 hover:rotate-0 transition relative">
                    <div class="absolute -top-4 -right-4 w-12 h-12 bg-white rounded-full border-4 border-black flex items-center justify-center font-heavy text-2xl shadow-[4px_4px_0_#000]">
                        A
                    </div>
                    <div class="text-center">
                        <div class="text-6xl mb-4">💡</div>
                        <h3 class="font-heavy text-2xl mb-4 text-white">Action<br>改善</h3>
                        <p class="text-lg font-bold text-white">みんなの意見を<br>見て次の<br>アクションを考える！</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 特徴 -->
        <section class="toy-box p-6 md:p-8 bg-[#9370DB] relative transform rotate-1">
            <div class="absolute -top-6 left-1/2 transform -translate-x-1/2 bg-white border-4 border-black px-4 md:px-6 py-2 font-heavy text-xl md:text-2xl shadow-[4px_4px_0_#000] -rotate-2 z-10 whitespace-nowrap">
                ✨ ③でPONの特徴 ✨
            </div>
            
            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white border-4 border-black p-4 flex items-center gap-4 shadow-[6px_6px_0_#FF00FF] transform -rotate-2 hover:rotate-0 transition relative">
                    <div class="absolute -top-3 -left-3 w-8 h-8 bg-red-500 rounded-full border-4 border-black"></div>
                    <div class="w-16 h-16 bg-pink-500 text-white flex items-center justify-center font-heavy text-3xl border-4 border-black rounded-full flex-shrink-0">
                        📊
                    </div>
                    <div>
                        <p class="font-bold text-lg md:text-xl font-dot">数値で簡単評価！</p>
                        <p class="text-sm mt-1">言葉にできなくても大丈夫</p>
                    </div>
                </div>

                <div class="bg-white border-4 border-black p-4 flex items-center gap-4 shadow-[6px_6px_0_#FFFF00] transform rotate-2 hover:rotate-0 transition relative">
                    <div class="absolute -top-3 -right-3 w-8 h-8 bg-blue-500 rounded-full border-4 border-black"></div>
                    <div class="w-16 h-16 bg-blue-500 text-white flex items-center justify-center font-heavy text-3xl border-4 border-black rounded-full flex-shrink-0">
                        🎮
                    </div>
                    <div>
                        <p class="font-bold text-lg md:text-xl font-dot">ゲーム感覚で楽しい！</p>
                        <p class="text-sm mt-1">コインを貯めてガチャを回そう</p>
                    </div>
                </div>

                <div class="bg-white border-4 border-black p-4 flex items-center gap-4 shadow-[6px_6px_0_#00FFFF] transform rotate-1 hover:rotate-0 transition relative">
                    <div class="absolute -top-3 -left-3 w-8 h-8 bg-green-500 rounded-full border-4 border-black"></div>
                    <div class="w-16 h-16 bg-green-500 text-white flex items-center justify-center font-heavy text-3xl border-4 border-black rounded-full flex-shrink-0">
                        👥
                    </div>
                    <div>
                        <p class="font-bold text-lg md:text-xl font-dot">チームの状況が見える！</p>
                        <p class="text-sm mt-1">みんなの意見を確認できる</p>
                    </div>
                </div>

                <div class="bg-white border-4 border-black p-4 flex items-center gap-4 shadow-[6px_6px_0_#FF69B4] transform -rotate-1 hover:rotate-0 transition relative">
                    <div class="absolute -top-3 -right-3 w-8 h-8 bg-yellow-500 rounded-full border-4 border-black"></div>
                    <div class="w-16 h-16 bg-purple-500 text-white flex items-center justify-center font-heavy text-3xl border-4 border-black rounded-full flex-shrink-0">
                        🔄
                    </div>
                    <div>
                        <p class="font-bold text-lg md:text-xl font-dot">継続しやすい仕組み！</p>
                        <p class="text-sm mt-1">形だけにならない振り返り</p>
                    </div>
                </div>
            </div>

            <div class="absolute inset-0 bg-[radial-gradient(#000_2px,transparent_2px)] bg-[size:20px_20px] opacity-10 pointer-events-none"></div>
        </section>

        <!-- 使い方 -->
        <section class="toy-box p-6 md:p-8 bg-[#FFFF00] relative transform -rotate-1">
            <div class="absolute -top-6 left-1/2 transform -translate-x-1/2 bg-[#FF69B4] border-4 border-black px-4 md:px-6 py-2 font-heavy text-xl md:text-2xl shadow-[4px_4px_0_#000] rotate-2 z-10 text-white">
                📖 使い方 📖
            </div>
            
            <div class="mt-8 space-y-4">
                <div class="bg-white border-4 border-black p-4 shadow-[4px_4px_0_#000] transform hover:translate-x-2 transition">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-[#FF69B4] border-4 border-black rounded-full flex items-center justify-center font-heavy text-2xl flex-shrink-0">1</div>
                        <p class="font-bold text-lg md:text-xl">毎日PDCAを記録する</p>
                    </div>
                </div>
                
                <div class="bg-white border-4 border-black p-4 shadow-[4px_4px_0_#000] transform hover:translate-x-2 transition">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-[#87CEEB] border-4 border-black rounded-full flex items-center justify-center font-heavy text-2xl flex-shrink-0">2</div>
                        <p class="font-bold text-lg md:text-xl">記録するとコインがもらえる</p>
                    </div>
                </div>
                
                <div class="bg-white border-4 border-black p-4 shadow-[4px_4px_0_#000] transform hover:translate-x-2 transition">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-[#32CD32] border-4 border-black rounded-full flex items-center justify-center font-heavy text-2xl flex-shrink-0">3</div>
                        <p class="font-bold text-lg md:text-xl">コインでガチャを回す</p>
                    </div>
                </div>
                
                <div class="bg-white border-4 border-black p-4 shadow-[4px_4px_0_#000] transform hover:translate-x-2 transition">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-[#FFD700] border-4 border-black rounded-full flex items-center justify-center font-heavy text-2xl flex-shrink-0">4</div>
                        <p class="font-bold text-lg md:text-xl">メンバーのカードを集めよう！</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="toy-box p-8 md:p-12 bg-gradient-to-br from-[#FF69B4] via-[#FFD700] to-[#87CEEB] relative text-center">
            <img src="/assets/img/gacha.png" alt="ガチャガチャ" class="w-32 h-32 md:w-40 md:h-40 mx-auto mb-6 object-contain">
            <h2 class="font-heavy text-3xl md:text-5xl text-white mb-6 [text-shadow:4px_4px_0_#000]">
                さあ、はじめよう！
            </h2>
            <p class="text-xl md:text-2xl text-white font-bold mb-8 [text-shadow:2px_2px_0_#000]">
                チーム開発をもっと楽しく！
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="/auth/login.php" class="inline-block bg-white text-black font-heavy text-2xl md:text-3xl py-4 px-8 border-6 border-black shadow-[8px_8px_0_#000] hover:translate-y-2 hover:shadow-[4px_4px_0_#000] transition-all transform hover:scale-105">
                    <span class="inline-block">ログイン 🔑</span>
                </a>
                <a href="/auth/register.php" class="inline-block bg-[#00FFFF] text-black font-heavy text-2xl md:text-3xl py-4 px-8 border-6 border-black shadow-[8px_8px_0_#000] hover:translate-y-2 hover:shadow-[4px_4px_0_#000] transition-all transform hover:scale-105">
                    <span class="inline-block">新規登録 ✨</span>
                </a>
            </div>
        </section>

    </div>

    <!-- フッター -->
    <footer class="mt-16 mb-8">
        <div class="text-center bg-white border-4 border-black p-4 shadow-[6px_6px_0_#000] max-w-2xl mx-auto transform hover:rotate-1 transition">
            <p class="font-heavy text-xl">© 2025 ③でPON 🎰</p>
            <p class="text-sm mt-2 font-bold">チーム開発を楽しく振り返ろう！</p>
        </div>
    </footer>

</body>
</html>
