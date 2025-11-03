#!/bin/bash
# ============================================================================
# COREGRE - Docker Entrypoint Script
# Prepara il container all'avvio
# ============================================================================

set -e

echo "============================================"
echo "COREGRE Container Starting..."
echo "============================================"

# ============================================================================
# 1. Wait for MySQL to be ready
# ============================================================================
echo "[1/7] Waiting for MySQL connection..."

max_retries=30
retry_count=0

# Use PHP mysqli to test connection (compatible with MySQL 8 caching_sha2_password)
until php -r "
    \$conn = @new mysqli('${DB_HOST}', '${DB_USER}', '${DB_PASS}', '', ${DB_PORT});
    if (\$conn->connect_error) {
        exit(1);
    }
    \$conn->close();
    exit(0);
" > /dev/null 2>&1; do
    retry_count=$((retry_count + 1))

    if [ $retry_count -ge $max_retries ]; then
        echo "❌ ERROR: Could not connect to MySQL after $max_retries attempts"
        echo "   Host: ${DB_HOST}:${DB_PORT}"
        echo "   User: ${DB_USER}"
        echo "   Database: ${DB_NAME}"
        # Show actual error
        php -r "
            \$conn = @new mysqli('${DB_HOST}', '${DB_USER}', '${DB_PASS}', '', ${DB_PORT});
            if (\$conn->connect_error) {
                echo '   Error: ' . \$conn->connect_error . PHP_EOL;
            }
        "
        exit 1
    fi

    echo "   MySQL not ready yet... ($retry_count/$max_retries)"
    sleep 2
done

echo "✓ MySQL connection successful!"

# ============================================================================
# 2. Create database if not exists
# ============================================================================
echo "[2/7] Checking database..."

# Use PHP to create database
php -r "
    \$conn = new mysqli('${DB_HOST}', '${DB_USER}', '${DB_PASS}', '', ${DB_PORT});
    if (\$conn->connect_error) {
        echo '⚠ Warning: Could not connect to create database' . PHP_EOL;
        exit(0);
    }
    \$sql = 'CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci';
    if (!\$conn->query(\$sql)) {
        echo '⚠ Warning: Could not create database (might already exist or insufficient permissions)' . PHP_EOL;
    }
    \$conn->close();
" || {
    echo "⚠ Warning: Could not create database"
}

echo "✓ Database ready"

# ============================================================================
# 3. Set correct permissions
# ============================================================================
echo "[3/7] Setting permissions..."

# Create directories if not exist
mkdir -p /var/www/html/storage/{cache,logs,sessions,uploads}
mkdir -p /var/www/html/storage/import/temp
mkdir -p /var/www/html/public/uploads
mkdir -p /var/log/supervisor

# Set ownership
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/public/uploads

# Set permissions (775 = rwxrwxr-x, allows www-data group to write)
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/public/uploads

# Set SGID bit to preserve group ownership on new files
chmod -R g+s /var/www/html/storage
chmod -R g+s /var/www/html/public/uploads

echo "✓ Permissions set (775 with SGID)"

# ============================================================================
# 4. Copy .env if not exists
# ============================================================================
echo "[4/7] Checking .env file..."

if [ ! -f /var/www/html/.env ]; then
    if [ -f /var/www/html/.env.docker ]; then
        echo "   Copying .env.docker to .env..."
        cp /var/www/html/.env.docker /var/www/html/.env
    else
        echo "⚠ Warning: No .env file found!"
    fi
else
    echo "✓ .env file exists"
fi

# ============================================================================
# 5. Run migrations (opzionale, decommentare se necessario)
# ============================================================================
echo "[5/7] Database migrations..."

# Decommenta se vuoi eseguire migrations automaticamente all'avvio
# php /var/www/html/artisan migrate --force || {
#     echo "⚠ Warning: Migrations failed or not configured"
# }

echo "⊘ Migrations skipped (enable in entrypoint.sh if needed)"

# ============================================================================
# 6. Clear and cache optimization
# ============================================================================
echo "[6/7] Optimizing cache..."

# Clear cache se esiste il comando artisan
if [ -f /var/www/html/artisan ]; then
    # php /var/www/html/artisan cache:clear 2>/dev/null || true
    # php /var/www/html/artisan config:cache 2>/dev/null || true
    # php /var/www/html/artisan route:cache 2>/dev/null || true
    echo "⊘ Laravel cache optimization skipped (not a Laravel app)"
fi

# Clear opcache se necessario (reset automatico al restart)
echo "✓ Cache ready"

# ============================================================================
# 7. Display startup info
# ============================================================================
echo "[7/7] Startup information..."

echo ""
echo "============================================"
echo "✓ COREGRE Container Ready!"
echo "============================================"
echo "Application:  ${APP_NAME} v${APP_VERSION}"
echo "Environment:  ${APP_ENV}"
echo "Debug:        ${APP_DEBUG}"
echo "Database:     ${DB_HOST}:${DB_PORT}/${DB_NAME}"
echo "PHP Version:  $(php -v | head -n 1)"
echo "Opcache:      Enabled (JIT: tracing)"
echo "Timezone:     ${TZ}"
echo "============================================"
echo ""

# ============================================================================
# Execute CMD (start supervisor)
# ============================================================================
exec "$@"
