<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?> | <?= SITENAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans text-gray-800 antialiased">

    <?php 
        $backUrl = URLROOT . '/courses/show/' . $course->id;
        $backText = 'Back to Curriculum';
        require APPROOT . '/Views/partials/navbar.php'; 
    ?>

    <div class="max-w-4xl mx-auto px-4 mt-12 mb-16">
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8 border-b border-gray-100 text-center">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Test Results</h1>
                <p class="text-gray-500"><?= htmlspecialchars($test->title, ENT_QUOTES, 'UTF-8') ?></p>
                
                <?php 
                    $percentage = ($total > 0) ? round(($score / $total) * 100) : 0;
                    $colorClass = $percentage >= 70 ? 'text-green-500' : ($percentage >= 50 ? 'text-yellow-500' : 'text-red-500');
                ?>
                <div class="mt-6">
                    <span class="text-6xl font-extrabold <?= $colorClass ?>"><?= $score ?></span>
                    <span class="text-3xl font-bold text-gray-400">/ <?= $total ?></span>
                </div>
                <p class="mt-2 text-lg font-medium text-gray-600">You scored <?= $percentage ?>%</p>
            </div>
            
            <div class="p-8 space-y-8">
                <?php foreach ($results as $index => $result): ?>
                    <div class="border border-gray-200 rounded-xl p-6 <?= $result['is_correct'] ? 'bg-green-50/30' : 'bg-red-50/30' ?>">
                        <div class="flex items-start mb-4">
                            <?php if ($result['is_correct']): ?>
                                <svg class="w-6 h-6 text-green-500 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <?php else: ?>
                                <svg class="w-6 h-6 text-red-500 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            <?php endif; ?>
                            <p class="text-lg font-semibold text-gray-800">
                                <span class="text-gray-400 mr-2"><?= $index + 1 ?>.</span>
                                <?= htmlspecialchars($result['question']->question_text, ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        </div>
                        
                        <div class="pl-9 space-y-2">
                            <?php foreach ($result['answers'] as $answer): ?>
                                <?php 
                                    $isUserAnswer = ($result['submitted_answer_id'] === $answer->id);
                                    $isCorrectAnswer = ($answer->is_correct);
                                    
                                    $itemClass = "p-3 rounded-lg border ";
                                    if ($isCorrectAnswer) {
                                        $itemClass .= "bg-green-100 border-green-500 text-green-800 font-medium";
                                    } elseif ($isUserAnswer && !$isCorrectAnswer) {
                                        $itemClass .= "bg-red-100 border-red-500 text-red-800 font-medium";
                                    } else {
                                        $itemClass .= "bg-white border-gray-200 text-gray-600 opacity-60";
                                    }
                                ?>
                                <div class="<?= $itemClass ?> flex items-center justify-between">
                                    <span><?= htmlspecialchars($answer->answer_text, ENT_QUOTES, 'UTF-8') ?></span>
                                    
                                    <?php if ($isUserAnswer): ?>
                                        <span class="text-xs uppercase tracking-wider font-bold opacity-75">Your Answer</span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div class="mt-8 text-center">
                    <a href="<?= URLROOT ?>/courses/show/<?= $course->id ?>" class="inline-block bg-gray-900 hover:bg-gray-800 text-white font-bold py-3 px-8 rounded-lg transition-colors">
                        Return to Course
                    </a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>