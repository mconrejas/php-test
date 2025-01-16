<?php

namespace Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use Redis;

class RedisCacheTest extends TestCase {
    private $redisMock;
    private $traitInstance;

    protected function setUp(): void {
        // Mock Redis instance
        $this->redisMock = $this->createMock(Redis::class);

        // Create a class that uses the RedisCache trait
        $this->traitInstance = new class {
            use \App\Core\RedisCache;

            public function setRedisMock($mock) {
                $this->redis = $mock;
            }
        };

        // Inject the mock Redis into the trait
        $this->traitInstance->setRedisMock($this->redisMock);
    }

    public function testConnectToRedis(): void {
        $this->redisMock->expects($this->once())
            ->method('get')
            ->with('test_key')
            ->willReturn(null);

        // Act
        $this->traitInstance->get('test_key');

        // Assert
        $this->assertTrue(true); // No exceptions mean the connection attempt was successful
    }    

    public function testGetReturnsCachedValue(): void {
        // Arrange
        $this->redisMock->expects($this->once())
            ->method('get')
            ->with('test_key')
            ->willReturn(json_encode(['name' => 'John']));

        // Act
        $result = $this->traitInstance->get('test_key');

        // Assert
        $this->assertEquals(['name' => 'John'], $result);
    }

    public function testSetCachesValue(): void {
        // Arrange
        $this->redisMock->expects($this->once())
            ->method('set')
            ->with('test_key', json_encode(['name' => 'John']), 3600);

        // Act
        $this->traitInstance->set('test_key', ['name' => 'John'], 3600);

        // Assert
        $this->assertTrue(true); // No exceptions mean the set operation was successful
    }

    public function testClearRemovesKey(): void {
        // Arrange
        $this->redisMock->expects($this->once())
            ->method('del')
            ->with('test_key');

        // Act
        $this->traitInstance->clear('test_key');

        // Assert
        $this->assertTrue(true); // No exceptions mean the clear operation was successful
    }

    public function testHasChecksKeyExistence(): void {
        // Arrange
        $this->redisMock->expects($this->once())
            ->method('exists')
            ->with('test_key')
            ->willReturn(1);

        // Act
        $result = $this->traitInstance->has('test_key');

        // Assert
        $this->assertTrue($result);
    }

    public function testHasReturnsFalseForNonexistentKey(): void {
        // Arrange
        $this->redisMock->expects($this->once())
            ->method('exists')
            ->with('test_key')
            ->willReturn(0);

        // Act
        $result = $this->traitInstance->has('test_key');

        // Assert
        $this->assertFalse($result);
    }
}
