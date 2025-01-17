FROM php:8.1-apache

# Install required packages
RUN apt-get update && apt-get install -y \
    libpq-dev \
    cron \
    redis-server \
    && docker-php-ext-install pdo pdo_pgsql

# Install and enable the Redis PHP extension
RUN pecl install redis && docker-php-ext-enable redis
RUN docker-php-ext-install pcntl

# Set permissions for web server
RUN chown -R www-data:www-data /var/www/html

# Create necessary directories
RUN mkdir -p /var/www/html/logs && chmod 777 /var/www/html/logs

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy application code
COPY ./ /var/www/html
RUN chmod 644 .htaccess

RUN echo "0 * * * * /usr/local/bin/php /var/www/html/scripts/cron.php >> /var/www/html/logs/cron.log 2>&1" > /etc/cron.d/app-cron
RUN chmod 0644 /etc/cron.d/app-cron && crontab /etc/cron.d/app-cron

# Ensure both services start
CMD ["cron", "-f"]

# Clean up to reduce image size
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Expose port 80
EXPOSE 80
