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

        <div class="flex flex-col md:flex-row gap-8 mb-10 items-start">
            <!-- Sidebar: Filters and Sorts (Vertical) -->
            <div class="w-full md:w-1/4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex-shrink-0">
                <form action="<?= URLROOT ?>" method="GET" id="filter-form">
                    
                    <!-- Preserve search query when filtering -->
                    <input type="hidden" name="search" value="<?= htmlspecialchars($filters['search'], ENT_QUOTES, 'UTF-8') ?>">
                    
                    <h3 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100 flex items-center justify-between">
                        Filters
                        <a href="<?= URLROOT ?>" class="text-sm font-medium text-gray-400 hover:text-green-600 transition-colors">Clear</a>
                    </h3>

                    <!-- Language Filter -->
                    <div class="mb-5">
                        <label for="language-select" class="block text-sm font-medium text-gray-700 mb-2">Language</label>
                        <select id="language-select" name="language" class="block w-full pl-3 pr-10 py-2.5 text-sm border border-gray-200 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-lg transition-colors bg-gray-50">
                            <option value="">All Languages</option>
                            <?php foreach ($languages as $lang): ?>
                                <option value="<?= htmlspecialchars($lang, ENT_QUOTES, 'UTF-8') ?>" <?= $filters['language'] === $lang ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($lang, ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Difficulty Filter -->
                    <div class="mb-5">
                        <label for="difficulty-select" class="block text-sm font-medium text-gray-700 mb-2">Difficulty</label>
                        <select id="difficulty-select" name="difficulty" class="block w-full pl-3 pr-10 py-2.5 text-sm border border-gray-200 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-lg transition-colors bg-gray-50">
                            <option value="">All Levels</option>
                            <option value="Beginner" <?= $filters['difficulty'] === 'Beginner' ? 'selected' : '' ?>>Beginner</option>
                            <option value="Intermediate" <?= $filters['difficulty'] === 'Intermediate' ? 'selected' : '' ?>>Intermediate</option>
                            <option value="Advanced" <?= $filters['difficulty'] === 'Advanced' ? 'selected' : '' ?>>Advanced</option>
                        </select>
                    </div>

                    <!-- Enrollment Filter -->
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="mb-5">
                            <label for="enrolled-select" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select id="enrolled-select" name="enrolled" class="block w-full pl-3 pr-10 py-2.5 text-sm border border-gray-200 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-lg transition-colors bg-gray-50">
                                <option value="">All Courses</option>
                                <option value="yes" <?= $filters['enrolled'] === 'yes' ? 'selected' : '' ?>>My Enrolled Courses</option>
                                <option value="no" <?= $filters['enrolled'] === 'no' ? 'selected' : '' ?>>Not Enrolled</option>
                            </select>
                        </div>
                    <?php endif; ?>

                    <h3 class="text-lg font-bold text-gray-900 mb-4 mt-8 pb-2 border-b border-gray-100">Sort By</h3>
                    
                    <div class="space-y-3 mb-6">
                        <label class="flex items-center cursor-pointer group">
                            <input type="radio" name="sort" value="" <?= empty($filters['sort']) ? 'checked' : '' ?> class="form-radio text-green-600 focus:ring-green-500 h-4 w-4">
                            <span class="ml-3 text-sm text-gray-600 group-hover:text-gray-900 transition-colors">Relevance</span>
                        </label>
                        <label class="flex items-center cursor-pointer group">
                            <input type="radio" name="sort" value="name_asc" <?= $filters['sort'] === 'name_asc' ? 'checked' : '' ?> class="form-radio text-green-600 focus:ring-green-500 h-4 w-4">
                            <span class="ml-3 text-sm text-gray-600 group-hover:text-gray-900 transition-colors">Name (A-Z)</span>
                        </label>
                        <label class="flex items-center cursor-pointer group">
                            <input type="radio" name="sort" value="difficulty_asc" <?= $filters['sort'] === 'difficulty_asc' ? 'checked' : '' ?> class="form-radio text-green-600 focus:ring-green-500 h-4 w-4">
                            <span class="ml-3 text-sm text-gray-600 group-hover:text-gray-900 transition-colors">Difficulty (Low-High)</span>
                        </label>
                        <label class="flex items-center cursor-pointer group">
                            <input type="radio" name="sort" value="difficulty_desc" <?= $filters['sort'] === 'difficulty_desc' ? 'checked' : '' ?> class="form-radio text-green-600 focus:ring-green-500 h-4 w-4">
                            <span class="ml-3 text-sm text-gray-600 group-hover:text-gray-900 transition-colors">Difficulty (High-Low)</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                        Apply Changes
                    </button>
                </form>
            </div>

            <!-- Main Content Area: Search & Results -->
            <div class="w-full md:w-3/4 flex flex-col">
                
                <!-- Search Bar -->
                <div class="mb-8">
                    <!-- The search bar submits to the same page. Since it's a GET request, we use a separate tiny form just for the search bar, but include hidden inputs for current filters so they don't get lost when searching. -->
                    <form action="<?= URLROOT ?>" method="GET" class="relative">
                        <!-- Preserve current filters -->
                        <input type="hidden" name="language" value="<?= htmlspecialchars($filters['language'], ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="difficulty" value="<?= htmlspecialchars($filters['difficulty'], ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="sort" value="<?= htmlspecialchars($filters['sort'], ENT_QUOTES, 'UTF-8') ?>">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <input type="hidden" name="enrolled" value="<?= htmlspecialchars($filters['enrolled'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <?php endif; ?>

                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <label for="search-input" class="sr-only">Search</label>
                        <input id="search-input" type="text" name="search" value="<?= htmlspecialchars($filters['search'], ENT_QUOTES, 'UTF-8') ?>" placeholder="Search courses by name or language (e.g. Inglish)..." class="block w-full pl-14 pr-32 py-5 border-2 border-gray-100 rounded-2xl leading-5 bg-white placeholder-gray-400 focus:outline-none focus:border-green-500 focus:ring-0 text-lg transition-colors shadow-sm">
                        <div class="absolute inset-y-2 right-2">
                            <button type="submit" class="h-full bg-gray-900 hover:bg-gray-800 text-white font-bold py-2 px-8 rounded-xl transition-colors shadow-sm text-sm tracking-wide">
                                SEARCH
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Course List -->
                <?php if (empty($courses)): ?>
                    <div class="text-center bg-white p-12 rounded-2xl shadow-sm border border-gray-100 flex-1 flex flex-col items-center justify-center">
                        <div class="w-20 h-20 bg-gray-50 text-gray-300 rounded-full flex items-center justify-center mb-6">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">No courses found</h3>
                        <p class="mt-2 text-gray-500 max-w-md">We couldn't find any courses matching "<?= htmlspecialchars($filters['search'], ENT_QUOTES, 'UTF-8') ?>" with your current filters.</p>
                        <a href="<?= URLROOT ?>" class="mt-6 inline-block bg-white text-gray-600 border border-gray-300 hover:bg-gray-50 hover:text-gray-900 font-bold py-2 px-6 rounded-lg transition-colors">Clear all filters</a>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <?php foreach ($courses as $course): ?>
                            
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col group">
                                
                                <!-- Course Preview Image -->
                                <div class="h-48 bg-gray-100 w-full relative overflow-hidden">
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
                                    <h2 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-green-600 transition-colors duration-200 line-clamp-2">
                                        <?= htmlspecialchars($course->name, ENT_QUOTES, 'UTF-8') ?>
                                    </h2>
                                    <p class="text-sm text-gray-600 flex-1 leading-relaxed mb-6 line-clamp-3">
                                        <?= htmlspecialchars($course->description, ENT_QUOTES, 'UTF-8') ?>
                                    </p>
                                    
                                    <!-- Action Button -->
                                    <div class="mt-auto pt-4 border-t border-gray-100">
                                        <a href="<?= URLROOT ?>/courses/show/<?= $course->id ?>" class="block w-full text-center bg-gray-50 hover:bg-green-500 text-gray-800 hover:text-white font-semibold py-2.5 px-4 rounded-xl transition-all duration-200 border border-gray-200 hover:border-transparent text-sm">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </div>

                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</body>
</html>