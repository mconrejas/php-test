<?php

namespace Tests\Core;

use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase {
    private string $tempViewsDir;
    private string $viewTesterClass;

    protected function setUp(): void {
        parent::setUp();

        // Create a temporary directory for test views
        $this->tempViewsDir = sys_get_temp_dir() . '/test-views';
        if (!is_dir($this->tempViewsDir)) {
            mkdir($this->tempViewsDir, 0777, true);
        }

        // Generate a unique class name for each test
        $uniqueClassName = 'ViewTester' . uniqid();

        // Dynamically create the class
        eval(<<<PHP
        namespace App\Core;

        class $uniqueClassName {
            use \\App\\Core\\View;

            private string \$viewsDir;

            public function __construct(string \$viewsDir) {
                \$this->viewsDir = \$viewsDir;
            }

            public function view(string \$viewPath, array \$data = []): void {
                \$viewFile = \$this->viewsDir . '/' . str_replace('.', '/', \$viewPath) . '.php';
                if (!file_exists(\$viewFile)) {
                    throw new \Exception("View file not found: \$viewFile");
                }
                extract(\$data);
                require \$viewFile;
            }
        }
        PHP);

        // Save the class name for later use
        $this->viewTesterClass = "App\\Core\\$uniqueClassName";
    }

    protected function tearDown(): void {
        parent::tearDown();

        // Clean up temporary files and directory
        array_map('unlink', glob($this->tempViewsDir . '/*'));
        rmdir($this->tempViewsDir);
    }

    public function testViewRendersSuccessfully(): void {
        // Arrange: Create a test view file
        $viewPath = $this->tempViewsDir . '/sample-view.php';
        file_put_contents($viewPath, "Hello, <?= \$name ?>!");

        // Instantiate the dynamically created class
        $viewTester = new $this->viewTesterClass($this->tempViewsDir);

        // Act: Capture the output of the view rendering
        ob_start();
        $viewTester->view('sample-view', ['name' => 'John']);
        $output = ob_get_clean();

        // Assert: Verify the rendered output
        $this->assertEquals("Hello, John!", $output);
    }

    public function testViewThrowsExceptionForMissingFile(): void {
        // Arrange: Define a nonexistent view file path
        $missingView = 'nonexistent-view';

        // Instantiate the dynamically created class
        $viewTester = new $this->viewTesterClass($this->tempViewsDir);

        // Assert: Expect an exception when the view file is missing
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("View file not found:");

        // Act: Try to render the missing view
        $viewTester->view($missingView);
    }
}
