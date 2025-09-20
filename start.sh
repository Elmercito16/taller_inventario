#!/bin/bash
set -e  # Salir si hay errores

echo "=== INICIANDO APLICACIÓN LARAVEL ==="

# Configurar el puerto dinámico de Render
export PORT=${PORT:-80}
echo "Puerto configurado: $PORT"

# Actualizar configuración de Apache con el puerto correcto
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/:80>/:${PORT}>/" /etc/apache2/sites-available/000-default.conf

# Verificar permisos ANTES de todo
echo "=== CONFIGURANDO PERMISOS ==="
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Crear directorios si no existen
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/bootstrap/cache

# IMPORTANTE: Verificar configuración antes de ejecutar comandos
echo "=== VERIFICANDO CONFIGURACIÓN ==="
echo "APP_ENV: $(php artisan tinker --execute='echo config("app.env");')"
echo "DB_CONNECTION: $(php artisan tinker --execute='echo config("database.default");')"
echo "CACHE_DRIVER: $(php artisan tinker --execute='echo config("cache.default");')"

# Generar APP_KEY si no existe (ahora que tenemos variables de entorno)
php artisan key:generate --force || echo "Error generando key"

# LIMPIAR todos los caches ANTES de verificar BD
echo "=== LIMPIANDO CACHES (SIN BASE DE DATOS) ==="
rm -rf /var/www/html/bootstrap/cache/*.php
rm -rf /var/www/html/storage/framework/cache/*
rm -rf /var/www/html/storage/framework/sessions/*
rm -rf /var/www/html/storage/framework/views/*

# NO ejecutar comandos que usen BD para limpiar cache
echo "Archivos de cache eliminados manualmente"

# Verificar conexión a base de datos
echo "=== VERIFICANDO CONEXIÓN A BD ==="
php artisan tinker --execute="
try { 
    DB::connection()->getPdo(); 
    echo 'BD conectada correctamente' . PHP_EOL; 
} catch(Exception \$e) { 
    echo 'Error BD: ' . \$e->getMessage() . PHP_EOL; 
}" || echo "Error al verificar BD"

# Ejecutar migraciones
echo "=== EJECUTANDO MIGRACIONES ==="
php artisan migrate --force || echo "Error en migraciones (continuando...)"

# CACHEAR configuraciones solo DESPUÉS de verificar que todo funciona
echo "=== CACHEANDO CONFIGURACIONES ==="
php artisan config:cache || echo "Error en config:cache"

echo "=== INICIANDO APACHE ==="
# Iniciar Apache
exec apache2-foreground