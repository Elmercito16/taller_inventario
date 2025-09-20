#!/bin/bash

# Configurar el puerto dinámico de Render
export PORT=${PORT:-80}

# Actualizar configuración de Apache con el puerto correcto
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/:80>/:${PORT}>/" /etc/apache2/sites-available/000-default.conf

# Ejecutar migraciones (por si no se ejecutaron en build)
php artisan migrate --force

# Limpiar y cachear configuraciones
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Verificar permisos
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Iniciar Apache
exec apache2-foreground