# AgriLink Rwanda — PHP + Apache Docker image
FROM php:8.3-apache

# Install system dependencies and PHP extensions needed for MySQL (PDO)
RUN apt-get update \
    && apt-get install -y --no-install-recommends libzip-dev unzip \
    && docker-php-ext-install pdo pdo_mysql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite (useful for future clean URLs)
RUN a2enmod rewrite

# Apache should serve the project root directly (plain PHP, no /public folder)
WORKDIR /var/www/html
COPY . /var/www/html

# Ensure the uploads directory is writable by the web server
RUN mkdir -p /var/www/html/uploads \
    && chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
