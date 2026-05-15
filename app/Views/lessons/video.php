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
                    <span class="text-sm font-bold text-blue-600 uppercase tracking-wide">Video Lesson</span>
                    <h1 class="text-3xl font-bold text-gray-900 mt-2"><?= htmlspecialchars($lesson->title, ENT_QUOTES, 'UTF-8') ?></h1>
                </div>
            </div>
            
            <div class="aspect-w-16 aspect-h-9 bg-gray-900">
                <!-- Using HTML5 Video tag. For YouTube/Vimeo, you would use an iframe here based on content_path -->
                <video controls class="w-full h-full object-cover">
                    <source src="<?= htmlspecialchars($lesson->content_path, ENT_QUOTES, 'UTF-8') ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
            
            <div class="p-8 bg-gray-50">
                <p class="text-gray-600">Watch the video carefully. You can pause, rewind, and re-watch as many times as you need.</p>
            </div>
        </div>
    </div>

</body>
</html>