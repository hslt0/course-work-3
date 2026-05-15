<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?> | <?= SITENAME ?></title>
    <!-- Include Tailwind Typography plugin for styling rendered Markdown -->
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <!-- Include Marked.js to parse Markdown on the client side -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
</head>
<body class="bg-gray-50 font-sans text-gray-800 antialiased">

    <?php 
        $backUrl = URLROOT . '/courses/show/' . $course->id;
        $backText = 'Back to Curriculum';
        require APPROOT . '/Views/partials/navbar.php'; 
    ?>

    <div class="max-w-4xl mx-auto px-4 mt-12 mb-16">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8 border-b border-gray-100">
                <span class="text-sm font-bold text-purple-600 uppercase tracking-wide">Reading Material</span>
                <h1 class="text-3xl font-bold text-gray-900 mt-2"><?= htmlspecialchars($lesson->title, ENT_QUOTES, 'UTF-8') ?></h1>
            </div>
            
            <!-- We will load the Markdown content via JS and render it here -->
            <div class="p-8 prose prose-lg prose-green max-w-none text-gray-700" id="markdown-content">
                <div class="flex justify-center items-center h-32">
                    <svg class="animate-spin h-8 w-8 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Use a relative path from the root URL to fetch the markdown file
        const contentPath = '<?= URLROOT ?>' + '<?= htmlspecialchars($lesson->content_path, ENT_QUOTES, 'UTF-8') ?>';
        
        fetch(contentPath)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(text => {
                document.getElementById('markdown-content').innerHTML = marked.parse(text);
            })
            .catch(error => {
                console.error('Error fetching markdown:', error);
                document.getElementById('markdown-content').innerHTML = '<div class="bg-red-50 text-red-500 p-4 rounded-lg border border-red-200">Error loading lesson content. Please make sure the file exists.</div>';
            });
    </script>
</body>
</html>