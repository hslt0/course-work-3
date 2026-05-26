<?php
/**
 * @var App\Models\Course[] $courses
 * @var array $filters (optional)
 */
?>
<?php if (empty($courses)): ?>
    <div class="text-center bg-white p-12 rounded-2xl shadow-sm border border-gray-100 flex-1 flex flex-col items-center justify-center">
        <div class="w-20 h-20 bg-gray-50 text-gray-300 rounded-full flex items-center justify-center mb-6">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900">No courses found</h3>
        <p class="mt-2 text-gray-500 max-w-md">We couldn't find any courses matching your current filters.</p>
        <a href="<?= URLROOT ?>" class="mt-6 inline-block bg-white text-gray-600 border border-gray-300 hover:bg-gray-50 hover:text-gray-900 font-bold py-2 px-6 rounded-lg transition-colors">Clear all filters</a>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-8">
        <?php foreach ($courses as $course): ?>
            <a href="<?= URLROOT ?>/courses/show/<?= $course->id ?>" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col group">
                
                <div class="h-48 bg-gray-100 w-full relative overflow-hidden">
                    <?php if (!empty($course->preview_image)): ?>
                        <img src="<?= htmlspecialchars($course->preview_image, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($course->name, ENT_QUOTES, 'UTF-8') ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center bg-gray-50 text-green-500 group-hover:scale-105 transition-transform duration-500">
                            <svg class="w-20 h-20 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                    <?php endif; ?>
                    
                    <div class="absolute top-4 right-4 flex flex-col space-y-2 items-end">
                        <span class="bg-gray-900 text-green-400 text-xs font-bold px-3 py-1.5 rounded-full uppercase tracking-wider shadow-sm border border-gray-700">
                            <?= htmlspecialchars($course->language, ENT_QUOTES, 'UTF-8') ?>
                        </span>
                        <?php 
                            $diffColor = match($course->difficulty_level) {
                                'Beginner' => 'bg-blue-100 text-blue-800 border-blue-200',
                                'Intermediate' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                'Advanced' => 'bg-red-100 text-red-800 border-red-200',
                                default => 'bg-gray-100 text-gray-800 border-gray-200'
                            };
                        ?>
                        <span class="text-[10px] font-bold px-2 py-1 rounded-full uppercase tracking-wide border <?= $diffColor ?>">
                            <?= htmlspecialchars($course->difficulty_level, ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </div>
                </div>

                <div class="p-6 flex-1 flex flex-col">
                    <h2 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-green-600 transition-colors duration-200 line-clamp-2">
                        <?= htmlspecialchars($course->name, ENT_QUOTES, 'UTF-8') ?>
                    </h2>
                    <p class="text-sm text-gray-600 flex-1 leading-relaxed line-clamp-3">
                        <?= htmlspecialchars($course->description, ENT_QUOTES, 'UTF-8') ?>
                    </p>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>