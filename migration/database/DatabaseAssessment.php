<?php
namespace Migration;

use PDO;
use Exception;

class DatabaseAssessment {
    private $pdo;
    private $timestamp;
    private $user;
    private $logFile;

    public function __construct() {
        $this->timestamp = '2025-02-26 06:50:20';
        $this->user = 'niloc95';
        $this->logFile = __DIR__ . '/assessment_log.txt';
        
        try {
            $this->pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
                DB_USER,
                DB_PASS,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (Exception $e) {
            $this->log("Database connection failed: " . $e->getMessage());
            throw new Exception("Unable to connect to database: " . $e->getMessage());
        }
    }

    private function assessTables() {
        $this->log("Assessing tables");
        $tables = [];
        
        $query = "SHOW TABLES";
        $result = $this->pdo->query($query);
        
        while ($row = $result->fetch(PDO::FETCH_NUM)) {
            $tableName = $row[0];
            $tables[$tableName] = [
                'columns' => $this->getTableColumns($tableName),
                'rowCount' => $this->getTableRowCount($tableName),
                'size' => $this->getTableSize($tableName)
            ];
        }
        
        return $tables;
    }

    private function assessRelationships() {
        $this->log("Assessing relationships");
        $relationships = [];
        
        $query = "
            SELECT 
                TABLE_NAME,
                COLUMN_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE REFERENCED_TABLE_NAME IS NOT NULL
                AND TABLE_SCHEMA = ?
        ";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([DB_NAME]);
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $relationships[] = $row;
        }
        
        return $relationships;
    }

    private function assessDataTypes() {
        $this->log("Assessing data types");
        $dataTypes = [];
        
        $query = "
            SELECT 
                TABLE_NAME,
                COLUMN_NAME,
                DATA_TYPE,
                CHARACTER_MAXIMUM_LENGTH,
                IS_NULLABLE
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = ?
            ORDER BY TABLE_NAME, ORDINAL_POSITION
        ";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([DB_NAME]);
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dataTypes[] = $row;
        }
        
        return $dataTypes;
    }

    private function assessIndexes() {
        $this->log("Assessing indexes");
        $indexes = [];
        
        foreach ($this->assessTables()['tables'] as $tableName => $tableInfo) {
            $query = "SHOW INDEX FROM " . $tableName;
            $result = $this->pdo->query($query);
            $indexes[$tableName] = $result->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return $indexes;
    }

    private function assessConstraints() {
        $this->log("Assessing constraints");
        $constraints = [];
        
        $query = "
            SELECT 
                TABLE_NAME,
                CONSTRAINT_NAME,
                CONSTRAINT_TYPE
            FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = ?
        ";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([DB_NAME]);
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $constraints[] = $row;
        }
        
        return $constraints;
    }

    private function generateReport($assessment) {
        $reportFile = __DIR__ . '/database_assessment_' . date('Y-m-d_H-i-s') . '.md';
        
        $markdown = "# Database Assessment Report\n\n";
        $markdown .= "Generated: {$this->timestamp}\n";
        $markdown .= "Generated by: {$this->user}\n\n";
        
        // Tables Summary
        $markdown .= "## Tables Summary\n\n";
        foreach ($assessment['tables'] as $tableName => $tableInfo) {
            $markdown .= "### $tableName\n";
            $markdown .= "- Row Count: {$tableInfo['rowCount']}\n";
            $markdown .= "- Size: {$tableInfo['size']} MB\n";
            $markdown .= "- Columns:\n";
            foreach ($tableInfo['columns'] as $column) {
                $markdown .= "  - {$column['COLUMN_NAME']} ({$column['DATA_TYPE']})\n";
            }
            $markdown .= "\n";
        }
        
        // Relationships
        $markdown .= "## Relationships\n\n";
        foreach ($assessment['relationships'] as $rel) {
            $markdown .= "- {$rel['TABLE_NAME']}.{$rel['COLUMN_NAME']} → ";
            $markdown .= "{$rel['REFERENCED_TABLE_NAME']}.{$rel['REFERENCED_COLUMN_NAME']}\n";
        }
        
        file_put_contents($reportFile, $markdown);
        $this->log("Assessment report generated: $reportFile");
    }

    private function log($message) {
        $logMessage = "[{$this->timestamp}] {$this->user}: $message\n";
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
    }

    private function getTableColumns($tableName) {
        $query = "SHOW COLUMNS FROM " . $tableName;
        return $this->pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getTableRowCount($tableName) {
        $query = "SELECT COUNT(*) FROM " . $tableName;
        return $this->pdo->query($query)->fetchColumn();
    }

    private function getTableSize($tableName) {
        $query = "
            SELECT 
                ROUND(((data_length + index_length) / 1024 / 1024), 2) 
            FROM information_schema.tables 
            WHERE table_schema = ? AND table_name = ?
        ";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([DB_NAME, $tableName]);
        return $stmt->fetchColumn();
    }
}