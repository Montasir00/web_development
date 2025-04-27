# Use the official PHP image
FROM php:8.4-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    default-libmysqlclient-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mysqli

# Enable mod_rewrite for Apache
RUN a2enmod rewrite

# Set the working directory to /var/www/html
WORKDIR /var/www/html

# Copy the current directory contents into the container
COPY . .

# Set permissions (make sure Apache can access files)
RUN chown -R www-data:www-data /var/www/html

# Expose port 80 (default HTTP port)
EXPOSE 80
