<?php
/**
 * @var string $title
 * @var int $course_id
 * @var string $test_title
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
            <form action="<?= URLROOT ?>/tests/create_test/<?= $course_id ?>" method="POST" class="p-8 space-y-6">
                
                <?php if (!empty($error)): ?>
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                        <?= $error ?>
                    </div>
                <?php endif; ?>

                <div>
                    <label for="test_title" class="block text-sm font-bold text-gray-700 mb-2">Test Title *</label>
                    <input type="text" name="test_title" id="test_title" required value="<?= htmlspecialchars($test_title, ENT_QUOTES, 'UTF-8') ?>" placeholder="e.g. Final Exam" class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-green-500 focus:border-green-500 sm:text-sm">
                </div>

                <div class="pt-6 border-t border-gray-100 flex justify-end gap-4">
                    <a href="<?= URLROOT ?>/courses/manage_course/<?= $course_id ?>" class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 bg-white hover:bg-gray-50 font-medium transition-colors">Cancel</a>
                    <button type="submit" class="px-8 py-3 border border-transparent rounded-xl text-white bg-green-600 hover:bg-green-700 font-bold shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                        Add Test
                    </button>
                </div>
            </form>
        </div>

    </div>
</body>
</html>