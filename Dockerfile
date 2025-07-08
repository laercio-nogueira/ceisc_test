FROM php:8.2-fpm

# Instalar dependências
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    git \
    zip \
    curl \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring xml

# Instalar composer
COPY ./composer /usr/bin/composer

WORKDIR /var/www

COPY . .
COPY ./default.conf /etc/nginx/conf.d/default.conf

RUN composer install --no-interaction --prefer-dist --optimize-autoloader

RUN chown -R www-data:www-data /var/www || true \
    && chmod -R 775 storage bootstrap/cache
