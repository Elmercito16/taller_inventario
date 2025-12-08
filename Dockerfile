FROM php:8.2-fpm-alpine

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_NO_INTERACTION=1

# Instalar dependencias del sistema
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
    curl \
    nodejs \
    npm \
    bash

# Instalar extensiones PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_pgsql \
        pgsql \
        zip \
        gd \
        bcmath \
        exif \
        pcntl

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY . .

# Instalar dependencias de Composer
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader

# Instalar y compilar assets de Node
RUN npm ci --only=production && npm run build

# Crear directorios y permisos
RUN mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Limpiar
RUN npm cache clean --force && rm -rf node_modules

EXPOSE 8000

# Crear script de inicio inline
RUN echo '#!/bin/bash' > /entrypoint.sh && \
    echo 'set -e' >> /entrypoint.sh && \
    echo 'echo "🚀 Iniciando aplicación..."' >> /entrypoint.sh && \
    echo 'echo "⏳ Esperando PostgreSQL..."' >> /entrypoint.sh && \
    echo 'until pg_isready -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USERNAME" 2>/dev/null; do' >> /entrypoint.sh && \
    echo '    echo "Esperando conexión a base de datos..."; sleep 2;' >> /entrypoint.sh && \
    echo 'done' >> /entrypoint.sh && \
    echo 'echo "✅ PostgreSQL conectado"' >> /entrypoint.sh && \
    echo 'php artisan migrate --force || echo "⚠️ Migraciones fallaron"' >> /entrypoint.sh && \
    echo 'php artisan config:cache' >> /entrypoint.sh && \
    echo 'php artisan route:cache' >> /entrypoint.sh && \
    echo 'php artisan view:cache' >> /entrypoint.sh && \
    echo 'echo "✨ Aplicación lista!"' >> /entrypoint.sh && \
    echo 'exec "$@"' >> /entrypoint.sh && \
    chmod +x /entrypoint.sh

USER www-data

ENTRYPOINT ["/entrypoint.sh"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
