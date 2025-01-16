<?php

namespace App\Models;

use PDO;
use App\Core\Database;

class Author extends Database {

    public function __construct() {
        parent::__construct();
    }

    public function search($searchTerm): array {
        $query = "SELECT a.author_name, b.book_name 
                FROM authors a
                LEFT JOIN books b ON a.author_id = b.author_id
                WHERE a.author_name ILIKE :search_term
                OR b.book_name ILIKE :search_term
                ORDER BY a.author_name, b.book_name";

        $stmt = self::$pdo->prepare($query);
        $stmt->execute([':search_term' => '%' . $searchTerm . '%']);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function upsertAuthor($authorName): mixed {
        if (empty($authorName)) {
            throw new \Exception("Author name cannot be empty.");
        }
    
        $query = "
            INSERT INTO authors (author_name)
            VALUES (:author_name)
            ON CONFLICT (author_name) DO UPDATE 
            SET updated_at = CURRENT_TIMESTAMP
            RETURNING author_id
        ";
    
        $stmt = self::$pdo->prepare($query);
        $stmt->execute([':author_name' => $authorName]);
    
        return $stmt->fetchColumn();
    }
}
