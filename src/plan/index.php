<?php
// サブページ判定 (デフォルトは作成画面)
$sub = isset($_GET['sub']) ? $_GET['sub'] : 'create';

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

<div class="h-full flex flex-col">

    <div class="flex gap-4 mb-6 px-2">
        <a href="?page=plan&sub=create" class="<?php echo getToySubNav($sub, 'create', '作成', 'pink'); ?>">
            <i class="fa-solid fa-pen-nib mr-1"></i> MAKE
        </a>
        <a href="?page=plan&sub=my" class="<?php echo getToySubNav($sub, 'my', '自分', 'yellow'); ?>">
            <i class="fa-solid fa-user mr-1"></i> MINE
        </a>
        <a href="?page=plan&sub=team" class="<?php echo getToySubNav($sub, 'team', 'みんな', 'blue'); ?>">
            <i class="fa-solid fa-users mr-1"></i> TEAM
        </a>
    </div>

    <div class="flex-grow">
        
        <?php if ($sub === 'create'): ?>
        <div class="toy-box p-6 bg-[#FF69B4] relative h-full flex flex-col">
            <div class="absolute -top-5 left-1/2 transform -translate-x-1/2 bg-yellow-400 border-4 border-black px-6 py-2 font-heavy text-xl shadow-[4px_4px_0_#000] rotate-2 z-10 whitespace-nowrap">
                NEW MISSION 📝
            </div>

            <form action="" method="POST" class="mt-8 flex-grow flex flex-col gap-6 relative z-0">
                
                <div class="flex-grow">
                    <label class="font-heavy text-white text-lg drop-shadow-md mb-2 block">
                        <i class="fa-solid fa-bullseye"></i> 具体的な行動計画
                    </label>
                    <div class="relative p-2 bg-white border-4 border-black shadow-inner h-full">
                        <textarea class="w-full h-full bg-transparent resize-none focus:outline-none font-heavy text-gray-800 text-xl leading-relaxed placeholder-pink-200 p-2" 
                            style="background-image: repeating-linear-gradient(transparent, transparent 38px, #ffb6c1 39px, #ffb6c1 40px); line-height: 40px;"
                            placeholder="例：&#13;&#10;ペアプロの時間を増やす！"></textarea>
                        <div class="absolute bottom-2 right-2 text-3xl transform rotate-12">🖍️</div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="font-heavy text-white text-sm drop-shadow-md mb-1 block">START DATE</label>
                        <input type="date" class="w-full bg-white border-4 border-black p-2 font-bold shadow-[4px_4px_0_#000] focus:translate-y-1 focus:shadow-none outline-none">
                    </div>
                    <div>
                        <label class="font-heavy text-white text-sm drop-shadow-md mb-1 block">END DATE</label>
                        <input type="date" class="w-full bg-white border-4 border-black p-2 font-bold shadow-[4px_4px_0_#000] focus:translate-y-1 focus:shadow-none outline-none">
                    </div>
                </div>

                <button type="button" class="w-full bg-[#00FFFF] text-black font-heavy text-2xl py-4 border-4 border-black shadow-[8px_8px_0_#000] hover:translate-y-2 hover:shadow-[4px_4px_0_#000] transition-all group relative overflow-hidden">
                    <span class="relative z-10 group-hover:scale-110 inline-block transition"><i class="fa-solid fa-plus mr-2"></i> PLANを追加！</span>
                </button>
            </form>
        </div>

        <?php elseif ($sub === 'my'): ?>
        <div class="toy-box p-6 bg-[#FFD700] relative h-full flex flex-col">
            <div class="bg-white border-4 border-black p-4 mb-6 shadow-[4px_4px_0_rgba(0,0,0,0.2)]">
                
                <div class="mb-4">
                    <div class="text-xs font-heavy mb-2">▼ 期間で絞り込み</div>
                    <div class="flex flex-wrap gap-2">
                        <button class="bg-black text-white px-3 py-1 font-bold border-2 border-black transform scale-105">すべて</button>
                        <button class="bg-white text-black px-3 py-1 font-bold border-2 border-black hover:bg-gray-100">今週</button>
                        <button class="bg-white text-black px-3 py-1 font-bold border-2 border-black hover:bg-gray-100">先週</button>
                    </div>
                </div>
                
                <div>
                    <div class="text-xs font-heavy mb-2">▼ ステータス</div>
                    <div class="flex flex-wrap gap-2">
                        <button class="bg-blue-500 text-white px-3 py-1 font-bold border-2 border-black hover:brightness-110">すべて</button>
                        <button class="bg-white text-black px-3 py-1 font-bold border-2 border-black hover:bg-gray-100">完了</button>
                        <button class="bg-white text-black px-3 py-1 font-bold border-2 border-black hover:bg-gray-100">進行中</button>
                    </div>
                </div>
            </div>

            <div class="flex-grow overflow-y-auto space-y-4 pr-2">
                
                <div class="bg-white border-4 border-black p-4 shadow-[6px_6px_0_#FFA500] relative group hover:scale-[1.02] transition-transform">
                    <div class="flex justify-between items-start mb-2">
                        <span class="bg-yellow-400 text-black text-xs font-heavy px-2 py-1 border-2 border-black animate-pulse">
                            <i class="fa-solid fa-person-running"></i> RUNNING!
                        </span>
                        <button class="w-8 h-8 bg-gray-200 border-2 border-black flex items-center justify-center hover:bg-blue-300">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                    </div>
                    <h3 class="font-heavy text-lg leading-tight mb-2">毎日30分、ペアプロの時間を設ける</h3>
                    <div class="text-xs font-bold text-gray-500 font-dot">
                        <i class="fa-regular fa-calendar"></i> 2024/12/02 ～ 2024/12/08
                    </div>
                </div>

                <div class="w-16 h-16 bg-green-500 text-white flex items-center justify-center font-heavy text-3xl border-4 border-black rounded-full">M</div>
                <div>
                    <span class="bg-green-200 border-2 border-black px-2 py-1 text-xs font-bold">まほ</span>
                    <p class="font-bold text-xl mt-2 font-dot">「DBつくるぞー」</p>
                </div>

                <div class="bg-gray-100 border-4 border-gray-400 p-4 shadow-none relative opacity-80">
                    <div class="flex justify-between items-start mb-2">
                        <span class="bg-green-500 text-white text-xs font-heavy px-2 py-1 border-2 border-black">
                            <i class="fa-solid fa-check"></i> CLEAR!!
                        </span>
                        <button class="w-8 h-8 bg-gray-200 border-2 border-black flex items-center justify-center">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                    </div>
                    <h3 class="font-heavy text-lg leading-tight mb-2 line-through text-gray-500">APIドキュメントを詳しく記述する</h3>
                    <div class="text-xs font-bold text-gray-400 font-dot">
                        <i class="fa-regular fa-calendar"></i> 2024/11/25 ～ 2024/12/01
                    </div>
                </div>

            </div>
        </div>

        <?php elseif ($sub === 'team'): ?>
        <div class="bg-[#32CD32] border-4 border-black p-6 shadow-[12px_12px_0_#006400] h-full flex flex-col relative rounded-[1rem]">
            
            <div class="bg-white border-4 border-black p-4 mb-6 relative z-10">
                <div class="text-xs font-heavy mb-2 text-center">▼ メンバーで絞り込み</div>
                <div class="flex justify-center flex-wrap gap-3">
                    <button class="w-10 h-10 rounded-full bg-blue-500 text-white font-heavy border-2 border-black hover:scale-110 transition shadow-[2px_2px_0_#000]">全</button>
                    <button class="w-10 h-10 rounded-full bg-pink-400 text-white font-heavy border-2 border-black hover:scale-110 transition shadow-[2px_2px_0_#000]">A</button>
                    <button class="w-10 h-10 rounded-full bg-blue-400 text-white font-heavy border-2 border-black hover:scale-110 transition shadow-[2px_2px_0_#000]">B</button>
                    <button class="w-10 h-10 rounded-full bg-purple-400 text-white font-heavy border-2 border-black hover:scale-110 transition shadow-[2px_2px_0_#000]">C</button>
                </div>
            </div>

            <div class="flex-grow overflow-y-auto space-y-6 pr-2">
                
                <div class="relative pl-4 border-l-4 border-dashed border-black/30">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 rounded-full bg-pink-400 border-2 border-black text-white flex items-center justify-center font-heavy">A</div>
                        <span class="font-heavy bg-white px-2 border-2 border-black shadow-[2px_2px_0_#000]">メンバーA</span>
                    </div>

                    <div class="bg-white border-4 border-black p-3 mb-3 shadow-[4px_4px_0_#FF69B4] transition">
                         <div class="flex justify-between items-start">
                            <span class="bg-yellow-400 text-[10px] font-heavy px-1 border border-black mb-1 inline-block">RUNNING</span>
                            <i class="fa-regular fa-comment text-gray-400 hover:text-blue-500 cursor-pointer"></i>
                         </div>
                         <p class="font-bold text-sm leading-tight">毎日30分、ペアプロの時間を設ける</p>
                         <div class="text-[10px] text-gray-500 mt-1 font-dot">2024/12/02 - 12/08</div>
                    </div>
                    
                    <div class="bg-white border-4 border-black p-3 shadow-[4px_4px_0_#FF69B4] transition">
                         <div class="flex justify-between items-start">
                            <span class="bg-yellow-400 text-[10px] font-heavy px-1 border border-black mb-1 inline-block">RUNNING</span>
                            <i class="fa-regular fa-comment text-gray-400 hover:text-blue-500 cursor-pointer"></i>
                         </div>
                         <p class="font-bold text-sm leading-tight">レビューのフィードバックを24時間以内に返す</p>
                         <div class="text-[10px] text-gray-500 mt-1 font-dot">2024/12/02 - 12/08</div>
                    </div>
                </div>

                 <div class="relative pl-4 border-l-4 border-dashed border-black/30">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 rounded-full bg-blue-400 border-2 border-black text-white flex items-center justify-center font-heavy">B</div>
                        <span class="font-heavy bg-white px-2 border-2 border-black shadow-[2px_2px_0_#000]">メンバーB</span>
                    </div>

                    <div class="bg-white border-4 border-black p-3 shadow-[4px_4px_0_#00BFFF] transition">
                         <div class="flex justify-between items-start">
                            <span class="bg-yellow-400 text-[10px] font-heavy px-1 border border-black mb-1 inline-block">RUNNING</span>
                            <i class="fa-regular fa-comment text-gray-400 hover:text-blue-500 cursor-pointer"></i>
                         </div>
                         <p class="font-bold text-sm leading-tight">テストコードのカバレッジを80%以上にする</p>
                         <div class="text-[10px] text-gray-500 mt-1 font-dot">2024/12/02 - 12/08</div>
                    </div>
                </div>

            </div>
            <div class="absolute inset-0 bg-[radial-gradient(#000_2px,transparent_2px)] bg-[size:20px_20px] opacity-10 pointer-events-none z-0"></div>
        </div>
        <?php endif; ?>

    </div>
</div>