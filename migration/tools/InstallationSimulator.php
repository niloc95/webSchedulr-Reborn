<?php
namespace Migration\Tools;

class InstallationSimulator {
    private $timestamp = '2025-02-26 10:50:29';
    private $user = 'niloc95';
    private $pathManager;
    private $logFile;
    private $baseDir;

    public function __construct() {
        $this->pathManager = new PathManager($this->timestamp, $this->user);
        $this->baseDir = $this->pathManager->getBaseDir();
        $this->logFile = $this->pathManager->getPath('logs', 'installation_simulation.log');
        $this->ensureLogFile();
    }

    private function ensureLogFile() {
        $logDir = dirname($this->logFile);
        if (!file_exists($logDir)) {
            mkdir($logDir, 0755, true);
        }
        if (!file_exists($this->logFile)) {
            touch($this->logFile);
        }
    }

    private function ensureDirectoryExists($path) {
        if (!file_exists($path)) {
            return mkdir($path, 0755, true);
        }
        return true;
    }

    public function simulate() {
        try {
            $this->log("Starting installation simulation");
            
            $steps = [
                'verify_structure' => $this->verifyFileStructure(),
                'check_permissions' => $this->checkPermissions(),
                'create_directories' => $this->createRequiredDirectories(),
                'verify_requirements' => $this->verifyServerRequirements()
            ];

            $this->generateSimulationReport($steps);
            return $steps;
        } catch (\Exception $e) {
            $this->log("Error during simulation: " . $e->getMessage());
            throw $e;
        }
    }

    private function verifyFileStructure() {
        $requiredFiles = [
            '/application/config',
            '/application/controllers',
            '/application/models',
            '/application/views',
            '/application/uploads',
            '/application/logs',
            '/assets/css',
            '/assets/js',
            '/assets/images',
            '/assets/cache',
            '/migration/logs',
            '/migration/reports',
            '/migration/tools'
        ];

        $missing = [];
        foreach ($requiredFiles as $file) {
            $fullPath = $this->baseDir . $file;
            if (!file_exists($fullPath)) {
                $missing[] = $file;
            }
        }

        return [
            'status' => empty($missing) ? 'success' : 'failed',
            'missing' => $missing
        ];
    }

    private function checkPermissions() {
        $writableDirs = [
            '/application/config',
            '/application/uploads',
            '/application/logs',
            '/assets/cache',
            '/migration/logs'
        ];

        $issues = [];
        foreach ($writableDirs as $dir) {
            $fullPath = $this->baseDir . $dir;
            
            // Ensure directory exists
            if (!$this->ensureDirectoryExists($fullPath)) {
                $issues[] = [
                    'path' => $dir,
                    'error' => 'Failed to create directory'
                ];
                continue;
            }
            
            // Check permissions
            if (!is_writable($fullPath)) {
                $issues[] = [
                    'path' => $dir,
                    'error' => 'Directory not writable',
                    'current_perms' => substr(sprintf('%o', fileperms($fullPath)), -4)
                ];
            }
        }

        return [
            'status' => empty($issues) ? 'success' : 'failed',
            'issues' => $issues
        ];
    }

    private function createRequiredDirectories() {
        $directories = [
            '/application/logs',
            '/application/cache',
            '/application/uploads',
            '/assets/cache',
            '/migration/logs',
            '/migration/reports',
            '/migration/temp'
        ];

        $created = [];
        $failed = [];

        foreach ($directories as $dir) {
            $fullPath = $this->baseDir . $dir;
            if (!file_exists($fullPath)) {
                if ($this->ensureDirectoryExists($fullPath)) {
                    $created[] = $dir;
                } else {
                    $failed[] = $dir;
                }
            }
        }

        return [
            'status' => empty($failed) ? 'success' : 'failed',
            'created' => $created,
            'failed' => $failed
        ];
    }

    private function verifyServerRequirements() {
        return [
            'status' => 'success',
            'requirements' => [
                'php_version' => [
                    'required' => '8.1.0',
                    'current' => PHP_VERSION,
                    'status' => version_compare(PHP_VERSION, '8.1.0', '>=') ? 'success' : 'failed'
                ],
                'extensions' => [
                    'json' => extension_loaded('json'),
                    'session' => extension_loaded('session'),
                    'hash' => extension_loaded('hash'),
                    'pdo' => extension_loaded('pdo'),
                    'pdo_mysql' => extension_loaded('pdo_mysql')
                ],
                'writable_dirs' => $this->checkPermissions()
            ]
        ];
    }

    private function generateSimulationReport($steps) {
        $reportDir = $this->pathManager->getPath('reports');
        if (!file_exists($reportDir)) {
            mkdir($reportDir, 0755, true);
        }

        $reportFile = $reportDir . '/simulation_report_' . 
                     date('Y-m-d_H-i-s') . '.md';

        $markdown = "# WebSchedulr Installation Simulation Report\n\n";
        $markdown .= "Generated: {$this->timestamp}\n";
        $markdown .= "Generated by: {$this->user}\n\n";

        foreach ($steps as $step => $results) {
            $markdown .= "## " . ucfirst(str_replace('_', ' ', $step)) . "\n\n";
            $markdown .= "Status: **{$results['status']}**\n\n";
            
            if (isset($results['missing']) && !empty($results['missing'])) {
                $markdown .= "Missing Files:\n";
                foreach ($results['missing'] as $file) {
                    $markdown .= "- $file\n";
                }
            }

            if (isset($results['issues']) && !empty($results['issues'])) {
                $markdown .= "Issues:\n";
                foreach ($results['issues'] as $issue) {
                    $markdown .= "- {$issue['path']}: {$issue['error']}\n";
                }
            }

            $markdown .= "\n";
        }

        file_put_contents($reportFile, $markdown);
        $this->log("Simulation report generated: $reportFile");
    }

    private function log($message) {
        $logMessage = "[{$this->timestamp}] {$this->user}: $message\n";
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
    }
}