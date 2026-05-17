<?php
/**
 * @var string $title
 * @var App\Models\Course $course
 * @var App\Models\Lesson $lesson
 * @var bool $isCompleted
 * @var App\Models\Lesson|null $prevLesson
 * @var App\Models\Lesson|null $nextLesson
 * @var array $comments
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?> | <?= SITENAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans text-gray-800 antialiased h-screen flex flex-col">

    <?php 
        $backUrl = URLROOT . '/courses/show/' . $course->id;
        $backText = 'Back to Curriculum';
        require APPROOT . '/Views/partials/navbar.php'; 
    ?>

    <div class="flex-1 flex flex-col items-center justify-center p-4 max-w-4xl mx-auto w-full mt-12 mb-16">
        
        <!-- Lesson Navigation Header -->
        <div class="flex justify-between items-center mb-6 w-full">
            <?php if ($prevLesson): ?>
                <a href="<?= URLROOT ?>/lessons/show/<?= $prevLesson->id ?>" class="flex items-center text-sm font-medium text-gray-500 hover:text-green-600 transition-colors bg-white px-4 py-2 rounded-lg border border-gray-200 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Previous Lesson
                </a>
            <?php else: ?>
                <div></div>
            <?php endif; ?>

            <?php if ($nextLesson): ?>
                <a href="<?= URLROOT ?>/lessons/show/<?= $nextLesson->id ?>" class="flex items-center text-sm font-medium text-gray-500 hover:text-green-600 transition-colors bg-white px-4 py-2 rounded-lg border border-gray-200 shadow-sm">
                    Next Lesson
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            <?php else: ?>
                <div></div>
            <?php endif; ?>
        </div>

        <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-10 w-full text-center mb-12">
            
            <?php if ($lesson->type === 'pptx' || $lesson->type === 'ppt'): ?>
                <div class="w-20 h-20 bg-orange-100 text-orange-500 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <span class="text-sm font-bold text-orange-600 uppercase tracking-wide">Presentation Presentation</span>
            <?php else: ?>
                <div class="w-20 h-20 bg-gray-100 text-gray-500 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <span class="text-sm font-bold text-gray-600 uppercase tracking-wide">Downloadable File</span>
            <?php endif; ?>

            <h1 class="text-3xl font-bold text-gray-900 mt-4 mb-2"><?= htmlspecialchars($lesson->title, ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="text-gray-500 mb-8">This lesson requires an external application to view. Please download the file to continue.</p>
            
            <div class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                <a href="<?= htmlspecialchars($lesson->content_path, ENT_QUOTES, 'UTF-8') ?>" download class="inline-flex items-center justify-center w-full sm:w-auto bg-gray-800 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-xl transition-all duration-200 shadow-sm hover:shadow-md">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Download <?= strtoupper($lesson->type) ?> File
                </a>
                
                <?php if (!$isCompleted): ?>
                    <form action="<?= URLROOT ?>/lessons/complete/<?= $lesson->id ?>" method="POST" class="w-full sm:w-auto">
                        <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent text-sm font-medium rounded-xl text-white bg-green-600 hover:bg-green-700 transition-colors shadow-sm">
                            Mark as Complete & Continue
                        </button>
                    </form>
                <?php endif; ?>
            </div>
            <?php if ($isCompleted): ?>
                <p class="mt-6 text-sm text-green-600 font-medium flex items-center justify-center">
                    <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    You have marked this lesson as complete.
                </p>
            <?php endif; ?>
        </div>
        
        <div class="w-full">
            <?php require APPROOT . '/Views/partials/lesson_discussion.php'; ?>
        </div>
    </div>

</body>
</html>