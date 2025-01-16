<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\XMLProcessor;

$xmlProcessor = new XMLProcessor();

try {
    $xmlProcessor->processFolder(__DIR__ . '/../assets/xml');
    echo "Cron job completed successfully.\n";
} catch (Exception $e) {
    echo "Cron job failed: " . $e->getMessage() . "\n";
}
