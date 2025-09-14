FROM php:8.2-apache

# 1) Toolchain y libs del sistema (imprescindibles para compilar)
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
  ; \
  rm -rf /var/lib/apt/lists/*

# 2) Extensiones PHP: compilar UNA POR UNA para saber qué falla
#    (no instales "pdo"; viene con PHP. Sí "pdo_pgsql")
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
  echo "===> Instalando curl"; \
  docker-php-ext-install -j"$(nproc)" curl || true; \
  echo "===> Instalando bcmath"; \
  docker-php-ext-install -j"$(nproc)" bcmath; \
  echo "===> Instalando pdo_pgsql"; \
  docker-php-ext-install -j"$(nproc)" pdo_pgsql; \
  echo "===> Instalando gd"; \
  docker-php-ext-install -j"$(nproc)" gd

# 3) Apache + DocumentRoot a /public
RUN a2enmod rewrite
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN set -eux; \
  sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf; \
  sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# 4) Render usa puerto 10000
ENV PORT=10000
RUN set -eux; \
  sed -ri -e 's!Listen 80!Listen ${PORT}!g' /etc/apache2/ports.conf; \
  sed -ri -e 's!<VirtualHost \*:80>!<VirtualHost \*:${PORT}>!g' /etc/apache2/sites-available/000-default.conf

# 5) Código + Composer
WORKDIR /var/www/html
COPY . .

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_MEMORY_LIMIT=-1
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 6) Dependencias del proyecto (usa -vvv para ver detalle si falla)
RUN set -eux; \
  composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction -vvv

# 7) Permisos Laravel
RUN set -eux; \
  chown -R www-data:www-data storage bootstrap/cache; \
  chmod -R 775 storage bootstrap/cache

# 8) Cacheos (no rompas el build si faltan envs)
RUN set -eux; \
  php artisan config:cache || true; \
  php artisan route:cache  || true; \
  php artisan view:cache   || true

EXPOSE 10000
CMD ["apache2-foreground"]
