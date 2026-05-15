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

    <div class="flex-1 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-10 max-w-lg w-full text-center">
            
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
            
            <a href="<?= htmlspecialchars($lesson->content_path, ENT_QUOTES, 'UTF-8') ?>" download class="inline-flex items-center justify-center w-full bg-green-500 hover:bg-green-600 text-white font-bold py-4 px-8 rounded-xl transition-all duration-200 shadow-sm hover:shadow-md">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Download <?= strtoupper($lesson->type) ?> File
            </a>
        </div>
    </div>

</body>
</html>