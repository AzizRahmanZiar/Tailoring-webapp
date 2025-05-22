FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    libonig-dev \
    libxml2-dev \
    npm \
    nodejs \
    sqlite3 \
    libsqlite3-dev

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy existing application directory
COPY . .

# Install dependencies
RUN composer install \
    && npm install --prefix resources \
    && npm run build --prefix resources

# Set permissions
RUN chown -R www-data:www-data /var/www

# Generate application key
RUN php artisan key:generate

# Expose port
EXPOSE 8000

# Start the Laravel server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
