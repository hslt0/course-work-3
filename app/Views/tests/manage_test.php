<?php
/**
 * @var string $title
 * @var App\Models\Test $test
 * @var App\Models\Course $course
 * @var array $questionsWithAnswers
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
        $backUrl = URLROOT . '/courses/manage_course/' . $course->id;
        $backText = 'Back to Course Management';
        require APPROOT . '/Views/partials/navbar.php'; 
    ?>

    <div class="flex-grow max-w-4xl mx-auto px-4 mt-12 mb-16 w-full">
        
        <div class="mb-8 border-b border-gray-200 pb-6 flex justify-between items-end">
            <div>
                <span class="text-sm font-bold text-indigo-600 uppercase tracking-wide">Test Builder</span>
                <h1 class="text-3xl font-extrabold text-gray-900 mt-1">
                    <?= htmlspecialchars($test->title, ENT_QUOTES, 'UTF-8') ?>
                </h1>
                <p class="mt-2 text-gray-500 text-sm">Course: <?= htmlspecialchars($course->name, ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <div>
                <a href="<?= URLROOT ?>/question/create_question/<?= $test->id ?>" class="inline-flex items-center justify-center px-6 py-2.5 border border-transparent text-sm font-bold rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Add Question
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            
            <?php if (empty($questionsWithAnswers)): ?>
                <div class="p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <h3 class="text-lg font-medium text-gray-900">No questions yet</h3>
                    <p class="mt-1 text-gray-500">Get started by creating your first multiple-choice question.</p>
                </div>
            <?php else: ?>
                <ul class="divide-y divide-gray-100">
                    <?php foreach ($questionsWithAnswers as $index => $item): ?>
                        <li class="p-8 hover:bg-gray-50 transition-colors group relative">
                            
                            <!-- Actions (Visible on hover) -->
                            <div class="absolute top-8 right-8 opacity-0 group-hover:opacity-100 transition-opacity flex gap-2">
                                <a href="<?= URLROOT ?>/question/edit_question/<?= $item['question']->id ?>" class="text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 p-2 rounded-lg transition-colors" title="Edit Question">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </a>
                                <form action="<?= URLROOT ?>/question/delete_question/<?= $item['question']->id ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this question?');">
                                    <input type="hidden" name="test_id" value="<?= $test->id ?>">
                                    <button type="submit" class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-2 rounded-lg transition-colors" title="Delete Question">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>

                            <div class="pr-24">
                                <p class="text-lg font-bold text-gray-900 mb-4">
                                    <span class="text-gray-400 mr-2">Q<?= $index + 1 ?>.</span>
                                    <?= htmlspecialchars($item['question']->question_text, ENT_QUOTES, 'UTF-8') ?>
                                </p>
                                
                                <div class="space-y-2 pl-8">
                                    <?php foreach ($item['answers'] as $answer): ?>
                                        <div class="flex items-center p-3 rounded-lg border <?= $answer->is_correct ? 'bg-green-50 border-green-200 text-green-800' : 'bg-white border-gray-200 text-gray-600' ?>">
                                            <?php if ($answer->is_correct): ?>
                                                <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            <?php else: ?>
                                                <div class="w-5 h-5 rounded-full border-2 border-gray-300 mr-3 flex-shrink-0"></div>
                                            <?php endif; ?>
                                            
                                            <span class="<?= $answer->is_correct ? 'font-bold' : '' ?>">
                                                <?= htmlspecialchars($answer->answer_text, ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

    </div>
</body>
</html>