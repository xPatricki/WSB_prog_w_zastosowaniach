FROM php:8.2-apache

# Instalacja zależności systemowych
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl

# Instalacja rozszerzeń PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Włączenie mod_rewrite dla Apache
RUN a2enmod rewrite

# Instalacja Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Ustawienie katalogu roboczego
WORKDIR /var/www/html

# Zmiana właściciela plików
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80

# Uruchomienie Apache w trybie foreground
CMD ["apache2-foreground"]
