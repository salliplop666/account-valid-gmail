FROM php:8.2-apache

# Habilita el módulo de reescritura de Apache y la extensión de PostgreSQL (pdo_pgsql)
RUN a2enmod rewrite && docker-php-ext-install pdo_pgsql

# Copia todos los archivos del proyecto al servidor web
COPY . /var/www/html/

# Ajusta los permisos de la carpeta del servidor
RUN chown -R www-data:www-data /var/www/html

# Expone el puerto 80
EXPOSE 80
