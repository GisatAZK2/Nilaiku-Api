#!/bin/bash

set -e

php artisan config:clear
php artisan config:cache

# Jalankan migrate hanya jika belum ada tabel migration tertentu
if ! php artisan migrate:status | grep -q '202'; then
    echo "📦 Menjalankan migrasi database..."
    php artisan migrate --force
else
    echo "✅ Migrasi sudah pernah dijalankan, skip..."
fi

php artisan vendor:publish --provider="L5Swagger\\L5SwaggerServiceProvider" --force
php artisan l5-swagger:generate

exec apache2-foreground
