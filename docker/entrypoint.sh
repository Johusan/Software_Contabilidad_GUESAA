#!/bin/sh
set -e

echo "========================================================="
echo "  🚀 INICIANDO GUESAA SIC (DOCKER CONTAINER)"
echo "========================================================="

# Esperar a que la base de datos esté lista
echo "⏳ Esperando conexión con PostgreSQL ($DB_HOST:$DB_PORT)..."
until pg_isready -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USERNAME" > /dev/null 2>&1; do
    sleep 1
done
echo "✅ Conexión con PostgreSQL establecida."

# Permisos de almacenamiento
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Generar APP_KEY si está vacía
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generando Application Key..."
    php artisan key:generate --force
fi

# Migraciones y seeders automáticos
echo "📦 Verificando migraciones y catálogo contable PCGE 2026..."
php artisan migrate --force --seed

# Optimización de caché para producción
echo "⚡ Optimizando caché de configuración y rutas..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "========================================================="
echo "  ✅ SISTEMA CONTABLE GUESAA SIC LISTO EN PUERTO 8000"
echo "========================================================="

# Iniciar Nginx + PHP-FPM con supervisord
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
