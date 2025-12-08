# Usar PHP 8.2 FPM Alpine para imagen ligera
FROM php:8.2-fpm-alpine

# Establecer variables de entorno
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
    zlib-dev \
    zip \
    unzip \
    git \
    curl \
    nodejs \
    npm \
    bash \
    supervisor

# Instalar extensiones PHP requeridas por tu proyecto
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

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos de dependencias primero (para cache)
COPY composer.json composer.lock ./
COPY package.json package-lock.json* ./

# Instalar dependencias de Composer (sin dev para producción)
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --optimize-autoloader

# Instalar dependencias de Node
RUN npm ci --only=production

# Copiar el resto del código
COPY . .

# Generar autoloader optimizado
RUN composer dump-autoload --optimize --no-dev

# Compilar assets de Vite
RUN npm run build

# Crear directorios necesarios y establecer permisos
RUN mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Limpiar cache de npm
RUN npm cache clean --force && rm -rf node_modules

# Exponer puerto 8000
EXPOSE 8000

# Script de inicio
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Usuario www-data para seguridad
USER www-data

# Comando de inicio
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
