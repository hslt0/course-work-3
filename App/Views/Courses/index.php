<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Our Courses', ENT_QUOTES, 'UTF-8') ?></title>
    <style>
        body { font-family: sans-serif; }
        .container { width: 80%; margin: auto; }
        .course { border: 1px solid #ccc; padding: 10px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h1><?= htmlspecialchars($title ?? 'Our Courses', ENT_QUOTES, 'UTF-8') ?></h1>
        <?php if (empty($courses)): ?>
            <p>No courses available at the moment.</p>
        <?php else: ?>
            <?php foreach ($courses as $course): ?>
                <div class="course">
                    <h2><?= htmlspecialchars($course->name, ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($course->language, ENT_QUOTES, 'UTF-8') ?>)</h2>
                    <p><?= htmlspecialchars($course->description, ENT_QUOTES, 'UTF-8') ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
