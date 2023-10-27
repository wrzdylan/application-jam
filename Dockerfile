# Utiliser l'image de base PHP avec Apache
FROM php:apache

# Mettre à jour la liste des paquets et installer les dépendances requises
RUN apt-get update \
    && apt-get install -y libzip-dev wget ca-certificates curl gnupg libicu-dev libpq-dev rsync

# Configurer et installer les extensions PHP
RUN docker-php-ext-configure intl
RUN docker-php-ext-install pdo pdo_pgsql zip intl

# Installer Composer
RUN wget https://getcomposer.org/download/2.4.4/composer.phar \
    && mv composer.phar /usr/bin/composer \
    && chmod +x /usr/bin/composer

# Installer Node.js et NPM
RUN mkdir -p /etc/apt/keyrings
RUN curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg
RUN echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_18.x nodistro main" | tee /etc/apt/sources.list.d/nodesource.list
RUN apt-get update
RUN apt-get install nodejs -y

# Copier les fichiers sources de l'application dans l'image
COPY ./ /var/www

# Copier le script entrypoint.sh modifié dans l'image
COPY ./docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Mettre à jour la configuration Apache pour servir l'application
COPY ./docker/apache.conf /etc/apache2/sites-enabled/000-default.conf

# Activer le module rewrite d'Apache
RUN a2enmod rewrite

# Définir le répertoire de travail
WORKDIR /var/www

# Définir le script entrypoint.sh comme point d'entrée
ENTRYPOINT [ "/entrypoint.sh" ]

# Lancer Apache en mode avant-plan
CMD ["apache2-foreground"]
