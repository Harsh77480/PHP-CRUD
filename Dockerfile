# Use an official PHP image as the base
FROM php:8.1-cli

# Set the working directory inside the container
WORKDIR /app

# Install dependencies (e.g., for Slim framework, GD, etc.)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Install Composer to manage dependencies
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Verify Composer installation
RUN composer --version

# Copy the current directory contents into the container
COPY . /app

# Install the PHP dependencies
RUN composer install --no-interaction --verbose

# Expose port 8080
EXPOSE 8080

# Start the PHP built-in server
CMD ["php", "-S", "0.0.0.0:8080", "public/index.php"]
