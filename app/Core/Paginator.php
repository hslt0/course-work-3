<?php

namespace App\Core;

class Paginator
{
    public int $totalItems;
    public int $itemsPerPage;
    public int $currentPage;
    public int $totalPages;

    public function __construct(int $totalItems, int $itemsPerPage, int $currentPage = 1)
    {
        $this->totalItems = $totalItems;
        $this->itemsPerPage = $itemsPerPage;
        $this->totalPages = (int)ceil($totalItems / $itemsPerPage);
        $this->currentPage = $this->sanitizeCurrentPage($currentPage);
    }

    private function sanitizeCurrentPage(int $page): int
    {
        if ($page < 1) {
            return 1;
        }
        if ($this->totalPages > 0 && $page > $this->totalPages) {
            return $this->totalPages;
        }
        return $page;
    }

    public function getOffset(): int
    {
        return ($this->currentPage - 1) * $this->itemsPerPage;
    }

    public function hasPrevious(): bool
    {
        return $this->currentPage > 1;
    }

    public function hasNext(): bool
    {
        return $this->currentPage < $this->totalPages;
    }

    public function getLinks(string $baseUrl, array $queryParams = []): string
    {
        if ($this->totalPages <= 1) {
            return '';
        }

        $html = '<nav class="flex items-center justify-between" aria-label="Pagination">';
        
        // Previous Button
        $prevDisabled = !$this->hasPrevious() ? 'opacity-50 pointer-events-none' : '';
        $queryParams['page'] = $this->currentPage - 1;
        $prevUrl = $baseUrl . '?' . http_build_query($queryParams);
        $html .= '<a href="' . $prevUrl . '" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 ' . $prevDisabled . '">Previous</a>';

        // Page Links (simplified for brevity)
        $html .= '<div class="hidden sm:block">';
        $html .= '<p class="text-sm text-gray-700">Page <span class="font-medium">' . $this->currentPage . '</span> of <span class="font-medium">' . $this->totalPages . '</span></p>';
        $html .= '</div>';

        // Next Button
        $nextDisabled = !$this->hasNext() ? 'opacity-50 pointer-events-none' : '';
        $queryParams['page'] = $this->currentPage + 1;
        $nextUrl = $baseUrl . '?' . http_build_query($queryParams);
        $html .= '<a href="' . $nextUrl . '" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 ' . $nextDisabled . '">Next</a>';

        $html .= '</nav>';

        return $html;
    }
}
