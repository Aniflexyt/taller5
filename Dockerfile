# Usar la imagen oficial de PHP con Apache
FROM php:8.2-apache

# Instalar dependencias del sistema requeridas para PostgreSQL y MongoDB
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libssl-dev \
    pkg-config \
    git \
    unzip

# Instalar extensión de PostgreSQL
RUN docker-php-ext-install pdo pdo_pgsql

# Instalar y habilitar extensión de MongoDB
RUN pecl install mongodb && docker-php-ext-enable mongodb

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar los archivos del proyecto al directorio de Apache
COPY . /var/www/html/

# Dar permisos al directorio
RUN chown -R www-data:www-data /var/www/html/

# Instalar dependencias de PHP (MongoDB)
WORKDIR /var/www/html/
RUN composer install --no-dev --optimize-autoloader

# Exponer el puerto 80
EXPOSE 80d
