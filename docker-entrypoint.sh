#!/bin/bash
set -e

echo "🚀 Iniciando aplicación Laravel..."

# Esperar a que la base de datos esté lista
echo "⏳ Esperando PostgreSQL..."
until pg_isready -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USERNAME" 2>/dev/null; do
    echo "PostgreSQL no está listo - esperando..."
    sleep 2
done

echo "✅ PostgreSQL está listo!"

# Ejecutar migraciones
echo "📦 Ejecutando migraciones..."
php artisan migrate --force --no-interaction || echo "⚠️ Error en migraciones (puede ser normal si ya están ejecutadas)"

# Crear link de storage si no existe
echo "🔗 Creando storage link..."
php artisan storage:link || echo "⚠️ Storage link ya existe"

# Limpiar y optimizar cache
echo "🧹 Optimizando aplicación..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

echo "✨ Aplicación lista!"

# Ejecutar comando
exec "$@"
