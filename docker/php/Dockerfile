FROM php:8.2-fpm-alpine
# Set working directory

# Install dependencies
RUN apk update && apk add --no-cache \
    libzip-dev \
    zip \
    unzip \
    git \
    zlib-dev \
    libpng-dev \
    oniguruma-dev \
    npm

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring zip

# Clear cache
RUN rm -rf /var/cache/apk/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy application files
# COPY ../../ /var/www

# List copied files
# RUN ls -al && echo "File listing complete."

# Set file permissions
# RUN chown -R www-data:www-data /var/www \
#     && chmod -R 755 /var/www/storage
WORKDIR /var/www

# USER www-data

# Install application dependencies
# RUN composer install

# Generate Laravel application key
# RUN php artisan key:generate

# Expose port 9000 for PHP-FPM
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]