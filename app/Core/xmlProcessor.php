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

    /**
     * Process a folder of XML files.
     */
    public function processFolder(string $folderPath, int $batchSize = 100): void {
        // Detect if the dataset is large and adjust batch size
        if ($this->isLargeDataset($folderPath)) {
            $batchSize *= 4; // Increase batch size for large datasets
            echo "Large dataset detected. Adjusting batch size to $batchSize.\n";
        }

        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($folderPath));
        $batch = [];

        foreach ($files as $file) {
            if ($file->getExtension() === 'xml') {
                $xml = simplexml_load_file($file->getPathname());
                foreach ($xml->book as $book) {
                    $batch[] = $book;

                    if (count($batch) >= $batchSize) {
                        $this->processInParallel($batch); // Process batch in parallel
                        $batch = []; // Reset batch
                    }
                }
            }
        }

        // Process remaining records
        if (!empty($batch)) {
            $this->processInParallel($batch);
        }
    }

    /**
     * Check if the dataset is large based on file size and count.
     */
    protected function isLargeDataset(string $folderPath): bool {
        $totalSize = 0;
        $fileCount = 0;

        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($folderPath));
        foreach ($files as $file) {
            if ($file->getExtension() === 'xml') {
                $totalSize += $file->getSize();
                $fileCount++;
            }
        }

        return $totalSize > 100 * 1024 * 1024 || $fileCount > 50; // Example: >100MB or >50 files
    }

    /**
     * Process a batch in parallel using pcntl_fork.
     */
    private function processInParallel(array $batch): void {
        $childPid = pcntl_fork();

        if ($childPid === 0) {
            // Child process: Process the batch
            try {
                $this->processBatch($batch);
            } catch (\Exception $e) {
                echo "Error in child process: " . $e->getMessage() . "\n";
            }
            exit(0); // Ensure child process exits
        } elseif ($childPid > 0) {
            // Parent process: Wait for child to complete
            pcntl_wait($status);
        } else {
            throw new \Exception("Failed to fork process.");
        }
    }

    /**
     * Process a batch of books.
     */
    protected function processBatch(array $batch): void {
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
