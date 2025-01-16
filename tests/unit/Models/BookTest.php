<?php

namespace Tests\Models;

use PHPUnit\Framework\TestCase;
use App\Models\Book;
use PDO;

class BookTest extends TestCase {
    private $pdoMock;
    private $book;

    protected function setUp(): void {
		// Mock the PDO instance
		$this->pdoMock = $this->createMock(PDO::class);
	
		// Assign the mocked PDO to the static property
		Book::$pdo = $this->pdoMock;
	
		// Initialize the Author model
		$this->book = new Book();
	}

    public function testUpsertBookInsertsNewBook(): void {
        // Arrange: Simulate a successful insert
        $stmtMock = $this->createMock(\PDOStatement::class);
        $stmtMock->expects($this->once())
            ->method('execute')
            ->with([
                ':author_id' => 1,
                ':book_name' => 'War and Peace'
            ]);

        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->with($this->stringContains('INSERT INTO books'))
            ->willReturn($stmtMock);

        // Act
        $this->book->upsertBook(1, 'War and Peace');

        // Assert
        $this->assertTrue(true); // If no exception is thrown, the test passes
    }

    public function testUpsertBookUpdatesExistingBook(): void {
        // Arrange: Simulate a conflict update
        $stmtMock = $this->createMock(\PDOStatement::class);
        $stmtMock->expects($this->once())
            ->method('execute')
            ->with([
                ':author_id' => 1,
                ':book_name' => 'War and Peace'
            ]);

        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->with($this->stringContains('ON CONFLICT'))
            ->willReturn($stmtMock);

        // Act
        $this->book->upsertBook(1, 'War and Peace');

        // Assert
        $this->assertTrue(true); // If no exception is thrown, the test passes
    }

    public function testUpsertBookThrowsExceptionForMissingAuthorId(): void {
        // Assert: Expect an exception
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid input: authorId and bookName must be provided.');

        // Act
        $this->book->upsertBook(null, 'War and Peace');
    }
}
