FROM php:8.2-apache

# Install dependencies and extensions
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache modules
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html

# Update Apache configuration to point to public directory
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Create uploads directory and set permissions
RUN mkdir -p /var/www/html/public/uploads && \
    chown -R www-data:www-data /var/www/html/public/uploads && \
    chmod -R 755 /var/www/html/public/uploads

# Expose port 8080 (Cloud Run default)
EXPOSE 8080
# Configure Apache to listen on 8080
RUN sed -i 's/80/8080/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf
