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

# Verificar conexión a base de datos
echo "=== VERIFICANDO CONEXIÓN A BD ==="
php artisan tinker --execute="DB::connection()->getPdo(); echo 'BD conectada correctamente';" || echo "Error de conexión a BD"

# Ejecutar migraciones
echo "=== EJECUTANDO MIGRACIONES ==="
php artisan migrate --force || echo "Error en migraciones (continuando...)"

# Limpiar y cachear configuraciones
echo "=== LIMPIANDO CACHES ==="
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "=== CACHEANDO CONFIGURACIONES ==="
php artisan config:cache || echo "Error en config:cache"
php artisan route:cache || echo "Error en route:cache"
php artisan view:cache || echo "Error en view:cache"

# Verificar que Laravel puede escribir logs
echo "=== VERIFICANDO LOGS ==="
php artisan tinker --execute="Log::info('Test log desde container');" || echo "Error al escribir logs"

echo "=== INICIANDO APACHE ==="
# Iniciar Apache
exec apache2-foreground