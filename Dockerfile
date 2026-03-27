FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy project files
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Generate key (optional fallback)
RUN php artisan key:generate || true

# Expose port
EXPOSE 10000

# Start Laravel
CMD php artisan config:clear && php artisan cache:clear && php artisan migrate --force && php artisan serve --host 0.0.0.0 --port 10000