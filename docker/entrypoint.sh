#!/bin/bash

# On se rend dans le répertoire de l'application Vue.js
cd /var/www/vue-app

# On installe les dépendances et fait le build
npm install
npm run build

# On revient au répertoire de base
cd /var/www

# On déplace le contenu du dossier public dans le répertoire de base
rsync -av vue-app/dist/ public/

# On continue avec les migrations et l'installation des packages php
composer install -n
bin/console doctrine:database:drop --no-interaction --force
bin/console doctrine:database:create --no-interaction
bin/console doctrine:migration:migrate --no-interaction
bin/console doctrine:fixtures:load --no-interaction

# Exécuter la commande donnée en paramètre au script
exec "$@"
