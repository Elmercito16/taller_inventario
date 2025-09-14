FROM php:8.2-apache

# ── Paquetes del sistema y toolchain para compilar extensiones ────────────────
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

# ── Extensiones PHP (no instales 'pdo' a mano; ya viene con PHP) ──────────────
# GD necesita los flags de freetype/jpeg; intl usa ICU; zip usa libzip
RUN set -eux; \
  docker-php-ext-configure gd --with-freetype --with-jpeg; \
  docker-php-ext-install -j"$(nproc)" \
    pdo_pgsql \
    bcmath \
    intl \
    zip \
    gd \
    mbstring \
    exif \
    curl

# ── Apache y DocumentRoot a /public ───────────────────────────────────────────
RUN a2enmod rewrite
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN set -eux; \
  sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf; \
  sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# Render expone 10000: cambia Apache de 80 a 10000
ENV PORT=10000
RUN set -eux; \
  sed -ri -e 's!Listen 80!Listen ${PORT}!g' /etc/apache2/ports.conf; \
  sed -ri -e 's!<VirtualHost \*:80>!<VirtualHost \*:${PORT}>!g' /etc/apache2/sites-available/000-default.conf

# ── Código y Composer ─────────────────────────────────────────────────────────
WORKDIR /var/www/html
COPY . .

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_MEMORY_LIMIT=-1
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Instalar dependencias PHP del proyecto (usa -vvv para ver cualquier fallo)
RUN set -eux; \
  composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction -vvv

# Permisos Laravel
RUN set -eux; \
  chown -R www-data:www-data storage bootstrap/cache; \
  chmod -R 775 storage bootstrap/cache

# Cachear (si faltan envs no rompas el build)
RUN set -eux; \
  php artisan config:cache || true; \
  php artisan route:cache  || true; \
  php artisan view:cache   || true

EXPOSE 10000
CMD ["apache2-foreground"]
