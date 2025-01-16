<?php

namespace App\Core;

use App\Models\Author;
use App\Models\Book;

class XMLProcessor {
    private Author $authorModel;
    private Book $bookModel;

    public function __construct() {
        $this->authorModel = new Author();
        $this->bookModel = new Book();
    }

    public function processFolder(string $folderPath, int $batchSize = 100): void {
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($folderPath));
        $batch = [];

        foreach ($files as $file) {
            if ($file->getExtension() === 'xml') {
                $xml = simplexml_load_file($file->getPathname());
                foreach ($xml->book as $book) {
                    $batch[] = $book;

                    if (count($batch) >= $batchSize) {
                        $this->processBatch($batch);
                        $batch = []; // Reset the batch
                    }
                }
            }
        }

        // Process any remaining data
        if (!empty($batch)) {
            $this->processBatch($batch);
        }
    }

    private function processBatch(array $batch): void {
        foreach ($batch as $book) {
            $authorName = trim((string)$book->author);
            $bookName = trim((string)$book->name);

            if (!empty($authorName)) {
                $authorId = $this->authorModel->upsertAuthor($authorName);
                $this->bookModel->upsertBook($authorId, $bookName);
            }
        }
    }
}
