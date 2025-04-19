# BlogPodcast

BlogPodcast est une plateforme web moderne développée avec Symfony permettant de gérer et publier des articles de blog ainsi que des podcasts. Cette application tout-en-un offre une interface intuitive pour la gestion de contenu multimédia.

## Fonctionnalités

- Gestion des articles de blog
- Publication de podcasts
- Interface d'administration
- Système de commentaires
- Gestion des utilisateurs

## Prérequis techniques

- PHP 8.1 ou supérieur
- Composer
- Symfony CLI
- MySQL/MariaDB
- Serveur web (Apache/Nginx)

## Installation

### 1. Récupération du projet

```bash
git clone https://github.com/alexandre-verbreugh/BlogPodcast.git
```

```bash
cd BlogPodcast
```

### 2. Installation des dépendances

```bash
composer install
```

### 3. Configuration

Créez une copie du fichier `.env` et nommez-la `.env.local`. Configurez vos variables d'environnement, notamment la connexion à la base de données :

```bash
cp .env .env.local
```

Modifiez la ligne suivante dans `.env.local` avec vos informations :
```
DATABASE_URL="mysql://votre_utilisateur:votre_mot_de_passe@127.0.0.1:3306/blogpodcast?serverVersion=8.0"
```

### 4. Base de données

```bash
php bin/console doctrine:database:create
```

```bash
php bin/console doctrine:migrations:migrate
```

### 5. Démarrage du serveur

```bash
symfony server:start
```

## Accès à l'application

Une fois l'installation terminée, l'application est accessible à l'adresse :
- http://localhost:8000

## Contribution

Les contributions sont les bienvenues ! N'hésitez pas à soumettre vos pull requests.

## Licence

Ce projet est sous licence MIT.
