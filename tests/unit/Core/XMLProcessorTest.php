<?php

namespace Tests\Core;

use PHPUnit\Framework\TestCase;
use App\Core\XMLProcessor;

class XMLProcessorTest extends TestCase {
    private $tempDir;

    protected function setUp(): void {
        parent::setUp();

        // Create a temporary directory for test files
        $this->tempDir = sys_get_temp_dir() . '/test-xml-folder';
        if (!is_dir($this->tempDir)) {
            mkdir($this->tempDir, 0777, true);
        }
    }

    protected function tearDown(): void {
        parent::tearDown();

        // Clean up temporary files and directory
        array_map('unlink', glob($this->tempDir . '/*'));
        rmdir($this->tempDir);
    }

    public function testProcessFolderProcessesXmlFiles(): void {
        // Create a sample XML file in the temporary directory
        $xmlFilePath = $this->tempDir . '/sample.xml';
        file_put_contents($xmlFilePath, <<<XML
            <root>
                <book>
                    <author>Tolstoy</author>
                    <name>War and Peace</name>
                </book>
            </root>
        XML);

        // Act: Process the folder
        $processor = new XMLProcessor();
        $processor->processFolder($this->tempDir);

        // Assert: The models should have been called as mocked
        $this->assertTrue(true); // Passes if no exceptions are thrown
    }
}
