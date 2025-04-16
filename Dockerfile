# Use official PHP image with Apache
FROM php:8.3-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo pdo_mysql zip intl opcache \
    && a2enmod rewrite headers \
    && echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

# Install PHP dependencies (no-dev for production)
RUN composer install --prefer-dist --no-dev --no-interaction --optimize-autoloader

# Configure Apache document root
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Change Apache port to 8000 (for Railway compatibility)
RUN sed -i "s/80/8000/g" /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

# CORS Configuration (Apache level) - Wildcard Version
RUN echo '<IfModule mod_headers.c>\n\
    Header always set Access-Control-Allow-Origin "*"\n\
    Header always set Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS"\n\
    Header always set Access-Control-Allow-Headers "Origin, Content-Type, Accept, Authorization, X-Requested-With"\n\
    Header always set Vary "Origin"\n\
    RewriteEngine On\n\
    RewriteCond %{REQUEST_METHOD} OPTIONS\n\
    RewriteRule ^(.*)$ $1 [R=204,L]\n\
</IfModule>' > /etc/apache2/conf-available/cors.conf \
&& a2enconf cors

# Set file permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Optimize Laravel
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Copy and enable entrypoint
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Expose port 8000
EXPOSE 8000

# Start Apache with entrypoint
CMD ["/entrypoint.sh"]