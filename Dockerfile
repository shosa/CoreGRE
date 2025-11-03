# ============================================================================
# CoreGre - Production Dockerfile
# Multi-stage build per ottimizzare dimensioni e performance
# Parte della CoreSuite
# ============================================================================

# ============================================================================
# STAGE 1: Composer Dependencies
# ============================================================================
FROM php:8.3-fpm-alpine3.19 AS composer-build

WORKDIR /app

# Install system dependencies and Composer
RUN apk add --no-cache \
    git \
    zip \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    && rm -rf /var/cache/apk/*

# Install PHP extensions needed by composer dependencies
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        mysqli \
        mbstring \
        zip \
        exif \
        gd \
        intl \
        calendar

# Install Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Copy composer files and directory structure
COPY composer.json composer.lock ./

# Create directory structure for classmap (composer needs these to exist)
RUN mkdir -p app/controllers app/utils app/models core

# Install dependencies (production, optimized)
# Note: without --classmap-authoritative initially since directories are empty
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader

# Copy application code
COPY . .

# Generate optimized autoload with classmap-authoritative now that code is present
RUN composer dump-autoload --optimize --classmap-authoritative --no-dev

# ============================================================================
# STAGE 2: Production Image
# ============================================================================
FROM php:8.3-fpm-alpine3.19

LABEL maintainer="CoreSuite Team"
LABEL description="CoreGre - ERP System - Production Container"
LABEL version="1.0.0"

# Install system dependencies
# Note: mysql-client not needed, using PHP mysqli for DB connectivity checks
RUN apk add --no-cache \
    nginx \
    supervisor \
    tzdata \
    bash \
    curl \
    git \
    zip \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    && rm -rf /var/cache/apk/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        mysqli \
        mbstring \
        zip \
        exif \
        pcntl \
        bcmath \
        gd \
        intl \
        opcache \
        calendar

# Install Redis extension (opzionale, per caching futuro)
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install redis-6.0.2 \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# Set timezone
RUN cp /usr/share/zoneinfo/Europe/Rome /etc/localtime \
    && echo "Europe/Rome" > /etc/timezone

# Create application directory
WORKDIR /var/www/html

# Copy application from composer stage
COPY --from=composer-build --chown=www-data:www-data /app /var/www/html

# Create required directories with proper permissions
RUN mkdir -p \
    /var/www/html/storage/cache \
    /var/www/html/storage/logs \
    /var/www/html/storage/sessions \
    /var/www/html/storage/uploads \
    /var/www/html/storage/cache/mpdf \
    && chown -R www-data:www-data /var/www/html/storage \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R g+s /var/www/html/storage

# Copy PHP configuration
COPY --chmod=644 docker/php/php.ini /usr/local/etc/php/conf.d/99-coregre.ini
COPY --chmod=644 docker/php/php-fpm.conf /usr/local/etc/php-fpm.d/zz-coregre.conf

# Copy Nginx configuration
COPY --chmod=644 docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY --chmod=644 docker/nginx/default.conf /etc/nginx/http.d/default.conf

# Copy Supervisor configuration
COPY --chmod=644 docker/supervisor/supervisord.conf /etc/supervisord.conf

# Copy crontab configuration
COPY --chmod=644 docker/crontab /etc/crontabs/root

# Copy entrypoint script with proper line endings and permissions
COPY --chmod=755 docker/entrypoint.sh /usr/local/bin/entrypoint.sh
# Convert line endings to Unix format (in case of Windows CRLF)
RUN sed -i 's/\r$//' /usr/local/bin/entrypoint.sh

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=40s --retries=3 \
    CMD curl -f http://localhost/health || exit 1

# Expose port
EXPOSE 80

# Set entrypoint
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Start supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
