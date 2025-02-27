<?php
namespace Migration\Tools;

class InstallationSimulator {
    private $timestamp = '2025-02-26 09:47:27';
    private $user = 'niloc95';
    private $baseDir;
    private $logsDir;
    private $logFile;

    public function __construct($baseDir = null) {
        $this->baseDir = $baseDir ?: dirname(dirname(__DIR__));
        $this->logsDir = $this->baseDir . '/migration/logs';
        $this->logFile = $this->logsDir . '/installation_simulation.log';

        // Ensure logs directory exists
        if (!$this->ensureDirectoryExists($this->logsDir)) {
            throw new \RuntimeException("Failed to create logs directory: " . $this->logsDir);
        }
    }

    public function simulate() {
        $this->log("Starting installation simulation");
        
        $steps = [
            'verify_structure' => $this->verifyFileStructure(),
            'check_permissions' => $this->checkPermissions(),
            'create_directories' => $this->createRequiredDirectories(),
            'generate_config' => $this->generateConfiguration(),
            'verify_requirements' => $this->verifyServerRequirements()
        ];

        $this->generateSimulationReport($steps);
        return $steps;
    }

    private function ensureDirectoryExists($dir) {
        if (!file_exists($dir)) {
            if (!mkdir($dir, 0755, true)) {
                return false;
            }
        }
        return true;
    }

    private function verifyFileStructure() {
        $requiredDirs = [
            'application' => [
                'config',
                'controllers',
                'models',
                'views',
                'uploads',
                'logs'
            ],
            'assets' => [
                'css',
                'js',
                'images',
                'cache'
            ],
            'migration' => [
                'logs',
                'tools',
                'reports'
            ]
        ];

        $status = ['status' => 'success', 'missing' => []];

        foreach ($requiredDirs as $parent => $children) {
            $parentPath = $this->baseDir . '/' . $parent;
            if (!file_exists($parentPath)) {
                $status['missing'][] = $parent;
                $status['status'] = 'failed';
            } else {
                foreach ($children as $child) {
                    $childPath = $parentPath . '/' . $child;
                    if (!file_exists($childPath)) {
                        $status['missing'][] = "$parent/$child";
                        $status['status'] = 'failed';
                    }
                }
            }
        }

        return $status;
    }

    private function checkPermissions() {
        $writableDirs = [
            'application/config',
            'application/uploads',
            'application/logs',
            'assets/cache',
            'migration/logs'
        ];

        $status = ['status' => 'success', 'issues' => []];

        foreach ($writableDirs as $dir) {
            $path = $this->baseDir . '/' . $dir;
            
            // Create directory if it doesn't exist
            if (!file_exists($path)) {
                if (!$this->ensureDirectoryExists($path)) {
                    $status['issues'][] = [
                        'path' => $dir,
                        'error' => 'Failed to create directory'
                    ];
                    $status['status'] = 'failed';
                    continue;
                }
            }

            if (!is_writable($path)) {
                $status['issues'][] = [
                    'path' => $dir,
                    'error' => 'Directory not writable',
                    'current_perms' => substr(sprintf('%o', fileperms($path)), -4)
                ];
                $status['status'] = 'failed';
            }
        }

        return $status;
    }

    private function createRequiredDirectories() {
        $directories = [
            'application/logs',
            'application/cache',
            'application/uploads',
            'assets/cache',
            'migration/logs',
            'migration/reports'
        ];

        $status = ['status' => 'success', 'created' => []];

        foreach ($directories as $dir) {
            $path = $this->baseDir . '/' . $dir;
            if (!file_exists($path)) {
                if ($this->ensureDirectoryExists($path)) {
                    $status['created'][] = $dir;
                } else {
                    $status['status'] = 'failed';
                }
            }
        }

        return $status;
    }

    private function generateConfiguration() {
        $configDir = $this->baseDir . '/application/config';
        if (!$this->ensureDirectoryExists($configDir)) {
            return ['status' => 'failed', 'error' => 'Could not create config directory'];
        }

        $configGen = new ConfigGenerator();
        $config = $configGen->generateConfig('production');
        
        $configFile = $configDir . '/config.php';
        $result = $configGen->writeConfigFile($config, $configFile);

        return [
            'status' => $result ? 'success' : 'failed',
            'config_file' => $configFile
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
                    'hash' => extension_loaded('hash')
                ]
            ]
        ];
    }

    private function generateSimulationReport($steps) {
        $reportsDir = $this->baseDir . '/migration/reports';
        if (!$this->ensureDirectoryExists($reportsDir)) {
            throw new \RuntimeException("Failed to create reports directory");
        }

        $reportFile = $reportsDir . '/simulation_report_' . 
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

        if (!file_put_contents($reportFile, $markdown)) {
            throw new \RuntimeException("Failed to write simulation report");
        }

        $this->log("Simulation report generated: $reportFile");
    }

    private function log($message) {
        $logMessage = "[{$this->timestamp}] {$this->user}: $message\n";
        if (!file_put_contents($this->logFile, $logMessage, FILE_APPEND)) {
            throw new \RuntimeException("Failed to write to log file: {$this->logFile}");
        }
    }
}