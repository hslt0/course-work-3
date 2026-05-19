<?php
/**
 * @var string $title
 * @var App\Models\Course $course
 * @var App\Models\Lesson[] $lessons
 * @var App\Models\Test[] $tests
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
        $backText = 'Back to Dashboard';
        require APPROOT . '/Views/partials/navbar.php'; 
    ?>

    <div class="flex-grow max-w-6xl mx-auto px-4 mt-12 mb-16 w-full">
        
        <div class="mb-10 border-b border-gray-200 pb-6">
            <h1 class="text-3xl font-extrabold text-gray-900">
                Manage Content: <?= htmlspecialchars($course->name, ENT_QUOTES, 'UTF-8') ?>
            </h1>
            <p class="mt-2 text-gray-600">Add, edit, or remove lessons and tests for this course.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <!-- Lessons Panel -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h2 class="text-xl font-bold text-gray-900">Lessons</h2>
                    <a href="<?= URLROOT ?>/lessons/create_lesson/<?= $course->id ?>" class="text-sm bg-green-100 hover:bg-green-200 text-green-800 font-bold py-1.5 px-4 rounded-full transition-colors flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        Add Lesson
                    </a>
                </div>
                
                <?php if (empty($lessons)): ?>
                    <div class="p-8 text-center">
                        <p class="text-gray-500">No lessons currently in this course.</p>
                    </div>
                <?php else: ?>
                    <ul class="divide-y divide-gray-100">
                        <?php foreach ($lessons as $lesson): ?>
                            <li class="p-6 hover:bg-gray-50 flex items-center justify-between group transition-colors">
                                <div>
                                    <p class="font-bold text-gray-900"><?= htmlspecialchars($lesson->title, ENT_QUOTES, 'UTF-8') ?></p>
                                    <p class="text-xs text-gray-500 uppercase tracking-wide mt-1"><?= htmlspecialchars($lesson->type, ENT_QUOTES, 'UTF-8') ?></p>
                                </div>
                                <div class="opacity-0 group-hover:opacity-100 transition-opacity flex gap-2">
                                    <a href="<?= URLROOT ?>/lessons/edit_lesson/<?= $lesson->id ?>" class="text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 p-2 rounded-lg transition-colors" title="Edit Lesson">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </a>
                                    <form action="<?= URLROOT ?>/lessons/delete_lesson/<?= $lesson->id ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this lesson?');">
                                        <button type="submit" class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-2 rounded-lg transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <!-- Tests Panel -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h2 class="text-xl font-bold text-gray-900">Knowledge Tests</h2>
                    <a href="<?= URLROOT ?>/tests/create_test/<?= $course->id ?>" class="text-sm bg-indigo-100 hover:bg-indigo-200 text-indigo-800 font-bold py-1.5 px-4 rounded-full transition-colors flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        Add Test
                    </a>
                </div>
                
                <?php if (empty($tests)): ?>
                    <div class="p-8 text-center">
                        <p class="text-gray-500">No tests currently in this course.</p>
                    </div>
                <?php else: ?>
                    <ul class="divide-y divide-gray-100">
                        <?php foreach ($tests as $test): ?>
                            <li class="p-6 hover:bg-gray-50 flex items-center justify-between group transition-colors">
                                <div>
                                    <p class="font-bold text-gray-900"><?= htmlspecialchars($test->title, ENT_QUOTES, 'UTF-8') ?></p>
                                </div>
                                <div class="opacity-0 group-hover:opacity-100 transition-opacity flex gap-3">
                                    <a href="<?= URLROOT ?>/tests/manage_test/<?= $test->id ?>" class="text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 p-2 rounded-lg transition-colors" title="Manage Test">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </a>
                                    <form action="<?= URLROOT ?>/tests/delete_test/<?= $test->id ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this test?');">
                                        <button type="submit" class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-2 rounded-lg transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

        </div>
    </div>
</body>
</html>