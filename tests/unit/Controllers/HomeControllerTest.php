<?php

namespace Tests\Controllers;

use PHPUnit\Framework\TestCase;
use App\Controllers\HomeController;
use App\Models\Author;

class HomeControllerTest extends TestCase {
    private $controllerMock;
    private $authorMock;

    protected function setUp(): void {
        // Mock the Author model
        $this->authorMock = $this->createMock(Author::class);

        // Create a HomeController instance with the mocked Author
        $this->controllerMock = $this->getMockBuilder(HomeController::class)
            ->setConstructorArgs([$this->authorMock]) // Inject the mock
            ->onlyMethods(['get', 'set', 'view'])    // Mock RedisCache and View methods
            ->getMock();
    }

    public function testIndexUsesCachedResults(): void {
        $cachedResults = [
            ['author_name' => 'Tolstoy', 'book_name' => 'War and Peace'],
            ['author_name' => 'Dostoevsky', 'book_name' => 'Crime and Punishment']
        ];

        $this->controllerMock->expects($this->once())
            ->method('get')
            ->with('search:')
            ->willReturn($cachedResults);

        $this->controllerMock->expects($this->never())
            ->method('set'); // No need to set cache when data is already cached

        $this->controllerMock->expects($this->once())
            ->method('view')
            ->with('search', [
                'title' => 'Search Authors and Books',
                'results' => $cachedResults
            ]);

        // Act
        $this->controllerMock->index();
    }

    public function testIndexCachesAndUsesDatabaseResults(): void {
		// Arrange: Simulate cache miss
		$this->controllerMock->expects($this->once())
			->method('get')
			->with('search:')
			->willReturn(null); // Cache miss
	
		// Simulate database results
		$dbResults = [
			['author_name' => 'Shakespeare', 'book_name' => 'Hamlet'],
			['author_name' => 'Austen', 'book_name' => 'Pride and Prejudice']
		];
	
		$this->authorMock->expects($this->once())
			->method('search')
			->with('')
			->willReturn($dbResults);
	
		$this->controllerMock->expects($this->once())
			->method('set')
			->with('search:', $dbResults, 3600); // Cache the results
	
		$this->controllerMock->expects($this->once())
			->method('view')
			->with('search', [
				'title' => 'Search Authors and Books',
				'results' => $dbResults
			]);
	
		// Act
		$this->controllerMock->index();
	}	

    public function testIndexHandlesEmptyDatabaseResults(): void {
        // Arrange: Simulate cache miss
        $this->controllerMock->expects($this->once())
            ->method('get')
            ->with('search:')
            ->willReturn(null); // Cache miss

        // Simulate no results from database
        $this->authorMock->expects($this->once())
            ->method('search')
            ->with('')
            ->willReturn([]);

        $this->controllerMock->expects($this->once())
            ->method('set')
            ->with('search:', [], 3600); // Cache empty results

        $this->controllerMock->expects($this->once())
            ->method('view')
            ->with('search', [
                'title' => 'Search Authors and Books',
                'results' => []
            ]);

        // Act
        $this->controllerMock->index();
    }
}
