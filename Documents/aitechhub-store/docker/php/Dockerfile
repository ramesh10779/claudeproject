FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libgd-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libicu-dev \
    libpq-dev \
    supervisor \
    nginx \
    unzip \
    libssl-dev \
    pkg-config

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl opcache

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Create user
RUN groupadd -g 1000 www && useradd -u 1000 -ms /bin/bash -g www www

# Set permissions for storage and bootstrap cache
RUN mkdir -p /var/www/customer/storage /var/www/customer/bootstrap/cache
RUN mkdir -p /var/www/admin/storage /var/www/admin/bootstrap/cache
RUN chown -R www:www /var/www/customer/storage /var/www/customer/bootstrap/cache
RUN chown -R www:www /var/www/admin/storage /var/www/admin/bootstrap/cache

USER www

EXPOSE 9000

CMD ["php-fpm"]