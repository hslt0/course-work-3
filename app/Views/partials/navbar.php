<!-- Navigation -->
<nav class="bg-gray-900 shadow-lg border-b border-green-500 flex-shrink-0">
    <div class="max-w-6xl mx-auto px-4">
        <div class="flex justify-between items-center h-16">
            <a href="<?= URLROOT ?>" class="flex items-center">
                <svg class="w-8 h-8 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path></svg>
                <span class="font-bold text-white text-xl tracking-wide"><?= SITENAME ?></span>
            </a>
            
            <?php if (isset($backUrl) && isset($backText)): ?>
                <a href="<?= htmlspecialchars($backUrl, ENT_QUOTES, 'UTF-8') ?>" class="text-gray-300 hover:text-white flex items-center transition-colors">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <?= htmlspecialchars($backText, ENT_QUOTES, 'UTF-8') ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>