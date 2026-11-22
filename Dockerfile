FROM php:8.2-cli-alpine

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    # Varios procesos para atender peticiones en paralelo con `artisan serve`
    PHP_CLI_SERVER_WORKERS=4

# Dependencias del sistema
RUN apk add --no-cache \
        bash \
        postgresql-dev \
        libzip-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        libwebp-dev \
        libxml2-dev \
        libxslt-dev \
        zip \
        unzip

# Extensiones PHP
# - pdo_pgsql: base de datos PostgreSQL (Supabase)
# - gd: logos e imágenes en PDF
# - soap, xsl: facturación electrónica SUNAT (Greenter)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j"$(nproc)" pdo pdo_pgsql pgsql zip gd bcmath soap xsl

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Dependencias PHP primero (aprovecha la caché de Docker)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --no-interaction --prefer-dist

# Código de la aplicación
COPY . .
RUN composer dump-autoload --optimize --no-dev \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views \
                storage/logs storage/app/public storage/app/sunat/certificados bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && chmod +x docker-entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["./docker-entrypoint.sh"]
