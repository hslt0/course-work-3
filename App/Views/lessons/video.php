<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?> | <?= SITENAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans text-gray-800 antialiased">

    <!-- Navigation -->
    <nav class="bg-gray-900 shadow-lg border-b border-green-500">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <a href="<?= URLROOT ?>" class="flex items-center">
                    <svg class="w-8 h-8 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path></svg>
                    <span class="font-bold text-white text-xl tracking-wide"><?= SITENAME ?></span>
                </a>
                <a href="<?= URLROOT ?>/courses/show/<?= $course->id ?>" class="text-gray-300 hover:text-white flex items-center transition-colors">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to Curriculum
                </a>
            </div>
        </div>
    </nav>

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
