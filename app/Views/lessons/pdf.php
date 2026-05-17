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
<body class="bg-gray-50 font-sans text-gray-800 antialiased">

    <?php 
        $backUrl = URLROOT . '/courses/show/' . $course->id;
        $backText = 'Back to Curriculum';
        require APPROOT . '/Views/partials/navbar.php'; 
    ?>

    <div class="max-w-4xl mx-auto px-4 mt-12 mb-16">
        
        <!-- Lesson Navigation Header -->
        <div class="flex justify-between items-center mb-6">
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

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <span class="text-sm font-bold text-red-600 uppercase tracking-wide">PDF Document</span>
                    <h1 class="text-3xl font-bold text-gray-900 mt-2"><?= htmlspecialchars($lesson->title, ENT_QUOTES, 'UTF-8') ?></h1>
                </div>
                
                <div class="flex items-center space-x-3">
                    <a href="<?= htmlspecialchars($lesson->content_path, ENT_QUOTES, 'UTF-8') ?>" download class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium py-2.5 px-4 rounded-lg transition-colors flex items-center text-sm shadow-sm">
                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Download PDF
                    </a>

                    <?php if ($isCompleted): ?>
                        <span class="inline-flex items-center px-4 py-2 bg-green-50 text-green-700 font-semibold rounded-lg border border-green-200 text-sm">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Completed
                        </span>
                    <?php else: ?>
                        <form action="<?= URLROOT ?>/lessons/complete/<?= $lesson->id ?>" method="POST">
                            <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 transition-colors shadow-sm">
                                Mark as Complete & Continue
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="w-full" style="height: 80vh;">
                <!-- Embedding PDF using an iframe. This relies on the browser's PDF viewing capabilities. -->
                <iframe src="<?= htmlspecialchars($lesson->content_path, ENT_QUOTES, 'UTF-8') ?>" class="w-full h-full border-0"></iframe>
            </div>
        </div>

        <?php require APPROOT . '/Views/partials/lesson_discussion.php'; ?>

    </div>

</body>
</html>