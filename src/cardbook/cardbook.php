<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Mochiy+Pop+One&family=Yomogi&display=swap" rel="stylesheet">
    <style>
        .font-pop { font-family: 'Mochiy Pop One', sans-serif; }
        .font-hand { font-family: 'Yomogi', cursive; font-weight: 700; }
        
        /* 基本のハードシャドウ */
        .shadow-hard { box-shadow: 6px 6px 0 #000; }
        .shadow-hard-sm { box-shadow: 3px 3px 0 #000; }
        
        /* コレクションレート用：内側の立体感（ハイライトとシェード） */
        .inset-highlight {
            box-shadow: inset 2px 2px 4px rgba(255,255,255,0.9), inset -2px -2px 4px rgba(0,0,0,0.1);
        }
        .inset-deep {
            box-shadow: inset 3px 3px 8px rgba(0,0,0,0.15), inset -1px -1px 2px rgba(255,255,255,0.5);
        }
    </style>
</head>
<body class="bg-[#f0f0f0] p-4 md:p-8 min-h-screen">

<div class="max-w-6xl mx-auto">
    
    <div class="bg-white border-4 border-black p-6 md:p-10 shadow-hard relative overflow-hidden">
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#000 2px, transparent 2px); background-size: 20px 20px;"></div>

        <div class="flex flex-col items-center justify-center mb-12 relative z-10">
            <div class="bg-[#FFD700] border-4 border-black px-10 py-4 transform -rotate-2 shadow-hard flex items-center gap-4">
                <i class="fa-solid fa-book-open text-3xl"></i>
                <h2 class="text-3xl md:text-4xl font-pop text-black tracking-widest mt-1">メンバー図鑑</h2>
            </div>
            <div class="mt-6 bg-white border-4 border-black rounded-2xl px-8 py-3 transform rotate-1 shadow-hard-sm relative">
                <div class="absolute -top-4 left-1/2 -translate-x-1/2 w-0 h-0 border-l-[10px] border-l-transparent border-r-[10px] border-r-transparent border-b-[16px] border-b-black"></div>
                <div class="absolute -top-[10px] left-1/2 -translate-x-1/2 w-0 h-0 border-l-[6px] border-l-transparent border-r-[6px] border-r-transparent border-b-[12px] border-b-white"></div>
                <p class="text-xl text-gray-900 font-bold">ガチャで獲得したPOSSEメンバーのコレクション！</p>
            </div>
        </div>
        
        <div class="mb-12 relative group z-10">
            <div class="bg-gradient-to-br from-green-100 to-green-200 border-4 border-black p-6 md:p-8 shadow-hard relative overflow-hidden rounded-xl inset-deep">
                <div class="flex justify-between items-end mb-4 font-pop text-black relative z-10">
                    <span class="text-lg md:text-xl font-bold drop-shadow-sm">COLLECTION RATE</span>
                    <div class="flex items-baseline bg-gradient-to-b from-white to-gray-100 border-2 border-black px-4 py-2 rounded-lg shadow-hard-sm transform -rotate-1 inset-highlight">
                        <span class="text-4xl md:text-5xl font-black text-[#ec4899] mr-2 drop-shadow-sm" style="-webkit-text-stroke: 1px black;">13.3</span>
                        <span class="text-xl text-black font-bold">%</span>
                    </div>
                </div>
                <div class="w-full bg-white p-2 rounded-full border-2 border-black shadow-[inset_0_2px_4px_rgba(0,0,0,0.2)]">
                    <div class="h-6 bg-gradient-to-r from-yellow-300 via-orange-400 to-pink-500 rounded-full border-2 border-black relative overflow-hidden shadow-[0_2px_0_rgba(255,255,255,0.5)_inset]" style="width: 13.3%">
                        <div class="absolute inset-0 bg-[linear-gradient(45deg,rgba(255,255,255,0.3)_25%,transparent_25%,transparent_50%,rgba(255,255,255,0.3)_50%,rgba(255,255,255,0.3)_75%,transparent_75%,transparent)] bg-[size:20px_20px]"></div>
                        <div class="absolute top-0 left-0 right-0 h-1/3 bg-white/40 rounded-full"></div>
                    </div>
                </div>
                <div class="text-right mt-3 font-bold text-black text-xl drop-shadow-sm">2 / 15 人 ゲットだぜ！</div>
            </div>
        </div>
        <div class="bg-yellow-100 border-4 border-black p-6 mb-10 shadow-hard relative z-10">
            <div class="absolute -top-5 -left-4 bg-[#FFD700] border-4 border-black px-4 py-1 shadow-hard transform -rotate-6">
                <span class="font-pop text-xl">SSR</span>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-6 mt-4">
                <?php for($i=0; $i<2; $i++): ?>
                <div class="aspect-square bg-white border-4 border-black rounded-lg border-dashed flex flex-col items-center justify-center text-gray-400 opacity-60">
                    <i class="fa-solid fa-lock text-4xl mb-3"></i>
                    <span class="font-pop text-sm">LOCKED</span>
                </div>
                <?php endfor; ?>
            </div>
        </div>

        <div class="bg-pink-100 border-4 border-black p-6 mb-10 shadow-hard relative z-10">
            <div class="absolute -top-5 -left-4 bg-[#f472b6] border-4 border-black px-4 py-1 shadow-hard transform rotate-3">
                <span class="font-pop text-xl text-white">SR</span>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-6 mt-4">
                <?php for($i=0; $i<3; $i++): ?>
                <div class="aspect-square bg-white border-4 border-black rounded-lg border-dashed flex flex-col items-center justify-center text-gray-400 opacity-60">
                    <i class="fa-solid fa-lock text-4xl mb-3"></i>
                    <span class="font-pop text-sm">LOCKED</span>
                </div>
                <?php endfor; ?>
            </div>
        </div>

        <div class="bg-cyan-100 border-4 border-black p-6 shadow-hard relative z-10">
            <div class="absolute -top-5 -left-4 bg-[#60a5fa] border-4 border-black px-4 py-1 shadow-hard transform -rotate-2">
                <span class="font-pop text-xl text-white">N</span>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-6 mt-4">
                <div class="aspect-square bg-white border-4 border-black rounded-lg border-dashed flex flex-col items-center justify-center text-gray-400 opacity-60">
                    <i class="fa-solid fa-lock text-4xl mb-3"></i>
                    <span class="font-pop text-sm">LOCKED</span>
                </div>
                
                <div class="aspect-square bg-white border-4 border-black rounded-lg relative shadow-hard group cursor-pointer transition-all hover:-translate-y-1 hover:shadow-[8px_8px_0_#000]">
                    <img src="https://images.unsplash.com/photo-1517841905240-472988babdf9?w=300&q=80" alt="Member" class="w-full h-full object-cover rounded-sm grayscale group-hover:grayscale-0 transition-all duration-300">
                    <div class="absolute -bottom-3 -right-2 bg-[#fde047] border-2 border-black px-3 py-1 shadow-sm transform -rotate-3 group-hover:rotate-0 transition-transform">
                        <div class="font-pop text-sm text-black">あおい</div>
                    </div>
                </div>

                <div class="aspect-square bg-white border-4 border-black rounded-lg border-dashed flex flex-col items-center justify-center text-gray-400 opacity-60">
                    <i class="fa-solid fa-lock text-4xl mb-3"></i>
                    <span class="font-pop text-sm">LOCKED</span>
                </div>

                <div class="aspect-square bg-white border-4 border-black rounded-lg relative shadow-hard group cursor-pointer transition-all hover:-translate-y-1 hover:shadow-[8px_8px_0_#000]">
                    <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=300&q=80" alt="Member" class="w-full h-full object-cover rounded-sm grayscale group-hover:grayscale-0 transition-all duration-300">
                    <div class="absolute -bottom-3 -right-2 bg-[#67e8f9] border-2 border-black px-3 py-1 shadow-sm transform rotate-2 group-hover:rotate-0 transition-transform">
                        <div class="font-pop text-sm text-black">まお</div>
                    </div>
                </div>
                
                <div class="aspect-square bg-white border-4 border-black rounded-lg border-dashed flex flex-col items-center justify-center text-gray-400 opacity-60">
                    <i class="fa-solid fa-lock text-4xl mb-3"></i>
                    <span class="font-pop text-sm">LOCKED</span>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>