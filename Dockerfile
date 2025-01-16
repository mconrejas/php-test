FROM php:8.1-apache

# Enable required PHP extensions
RUN apt-get update && apt-get install -y \
    libpq-dev \
    redis-server \
    && docker-php-ext-install pdo pdo_pgsql

# Install and enable the Redis PHP extension
RUN pecl install redis && docker-php-ext-enable redis
RUN docker-php-ext-install pcntl

# Set the working directory
WORKDIR /var/www/html

# Copy application code
COPY ./ /var/www/html

# Set permissions for web server
RUN chown -R www-data:www-data /var/www/html
RUN chmod 644 .htaccess

RUN apt-get update && apt-get install -y cron
COPY ./scripts/cron.php /var/www/html/scripts/
RUN echo "0 * * * * php /var/www/html/scripts/cron.php >> /var/www/html/logs/cron.log 2>&1" > /etc/cron.d/app-cron
RUN chmod 0644 /etc/cron.d/app-cron
RUN crontab /etc/cron.d/app-cron

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Clean up to reduce image size
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Expose port 80
EXPOSE 80
