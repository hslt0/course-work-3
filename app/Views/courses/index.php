<?php
/**
 * @var string $title
 * @var App\Models\Course[] $courses
 * @var App\Core\Paginator $paginator
 * @var array $languages
 * @var array $filters
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Our courses', ENT_QUOTES, 'UTF-8') ?> | <?= SITENAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans text-gray-800 antialiased">

    <?php require_once APPROOT . '/Views/partials/navbar.php'; ?>

    <div class="max-w-6xl mx-auto px-4 mt-12 mb-16">
        
        <div class="text-center mb-10">
            <h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl sm:tracking-tight lg:text-6xl">
                <?= htmlspecialchars($title ?? 'Our courses', ENT_QUOTES, 'UTF-8') ?>
            </h1>
            <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-600">
                Unlock a world of possibilities. Start your journey to fluency today with our expertly crafted language courses.
            </p>
        </div>

        <!-- Search Bar -->
        <div class="mb-8">
            <form action="<?= URLROOT ?>" method="GET" class="relative max-w-4xl mx-auto" id="search-form">
                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <label for="search-input" class="sr-only">Search</label>
                <input id="search-input" type="text" name="search" value="<?= htmlspecialchars($filters['search'], ENT_QUOTES, 'UTF-8') ?>" placeholder="Search courses by name or language..." class="block w-full pl-14 pr-32 py-5 border-2 border-gray-100 rounded-2xl leading-5 bg-white placeholder-gray-400 focus:outline-none focus:border-green-500 focus:ring-0 text-lg transition-colors shadow-sm">
                <div class="absolute inset-y-2 right-2">
                    <button type="submit" class="h-full bg-gray-900 hover:bg-gray-800 text-white font-bold py-2 px-8 rounded-xl transition-colors shadow-sm text-sm tracking-wide md:hidden">
                        SEARCH
                    </button>
                </div>
            </form>
        </div>

        <!-- Horizontal Filters -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-8 w-full">
            <form action="<?= URLROOT ?>" method="GET" id="filter-form" class="flex flex-col md:flex-row gap-4 items-end justify-between">
                
                <input type="hidden" name="search" id="hidden-search" value="<?= htmlspecialchars($filters['search'], ENT_QUOTES, 'UTF-8') ?>">
                
                <div class="w-full md:flex-1">
                    <label for="language-select" class="block text-sm font-medium text-gray-700 mb-2">Language</label>
                    <select id="language-select" name="language" class="filter-input block w-full pl-3 pr-10 py-2.5 text-sm border border-gray-200 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-lg transition-colors bg-gray-50">
                        <option value="">All Languages</option>
                        <?php foreach ($languages as $lang): ?>
                            <option value="<?= htmlspecialchars($lang, ENT_QUOTES, 'UTF-8') ?>" <?= $filters['language'] === $lang ? 'selected' : '' ?>>
                                <?= htmlspecialchars($lang, ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="w-full md:flex-1">
                    <label for="difficulty-select" class="block text-sm font-medium text-gray-700 mb-2">Difficulty</label>
                    <select id="difficulty-select" name="difficulty" class="filter-input block w-full pl-3 pr-10 py-2.5 text-sm border border-gray-200 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-lg transition-colors bg-gray-50">
                        <option value="">All Levels</option>
                        <option value="Beginner" <?= $filters['difficulty'] === 'Beginner' ? 'selected' : '' ?>>Beginner</option>
                        <option value="Intermediate" <?= $filters['difficulty'] === 'Intermediate' ? 'selected' : '' ?>>Intermediate</option>
                        <option value="Advanced" <?= $filters['difficulty'] === 'Advanced' ? 'selected' : '' ?>>Advanced</option>
                    </select>
                </div>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="w-full md:flex-1">
                        <label for="enrolled-select" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select id="enrolled-select" name="enrolled" class="filter-input block w-full pl-3 pr-10 py-2.5 text-sm border border-gray-200 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-lg transition-colors bg-gray-50">
                            <option value="">All Courses</option>
                            <option value="yes" <?= $filters['enrolled'] === 'yes' ? 'selected' : '' ?>>My Enrolled Courses</option>
                            <option value="no" <?= $filters['enrolled'] === 'no' ? 'selected' : '' ?>>Not Enrolled</option>
                        </select>
                    </div>
                <?php endif; ?>

                <div class="w-full md:flex-1">
                    <label for="sort-select" class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                    <select id="sort-select" name="sort" class="filter-input block w-full pl-3 pr-10 py-2.5 text-sm border border-gray-200 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-lg transition-colors bg-gray-50">
                        <option value="" <?= empty($filters['sort']) ? 'selected' : '' ?>>Relevance</option>
                        <option value="name_asc" <?= $filters['sort'] === 'name_asc' ? 'selected' : '' ?>>Name (A-Z)</option>
                        <option value="difficulty_asc" <?= $filters['sort'] === 'difficulty_asc' ? 'selected' : '' ?>>Difficulty (Low-High)</option>
                        <option value="difficulty_desc" <?= $filters['sort'] === 'difficulty_desc' ? 'selected' : '' ?>>Difficulty (High-Low)</option>
                    </select>
                </div>

                <div class="w-full md:w-auto mt-4 md:mt-0 flex gap-2">
                    <button type="submit" class="md:hidden flex-1 px-6 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-green-600 hover:bg-green-700 focus:outline-none transition-colors">
                        Apply
                    </button>
                    <button type="button" id="clear-filters" class="flex-1 md:flex-none px-6 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors shadow-sm">
                        Clear
                    </button>
                </div>
            </form>
        </div>

        <!-- Course List Content -->
        <div class="w-full flex flex-col">
            <div id="courses-container">
                <?php require '_courses_list.php'; ?>
            </div>
            
            <div id="pagination-container" class="mt-4 border-t border-gray-200 pt-6">
                <?= $paginator->getLinks(URLROOT . '/courses', $filters) ?>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterForm = document.getElementById('filter-form');
            const searchForm = document.getElementById('search-form');
            const searchInput = document.getElementById('search-input');
            const hiddenSearch = document.getElementById('hidden-search');
            const coursesContainer = document.getElementById('courses-container');
            const paginationContainer = document.getElementById('pagination-container');
            const filterInputs = document.querySelectorAll('.filter-input');
            const clearBtn = document.getElementById('clear-filters');

            let timeoutId;

            function fetchCourses(url) {
                coursesContainer.style.opacity = '0.5';
                
                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    coursesContainer.innerHTML = data.courses;
                    paginationContainer.innerHTML = data.pagination;
                    coursesContainer.style.opacity = '1';
                    
                    window.history.pushState({}, '', url.replace('/filter', ''));
                })
                .catch(error => {
                    console.error('Error fetching courses:', error);
                    coursesContainer.style.opacity = '1';
                });
            }

            function buildUrl() {
                const formData = new FormData(filterForm);
                const params = new URLSearchParams(formData);
                params.set('search', searchInput.value);
                return '<?= URLROOT ?>/courses/filter?' + params.toString();
            }

            function handleFilterChange() {
                hiddenSearch.value = searchInput.value;
                const url = buildUrl();
                fetchCourses(url);
            }

            filterInputs.forEach(input => {
                input.addEventListener('change', handleFilterChange);
            });

            searchInput.addEventListener('input', function() {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(handleFilterChange, 300);
            });

            filterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                handleFilterChange();
            });

            searchForm.addEventListener('submit', function(e) {
                e.preventDefault();
                handleFilterChange();
            });

            clearBtn.addEventListener('click', function() {
                searchInput.value = '';
                filterInputs.forEach(input => {
                    if (input.tagName === 'SELECT') {
                        input.value = '';
                    } else if (input.type === 'radio' && input.value === '') {
                        input.checked = true;
                    }
                });
                handleFilterChange();
            });

            paginationContainer.addEventListener('click', function(e) {
                if (e.target.tagName === 'A') {
                    e.preventDefault();
                    const url = new URL(e.target.href);
                    const newUrl = '<?= URLROOT ?>/courses/filter' + url.search;
                    fetchCourses(newUrl);
                }
            });
        });
    </script>
</body>
</html>