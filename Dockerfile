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

# 2) Extensiones PHP
RUN set -eux; \
  echo "===> Configurando GD"; \
  docker-php-ext-configure gd --with-freetype --with-jpeg; \
  echo "===> Instalando mbstring"; \
  docker-php-ext-install -j"$(nproc)" mbstring; \
  echo "===> Instalando intl"; \
  docker-php-ext-install -j"$(nproc)" intl; \
  echo "===> Instalando zip"; \
  docker-php-ext-install -j"$(nproc)" zip; \
  echo "===> Instalando exif"; \
  docker-php-ext-install -j"$(nproc)" exif; \
  echo "===> Instalando bcmath"; \
  docker-php-ext-install -j"$(nproc)" bcmath; \
  echo "===> Instalando PostgreSQL (pdo_pgsql y pgsql)"; \
  docker-php-ext-install -j"$(nproc)" pdo_pgsql pgsql; \
  echo "===> Instalando gd"; \
  docker-php-ext-install -j"$(nproc)" gd

# 3) Apache + DocumentRoot
RUN a2enmod rewrite
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN set -eux; \
  sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf; \
  sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# 4) Puerto para Render (usa 10000)
ENV PORT=10000
RUN set -eux; \
  sed -ri -e 's!Listen 80!Listen ${PORT}!g' /etc/apache2/ports.conf; \
  sed -ri -e 's!<VirtualHost \*:80>!<VirtualHost \*:${PORT}>!g' /etc/apache2/sites-available/000-default.conf

# 5) Copiar el código de la app
WORKDIR /var/www/html
COPY . .

# 6) Instalar Composer
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_MEMORY_LIMIT=-1
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 7) Instalar dependencias PHP
RUN set -eux; composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction -vvv

# 8) Permisos y caches de Laravel
RUN set -eux; \
  chown -R www-data:www-data storage bootstrap/cache; \
  chmod -R 775 storage bootstrap/cache; \
  php artisan config:cache || true; \
  php artisan route:cache  || true; \
  php artisan view:cache   || true

EXPOSE 10000
CMD ["apache2-foreground"]
