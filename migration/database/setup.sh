#!/bin/bash

# Setup script for database assessment
echo "Setting up database assessment environment..."
echo "Timestamp: 2025-02-26 06:50:20"
echo "User: niloc95"

# Create required directories
mkdir -p migration/database/logs
mkdir -p migration/database/reports

# Set proper permissions
chmod 755 migration/database/logs
chmod 755 migration/database/reports

# Create empty log file
touch migration/database/logs/assessment.log

# Check PHP and MySQL requirements
echo "Checking requirements..."

# Check PHP version
PHP_VERSION=$(php -v | grep -oP "PHP \K[0-9]+\.[0-9]+\.[0-9]+")
echo "PHP Version: $PHP_VERSION"

# Check PDO extension
PHP_PDO=$(php -m | grep pdo)
if [ -z "$PHP_PDO" ]; then
    echo "ERROR: PDO extension is not installed!"
    echo "Please install PHP PDO extension:"
    echo "sudo apt-get install php-mysql"
    exit 1
fi

echo "Setup completed successfully!"