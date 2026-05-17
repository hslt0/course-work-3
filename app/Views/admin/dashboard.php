<?php
/**
 * @var string $title
 * @var App\Models\Course[] $courses
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?> | <?= SITENAME ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans text-gray-800 antialiased min-h-screen flex flex-col">

    <?php require_once APPROOT . '/Views/partials/navbar.php'; ?>

    <div class="flex-grow max-w-6xl mx-auto px-4 mt-12 mb-16 w-full">
        
        <!-- Header Section -->
        <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900">
                    Admin Dashboard
                </h1>
                <p class="mt-2 text-lg text-gray-600">
                    Manage your platform's courses, lessons, and tests.
                </p>
            </div>
            <div>
                <a href="<?= URLROOT ?>/admin/create_course" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-green-600 hover:bg-green-700 shadow-sm focus:outline-none transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Create New Course
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-900">All Courses</h2>
                <span class="bg-gray-200 text-gray-700 text-sm font-bold px-3 py-1 rounded-full"><?= count($courses) ?> Total</span>
            </div>
            
            <?php if (empty($courses)): ?>
                <div class="p-12 text-center">
                    <p class="text-gray-500">No courses have been created yet.</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Language</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Difficulty</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($courses as $course): ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        #<?= $course->id ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900"><?= htmlspecialchars($course->name, ENT_QUOTES, 'UTF-8') ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            <?= htmlspecialchars($course->language, ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= htmlspecialchars($course->difficulty_level, ENT_QUOTES, 'UTF-8') ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="<?= URLROOT ?>/admin/manage_course/<?= $course->id ?>" class="text-indigo-600 hover:text-indigo-900 mr-4">Manage Lessons & Tests</a>
                                        <a href="<?= URLROOT ?>/admin/edit_course/<?= $course->id ?>" class="text-blue-600 hover:text-blue-900 mr-4">Edit</a>
                                        <form action="<?= URLROOT ?>/admin/delete_course/<?= $course->id ?>" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to permanently delete this course? All associated lessons, tests, and user progress will be lost.');">
                                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

    </div>
</body>
</html>