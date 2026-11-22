#!/bin/bash
# Arranque del contenedor en Render (base de datos en Supabase).
set -e
cd /var/www/html

if [ -z "$APP_KEY" ]; then
    echo "ERROR: falta la variable APP_KEY. Genérala con: php artisan key:generate --show"
    exit 1
fi

echo "Preparando la aplicación..."
php artisan config:clear >/dev/null
php artisan storage:link >/dev/null 2>&1 || true

# Migraciones (con reintentos por si la base de datos tarda en responder)
intentos=0
until php artisan migrate --force; do
    intentos=$((intentos + 1))
    if [ "$intentos" -ge 5 ]; then
        echo "ERROR: no se pudieron ejecutar las migraciones. Revisa DB_URL."
        exit 1
    fi
    echo "Base de datos no disponible, reintentando en 5 s ($intentos/5)..."
    sleep 5
done

# Cuenta inicial (solo si no existe ningún usuario y se definieron las variables INICIAL_*)
php artisan sistema:cuenta-inicial || true

php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Aplicación lista en el puerto ${PORT:-8000}"
exec php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
