<?php
require_once __DIR__ . '/tools/PathManager.php';
require_once __DIR__ . '/tools/InstallationSimulator.php';

use Migration\Tools\PathManager;
use Migration\Tools\InstallationSimulator;

// Current configuration
$TIMESTAMP = '2025-02-26 10:20:02';
$USER = 'niloc95';

echo "Starting WebSchedulr Setup\n";
echo "Timestamp: $TIMESTAMP\n";
echo "User: $USER\n\n";

try {
    // Initialize path manager first
    $pathManager = new PathManager($TIMESTAMP, $USER);
    echo "Basic directory structure created.\n\n";

    // Run installation simulation
    $simulator = new InstallationSimulator($TIMESTAMP, $USER);
    $results = $simulator->simulate();
    
    echo "Simulation completed successfully!\n";
    echo "Check the reports in: " . $pathManager->getPath('reports') . "\n";
    
    // Print simulation results summary
    echo "\nSimulation Results Summary:\n";
    foreach ($results as $step => $result) {
        $status = isset($result['status']) ? $result['status'] : 'unknown';
        echo sprintf("- %-20s: %s\n", ucfirst(str_replace('_', ' ', $step)), $status);
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}