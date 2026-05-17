<?php
/**
 * @var string $title
 * @var int $course_id
 * @var string $lesson_title
 * @var string $type
 * @var string $content_path
 * @var string $error
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?> | <?= SITENAME ?> Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans text-gray-800 antialiased min-h-screen flex flex-col">

    <?php 
        $backUrl = URLROOT . '/admin/manage_course/' . $course_id;
        $backText = 'Back to Course Management';
        require APPROOT . '/Views/partials/navbar.php'; 
    ?>

    <div class="flex-grow max-w-3xl mx-auto px-4 mt-12 mb-16 w-full">
        
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900">
                <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>
            </h1>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <form action="<?= URLROOT ?>/admin/create_lesson/<?= $course_id ?>" method="POST" class="p-8 space-y-6">
                
                <?php if (!empty($error)): ?>
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                        <?= $error ?>
                    </div>
                <?php endif; ?>

                <div>
                    <label for="lesson_title" class="block text-sm font-bold text-gray-700 mb-2">Lesson Title *</label>
                    <input type="text" name="lesson_title" id="lesson_title" required value="<?= htmlspecialchars($lesson_title, ENT_QUOTES, 'UTF-8') ?>" class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-green-500 focus:border-green-500 sm:text-sm">
                </div>

                <div>
                    <label for="type" class="block text-sm font-bold text-gray-700 mb-2">Lesson Type *</label>
                    <select name="type" id="type" required class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-green-500 focus:border-green-500 sm:text-sm bg-white">
                        <option value="video" <?= $type === 'video' ? 'selected' : '' ?>>Video (MP4, YouTube Embed URL)</option>
                        <option value="pdf" <?= $type === 'pdf' ? 'selected' : '' ?>>PDF Document</option>
                        <option value="markdown" <?= $type === 'markdown' ? 'selected' : '' ?>>Markdown Text File</option>
                        <option value="pptx" <?= $type === 'pptx' ? 'selected' : '' ?>>PowerPoint (Downloadable)</option>
                    </select>
                </div>

                <div>
                    <label for="content_path" class="block text-sm font-bold text-gray-700 mb-2">Content Path or URL *</label>
                    <input type="text" name="content_path" id="content_path" required value="<?= htmlspecialchars($content_path, ENT_QUOTES, 'UTF-8') ?>" placeholder="/uploads/lessons/file.mp4 or https://example.com/file" class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    <p class="mt-2 text-sm text-gray-500">Provide the relative path (e.g., /uploads/lessons/video.mp4) or a full external URL.</p>
                </div>

                <div class="pt-6 border-t border-gray-100 flex justify-end gap-4">
                    <a href="<?= URLROOT ?>/admin/manage_course/<?= $course_id ?>" class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 bg-white hover:bg-gray-50 font-medium transition-colors">Cancel</a>
                    <button type="submit" class="px-8 py-3 border border-transparent rounded-xl text-white bg-green-600 hover:bg-green-700 font-bold shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                        Add Lesson
                    </button>
                </div>
            </form>
        </div>

    </div>
</body>
</html>