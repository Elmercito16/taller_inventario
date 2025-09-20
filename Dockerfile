FROM php:8.2-apache

# ==============================
# 1) Dependencias del sistema
# ==============================
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

# ==============================
# 2) Extensiones PHP
# ==============================
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j"$(nproc)" \
    mbstring intl zip exif bcmath \
    pdo_pgsql pgsql gd \
    opcache

# ==============================
# 3) Configuración de Apache
# ==============================
RUN a2enmod rewrite
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# VirtualHost + permisos para Laravel
RUN set -eux; \
  sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf; \
  sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf; \
  echo '<Directory "/var/www/html/public">\n\
        AllowOverride All\n\
        Require all granted\n\
  </Directory>' > /etc/apache2/conf-available/laravel.conf; \
  a2enconf laravel

# ==============================
# 4) Puerto dinámico (Render usa $PORT)
# ==============================
RUN sed -ri -e 's!Listen 80!Listen ${PORT}!g' /etc/apache2/ports.conf \
 && sed -ri -e 's!<VirtualHost \*:80>!<VirtualHost \*:${PORT}>!g' /etc/apache2/sites-available/000-default.conf

# ==============================
# 5) Copiar aplicación
# ==============================
WORKDIR /var/www/html
COPY . .

# ==============================
# 6) Instalar Composer
# ==============================
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_MEMORY_LIMIT=-1
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ==============================
# 7) Dependencias de Laravel
# ==============================
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction

# ==============================
# 8) Configuración PHP
# ==============================
RUN echo "memory_limit = 256M" > /usr/local/etc/php/conf.d/custom.ini \
 && echo "upload_max_filesize = 20M" >> /usr/local/etc/php/conf.d/custom.ini \
 && echo "post_max_size = 20M" >> /usr/local/etc/php/conf.d/custom.ini \
 && echo "max_execution_time = 120" >> /usr/local/etc/php/conf.d/custom.ini \
 && echo "log_errors = On" >> /usr/local/etc/php/conf.d/custom.ini \
 && echo "display_errors = On" >> /usr/local/etc/php/conf.d/custom.ini \
 && echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/custom.ini

# ==============================
# 9) Permisos y configuración Laravel
# ==============================
RUN chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

# Crear directorios necesarios
RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views \
 && mkdir -p bootstrap/cache

# IMPORTANTE: NO ejecutar comandos artisan que requieren BD durante build
# Solo limpiar archivos de cache, NO acceder a base de datos
RUN rm -rf bootstrap/cache/*.php \
 && rm -rf storage/framework/cache/* \
 && rm -rf storage/framework/sessions/* \
 && rm -rf storage/framework/views/*

# ==============================
# 10) Evitar warning de ServerName
# ==============================
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# ==============================
# 11) Script de inicio
# ==============================
COPY start.sh /start.sh
RUN chmod +x /start.sh

CMD ["/start.sh"]