COPY composer.json composer.lock ./
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Tambahkan flag --no-scripts
RUN composer install --no-dev --optimize-autoloader --no-scripts

COPY . .

# Opsional: Jalankan ini setelah copy semua file agar package discovery jalan
RUN php artisan package:discover --ansi
