#!/bin/bash
set -e

echo "🚀 Starting Laravel container..."

# Laravel setup commands
php artisan config:clear
php artisan config:cache
php artisan migrate:fresh --force
php artisan db:seed --class=DatabaseSeeder --force

# Publish Filament and Swagger assets
php artisan vendor:publish --provider="L5Swagger\\L5SwaggerServiceProvider" --force
php artisan vendor:publish --tag=filament-assets --force

# Optional: Storage symlink
php artisan storage:link || echo "Storage link already exists."

# Generate Swagger docs (allow failure)
php artisan l5-swagger:generate || echo "⚠️ Swagger generation failed, continuing anyway..."

# Start Apache
echo "✅ Laravel is ready. Starting Apache..."
exec apache2-foreground
