<?php
require_once __DIR__ . '/InstallationAssessment.php';

try {
    echo "Starting WebSchedulr installation assessment...\n";
    echo "Timestamp: 2025-02-26 09:25:19\n";
    echo "User: niloc95\n\n";

    $assessment = new Migration\InstallationAssessment();
    $results = $assessment->runAssessment();
    
    echo "Installation assessment completed successfully.\n";
    echo "Check the generated report in the migration directory.\n";
} catch (Exception $e) {
    echo "Error during assessment: " . $e->getMessage() . "\n";
    exit(1);
}