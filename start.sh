#!/bin/sh

# Buat symbolic link storage
php artisan storage:link

# Bersihkan cache
php artisan config:clear
php artisan cache:clear

# Jalankan migrasi database
php artisan migrate --force

# Jalankan seeder database (Jika belum pernah, Render akan me-run ini saat build)
# php artisan db:seed --force

# Mulai Nginx di background
nginx

# Mulai PHP-FPM di foreground
php-fpm
