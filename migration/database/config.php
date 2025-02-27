<?php
// Database configuration
define('DB_HOST', 'localhost');     // Change this to your database host
define('DB_NAME', 'ws');   // Change this to your database name
define('DB_USER', 'za_admin');          // Change this to your database user
define('DB_PASS', '!Shinesun12');              // Change this to your database password

// Migration configuration
define('MIGRATION_LOG_DIR', __DIR__ . '/logs');
define('MIGRATION_REPORT_DIR', __DIR__ . '/reports');

// Create directories if they don't exist
if (!file_exists(MIGRATION_LOG_DIR)) {
    mkdir(MIGRATION_LOG_DIR, 0755, true);
}

if (!file_exists(MIGRATION_REPORT_DIR)) {
    mkdir(MIGRATION_REPORT_DIR, 0755, true);
}