<?php
// Installation configuration checker
// Timestamp: 2025-02-26 09:25:19
// User: niloc95

function checkInstallationRequirements() {
    $requirements = [
        'php_version' => version_compare(PHP_VERSION, '8.1.0', '>='),
        'writeable_dirs' => [
            'application/config',
            'application/uploads',
            'application/logs'
        ],
        'php_extensions' => [
            'pdo',
            'pdo_mysql',
            'json',
            'session',
            'hash'
        ]
    ];

    $results = [];
    
    // Check PHP version
    $results['php_version'] = [
        'required' => '8.1.0',
        'current' => PHP_VERSION,
        'status' => $requirements['php_version'] ? 'OK' : 'Failed'
    ];

    // Check directory permissions
    foreach ($requirements['writeable_dirs'] as $dir) {
        $results['directories'][$dir] = [
            'status' => is_writable($dir) ? 'Writeable' : 'Not Writeable',
            'required_permission' => '755'
        ];
    }

    // Check PHP extensions
    foreach ($requirements['php_extensions'] as $ext) {
        $results['extensions'][$ext] = extension_loaded($ext) ? 'Loaded' : 'Not Loaded';
    }

    return $results;
}

// Run the check
$results = checkInstallationRequirements();
print_r($results);