<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? '404 Not Found', ENT_QUOTES, 'UTF-8') ?> | <?= SITENAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans text-gray-800 antialiased flex flex-col h-screen">

    <?php require APPROOT . '/Views/partials/navbar.php'; ?>

    <div class="flex-1 flex items-center justify-center">
        <div class="text-center">
            <h1 class="text-9xl font-bold text-gray-200">404</h1>
            <p class="text-2xl font-semibold text-gray-700 mt-4">Oops! Page not found.</p>
            <p class="text-gray-500 mt-2">The resource you are looking for does not exist.</p>
            <a href="<?= URLROOT ?>" class="inline-block mt-6 px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition">Return Home</a>
        </div>
    </div>
</body>
</html>