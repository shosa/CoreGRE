#!/bin/bash
# Fix permissions for CoreGRE storage directories

echo "Fixing CoreGRE storage permissions..."

# Directory base
BASE_DIR="/var/www/html"

# Crea le directory se non esistono
mkdir -p "$BASE_DIR/storage/logs"
mkdir -p "$BASE_DIR/storage/cache"
mkdir -p "$BASE_DIR/storage/sessions"
mkdir -p "$BASE_DIR/storage/uploads"

# Imposta i permessi corretti
chmod -R 775 "$BASE_DIR/storage"
chown -R www-data:www-data "$BASE_DIR/storage"

echo "✓ Permissions fixed!"
echo ""
echo "Directory structure:"
ls -la "$BASE_DIR/storage"
