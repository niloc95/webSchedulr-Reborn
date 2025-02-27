<?php
// Setup directories before running simulation
// Timestamp: 2025-02-26 09:47:27
// User: niloc95

$baseDir = dirname(__DIR__);
$directories = [
    'application/config',
    'application/controllers',
    'application/models',
    'application/views',
    'application/uploads',
    'application/logs',
    'assets/css',
    'assets/js',
    'assets/images',
    'assets/cache',
    'migration/logs',
    'migration/tools',
    'migration/reports'
];

echo "Setting up directories...\n";
echo "Timestamp: 2025-02-26 09:47:27\n";
echo "User: niloc95\n\n";

foreach ($directories as $dir) {
    $path = $baseDir . '/' . $dir;
    if (!file_exists($path)) {
        if (mkdir($path, 0755, true)) {
            echo "Created: $dir\n";
        } else {
            echo "Failed to create: $dir\n";
        }
    } else {
        echo "Already exists: $dir\n";
    }

    // Ensure directory is writable
    if (!is_writable($path)) {
        if (chmod($path, 0755)) {
            echo "Set permissions for: $dir\n";
        } else {
            echo "Failed to set permissions for: $dir\n";
        }
    }
}

echo "\nDirectory setup complete!\n";