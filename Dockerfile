FROM php:8.2-apache

# 1) Toolchain + libs del sistema (incluye libonig-dev para mbstring)
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
    libonig-dev \    # <-- NECESARIO para mbstring (oniguruma)
  ; \
  rm -rf /var/lib/apt/lists/*

# 2) Extensiones PHP (compila paso a paso para ver si algo falla)
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
  echo "===> Instalando pdo_pgsql"; \
  docker-php-ext-install -j"$(nproc)" pdo_pgsql; \
  echo "===> Instalando gd"; \
  docker-php-ext-install -j"$(nproc)" gd
# Nota: quitamos 'docker-php-ext-install curl' (no hace falta en 8.2; ya viene)

# 3) Apache + DocumentRoot, puerto 10000 para Render (igual que antes)...
RUN a2enmod rewrite
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN set -eux; \
  sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf; \
  sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

ENV PORT=10000
RUN set -eux; \
  sed -ri -e 's!Listen 80!Listen ${PORT}!g' /etc/apache2/ports.conf; \
  sed -ri -e 's!<VirtualHost \*:80>!<VirtualHost \*:${PORT}>!g' /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html
COPY . .

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_MEMORY_LIMIT=-1
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN set -eux; composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction -vvv

RUN set -eux; \
  chown -R www-data:www-data storage bootstrap/cache; \
  chmod -R 775 storage bootstrap/cache; \
  php artisan config:cache || true; \
  php artisan route:cache  || true; \
  php artisan view:cache   || true

EXPOSE 10000
CMD ["apache2-foreground"]
