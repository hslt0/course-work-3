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
                <div></div> <!-- Empty div to keep flex-between spacing -->
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
                    <span class="text-sm font-bold text-blue-600 uppercase tracking-wide">Video Lesson</span>
                    <h1 class="text-3xl font-bold text-gray-900 mt-2"><?= htmlspecialchars($lesson->title, ENT_QUOTES, 'UTF-8') ?></h1>
                </div>
                
                <div>
                    <?php if ($isCompleted): ?>
                        <span class="inline-flex items-center px-4 py-2 bg-green-50 text-green-700 font-semibold rounded-lg border border-green-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Completed
                        </span>
                    <?php else: ?>
                        <form action="<?= URLROOT ?>/lessons/complete/<?= $lesson->id ?>" method="POST">
                            <button type="submit" class="inline-flex items-center justify-center px-6 py-2.5 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 transition-colors shadow-sm">
                                Mark as Complete & Continue
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="aspect-w-16 aspect-h-9 bg-gray-900">
                <video controls class="w-full h-full object-cover">
                    <source src="<?= htmlspecialchars($lesson->content_path, ENT_QUOTES, 'UTF-8') ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
            
            <div class="p-8 bg-gray-50 border-t border-gray-100">
                <p class="text-gray-600">Watch the video carefully. You can pause, rewind, and re-watch as many times as you need. When you are finished, click "Mark as Complete & Continue" to track your progress and move to the next lesson.</p>
            </div>
        </div>

        <?php require APPROOT . '/Views/partials/lesson_discussion.php'; ?>

    </div>

</body>
</html>