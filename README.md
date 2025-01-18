# Author and Book Search Application

## Overview
This test project is a PHP-based web application that allows users to search for authors and their books. It demonstrates object-oriented programming principles, a custom routing system, Redis-based caching.

## Features
- **Object-Oriented Design**: Uses controllers, models, and traits for modular and reusable code.
- **Custom Router**: Handles routes dynamically without relying on external frameworks.
- **Redis Caching**: Optimizes performance by caching search results.
- **Rate Limiting**: Prevents abuse by limiting the number of requests per client.
- **Frontend**: Built with HTML5, CSS3, and native JavaScript for a modern user interface.

## Requirements
- docker desktop

## Setup
1. Clone the repository and navigate to the project directory:
  ```bash
  git clone https://github.com/mconrejas/php-test.git
  cd php-test
  ```

2. Start the Docker containers:
  ```bash
  docker-compose up -d --build
  ```

3. Access the application in your browser at `http://localhost:8080`

4. Manually run cron to populate database tables. (optional)
  ```bash
  docker exec -it apache php scripts/cron.php
  ```

## Testing
Run the test suite using PHPUnit:
  ```bash
  docker exec -it apache vendor/bin/phpunit tests
  ```

## Key Files
- app/Controllers/HomeController.php: Handles search functionality.
- app/Core/Database.php: Manages database connections.
- app/Core/Router.php: Manages application routes.
- app/Models/Author.php: Handles database interactions for authors.
- app/Models/Book.php: Handles database interactions for books.

## Technologies Used
- PHP
- Redis
- HTML5
- CSS3
- JavaScript
