FROM php:8.2-fpm

# Installation des dépendances pour PostgreSQL et autres outils nécessaires
RUN apt-get update && \
    apt-get install -y libzip-dev wget ca-certificates curl gnupg libicu-dev libpq-dev && \
    rm -rf /var/lib/apt/lists/*

# Configuration et installation des extensions PHP
RUN docker-php-ext-configure intl
RUN docker-php-ext-install pdo pdo_pgsql zip intl

# Installation de Composer
RUN wget https://getcomposer.org/download/2.4.4/composer.phar \
    && mv composer.phar /usr/bin/composer \
    && chmod +x /usr/bin/composer

# Copie des fichiers sources de l'application dans l'image
COPY ./ /var/www/html/

# Répertoire de travail
WORKDIR /var/www/html/

# Exécution des commandes pour configurer la base de données et initialiser l'application
RUN composer install -n \
    # && php bin/console doctrine:database:drop --no-interaction --force \
    # && php bin/console doctrine:database:create --no-interaction \
    # && php bin/console doctrine:migration:migrate --no-interaction \
    # && php bin/console doctrine:fixtures:load --no-interaction