# Multi-stage Dockerfile for Admin Backend App
# Optimized for production with security hardening

# ============================================
# Stage 1: Dependencies Builder
# ============================================
FROM composer:2.7 AS composer-builder

WORKDIR /app

# Copy dependency files
COPY admin/composer.json admin/composer.lock ./

# Install dependencies
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --no-interaction \
    --optimize-autoloader

# Copy application
COPY admin/ .

# Generate optimized autoloader
RUN composer dump-autoload --optimize --classmap-authoritative

# ============================================
# Stage 2: Final Production Image
# ============================================
FROM php:8.2-fpm-alpine

# Metadata
LABEL maintainer="AITechHub <dev@aitechhub.store>"
LABEL description="Admin Backend - Hardened Production Image"
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
    openssl \
    ca-certificates \
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

# Create application user (non-root)
RUN addgroup -g 1001 admin && \
    adduser -D -u 1001 -G admin admin

# Set working directory
WORKDIR /var/www/html

# Copy application from builder
COPY --from=composer-builder --chown=admin:admin /app ./

# Copy configuration files
COPY docker/backend/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/backend/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/backend/nginx.conf /etc/nginx/nginx.conf
COPY docker/backend/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Set secure permissions
RUN chown -R admin:admin /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache \
    && chmod 644 /var/www/html/.env.example \
    && mkdir -p /var/log/supervisor \
    && chown -R admin:admin /var/log/supervisor \
    && mkdir -p /run/nginx \
    && chown -R admin:admin /run/nginx

# Remove sensitive files
RUN rm -rf /var/www/html/.git \
    && rm -rf /var/www/html/tests \
    && rm -f /var/www/html/.env.example

# Laravel optimization
RUN php artisan config:cache || true \
    && php artisan route:cache || true \
    && php artisan view:cache || true

# Security hardening
RUN echo "expose_php = Off" >> /usr/local/etc/php/conf.d/security.ini \
    && echo "display_errors = Off" >> /usr/local/etc/php/conf.d/security.ini \
    && echo "log_errors = On" >> /usr/local/etc/php/conf.d/security.ini

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=40s --retries=3 \
    CMD curl -f http://localhost:8001/health || exit 1

# Switch to non-root user
USER admin

# Expose port
EXPOSE 8001

# Start supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
