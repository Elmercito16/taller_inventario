FROM php:8.2-apache

# 1) Toolchain + librerías del sistema
RUN set -eux; \
  apt-get update; \
  apt-get install -y --no-install-recommends \
    git curl unzip zip \
    build-essential pkg-config autoconf \
    libpq-dev \
    libzip-dev \
    libicu-dev \
    libjpeg62-turbo-dev libpng-dev libfreetype6-dev \
    libcurl4-openssl-dev \
    libonig-dev \
  ; \
  rm -rf /var/lib/apt/lists/*

# 2) Extensiones PHP necesarias
RUN set -eux; \
  docker-php-ext-configure gd --with-freetype --with-jpeg; \
  docker-php-ext-install -j"$(nproc)" mbstring intl zip exif bcmath pdo_pgsql pgsql gd

# 3) Apache + DocumentRoot
RUN a2enmod rewrite
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN set -eux; \
  sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf; \
  sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf; \
  echo "ServerName localhost" >> /etc/apache2/apache2.conf

# 4) Copiar el código de la app
WORKDIR /var/www/html
COPY . .

# 5) Instalar Composer
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_MEMORY_LIMIT=-1
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 6) Instalar dependencias PHP
RUN set -eux; composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction

# 7) Permisos y caches de Laravel
RUN set -eux; \
  chown -R www-data:www-data storage bootstrap/cache; \
  chmod -R 775 storage bootstrap/cache; \
  php artisan config:clear || true; \
  php artisan cache:clear || true; \
  php artisan route:clear || true; \
  php artisan view:clear  || true; \
  php artisan config:cache || true; \
  php artisan route:cache  || true; \
  php artisan view:cache   || true

# 8) Render inyecta el puerto en $PORT
EXPOSE 10000
CMD sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf && apache2-foreground

# Evitar warning de ServerName
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
