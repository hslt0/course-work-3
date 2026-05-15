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

    <!-- Navigation -->
    <nav class="bg-gray-900 shadow-lg border-b border-green-500">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between">
                <div class="flex space-x-7">
                    <div>
                        <a href="<?= URLROOT ?>" class="flex items-center py-4 px-2">
                            <svg class="w-8 h-8 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path></svg>
                            <span class="font-bold text-white text-xl tracking-wide"><?= SITENAME ?></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-4 mt-12 mb-16">
        
        <!-- Header Section -->
        <div class="text-center mb-16">
            <h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl sm:tracking-tight lg:text-6xl">
                <?= htmlspecialchars($title ?? 'Our courses', ENT_QUOTES, 'UTF-8') ?>
            </h1>
            <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-600">
                Unlock a world of possibilities. Start your journey to fluency today with our expertly crafted language courses.
            </p>
        </div>

        <?php if (empty($courses)): ?>
            <div class="text-center bg-white p-8 rounded-xl shadow-sm border border-gray-100">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No courses available</h3>
                <p class="mt-1 text-sm text-gray-500">Check back soon for new additions to our catalog!</p>
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
                            
                            <!-- Language Badge -->
                            <span class="absolute top-4 right-4 bg-gray-900 text-green-400 text-xs font-bold px-3 py-1.5 rounded-full uppercase tracking-wider shadow-sm border border-gray-700">
                                <?= htmlspecialchars($course->language, ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </div>

                        <!-- Course Content -->
                        <div class="p-6 flex-1 flex flex-col">
                            <h2 class="text-2xl font-bold text-gray-900 mb-3 group-hover:text-green-600 transition-colors duration-200">
                                <?= htmlspecialchars($course->name, ENT_QUOTES, 'UTF-8') ?>
                            </h2>
                            <p class="text-gray-600 flex-1 leading-relaxed mb-6">
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