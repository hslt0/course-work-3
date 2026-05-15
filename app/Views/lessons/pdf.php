<?php
/**
 * @var string $title
 * @var App\Models\Course $course
 * @var App\Models\Lesson $lesson
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
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <span class="text-sm font-bold text-red-600 uppercase tracking-wide">PDF Document</span>
                    <h1 class="text-3xl font-bold text-gray-900 mt-2"><?= htmlspecialchars($lesson->title, ENT_QUOTES, 'UTF-8') ?></h1>
                </div>
                <a href="<?= htmlspecialchars($lesson->content_path, ENT_QUOTES, 'UTF-8') ?>" download class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg transition-colors flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Download PDF
                </a>
            </div>
            
            <div class="w-full" style="height: 80vh;">
                <!-- Embedding PDF using an iframe. This relies on the browser's PDF viewing capabilities. -->
                <iframe src="<?= htmlspecialchars($lesson->content_path, ENT_QUOTES, 'UTF-8') ?>" class="w-full h-full border-0"></iframe>
            </div>
        </div>
    </div>

</body>
</html>