# BlogPodcast

BlogPodcast est une application web développée avec Symfony pour gérer des articles de blog et des podcasts.

## Prérequis

Avant de commencer, assure-toi d'avoir installé les éléments suivants :

- PHP 8.1 ou supérieur
- Composer
- Symfony CLI
- MySQL/MariaDB

## Installation

```bash
# Cloner le projet
git clone https://github.com/alexandre-verbreugh/BlogPodcast.git
cd BlogPodcast

# Installer les dépendances
composer install

# Configurer la base de données dans le fichier .env
DATABASE_URL="mysql://user\:password@127.0.0.1:3306/blogpodcast?serverVersion=8.0"

# Créer la base de données
php bin/console doctrine\:database\:create

# Exécuter les migrations
php bin/console doctrine\:migrations\:migrate

# Lancer le serveur Symfony
symfony server\:start
