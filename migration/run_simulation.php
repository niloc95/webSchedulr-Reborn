<?php
require_once __DIR__ . '/tools/ConfigGenerator.php';
require_once __DIR__ . '/tools/InstallationSimulator.php';

$baseDir = dirname(__DIR__);
$timestamp = '2025-02-26 09:32:04';
$user = 'niloc95';

echo "Starting WebSchedulr Installation Simulation\n";
echo "Timestamp: $timestamp\n";
echo "User: $user\n";
echo "Base Directory: $baseDir\n\n";

try {
    // Run installation simulation
    $simulator = new Migration\Tools\InstallationSimulator($baseDir);
    $results = $simulator->simulate();

    echo "Simulation completed successfully!\n";
    echo "Check the logs directory for detailed reports.\n\n";

    // Generate sample configurations
    $configGen = new Migration\Tools\ConfigGenerator();
    
    // Generate configs for different environments
    $environments = ['production', 'development', 'testing'];
    foreach ($environments as $env) {
        $config = $configGen->generateConfig($env);
        $configGen->writeConfigFile(
            $config, 
            $baseDir . "/config.$env.php"
        );
        echo "Generated $env configuration file\n";
    }

} catch (Exception $e) {
    echo "Error during simulation: " . $e->getMessage() . "\n";
    exit(1);
}