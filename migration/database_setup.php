<?php
// Generated at: 2025-02-27 06:28:17
// Generated by: niloc95

class DatabaseSetup
{
    private $host = '127.0.0.1';
    private $database = 'webschedulr';
    private $username;
    private $password;

    public function run()
    {
        $this->output("Starting Database Setup");
        
        // Get database credentials
        $this->getDatabaseCredentials();
        
        // Test connection
        if ($this->testConnection()) {
            $this->output("Database connection successful!");
            
            // Create database if it doesn't exist
            $this->createDatabase();
            
            // Update .env file
            $this->updateEnvFile();
            
            // Run migrations
            $this->runMigrations();
        }
    }

    private function getDatabaseCredentials()
    {
        echo "Enter MySQL username (default: root): ";
        $this->username = trim(fgets(STDIN)) ?: 'root';
        
        echo "Enter MySQL password: ";
        system('stty -echo');
        $this->password = trim(fgets(STDIN));
        system('stty echo');
        echo "\n";
    }

    private function testConnection()
    {
        try {
            $pdo = new PDO(
                "mysql:host={$this->host}",
                $this->username,
                $this->password
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return true;
        } catch (PDOException $e) {
            $this->output("Connection failed: " . $e->getMessage());
            return false;
        }
    }

    private function createDatabase()
    {
        try {
            $pdo = new PDO(
                "mysql:host={$this->host}",
                $this->username,
                $this->password
            );
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$this->database}`");
            $this->output("Database '{$this->database}' created or already exists");
        } catch (PDOException $e) {
            $this->output("Error creating database: " . $e->getMessage());
            exit(1);
        }
    }

    private function updateEnvFile()
    {
        $envFile = dirname(__DIR__) . '/.env';
        $env = file_get_contents($envFile);
        
        $env = preg_replace('/DB_USERNAME=.*/', "DB_USERNAME={$this->username}", $env);
        $env = preg_replace('/DB_PASSWORD=.*/', "DB_PASSWORD={$this->password}", $env);
        
        file_put_contents($envFile, $env);
        $this->output(".env file updated with database credentials");
    }

    private function runMigrations()
    {
        $this->output("Running migrations...");
        $output = shell_exec('php ../artisan migrate --force 2>&1');
        $this->output($output);
    }

    private function output($message)
    {
        $timestamp = date('Y-m-d H:i:s');
        echo "[$timestamp] $message\n";
    }
}

// Run the setup
$setup = new DatabaseSetup();
$setup->run();