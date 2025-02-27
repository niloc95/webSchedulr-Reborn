<?php
require_once __DIR__ . '/tools/PathManager.php';

use Migration\Tools\PathManager;

$timestamp = '2025-02-26 10:46:39';
$user = 'niloc95';

echo "Testing PathManager\n";
echo "Timestamp: $timestamp\n";
echo "User: $user\n\n";

try {
    $pathManager = new PathManager($timestamp, $user);
    
    echo "Base Directory: " . $pathManager->getBaseDir() . "\n\n";
    
    // Test directory creation
    echo "Verifying directory structure...\n";
    $directories = [
        '/migration/logs',
        '/migration/reports',
        '/migration/tools',
        '/migration/temp',
        '/application/config',
        '/application/controllers',
        '/application/models',
        '/application/views',
        '/application/logs',
        '/application/uploads',
        '/assets/css',
        '/assets/js',
        '/assets/images',
        '/assets/cache'
    ];

    foreach ($directories as $dir) {
        $path = $pathManager->getBaseDir() . $dir;
        if (file_exists($path)) {
            $perms = substr(sprintf('%o', fileperms($path)), -4);
            echo "✓ Directory exists: $dir (permissions: $perms)\n";
        } else {
            echo "✗ Directory missing: $dir\n";
        }
    }

    // Test path resolution
    echo "\nTesting path resolution...\n";
    $types = ['logs', 'reports', 'config', 'tools', 'temp'];
    foreach ($types as $type) {
        echo "Path for '$type': " . $pathManager->getPath($type) . "\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}