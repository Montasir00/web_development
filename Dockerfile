# Use the official PHP image with Apache
FROM php:8.4-apache

# Install required extensions and libraries
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    default-libmysqlclient-dev \
    curl \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mysqli

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy everything into the container
COPY . .

# Give Apache access to all files
RUN chown -R www-data:www-data /var/www/html

# Expose Apache on port 80
EXPOSE 80
