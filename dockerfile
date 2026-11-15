FROM php:8.2-apache

# Instalar la librería de desarrollo de PostgreSQL (necesaria para pdo_pgsql)
RUN apt-get update && apt-get install -y libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# Habilitar rewrite de Apache e instalar la extensión de PostgreSQL
RUN a2enmod rewrite && docker-php-ext-install pdo_pgsql

# Copiar el proyecto al servidor web
COPY . /var/www/html/

# Ajustar permisos
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
