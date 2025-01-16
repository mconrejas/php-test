<?php

namespace Tests\Models;

use PHPUnit\Framework\TestCase;
use App\Models\Author;
use PDO;

class AuthorTest extends TestCase {
    private $pdoMock;
    private $author;

    protected function setUp(): void {
		// Mock the PDO instance
		$this->pdoMock = $this->createMock(PDO::class);
	
		// Assign the mocked PDO to the static property
		Author::$pdo = $this->pdoMock;
	
		// Initialize the Author model
		$this->author = new Author();
	}	

    public function testSearchReturnsResults(): void {
        // Simulate query results
        $stmtMock = $this->createMock(\PDOStatement::class);
        $stmtMock->expects($this->once())
            ->method('execute')
            ->with([':search_term' => '%tolstoy%']);

        $stmtMock->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn([
                ['author_name' => 'Tolstoy', 'book_name' => 'War and Peace'],
                ['author_name' => 'Tolstoy', 'book_name' => 'Anna Karenina']
            ]);

        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->with($this->stringContains('ILIKE :search_term'))
            ->willReturn($stmtMock);

        // Act
        $results = $this->author->search('tolstoy');

        // Assert
        $this->assertCount(2, $results);
        $this->assertEquals('Tolstoy', $results[0]['author_name']);
    }

    public function testUpsertAuthorInsertsNewAuthor(): void {
        // Simulate a successful upsert returning author_id
        $stmtMock = $this->createMock(\PDOStatement::class);
        $stmtMock->expects($this->once())
            ->method('execute')
            ->with([':author_name' => 'Tolstoy']);

        $stmtMock->expects($this->once())
            ->method('fetchColumn')
            ->willReturn(1);

        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->with($this->stringContains('INSERT INTO authors'))
            ->willReturn($stmtMock);

        // Act
        $authorId = $this->author->upsertAuthor('Tolstoy');

        // Assert
        $this->assertEquals(1, $authorId);
    }

    public function testUpsertAuthorThrowsExceptionForEmptyName(): void {
        // Expect an exception
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Author name cannot be empty.');

        // Act
        $this->author->upsertAuthor('');
    }

    public function testUpsertAuthorUpdatesExistingAuthor(): void {
        // Arrange: Simulate updating an existing author
        $stmtMock = $this->createMock(\PDOStatement::class);

        $stmtMock->expects($this->once())
            ->method('execute')
            ->with([':author_name' => 'Tolstoy']);

        $stmtMock->expects($this->once())
            ->method('fetchColumn')
            ->willReturn(1);

        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->with($this->stringContains('ON CONFLICT'))
            ->willReturn($stmtMock);

        // Act
        $authorId = $this->author->upsertAuthor('Tolstoy');

        // Assert
        $this->assertEquals(1, $authorId);
    }
}
