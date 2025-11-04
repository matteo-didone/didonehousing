# ============================================================================
# Laravel 11 Backend - Multi-stage Dockerfile
# ============================================================================

# Base image with PHP 8.3 and essential extensions
FROM php:8.3-fpm-alpine AS base

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    postgresql-dev \
    icu-dev \
    oniguruma-dev \
    libxml2-dev \
    linux-headers \
    $PHPIZE_DEPS

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_pgsql \
        pgsql \
        gd \
        zip \
        intl \
        mbstring \
        xml \
        opcache \
        pcntl \
        bcmath \
        exif

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# ============================================================================
# Development stage
# ============================================================================
FROM base AS development

# Install Xdebug for development
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Copy PHP configuration
COPY infra/docker/php/php-dev.ini /usr/local/etc/php/conf.d/custom.ini

# Install Node.js (for Laravel Mix / Vite if needed)
RUN apk add --no-cache nodejs npm

# Install Puppeteer dependencies for PDF generation
RUN apk add --no-cache \
    chromium \
    nss \
    freetype \
    harfbuzz \
    ca-certificates \
    ttf-freefont

ENV PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=true \
    PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium-browser

# Set permissions
RUN chown -R www-data:www-data /var/www

USER www-data

# Expose port 8000 for Artisan serve
EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]

# ============================================================================
# Production builder stage
# ============================================================================
FROM base AS builder

# Copy application files
COPY --chown=www-data:www-data . /var/www

# Install production dependencies only
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# ============================================================================
# Production stage
# ============================================================================
FROM base AS production

# Copy PHP configuration
COPY infra/docker/php/php-prod.ini /usr/local/etc/php/conf.d/custom.ini

# Install Puppeteer dependencies for PDF generation
RUN apk add --no-cache \
    chromium \
    nss \
    freetype \
    harfbuzz \
    ca-certificates \
    ttf-freefont

ENV PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=true \
    PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium-browser

# Copy application from builder
COPY --from=builder --chown=www-data:www-data /var/www /var/www

# Optimize for production
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Set permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

USER www-data

EXPOSE 9000

CMD ["php-fpm"]
