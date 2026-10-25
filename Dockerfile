# Dockerfile for Library Book Management System on Render
FROM php:8.2-apache

# Enable Apache rewrite module and install PDO MySQL and PDO SQLite extensions
RUN a2enmod rewrite \
    && apt-get update && apt-get install -y --no-install-recommends libsqlite3-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_sqlite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /var/www/html

# Copy application files into container
COPY . /var/www/html/

# Create data directory and set permissions for Apache web user (www-data)
RUN mkdir -p /var/www/html/data \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/data

# Dynamic port handling script to bind to Render's dynamic $PORT (default 80/10000)
RUN printf '#!/bin/bash\n\
if [ -n "$PORT" ]; then\n\
    sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf\n\
    sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:$PORT>/g" /etc/apache2/sites-available/000-default.conf\n\
fi\n\
exec apache2-foreground\n' > /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
