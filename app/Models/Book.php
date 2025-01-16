<?php

namespace App\Models;

use App\Core\Database;

class Book extends Database {

    public function __construct() {
        parent::__construct();
    }

    public function upsertBook($authorId, $bookName) {
		if (empty($authorId)) {
			throw new \Exception("Invalid input: authorId and bookName must be provided.");
		}

		$query = "
			INSERT INTO books (author_id, book_name)
			VALUES (:author_id, :book_name)
			ON CONFLICT (author_id, book_name) DO UPDATE 
			SET updated_at = CURRENT_TIMESTAMP
		";
		
		$stmt = self::$pdo->prepare($query);
		$stmt->execute([':author_id' => $authorId, ':book_name' => $bookName]);
	}
}
