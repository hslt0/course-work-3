<?php
/**
 * @var string $title
 * @var App\Models\Course $course
 * @var App\Models\Lesson[] $lessons
 * @var App\Models\Test[] $tests
 * @var bool $isEnrolled
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
<body class="bg-gray-50 font-sans text-gray-800 antialiased">

    <?php 
        // Pass specific variables to the navbar partial
        $backUrl = URLROOT;
        $backText = 'Back to Courses';
        require APPROOT . '/Views/partials/navbar.php'; 
    ?>

    <div class="max-w-4xl mx-auto px-4 mt-12 mb-16">
        
        <!-- Course Header -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
            <?php if (!empty($course->preview_image)): ?>
                <div class="h-64 w-full bg-gray-200 relative">
                    <img src="<?= htmlspecialchars($course->preview_image, ENT_QUOTES, 'UTF-8') ?>" alt="Course Image" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-gray-900 to-transparent opacity-60"></div>
                    <div class="absolute bottom-6 left-8">
                        <span class="bg-green-500 text-white text-sm font-bold px-4 py-1.5 rounded-full shadow-sm">
                            <?= htmlspecialchars($course->language, ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </div>
                </div>
            <?php endif; ?>
            <div class="p-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2"><?= htmlspecialchars($course->name, ENT_QUOTES, 'UTF-8') ?></h1>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Difficulty: <span class="text-gray-900"><?= htmlspecialchars($course->difficulty_level, ENT_QUOTES, 'UTF-8') ?></span></p>
                    </div>

                    <!-- Enrollment Button Logic -->
                    <div>
                        <?php if ($isEnrolled): ?>
                            <span class="inline-flex items-center px-4 py-2 bg-green-50 text-green-700 font-semibold rounded-lg border border-green-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Enrolled
                            </span>
                        <?php else: ?>
                            <form action="<?= URLROOT ?>/courses/enroll/<?= $course->id ?>" method="POST">
                                <button type="submit" class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-green-600 hover:bg-green-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors w-full md:w-auto">
                                    Enroll Now
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="prose prose-green max-w-none text-gray-600">
                    <p class="text-lg leading-relaxed"><?= htmlspecialchars($course->description, ENT_QUOTES, 'UTF-8') ?></p>
                </div>
            </div>
        </div>

        <?php if (!$isEnrolled): ?>
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mb-8 flex items-start">
                <svg class="w-6 h-6 text-blue-500 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div>
                    <h3 class="text-sm font-medium text-blue-800">Enrollment Required</h3>
                    <p class="mt-1 text-sm text-blue-600">You must enroll in this course to access the curriculum and knowledge tests below.</p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Lessons Section -->
        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center <?= !$isEnrolled ? 'opacity-50' : '' ?>">
            <svg class="w-6 h-6 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            Course Curriculum
        </h2>

        <?php if (empty($lessons)): ?>
            <div class="bg-gray-50 border-2 border-dashed border-gray-200 rounded-xl p-8 text-center mb-8">
                <p class="text-gray-500">No lessons have been added to this course yet.</p>
            </div>
        <?php else: ?>
            <div class="space-y-4 mb-8 <?= !$isEnrolled ? 'opacity-50 pointer-events-none' : '' ?>">
                <?php foreach ($lessons as $index => $lesson): ?>
                    <a href="<?= $isEnrolled ? URLROOT . '/lessons/show/' . $lesson->id : '#' ?>" class="block bg-white border border-gray-200 rounded-xl p-5 hover:shadow-md transition-shadow duration-200 flex items-center justify-between group cursor-pointer">
                        <div class="flex items-center space-x-4">
                            <!-- Icon based on lesson type -->
                            <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center bg-gray-50 group-hover:bg-green-50 transition-colors">
                                <?php if ($lesson->type === 'pdf'): ?>
                                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                <?php elseif ($lesson->type === 'video'): ?>
                                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <?php elseif ($lesson->type === 'pptx' || $lesson->type === 'ppt'): ?>
                                    <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                <?php elseif ($lesson->type === 'markdown'): ?>
                                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <?php else: ?>
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <?php endif; ?>
                            </div>
                            
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 group-hover:text-green-600 transition-colors">
                                    <span class="text-gray-400 text-sm font-normal mr-2"><?= str_pad($index + 1, 2, '0', STR_PAD_LEFT) ?>.</span>
                                    <?= htmlspecialchars($lesson->title, ENT_QUOTES, 'UTF-8') ?>
                                </h3>
                                <p class="text-sm text-gray-500 uppercase tracking-wide mt-1 font-medium"><?= htmlspecialchars($lesson->type, ENT_QUOTES, 'UTF-8') ?></p>
                            </div>
                        </div>
                        
                        <div>
                            <?php if ($isEnrolled): ?>
                                <span class="bg-gray-100 group-hover:bg-green-500 text-gray-600 group-hover:text-white px-4 py-2 rounded-lg font-medium transition-colors border border-transparent group-hover:border-green-600 text-sm flex items-center">
                                    View Lesson
                                </span>
                            <?php else: ?>
                                <span class="text-gray-400 text-sm flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                    Locked
                                </span>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Tests Section -->
        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center mt-12 <?= !$isEnrolled ? 'opacity-50' : '' ?>">
            <svg class="w-6 h-6 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
            Knowledge Tests
        </h2>

        <?php if (empty($tests)): ?>
            <div class="bg-gray-50 border-2 border-dashed border-gray-200 rounded-xl p-8 text-center">
                <p class="text-gray-500">No tests available for this course yet.</p>
            </div>
        <?php else: ?>
            <div class="space-y-4 <?= !$isEnrolled ? 'opacity-50 pointer-events-none' : '' ?>">
                <?php foreach ($tests as $index => $test): ?>
                    <a href="<?= $isEnrolled ? URLROOT . '/tests/show/' . $test->id : '#' ?>" class="block bg-white border border-indigo-100 rounded-xl p-5 hover:shadow-md hover:border-indigo-300 transition-all duration-200 flex items-center justify-between group cursor-pointer">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center bg-indigo-50 group-hover:bg-indigo-100 transition-colors">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">
                                    <?= htmlspecialchars($test->title, ENT_QUOTES, 'UTF-8') ?>
                                </h3>
                                <p class="text-sm text-gray-500 uppercase tracking-wide mt-1 font-medium">Assessment</p>
                            </div>
                        </div>
                        
                        <div>
                            <?php if ($isEnrolled): ?>
                                <span class="bg-indigo-50 group-hover:bg-indigo-600 text-indigo-700 group-hover:text-white px-4 py-2 rounded-lg font-medium transition-colors border border-indigo-200 group-hover:border-indigo-600 text-sm flex items-center">
                                    Take Test
                                </span>
                            <?php else: ?>
                                <span class="text-gray-400 text-sm flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                    Locked
                                </span>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</body>
</html>