FROM php:apache

RUN apt-get update \
    && apt-get install -y libzip-dev wget ca-certificates curl gnupg libicu-dev libpq-dev

# On ajoute PostgreSQL qui par défaut n'est pas dans l'image
RUN docker-php-ext-configure intl
RUN docker-php-ext-install pdo pdo_pgsql zip intl;

# Installation de Composer
RUN wget https://getcomposer.org/download/2.4.4/composer.phar \
    && mv composer.phar /usr/bin/composer \
    && chmod +x /usr/bin/composer

# Installation de Node.js et NPM
RUN mkdir -p /etc/apt/keyrings
RUN curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg
RUN echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_18.x nodistro main" | tee /etc/apt/sources.list.d/nodesource.list
RUN apt-get update
RUN apt-get install nodejs -y

# Entrypoint pour le composer install
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Config Apache pour notre application
COPY docker/apache.conf /etc/apache2/sites-enabled/000-default.conf

# On copie les fichiers sources
COPY ./ /var/www

# On se place dans le répertoire d'Apache
WORKDIR /var/www

# On active le mode rewrite d'Apache
RUN a2enmod rewrite

# On lance le worker en foreground (mode attaché) sinon le conteneur s'arrêtera
CMD ["apache2-foreground"]

# Entrypoint pour l'installation des dépendences, migrations etc
ENTRYPOINT [ "/entrypoint.sh" ]