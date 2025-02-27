<?php
namespace Migration\Tools;

class PathManager {
    private $timestamp;
    private $user;
    private $baseDir;
    
    public function __construct($timestamp = null, $user = null) {
        $this->timestamp = $timestamp ?: '2025-02-26 10:46:39';
        $this->user = $user ?: 'niloc95';
        $this->baseDir = dirname(dirname(__DIR__));
        $this->ensureBasicStructure();
    }

    public function getBaseDir() {
        return $this->baseDir;
    }

    public function getTimestamp() {
        return $this->timestamp;
    }

    public function getUser() {
        return $this->user;
    }

    public function getPath($type, $file = '') {
        $paths = [
            'logs' => '/migration/logs',
            'reports' => '/migration/reports',
            'config' => '/application/config',
            'tools' => '/migration/tools',
            'temp' => '/migration/temp'
        ];

        if (!isset($paths[$type])) {
            throw new \RuntimeException("Invalid path type: $type");
        }

        $path = $this->baseDir . $paths[$type];
        return $file ? $path . '/' . $file : $path;
    }

    public function ensureBasicStructure() {
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
            $fullPath = $this->baseDir . $dir;
            if (!file_exists($fullPath)) {
                if (!mkdir($fullPath, 0755, true)) {
                    throw new \RuntimeException("Failed to create directory: $fullPath");
                }
                $this->logCreation($dir);
            }
        }
    }

    private function logCreation($path) {
        try {
            // Ensure logs directory exists before writing
            $logDir = $this->getPath('logs');
            if (!file_exists($logDir)) {
                mkdir($logDir, 0755, true);
            }

            $logFile = $this->getPath('logs', 'directory_creation.log');
            $message = "[{$this->timestamp}] {$this->user}: Created directory: $path\n";
            file_put_contents($logFile, $message, FILE_APPEND);
        } catch (\Exception $e) {
            // If logging fails, we'll continue without logging
            error_log("Failed to log directory creation: " . $e->getMessage());
        }
    }

    public function verifyPermissions($directory) {
        $fullPath = $this->baseDir . $directory;
        if (!file_exists($fullPath)) {
            return false;
        }

        $perms = fileperms($fullPath);
        // Convert to octal string
        $octalPerms = substr(sprintf('%o', $perms), -4);
        
        // Check if directory is writable
        return is_writable($fullPath);
    }

    public function ensurePermissions($directory, $permissions = 0755) {
        $fullPath = $this->baseDir . $directory;
        if (file_exists($fullPath)) {
            return chmod($fullPath, $permissions);
        }
        return false;
    }
}