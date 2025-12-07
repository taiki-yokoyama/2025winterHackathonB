<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Mochiy+Pop+One&family=Yomogi&display=swap" rel="stylesheet">
    <style>
        /* ハンドルを回すアクション */
        .turn-handle:active .handle-grip { 
            transform: rotate(180deg); 
            transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275); 
        }
        
        /* ドーム内のカプセルが暴れるアニメーション */
        @keyframes rumble {
            0% { transform: translate(0,0) rotate(0deg); } 
            25% { transform: translate(-3px, 3px) rotate(-5deg); }
            50% { transform: translate(3px, -3px) rotate(5deg); } 
            75% { transform: translate(-3px, -3px) rotate(-5deg); } 
            100% { transform: translate(0,0) rotate(0deg); }
        }
        /* ハンドルを押している間、中身が暴れる */
        .turn-handle:active ~ .machine-body .dome-contents { 
            animation: rumble 0.1s infinite; 
        }

        /* 背景の集中線（回転） */
        @keyframes spin-burst { from { transform: rotate(0); } to { transform: rotate(360deg); } }
        .sunburst-bg {
            background: repeating-conic-gradient(#FF00FF 0% 5%, #FFFF00 5% 10%);
            animation: spin-burst 20s linear infinite;
        }
    </style>
</head>

<body>
    <?php include '../components/header.php'; ?>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 h-full items-center px-4">
        
        <div class="relative transform scale-100 lg:scale-110 origin-center mx-auto">
            
            <div class="absolute -top-12 -left-8 font-heavy text-4xl text-[#00FFFF] z-40 transform -rotate-12 animate-bounce-slow" style="-webkit-text-stroke: 2px #000; text-shadow: 4px 4px 0 #000;">
                JACKPOT!!
            </div>

            <div class="turn-handle absolute top-[260px] left-1/2 transform -translate-x-1/2 z-30 cursor-pointer group w-32 h-32">
                <div class="handle-grip w-full h-full bg-white rounded-full border-[6px] border-black flex items-center justify-center shadow-[0_10px_20px_rgba(0,0,0,0.5)] relative">
                    <div class="w-8 h-36 bg-[#FF0000] absolute border-4 border-black rounded-full"></div>
                    <div class="w-16 h-16 bg-[#FFD700] rounded-full border-4 border-black z-10 flex items-center justify-center relative overflow-hidden">
                        <div class="absolute inset-0 bg-white opacity-40 rounded-full transform -translate-x-1/2 -translate-y-1/2 top-1/4 left-1/4"></div>
                        <span class="font-heavy text-xs text-black transform rotate-12">TURN</span>
                    </div>
                </div>
                <div class="absolute -right-16 top-0 text-4xl text-[#00FFFF] animate-pulse transform rotate-45" style="-webkit-text-stroke: 1px #000;">
                    <i class="fa-solid fa-arrow-rotate-left"></i>
                </div>
            </div>

            <div class="machine-body relative z-10">
                <div class="w-64 h-64 bg-blue-100/30 rounded-full border-[8px] border-black relative overflow-hidden shadow-[inset_0_0_40px_rgba(255,255,255,0.8)] mx-auto backdrop-blur-[2px]">
                    
                    <div class="absolute top-6 left-6 w-20 h-10 bg-white opacity-80 rounded-full transform -rotate-45 z-20 pointer-events-none"></div>
                    <div class="absolute bottom-6 right-8 w-10 h-6 bg-white opacity-60 rounded-full transform -rotate-45 z-20 pointer-events-none"></div>
                    
                    <div class="dome-contents absolute inset-0 w-full h-full">
                        <div class="absolute top-16 left-10 w-14 h-14 rounded-full border-4 border-black bg-gradient-to-br from-yellow-300 to-yellow-500 shadow-lg transform rotate-12"></div>
                        <div class="absolute bottom-16 right-12 w-14 h-14 rounded-full border-4 border-black bg-gradient-to-br from-pink-400 to-pink-600 shadow-lg transform -rotate-45"></div>
                        <div class="absolute top-24 right-10 w-14 h-14 rounded-full border-4 border-black bg-gradient-to-br from-cyan-300 to-cyan-500 shadow-lg transform rotate-90"></div>
                        <div class="absolute bottom-10 left-16 w-14 h-14 rounded-full border-4 border-black bg-gradient-to-br from-green-400 to-green-600 shadow-lg z-10"></div>
                        <div class="absolute top-10 right-20 w-12 h-12 rounded-full border-4 border-black bg-purple-500 shadow-lg -z-10 blur-[1px]"></div>
                    </div>
                </div>
                
                <div class="w-60 h-60 bg-[#FF0000] mx-auto -mt-20 rounded-b-[4rem] border-[8px] border-black relative z-20 flex flex-col items-center justify-center shadow-[16px_16px_0_#000]">
                    <div class="absolute top-6 w-full flex justify-between px-2">
                        <div class="w-3 h-3 bg-black rounded-full border-2 border-gray-500"></div>
                        <div class="w-3 h-3 bg-black rounded-full border-2 border-gray-500"></div>
                    </div>
                    <div class="absolute bottom-24 right-4 bg-yellow-400 text-black font-heavy text-xs px-2 py-1 border-2 border-black transform -rotate-6 shadow-sm">
                        1 COIN
                    </div>
                </div>
                
                <div class="w-36 h-20 bg-[#222] mx-auto -mt-6 rounded-t-[1.5rem] border-x-[6px] border-t-[6px] border-black relative overflow-hidden shadow-xl z-10">
                    <div class="absolute bottom-0 w-full h-4 bg-black"></div>
                    <div class="absolute top-0 w-full h-full bg-white/10 skew-x-12 border-r-4 border-white/20"></div>
                </div>
                
                <div class="w-48 mx-auto flex justify-between -mt-2">
                    <div class="w-8 h-8 bg-black rounded-b-lg"></div>
                    <div class="w-8 h-8 bg-black rounded-b-lg"></div>
                </div>
            </div>
        </div>

        <div class="toy-box h-[500px] flex flex-col items-center justify-center relative transform rotate-1 overflow-hidden">
            
            <div class="sunburst-bg absolute inset-0 opacity-20 pointer-events-none"></div>

            <div class="z-10 relative text-center w-full">
                
                <div class="w-56 h-56 mx-auto mb-8 relative group cursor-pointer transition-transform hover:scale-105 active:scale-95">
                    <div class="w-full h-1/2 bg-[#00FFFF] rounded-t-full border-8 border-b-4 border-black relative z-10">
                        <div class="absolute top-4 left-6 w-16 h-8 bg-white opacity-60 rounded-full transform -rotate-45"></div>
                    </div>
                    <div class="w-full h-1/2 bg-white rounded-b-full border-8 border-t-4 border-black flex items-center justify-center">
                        <span class="font-heavy text-6xl opacity-20 select-none">?</span>
                    </div>
                    
                    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-[#FF00FF] text-white font-heavy text-2xl px-6 py-2 border-4 border-black shadow-[4px_4px_0_#000] rotate-[-5deg] group-hover:rotate-0 transition-transform">
                        OPEN!
                    </div>
                </div>
                
                <div class="bg-black text-white p-4 border-4 border-white shadow-[8px_8px_0_#000] inline-block transform -rotate-1 max-w-xs">
                    <h3 class="font-heavy text-2xl text-[#FFFF00] mb-1">ガチャを回せ！</h3>
                    <p class="font-bold text-sm leading-tight">
                        ハンドルを<br>おもいっきり<br>回してね！！
                    </p>
                </div>
            </div>
            
            <div class="absolute top-2 left-2 text-2xl">➕</div>
            <div class="absolute top-2 right-2 text-2xl">➕</div>
            <div class="absolute bottom-2 left-2 text-2xl">➕</div>
            <div class="absolute bottom-2 right-2 text-2xl">➕</div>
        </div>
    </div>

</body>
</html>