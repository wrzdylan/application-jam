#!/bin/bash

composer install -n
npm install
npm run build
bin/console doctrine:database:drop --no-interaction --force
bin/console doctrine:database:create --no-interaction
bin/console doctrine:migration:migrate --no-interaction
bin/console doctrine:fixtures:load --no-interaction

exec "$@"