FROM php:8.2-apache

# Paquetes del sistema
RUN apt-get update && apt-get install -y \
    git curl unzip zip \
    libpq-dev \
    libzip-dev \
    libicu-dev \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libcurl4-openssl-dev \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j$(nproc) \
    pdo pdo_pgsql bcmath intl zip gd mbstring exif curl

# Habilitar mod_rewrite
RUN a2enmod rewrite

# DocumentRoot -> public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf \
 && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# Render escucha en 10000
ENV PORT=10000
RUN sed -ri -e 's!Listen 80!Listen ${PORT}!g' /etc/apache2/ports.conf \
 && sed -ri -e 's!<VirtualHost \*:80>!<VirtualHost \*:${PORT}>!g' /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html
COPY . .

# Composer (root permitido y sin límite de memoria)
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_MEMORY_LIMIT=-1
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 👇 Usa -vvv para ver el error exacto si vuelve a fallar
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction -vvv

# Permisos de Laravel
RUN chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

# Cache (no rompas el build si faltan envs)
RUN php artisan config:cache || true \
 && php artisan route:cache || true \
 && php artisan view:cache  || true

EXPOSE 10000
CMD ["apache2-foreground"]
