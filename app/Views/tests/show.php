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
            <div class="p-8 border-b border-gray-100">
                <span class="text-sm font-bold text-indigo-600 uppercase tracking-wide">Knowledge Test</span>
                <h1 class="text-3xl font-bold text-gray-900 mt-2"><?= htmlspecialchars($test->title, ENT_QUOTES, 'UTF-8') ?></h1>
            </div>
            
            <form action="<?= URLROOT ?>/tests/submit/<?= $test->id ?>" method="POST" class="p-8">
                
                <?php foreach ($questionsWithAnswers as $index => $item): ?>
                    <div class="mb-8 border-b border-gray-100 pb-8">
                        <p class="text-xl font-semibold text-gray-800 mb-4">
                            <span class="text-gray-400 mr-2"><?= $index + 1 ?>.</span>
                            <?= htmlspecialchars($item['question']->question_text, ENT_QUOTES, 'UTF-8') ?>
                        </p>
                        <div class="space-y-3">
                            <?php foreach ($item['answers'] as $answer): ?>
                                <label class="block w-full p-4 border border-gray-200 rounded-lg hover:bg-green-50 hover:border-green-400 transition-all cursor-pointer">
                                    <input type="radio" name="question_<?= $item['question']->id ?>" value="<?= $answer->id ?>" class="mr-3">
                                    <span class="text-gray-700"><?= htmlspecialchars($answer->answer_text, ENT_QUOTES, 'UTF-8') ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="mt-8">
                    <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-4 px-8 rounded-xl transition-all duration-200 shadow-sm hover:shadow-md text-lg">
                        Submit Test
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
