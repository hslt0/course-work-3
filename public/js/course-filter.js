document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filter-form');
    const searchForm = document.getElementById('search-form');
    const searchInput = document.getElementById('search-input');
    const hiddenSearch = document.getElementById('hidden-search');
    const coursesContainer = document.getElementById('courses-container');
    const paginationContainer = document.getElementById('pagination-container');
    const filterInputs = document.querySelectorAll('.filter-input');
    const clearBtn = document.getElementById('clear-filters');
    
    // Retrieve URLROOT from the script tag's data attribute
    const scriptTag = document.getElementById('course-filter-script');
    const urlRoot = scriptTag ? scriptTag.dataset.urlroot : '';

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
        return urlRoot + '/courses/filter?' + params.toString();
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
            const newUrl = urlRoot + '/courses/filter' + url.search;
            fetchCourses(newUrl);
        }
    });
});