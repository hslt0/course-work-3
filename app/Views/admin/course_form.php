<?php
/**
 * @var string $title
 * @var int|null $id
 * @var string $name
 * @var string $language
 * @var string $difficulty
 * @var string $description
 * @var string $preview_image
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
        $backUrl = URLROOT . '/admin/dashboard';
        $backText = 'Back to Admin Dashboard';
        require APPROOT . '/Views/partials/navbar.php'; 
    ?>

    <div class="flex-grow max-w-3xl mx-auto px-4 mt-12 mb-16 w-full">
        
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900">
                <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>
            </h1>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <form action="<?= isset($id) ? URLROOT . '/courses/edit_course/' . $id : URLROOT . '/courses/create_course' ?>" method="POST" class="p-8 space-y-6">
                
                <?php if (!empty($error)): ?>
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                        <?= $error ?>
                    </div>
                <?php endif; ?>

                <div>
                    <label for="name" class="block text-sm font-bold text-gray-700 mb-2">Course Name *</label>
                    <input type="text" name="name" id="name" required value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-green-500 focus:border-green-500 sm:text-sm">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="language" class="block text-sm font-bold text-gray-700 mb-2">Language *</label>
                        <input type="text" name="language" id="language" required value="<?= htmlspecialchars($language, ENT_QUOTES, 'UTF-8') ?>" placeholder="e.g. English, Spanish" class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="difficulty" class="block text-sm font-bold text-gray-700 mb-2">Difficulty Level *</label>
                        <select name="difficulty" id="difficulty" required class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-green-500 focus:border-green-500 sm:text-sm bg-white">
                            <option value="Beginner" <?= $difficulty === 'Beginner' ? 'selected' : '' ?>>Beginner</option>
                            <option value="Intermediate" <?= $difficulty === 'Intermediate' ? 'selected' : '' ?>>Intermediate</option>
                            <option value="Advanced" <?= $difficulty === 'Advanced' ? 'selected' : '' ?>>Advanced</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-bold text-gray-700 mb-2">Description *</label>
                    <textarea name="description" id="description" rows="5" required class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-green-500 focus:border-green-500 sm:text-sm"><?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>

                <div>
                    <label for="preview_image" class="block text-sm font-bold text-gray-700 mb-2">Preview Image URL (Optional)</label>
                    <input type="url" name="preview_image" id="preview_image" value="<?= htmlspecialchars($preview_image, ENT_QUOTES, 'UTF-8') ?>" placeholder="https://example.com/image.jpg" class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    <p class="mt-2 text-sm text-gray-500">Provide a direct link to an image. Leave blank to use the default placeholder.</p>
                </div>

                <div class="pt-6 border-t border-gray-100 flex justify-end gap-4">
                    <a href="<?= URLROOT ?>/admin/dashboard" class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 bg-white hover:bg-gray-50 font-medium transition-colors">Cancel</a>
                    <button type="submit" class="px-8 py-3 border border-transparent rounded-xl text-white bg-green-600 hover:bg-green-700 font-bold shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                        <?= isset($id) ? 'Update Course' : 'Create Course' ?>
                    </button>
                </div>
            </form>
        </div>

    </div>
</body>
</html>