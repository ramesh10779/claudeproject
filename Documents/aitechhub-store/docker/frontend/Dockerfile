# Multi-stage Dockerfile for Customer Frontend App
# Optimized for production with minimal size and maximum security

# ============================================
# Stage 1: Dependencies Builder
# ============================================
FROM composer:2.7 AS composer-builder

WORKDIR /app

# Copy only dependency files first (for caching)
COPY customer/composer.json customer/composer.lock ./

# Install PHP dependencies (production only)
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --no-interaction \
    --optimize-autoloader

# Copy application and generate optimized autoloader
COPY customer/ .
RUN composer dump-autoload --optimize --classmap-authoritative

# ============================================
# Stage 2: Node Builder (for assets)
# ============================================
FROM node:20-alpine AS node-builder

WORKDIR /app

# Copy package files
COPY customer/package*.json ./

# Install Node dependencies
RUN npm ci --only=production

# Copy source files and build assets
COPY customer/ .
RUN npm run build

# ============================================
# Stage 3: Final Production Image
# ============================================
FROM php:8.2-fpm-alpine

# Metadata
LABEL maintainer="AITechHub <dev@aitechhub.store>"
LABEL description="Customer Frontend - Optimized Production Image"
LABEL version="1.0"

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    zip \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    postgresql-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        pdo_pgsql \
        zip \
        gd \
        opcache \
        intl \
        bcmath

# Install Redis extension
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# Create application user
RUN addgroup -g 1000 appuser && \
    adduser -D -u 1000 -G appuser appuser

# Set working directory
WORKDIR /var/www/html

# Copy application from builder stages
COPY --from=composer-builder --chown=appuser:appuser /app ./
COPY --from=node-builder --chown=appuser:appuser /app/public/build ./public/build

# Copy configuration files
COPY docker/frontend/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/frontend/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/frontend/nginx.conf /etc/nginx/nginx.conf
COPY docker/frontend/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Set proper permissions
RUN chown -R appuser:appuser /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache \
    && mkdir -p /var/log/supervisor \
    && chown -R appuser:appuser /var/log/supervisor \
    && mkdir -p /run/nginx \
    && chown -R appuser:appuser /run/nginx

# Laravel optimization
RUN php artisan config:cache || true \
    && php artisan route:cache || true \
    && php artisan view:cache || true

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=40s --retries=3 \
    CMD curl -f http://localhost:8000/health || exit 1

# Switch to non-root user
USER appuser

# Expose port
EXPOSE 8000

# Start supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
