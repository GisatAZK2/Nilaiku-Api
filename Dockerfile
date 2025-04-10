FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libpng-dev libonig-dev libxml2-dev \
    libicu-dev libfreetype6-dev libjpeg62-turbo-dev libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo pdo_mysql zip intl \
    && a2enmod rewrite \
    && echo "ServerName localhost" >> /etc/apache2/apache2.conf

WORKDIR /var/www/html

COPY . .
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN composer install --prefer-dist --no-dev --no-interaction --optimize-autoloader

# Set dokument root ke /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Ganti port apache 80 ke $PORT dari Railway
RUN bash -c 'if [[ -n "$PORT" ]]; then \
    sed -i "s/80/${PORT}/g" /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf; \
    fi'

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage

# Railway akan mengatur $PORT, kita cukup EXPOSE
EXPOSE 8080

CMD php artisan config:clear && \
    php artisan config:cache && \
    php artisan migrate --force && \
    php artisan vendor:publish --provider="L5Swagger\\L5SwaggerServiceProvider" --force && \
    php artisan l5-swagger:generate && \
    apache2-foreground
