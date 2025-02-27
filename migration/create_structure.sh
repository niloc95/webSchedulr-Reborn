#!/bin/bash

# WebSchedulr Directory Structure Creation Script
# Timestamp: 2025-02-26 09:41:33
# User: niloc95

echo "Creating WebSchedulr directory structure..."

# Create main directories
mkdir -p \
    application/{config,controllers,models,views,logs,uploads} \
    assets/{css,js,images,cache} \
    migration/{logs,tools,reports} \
    backup \
    public

# Create necessary files to track empty directories
touch \
    application/config/.gitkeep \
    application/logs/.gitkeep \
    application/uploads/.gitkeep \
    assets/cache/.gitkeep \
    migration/logs/.gitkeep \
    backup/.gitkeep

# Set permissions
chmod -R 755 \
    application/{config,logs,uploads} \
    assets/cache \
    migration/logs \
    backup

echo "Directory structure created successfully!"