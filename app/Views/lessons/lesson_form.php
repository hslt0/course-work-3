<?php
/**
 * @var string $title
 * @var int $course_id
 * @var int|null $lesson_id
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
        $backUrl = URLROOT . '/courses/manage_course/' . $course_id;
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
            <!-- Ensure enctype is multipart/form-data for file uploads -->
            <form action="<?= isset($lesson_id) ? URLROOT . '/lessons/edit_lesson/' . $lesson_id : URLROOT . '/lessons/create_lesson/' . $course_id ?>" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
                
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

                <!-- Toggle between File Upload and URL/Path Input -->
                <div class="border-t border-gray-100 pt-6 mt-6">
                    <p class="text-sm font-bold text-gray-700 mb-4">Content Source *</p>
                    
                    <div class="space-y-4">
                        <label class="flex items-center">
                            <input type="radio" name="content_source" value="upload" id="source_upload" class="form-radio text-green-600 focus:ring-green-500" <?= empty($content_path) || (!filter_var($content_path, FILTER_VALIDATE_URL) && !str_starts_with($content_path, '/uploads/')) ? 'checked' : '' ?>>
                            <span class="ml-2 text-sm text-gray-700">Upload a New File</span>
                        </label>
                        
                        <div id="upload_container" class="pl-6 pb-2">
                            <input type="file" name="lesson_file" id="lesson_file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 cursor-pointer">
                            <p class="mt-1 text-xs text-gray-500">Max file size depends on your PHP config.</p>
                        </div>

                        <label class="flex items-center pt-2">
                            <input type="radio" name="content_source" value="path" id="source_path" class="form-radio text-green-600 focus:ring-green-500" <?= !empty($content_path) && (filter_var($content_path, FILTER_VALIDATE_URL) || str_starts_with($content_path, '/uploads/')) ? 'checked' : '' ?>>
                            <span class="ml-2 text-sm text-gray-700">Use Existing Path or URL</span>
                        </label>

                        <div id="path_container" class="pl-6 pb-2">
                            <label for="content_path"></label><input type="text" name="content_path" id="content_path" value="<?= htmlspecialchars($content_path, ENT_QUOTES, 'UTF-8') ?>" placeholder="e.g., https://youtube.com/..." class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            <p class="mt-1 text-xs text-gray-500">Only used if "Use Existing Path or URL" is selected.</p>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-100 flex justify-end gap-4">
                    <a href="<?= URLROOT ?>/courses/manage_course/<?= $course_id ?>" class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 bg-white hover:bg-gray-50 font-medium transition-colors">Cancel</a>
                    <button type="submit" class="px-8 py-3 border border-transparent rounded-xl text-white bg-green-600 hover:bg-green-700 font-bold shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                        <?= isset($lesson_id) ? 'Update Lesson' : 'Add Lesson' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Simple JS to toggle inputs based on radio selection -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const radioUpload = document.getElementById('source_upload');
            const radioPath = document.getElementById('source_path');
            const containerUpload = document.getElementById('upload_container');
            const containerPath = document.getElementById('path_container');
            
            function updateVisibility() {
                if (radioUpload.checked) {
                    containerUpload.style.opacity = '1';
                    containerUpload.style.pointerEvents = 'auto';
                    containerPath.style.opacity = '0.5';
                    containerPath.style.pointerEvents = 'none';
                } else {
                    containerUpload.style.opacity = '0.5';
                    containerUpload.style.pointerEvents = 'none';
                    containerPath.style.opacity = '1';
                    containerPath.style.pointerEvents = 'auto';
                }
            }

            radioUpload.addEventListener('change', updateVisibility);
            radioPath.addEventListener('change', updateVisibility);
            
            // Initial call
            updateVisibility();
        });
    </script>
</body>
</html>