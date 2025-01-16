<?php

namespace Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use App\Core\Router;

class RouterTest extends TestCase {
    private Router $router;

    protected function setUp(): void {
        parent::setUp();
        $this->router = new Router();
    }

    public function testGetRouteDispatchesCallback(): void {
        // Arrange: Register a GET route with a callback
        $this->router->get('/test', function () {
            echo "GET /test route hit";
        });

        // Simulate a GET request to "/test"
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/test';

        // Act: Capture output
        ob_start();
        $this->router->dispatch();
        $output = ob_get_clean();

        // Assert: Check that the callback output was executed
        $this->assertEquals("GET /test route hit", $output);
    }

    public function testPostRouteDispatchesCallback(): void {
        // Arrange: Register a POST route with a callback
        $this->router->post('/submit', function () {
            echo "POST /submit route hit";
        });

        // Simulate a POST request to "/submit"
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/submit';

        // Act: Capture output
        ob_start();
        $this->router->dispatch();
        $output = ob_get_clean();

        // Assert: Check that the callback output was executed
        $this->assertEquals("POST /submit route hit", $output);
    }

    public function testRouteWithParameters(): void {
        // Arrange: Register a route with a parameter
        $this->router->get('/user/{id}', function ($id) {
            echo "User ID: $id";
        });

        // Simulate a GET request to "/user/42"
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/user/42';

        // Act: Capture output
        ob_start();
        $this->router->dispatch();
        $output = ob_get_clean();

        // Assert: Check that the parameter was correctly passed to the callback
        $this->assertEquals("User ID: 42", $output);
    }

    public function testControllerMethodDispatch(): void {
		// Arrange: Define a mock controller class for the test
		eval(<<<PHP
		namespace App\Controllers;
	
		class TestController {
			public function testMethod(string \$id): void {
				echo "Test method called with ID: \$id";
			}
		}
		PHP);
	
		// Register the route in the router
		$this->router->get('/test/{id}', 'TestController@testMethod');
	
		// Simulate a GET request to "/test/123"
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$_SERVER['REQUEST_URI'] = '/test/123';
	
		// Act: Capture output
		ob_start();
		$this->router->dispatch();
		$output = ob_get_clean();
	
		// Assert: Verify that the controller method was executed
		$this->assertEquals("Test method called with ID: 123", $output);
	}	

    public function testNotFound(): void {
        // Arrange: No routes registered
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/nonexistent';

        // Act: Capture output
        ob_start();
        $this->router->dispatch();
        $output = ob_get_clean();

        // Assert: Check that a 404 message was displayed
        $this->assertEquals("404 - Not Found", $output);
    }
}
