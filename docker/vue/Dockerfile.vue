FROM node:21

WORKDIR /app

COPY package*.json ./

# RUN npm install

# COPY . .

# CMD ["npm", "run", "serve"]

# Installer Vue CLI globalement dans le conteneur
RUN npm install -g @vue/cli

# Commande par défaut pour garder le conteneur en marche
CMD ["tail", "-f", "/dev/null"]
