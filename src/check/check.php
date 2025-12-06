<div class="bg-white rounded-xl shadow-sm p-6 mb-6">
    <div class="flex items-center space-x-3 mb-2">
        <div class="p-2 bg-purple-100 rounded-lg text-purple-600">
            <i class="fa-solid fa-chart-column text-xl"></i>
        </div>
        <div>
            <h2 class="text-xl font-bold text-gray-800">Check - 評価フェーズ</h2>
            <p class="text-sm text-gray-500">チームメンバーの活動を評価しましょう。評価は相手の成長を応援するためのものです。</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-bold text-gray-800 mb-4">評価する</h3>
        
        <div class="mb-4">
            <label class="text-xs font-bold text-gray-500 mb-2 block">評価対象メンバー</label>
            <div class="flex space-x-4 overflow-x-auto pb-2">
                <div class="border-2 border-purple-400 bg-purple-50 rounded-lg p-4 w-32 flex flex-col items-center cursor-pointer shrink-0">
                    <div class="w-10 h-10 rounded-full bg-purple-400 text-white flex items-center justify-center font-bold mb-2">ぽ</div>
                    <span class="text-sm font-bold text-gray-800">ぼーちゃん</span>
                </div>
                </div>
        </div>

        <div class="mb-4">
            <label class="flex items-center text-sm font-bold text-gray-700 mb-2">
                <i class="fa-solid fa-bolt text-yellow-400 mr-2"></i> コード・技術面の評価
            </label>
            <div class="flex justify-between gap-2 bg-gray-50 p-2 rounded-lg" id="tech-rating">
                <?php for($i=1; $i<=5; $i++): ?>
                <button type="button" class="rating-btn flex-1 py-2 rounded bg-white text-gray-400 text-sm hover:bg-purple-100 hover:text-purple-600 transition shadow-sm border border-gray-100" data-rating="<?php echo $i; ?>"><?php echo $i; ?></button>
                <?php endfor; ?>
            </div>
        </div>

        <div class="mb-4">
            <label class="flex items-center text-sm font-bold text-gray-700 mb-2">
                <i class="fa-regular fa-heart text-pink-400 mr-2"></i> チームビルディング面の評価
            </label>
            <div class="flex justify-between gap-2 bg-gray-50 p-2 rounded-lg" id="team-rating">
                <?php for($i=1; $i<=5; $i++): ?>
                <button type="button" class="rating-btn flex-1 py-2 rounded bg-white text-gray-400 text-sm hover:bg-purple-100 hover:text-purple-600 transition shadow-sm border border-gray-100" data-rating="<?php echo $i; ?>"><?php echo $i; ?></button>
                <?php endfor; ?>
            </div>
        </div>

        <div class="mb-6">
            <label class="text-xs text-gray-500 mb-2 block">評価理由（記述式）</label>
            <textarea class="w-full p-3 border border-gray-200 rounded-lg text-sm h-24 focus:ring-2 focus:ring-purple-400 focus:outline-none" placeholder="例：今日はペアプロで丁寧に教えてくれてありがとう。"></textarea>
        </div>

        <button class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 rounded-lg shadow-md flex items-center justify-center transition">
            <i class="fa-regular fa-paper-plane mr-2"></i> 評価を送信
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-bold text-gray-800 mb-4">評価履歴</h3>
        
        <div class="space-y-4">
            <div class="bg-purple-50 rounded-lg p-4 border border-purple-100">
                <div class="flex items-center mb-2">
                    <div class="w-8 h-8 rounded-full bg-purple-400 text-white flex items-center justify-center text-xs font-bold mr-2">ぽ</div>
                    <div>
                        <div class="text-sm font-bold text-gray-700">ぼーちゃん さんへ</div>
                        <div class="text-[10px] text-gray-400">2025/12/6 19:01:55</div>
                    </div>
                </div>
                <div class="flex space-x-4 text-xs font-bold text-gray-600 mb-2">
                    <span><i class="fa-solid fa-bolt text-yellow-500"></i> 技術: 5/5</span>
                    <span><i class="fa-regular fa-heart text-pink-500"></i> チーム: 5/5</span>
                </div>
                <div class="bg-white p-3 rounded text-sm text-gray-600 shadow-sm">
                    わからないところを丁寧に教えてくれてありがとうございます。
                </div>
            </div>

            <div class="bg-purple-50 rounded-lg p-4 border border-purple-100">
                <div class="flex items-center mb-2">
                    <div class="w-8 h-8 rounded-full bg-purple-400 text-white flex items-center justify-center text-xs font-bold mr-2">ぽ</div>
                    <div>
                        <div class="text-sm font-bold text-gray-700">ぼーちゃん さんへ</div>
                        <div class="text-[10px] text-gray-400">2025/12/6 19:07:34</div>
                    </div>
                </div>
                <div class="flex space-x-4 text-xs font-bold text-gray-600 mb-2">
                    <span><i class="fa-solid fa-bolt text-yellow-500"></i> 技術: 4/5</span>
                    <span><i class="fa-regular fa-heart text-pink-500"></i> チーム: 4/5</span>
                </div>
                <div class="bg-white p-3 rounded text-sm text-gray-600 shadow-sm">
                    いぇーい
                </div>
            </div>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // 技術面の評価ボタン
    const techButtons = document.querySelectorAll('#tech-rating .rating-btn');
    techButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            techButtons.forEach(b => {
                b.classList.remove('bg-purple-600', 'text-white');
                b.classList.add('bg-white', 'text-gray-400');
            });
            this.classList.remove('bg-white', 'text-gray-400');
            this.classList.add('bg-purple-600', 'text-white');
        });
    });

    // チームビルディング面の評価ボタン
    const teamButtons = document.querySelectorAll('#team-rating .rating-btn');
    teamButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            teamButtons.forEach(b => {
                b.classList.remove('bg-purple-600', 'text-white');
                b.classList.add('bg-white', 'text-gray-400');
            });
            this.classList.remove('bg-white', 'text-gray-400');
            this.classList.add('bg-purple-600', 'text-white');
        });
    });
});
</script>
