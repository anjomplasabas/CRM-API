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

# Copy custom nginx configuration
COPY ./docker/config/nginx.conf /etc/nginx/nginx.conf
# Configure php-fpm
COPY ./docker/config/fpm-pool.conf /etc/php7/php-fpm.d/custom.conf
COPY ./docker/config/php.ini /etc/php7/conf.d/custom.ini
# Configure supervisord
COPY ./docker/config/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Make sure files/folders needed by the processes are accessable when they run under the nobody user
RUN chown -R www-data:www-data /var/www/html /run /var/lib/nginx /var/log/nginx

# Switch to use a non-root user from here on
USER www-data

# Copy Symfony application
COPY --chown=www-data:www-data . .

# Set working directory
WORKDIR /var/www/html

# Run composer install
RUN composer install

RUN php bin/console lexik:jwt:generate-keypair --overwrite

# Expose port 9000 and port 80 for PHP-FPM and Nginx respectively
EXPOSE 9000
EXPOSE 80
EXPOSE 443

# Start PHP-FPM and Nginx
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
