#!/bin/bash
set -e

# Railway injects PORT only if EXPOSE ≠ 80
PORT=${PORT:-8000}

echo "🚀 Starting Laravel container..."
echo "🌐 Apache listening on PORT=$PORT"

# Ganti Apache agar pakai port yang benar
sed -i "s/80/${PORT}/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/g" /etc/apache2/sites-available/000-default.conf

# Laravel setup commands
php artisan config:clear
php artisan config:cache
php artisan filament:optimize

# Publish Filament and Swagger assets
php artisan vendor:publish --provider="L5Swagger\\L5SwaggerServiceProvider" --force
php artisan vendor:publish --tag=filament-assets --force
php artisan vendor:publish --tag=filament-config --force

# Optional: Storage symlink
php artisan storage:link || echo "Storage link already exists."

# Generate Swagger docs (allow failure)
php artisan l5-swagger:generate || echo "⚠️ Swagger generation failed, continuing anyway..."

# Start Apache
echo "✅ Laravel is ready. Starting Apache..."
exec apache2-foreground