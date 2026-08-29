# ==========================================
# ETAPA 1: Compilación de Frontend con Node
# ==========================================
FROM node:20-alpine AS frontend-builder
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# ==========================================
# ETAPA 2: Runtime PHP-FPM + Nginx
# ==========================================
FROM php:8.2-fpm-alpine

# Instalar dependencias del sistema y extensiones PHP
RUN apk update && apk add --no-cache \
    nginx \
    supervisor \
    postgresql-client \
    postgresql-dev \
    libzip-dev \
    icu-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    curl \
    git

# Configurar y compilar extensiones PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_pgsql \
        pgsql \
        intl \
        zip \
        gd \
        bcmath \
        opcache

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configurar directorio de trabajo
WORKDIR /var/www/html

# Copiar configuración de Nginx y Supervisor
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Copiar archivos del proyecto
COPY . /var/www/html

# Copiar assets compilados de la Etapa 1
COPY --from=frontend-builder /app/public/build /var/www/html/public/build

# Instalar dependencias de PHP con Composer para producción
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Permisos de almacenamiento y caché
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Exponer puerto HTTP
EXPOSE 80

# Punto de entrada
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
