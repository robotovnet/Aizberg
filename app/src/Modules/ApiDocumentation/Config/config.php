<?php

declare(strict_types=1);

return [
    'per_page' => (int) env('API_DOCUMENTATION_PER_PAGE', 20),
    'search_limit' => (int) env('API_DOCUMENTATION_SEARCH_LIMIT', 20),
    'search_candidate_limit' => (int) env('API_DOCUMENTATION_SEARCH_CANDIDATE_LIMIT', 200),
];
