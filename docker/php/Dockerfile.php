FROM php:8.2-fpm

# Installer les dépendances pour PostgreSQL et autres outils nécessaires
RUN apt-get update && apt-get install -y \
    libpq-dev \
    git \
    unzip \
	libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql zip

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Répertoire de travail
WORKDIR /var/www/html

# Copier le fichier composer.json et installer les dépendances
COPY composer.json ./
RUN composer install --no-scripts --no-autoloader

# Copier le reste de l'application
COPY . .

# Finaliser l'installation des dépendances
RUN composer dump-autoload --optimize

# Créer le répertoire migrations
RUN mkdir -p /var/www/html/migrations

# Ajuster les permissions du répertoire (à ajuster en fonction de vos besoins)
RUN chown -R www-data:www-data /var/www/html/migrations
