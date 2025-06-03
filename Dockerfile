FROM php:8.1-apache-bullseye

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Configure Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Configure PHP for Cloud Run
RUN echo "memory_limit=256M" > /usr/local/etc/php/conf.d/memory-limit.ini
RUN echo "upload_max_filesize=10M" >> /usr/local/etc/php/conf.d/upload-limit.ini
RUN echo "post_max_size=10M" >> /usr/local/etc/php/conf.d/upload-limit.ini

# Create directory for Cloud Run
RUN mkdir -p /var/www/html/postimages /var/www/html/images \
    && chown -R www-data:www-data /var/www/html/postimages /var/www/html/images

# Expose port 8080 for Cloud Run
ENV PORT 8080
EXPOSE 8080

# Configure Apache to listen on PORT
RUN sed -i 's/Listen 80/Listen ${PORT}/' /etc/apache2/ports.conf
RUN sed -i 's/:80/:${PORT}/' /etc/apache2/sites-available/000-default.conf

CMD ["apache2-foreground"] 