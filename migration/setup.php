<?php
// Setup Script for WebSchedulr Migration Tools
// Timestamp: 2025-02-26 09:41:33
// User: niloc95

class MigrationSetup {
    private $timestamp;
    private $user;
    private $baseDir;
    private $directories = [
        'migration/logs',
        'application/config',
        'application/uploads',
        'application/logs',
        'assets/cache',
        'backup'
    ];

    public function __construct() {
        $this->timestamp = '2025-02-26 09:41:33';
        $this->user = 'niloc95';
        $this->baseDir = dirname(__DIR__); // Get project root directory
    }

    public function run() {
        echo "Starting WebSchedulr Migration Setup\n";
        echo "Timestamp: {$this->timestamp}\n";
        echo "User: {$this->user}\n";
        echo "Base Directory: {$this->baseDir}\n\n";

        $this->createDirectories();
        $this->setPermissions();
        $this->createInitialFiles();
    }

    private function createDirectories() {
        echo "Creating required directories...\n";
        
        foreach ($this->directories as $dir) {
            $path = $this->baseDir . '/' . $dir;
            if (!file_exists($path)) {
                if (mkdir($path, 0755, true)) {
                    echo "Created directory: $dir\n";
                } else {
                    echo "Failed to create directory: $dir\n";
                }
            } else {
                echo "Directory already exists: $dir\n";
            }
        }
    }

    private function setPermissions() {
        echo "\nSetting directory permissions...\n";
        
        foreach ($this->directories as $dir) {
            $path = $this->baseDir . '/' . $dir;
            if (file_exists($path)) {
                if (chmod($path, 0755)) {
                    echo "Set permissions for: $dir\n";
                } else {
                    echo "Failed to set permissions for: $dir\n";
                }
            }
        }
    }

    private function createInitialFiles() {
        echo "\nCreating initial files...\n";

        // Create .gitkeep files to track empty directories
        foreach ($this->directories as $dir) {
            $gitkeep = $this->baseDir . '/' . $dir . '/.gitkeep';
            if (!file_exists($gitkeep)) {
                file_put_contents($gitkeep, '');
                echo "Created .gitkeep in: $dir\n";
            }
        }

        // Create initial log file
        $logFile = $this->baseDir . '/migration/logs/installation_simulation.log';
        $initialLog = "[{$this->timestamp}] {$this->user}: Migration setup initialized\n";
        if (file_put_contents($logFile, $initialLog)) {
            echo "Created initial log file\n";
        } else {
            echo "Failed to create initial log file\n";
        }
    }
}

// Run setup
$setup = new MigrationSetup();
$setup->run();