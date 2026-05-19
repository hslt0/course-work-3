<?php
/**
 * @var string $title
 * @var int $test_id
 * @var int|null $question_id
 * @var string $question_text
 * @var array $answers
 * @var string $correct_answer
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
        $backUrl = URLROOT . '/tests/manage_test/' . $test_id; 
        $backText = 'Back to Test Builder';
        require APPROOT . '/Views/partials/navbar.php'; 
    ?>

    <div class="flex-grow max-w-3xl mx-auto px-4 mt-12 mb-16 w-full">
        
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900">
                <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>
            </h1>
            <p class="mt-2 text-gray-500">Provide the question text and up to 4 possible answers. Don't forget to select which one is the correct answer!</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <form action="<?= $question_id !== null ? URLROOT . '/question/edit_question/' . $question_id : URLROOT . '/question/create_question/' . $test_id ?>" method="POST" class="p-8 space-y-8">
                
                <?php if (!empty($error)): ?>
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                        <?= $error ?>
                    </div>
                <?php endif; ?>

                <div>
                    <label for="question_text" class="block text-sm font-bold text-gray-900 mb-2">Question Text *</label>
                    <textarea name="question_text" id="question_text" rows="3" required placeholder="e.g. What is the correct translation of 'Hello'?" class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 sm:text-base"><?= htmlspecialchars($question_text, ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>

                <div class="border-t border-gray-200 pt-8">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Answers</h3>
                    <p class="text-sm text-gray-500 mb-6">Fill out at least 2 answers. Select the radio button next to the correct answer.</p>

                    <div class="space-y-4">
                        <?php for ($i = 0; $i < 4; $i++): ?>
                            <div class="flex items-center gap-4 p-4 border border-gray-200 rounded-xl bg-gray-50 hover:bg-white hover:border-indigo-300 transition-colors focus-within:ring-1 focus-within:ring-indigo-500 focus-within:bg-white">
                                <div class="flex items-center h-full">
                                    <input type="radio" name="correct_answer" value="<?= $i ?>" id="correct_<?= $i ?>" <?= $correct_answer === (string)$i ? 'checked' : '' ?> class="w-5 h-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 cursor-pointer" aria-label="Mark option <?= $i + 1 ?> as correct">
                                </div>
                                <div class="flex-grow">
                                    <label for="answer_<?= $i ?>" class="sr-only">Answer <?= $i + 1 ?></label>
                                    <input type="text" name="answer_<?= $i ?>" id="answer_<?= $i ?>" value="<?= htmlspecialchars($answers[$i] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Option <?= $i + 1 ?>" class="block w-full bg-transparent border-0 border-b border-transparent focus:border-indigo-500 focus:ring-0 sm:text-base px-0 py-2">
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="pt-8 border-t border-gray-100 flex justify-end gap-4">
                    <a href="<?= URLROOT ?>/tests/manage_test/<?= $test_id ?>" class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 bg-white hover:bg-gray-50 font-medium transition-colors">Cancel</a>
                    <button type="submit" class="px-8 py-3 border border-transparent rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 font-bold shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        Save Question
                    </button>
                </div>
            </form>
        </div>

    </div>
</body>
</html>