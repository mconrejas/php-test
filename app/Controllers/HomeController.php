<?php

namespace App\Controllers;

use App\Core\RedisCache;
use App\Core\View;
use App\Models\Author;

class HomeController {
    use RedisCache;
    use View;

    private Author $author;

    public function __construct(?Author $author = null) {
        $this->author = $author ?? new Author();
    }    

    public function index(): void {
        $key = 'search:';

        // Check Redis cache
        $results = $this->get($key);

        if (!$results) {
            $results = $this->author->search('');

            // Cache the results for 1 hour
            $this->set($key, $results, 3600);
        }

        // Render the view
        $this->view('search', [
            'title' => 'Search Authors and Books',
            'results' => $results
        ]);
    }

    public function search(): void {
        header('Content-Type: application/json');

        $searchTerm = $_GET['author'] ?? '';
        $key = 'search:' . md5($searchTerm);

        // Check Redis cache
        $results = $this->get($key);

        if (!$results) {
            // Fetch from database if not in cache
            $results = $this->author->search($searchTerm);

            // Cache the results
            $this->set($key, $results, 3600);
        }

        // Return the results as JSON
        echo json_encode($results);
    }
}
