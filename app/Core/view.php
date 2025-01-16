<?php

namespace App\Core;

trait View {
    /**
     * Render a view file with optional data
     * 
     * @param string $viewPath The path to the view file
     * @param array $data Array of data to extract for the view
     */
    public function view(string $viewPath, array $data = []): void {
        // Convert dot notation (e.g., "folder.view") to directory separators
        $viewFile = __DIR__ . '/../Views/' . str_replace('.', '/', $viewPath) . '.php';

        // Check if the view file exists
        if (!file_exists($viewFile)) {
            throw new \Exception("View file not found: $viewFile");
        }

        // Extract data variables for use in the view
        extract($data);

        // Include the view file
        require $viewFile;
    }
}
