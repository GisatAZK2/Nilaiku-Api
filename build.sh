#!/bin/bash

composer install --prefer-dist --no-dev --no-interaction --optimize-autoloader
php artisan config:clear
php artisan config:cache
php artisan db:seed --class=DatabaseSeeder --force
php artisan vendor:publish --provider="L5Swagger\\L5SwaggerServiceProvider" --force
php artisan vendor:publish --tag=filament-assets --force
php artisan storage:link
php artisan l5-swagger:generate || true