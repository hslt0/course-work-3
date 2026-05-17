<?php
/**
 * @var string $title
 * @var App\Models\User $user
 * @var App\Models\Course[] $courses
 * @var array $testResults
 * @var array $completedLessonsByCourse
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?> | <?= SITENAME ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans text-gray-800 antialiased min-h-screen flex flex-col">

    <?php require_once APPROOT . '/Views/partials/navbar.php'; ?>

    <div class="flex-grow max-w-6xl mx-auto px-4 mt-12 mb-16 w-full">
        
        <!-- Header Section -->
        <div class="mb-10">
            <h1 class="text-3xl font-extrabold text-gray-900">
                Welcome back, <?= htmlspecialchars($user->name, ENT_QUOTES, 'UTF-8') ?>!
            </h1>
            <p class="mt-2 text-lg text-gray-600">
                Track your learning progress and view your recent test scores.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left Column: Enrolled Courses -->
            <div class="lg:col-span-2">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 border-b border-gray-200 pb-2">My Courses</h2>

                <?php if (empty($courses)): ?>
                    <div class="text-center bg-white p-12 rounded-2xl shadow-sm border border-gray-100">
                        <div class="w-20 h-20 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">No active enrollments</h3>
                        <p class="mt-2 text-gray-500 max-w-md mx-auto">You haven't enrolled in any courses yet. Browse our catalog to find the perfect course and start learning today!</p>
                        <a href="<?= URLROOT ?>" class="mt-6 inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-8 rounded-xl transition-colors shadow-sm">
                            Browse Courses
                        </a>
                    </div>
                <?php else: ?>
                    <div class="space-y-6">
                        <?php foreach ($courses as $course): ?>
                            <?php 
                                $completedCount = count($completedLessonsByCourse[$course->id] ?? []);
                                // For a real app, you'd want to fetch the total lesson count per course dynamically. 
                                // For now, we'll just show the raw number of completed lessons.
                            ?>
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col sm:flex-row items-center sm:items-stretch gap-6 hover:shadow-md transition-shadow">
                                
                                <div class="w-full sm:w-40 h-40 sm:h-auto flex-shrink-0 rounded-xl overflow-hidden bg-gray-100">
                                    <?php if (!empty($course->preview_image)): ?>
                                        <img src="<?= htmlspecialchars($course->preview_image, ENT_QUOTES, 'UTF-8') ?>" alt="" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <div class="w-full h-full flex items-center justify-center text-green-500">
                                            <svg class="w-12 h-12 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="flex-1 flex flex-col">
                                    <div class="flex justify-between items-start mb-2">
                                        <h3 class="text-xl font-bold text-gray-900"><?= htmlspecialchars($course->name, ENT_QUOTES, 'UTF-8') ?></h3>
                                        <span class="bg-gray-100 text-gray-800 text-xs font-bold px-2 py-1 rounded border border-gray-200 uppercase tracking-wide">
                                            <?= htmlspecialchars($course->difficulty_level, ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </div>
                                    
                                    <div class="mt-4 mb-6">
                                        <div class="flex justify-between text-sm text-gray-600 mb-1">
                                            <span class="font-medium">Lessons Completed:</span>
                                            <span class="font-bold text-green-600"><?= $completedCount ?></span>
                                        </div>
                                    </div>

                                    <div class="mt-auto flex space-x-3">
                                        <a href="<?= URLROOT ?>/courses/show/<?= $course->id ?>" class="flex-1 text-center bg-green-50 hover:bg-green-500 text-green-700 hover:text-white font-semibold py-2 px-4 rounded-lg transition-colors text-sm">
                                            Continue Learning
                                        </a>
                                        <form action="<?= URLROOT ?>/courses/unenroll/<?= $course->id ?>" method="POST" onsubmit="return confirm('Are you sure you want to unenroll? All your progress will be lost.');">
                                            <button type="submit" class="p-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition-colors border border-red-200" title="Unenroll">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right Column: Recent Test Results -->
            <div class="lg:col-span-1">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 border-b border-gray-200 pb-2">Recent Test Scores</h2>
                
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <?php if (empty($testResults)): ?>
                        <div class="p-8 text-center">
                            <svg class="mx-auto h-10 w-10 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                            <p class="text-gray-500 text-sm">You haven't taken any tests yet.</p>
                        </div>
                    <?php else: ?>
                        <ul class="divide-y divide-gray-100">
                            <?php foreach ($testResults as $result): ?>
                                <?php 
                                    $percentage = ($result->total > 0) ? round(($result->score / $result->total) * 100) : 0;
                                    $colorClass = $percentage >= 70 ? 'text-green-600 bg-green-50 border-green-200' : ($percentage >= 50 ? 'text-yellow-600 bg-yellow-50 border-yellow-200' : 'text-red-600 bg-red-50 border-red-200');
                                ?>
                                <li class="p-5 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1 pr-4">
                                            <p class="text-sm font-bold text-gray-900 line-clamp-1"><?= htmlspecialchars($result->test_title, ENT_QUOTES, 'UTF-8') ?></p>
                                            <p class="text-xs text-gray-500 mt-1 line-clamp-1"><?= htmlspecialchars($result->course_name, ENT_QUOTES, 'UTF-8') ?></p>
                                            <p class="text-xs text-gray-400 mt-2"><?= date('M j, Y', strtotime($result->taken_at)) ?></p>
                                        </div>
                                        <div class="flex-shrink-0 text-center flex flex-col items-center justify-center w-16 h-16 rounded-full border <?= $colorClass ?>">
                                            <span class="text-lg font-extrabold leading-none"><?= $percentage ?>%</span>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</body>
</html>