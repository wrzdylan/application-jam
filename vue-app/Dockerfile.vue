FROM node:21 AS build

# Copie du code source de l'application dans l'image
COPY . /app

# On définie le répertoire de travail
WORKDIR /app

# Installation du Vue CLI globalement dans le conteneur
RUN npm install -g @vue/cli

# Exécution des commandes pour installer les dépendances, construire l'application,
RUN npm install && npm run build

# Commande par défaut pour garder le conteneur en marche
CMD ["tail", "-f", "/dev/null"]

# Étape de production
FROM nginx:1.25
COPY --from=build /app/dist /usr/share/nginx/html
COPY nginx.conf /etc/nginx/conf.d/default.conf
EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]