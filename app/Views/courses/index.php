<?php
/**
 * @var string $title
 * @var App\Models\Course[] $courses
 * @var array $languages
 * @var array $filters
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Our courses', ENT_QUOTES, 'UTF-8') ?> | <?= SITENAME ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans text-gray-800 antialiased">

    <?php require_once APPROOT . '/Views/partials/navbar.php'; ?>

    <div class="max-w-6xl mx-auto px-4 mt-12 mb-16">
        
        <!-- Header Section -->
        <div class="text-center mb-10">
            <h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl sm:tracking-tight lg:text-6xl">
                <?= htmlspecialchars($title ?? 'Our courses', ENT_QUOTES, 'UTF-8') ?>
            </h1>
            <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-600">
                Unlock a world of possibilities. Start your journey to fluency today with our expertly crafted language courses.
            </p>
        </div>

        <!-- Search and Filter Bar -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-10">
            <form action="<?= URLROOT ?>" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                
                <!-- Search Box -->
                <div class="md:col-span-2 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <label>
                        <input type="text" name="search" value="<?= htmlspecialchars($filters['search'], ENT_QUOTES, 'UTF-8') ?>" placeholder="Search courses..." class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:border-green-500 focus:ring-1 focus:ring-green-500 sm:text-sm transition-colors">
                    </label>
                </div>

                <!-- Language Filter -->
                <div>
                    <label>
                        <select name="language" class="block w-full pl-3 pr-10 py-3 text-base border border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm rounded-xl transition-colors">
                            <option value="">All Languages</option>
                            <?php foreach ($languages as $lang): ?>
                                <option value="<?= htmlspecialchars($lang, ENT_QUOTES, 'UTF-8') ?>" <?= $filters['language'] === $lang ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($lang, ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                </div>

                <!-- Difficulty Filter -->
                <div>
                    <label>
                        <select name="difficulty" class="block w-full pl-3 pr-10 py-3 text-base border border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm rounded-xl transition-colors">
                            <option value="">All Levels</option>
                            <option value="Beginner" <?= $filters['difficulty'] === 'Beginner' ? 'selected' : '' ?>>Beginner</option>
                            <option value="Intermediate" <?= $filters['difficulty'] === 'Intermediate' ? 'selected' : '' ?>>Intermediate</option>
                            <option value="Advanced" <?= $filters['difficulty'] === 'Advanced' ? 'selected' : '' ?>>Advanced</option>
                        </select>
                    </label>
                </div>

                <!-- Sort Options -->
                <div class="md:col-span-3">
                    <div class="flex items-center space-x-4 text-sm">
                        <span class="text-gray-500 font-medium">Sort by:</span>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="sort" value="name_asc" <?= $filters['sort'] === 'name_asc' ? 'checked' : '' ?> class="form-radio text-green-600 focus:ring-green-500 h-4 w-4">
                            <span class="ml-2 text-gray-700 hover:text-gray-900">Name (A-Z)</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="sort" value="difficulty_asc" <?= $filters['sort'] === 'difficulty_asc' ? 'checked' : '' ?> class="form-radio text-green-600 focus:ring-green-500 h-4 w-4">
                            <span class="ml-2 text-gray-700 hover:text-gray-900">Difficulty (Low-High)</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="sort" value="difficulty_desc" <?= $filters['sort'] === 'difficulty_desc' ? 'checked' : '' ?> class="form-radio text-green-600 focus:ring-green-500 h-4 w-4">
                            <span class="ml-2 text-gray-700 hover:text-gray-900">Difficulty (High-Low)</span>
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="md:col-span-1">
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-gray-900 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-colors">
                        Apply Filters
                    </button>
                </div>

            </form>
        </div>

        <!-- Course List -->
        <?php if (empty($courses)): ?>
            <div class="text-center bg-white p-8 rounded-xl shadow-sm border border-gray-100">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No courses found</h3>
                <p class="mt-1 text-sm text-gray-500">We couldn't find any courses matching your search criteria. Try adjusting your filters.</p>
                <a href="<?= URLROOT ?>" class="mt-4 inline-block text-green-600 hover:text-green-800 font-medium">Clear all filters</a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($courses as $course): ?>
                    
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col group">
                        
                        <!-- Course Preview Image -->
                        <div class="h-52 bg-gray-100 w-full relative overflow-hidden">
                            <?php if (!empty($course->preview_image)): ?>
                                <img src="<?= htmlspecialchars($course->preview_image, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($course->name, ENT_QUOTES, 'UTF-8') ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <?php else: ?>
                                <!-- Fallback image if no preview is set -->
                                <div class="w-full h-full flex items-center justify-center bg-gray-50 text-green-500 group-hover:scale-105 transition-transform duration-500">
                                    <svg class="w-20 h-20 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Badges (Language & Difficulty) -->
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

                        <!-- Course Content -->
                        <div class="p-6 flex-1 flex flex-col">
                            <h2 class="text-2xl font-bold text-gray-900 mb-3 group-hover:text-green-600 transition-colors duration-200">
                                <?= htmlspecialchars($course->name, ENT_QUOTES, 'UTF-8') ?>
                            </h2>
                            <p class="text-gray-600 flex-1 leading-relaxed mb-6 line-clamp-3">
                                <?= htmlspecialchars($course->description, ENT_QUOTES, 'UTF-8') ?>
                            </p>
                            
                            <!-- Action Button -->
                            <div class="mt-auto pt-4">
                                <a href="<?= URLROOT ?>/courses/show/<?= $course->id ?>" class="block w-full text-center bg-green-50 hover:bg-green-500 text-green-700 hover:text-white font-semibold py-3 px-4 rounded-xl transition-all duration-200 border border-green-200 hover:border-transparent">
                                    Course Details
                                </a>
                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>