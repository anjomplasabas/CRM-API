# Use the official PHP image as a parent image
FROM php:8.3-fpm

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libicu-dev \
    libxml2-dev \
    libzip-dev \
    libonig-dev \
    libxslt-dev \
    g++ \
    git \
    unzip \
    nginx \
    supervisor \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install intl \
    && docker-php-ext-install zip \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install mysqli \
    && docker-php-ext-install opcache \
    && docker-php-ext-install exif \
    && docker-php-ext-install pcntl \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install mbstring \
    && docker-php-ext-install xsl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy Symfony application
COPY . .
RUN ls -la

# Set permissions for Symfony directories
RUN chown -R www-data:www-data var/cache /var/log \
    && chmod -R 755 var

# Expose port 9000 and port 80 for PHP-FPM and Nginx respectively
EXPOSE 9000
EXPOSE 80
EXPOSE 443

# Copy custom nginx configuration
COPY ./docker/config/nginx.conf /etc/nginx/nginx.conf
# Configure php-fpm
COPY ./docker/config/fpm-pool.conf /etc/php7/php-fpm.d/custom.conf
COPY ./docker/config/php.ini /etc/php7/conf.d/custom.ini
# Configure supervisord
COPY ./docker/config/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Start PHP-FPM and Nginx
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
