#cda_symfony_twig

**récupération du projet :**

```sh
# 1 Cloner le repository
git clone https://github.com/evaluationWeb/cda_symfony_twig.git

# 2 Se déplacer dans le projet
cd cda_symfony_twig

# 3 Créer la variable d'environnement dans .env
DATABASE_URL=

# 4 Créer la variable d'environnement dans .env.dev
DATABASE_URL="mysql://login:mdp@127.0.0.1:3306/nom_bdd?serverVersion=10.4.32-MariaDB&charset=utf8mb4"
# Modifier avec vos propres valeurs

# 5 Installer les dépendances
composer install

# 6 Créer la base de données (si elle n'existe pas)
symfony console d:d:c

# 7 Appliquer les migrations
symfony console d:m:m

# 8 Générer les fixtures
symfony console d:f:l

# 9 Installer le CLI Tailwind-css
curl -sLO https://github.com/tailwindlabs/tailwindcss/releases/download/v3.4.14/tailwindcss-windows-x64.exe

chmod +x tailwindcss-windows-x64.exe

mv tailwindcss-windows-x64.exe ./bin/tailwindcss

# 10 Démarrer le projet 
symfony serve -d

# En cas de pb vider le cache
symfony console cache:clear

