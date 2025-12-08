FROM php:8.2-fpm-alpine

ENV COMPOSER_ALLOW_SUPERUSER=1

# Instalar dependencias
RUN apk add --no-cache \
    postgresql-dev \
    postgresql-client \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    zip \
    unzip \
    git \
    nodejs \
    npm \
    bash

# Instalar extensiones PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) pdo pdo_pgsql pgsql zip gd bcmath

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiar archivos
COPY . .

# Instalar dependencias PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Instalar y compilar assets
RUN if [ -f "package.json" ]; then \
        npm install --legacy-peer-deps && \
        npm run build; \
    fi

# Crear directorios y permisos
RUN mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache && \
    chmod -R 777 storage bootstrap/cache

EXPOSE 8000

# Inicio SIN migraciones (ya están ejecutadas)
CMD echo "🚀 Iniciando aplicación..." && \
    sleep 2 && \
    php artisan config:clear && \
    php artisan view:clear && \
    php artisan storage:link 2>/dev/null || true && \
    echo "✨ Servidor listo" && \
    php artisan serve --host=0.0.0.0 --port=8000
