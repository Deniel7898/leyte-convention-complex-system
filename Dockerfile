# Use the official PHP 8.2 image with Apache
FROM php:8.2-apache-bookworm

# Install required system packages and PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
    zip unzip git curl \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev libzip-dev \
 && docker-php-ext-configure gd --with-freetype --with-jpeg=/usr/include \
 && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
 && apt-get clean && rm -rf /var/lib/apt/lists/*


# Enable Apache mod_rewrite for Laravel routes
RUN a2enmod rewrite

# Copy project files into the container
COPY . /var/www/html

# Set working directory
WORKDIR /var/www/html

# Change Apache document root to /public
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Give permission to storage & cache folders
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 80 for Apache
EXPOSE 80