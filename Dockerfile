# Use the official PHP image with CLI
FROM php:8.1-cli

# Install required PHP extensions for PDO MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Set the working directory to /var/www/html
WORKDIR /var/www/html

# Copy the PHP application files into the container
COPY ./api /var/www/html

# Expose port 8101 (the port for the PHP built-in web server)
EXPOSE 8101

# Run the PHP built-in server
CMD ["php", "-S", "0.0.0.0:8101", "-t", "/var/www/html"]