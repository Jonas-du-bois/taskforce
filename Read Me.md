# Installation du projet

Ce fichier décrit les étapes nécessaires pour installer et configurer le projet sur n'importe quelle machine.

## Prérequis

Assurez-vous que les éléments suivants sont installés sur votre machine avant de commencer :

- [PHP](https://www.php.net/downloads) (version 7.4 ou supérieure)
- [Composer](https://getcomposer.org/download/)
- Une connexion internet pour télécharger les dépendances

## Installation

1. **Cloner le projet**

   Clonez le dépôt sur votre machine locale :

   ```bash
   git clone <url_du_depot>
   cd <nom_du_repertoire>
   ```

2. **Installer les dépendances principales**

   Installez toutes les dépendances requises par le projet à l'aide de Composer :

   ```bash
   composer install
   ```

3. **Ajouter les bibliothèques nécessaires**

   Certaines bibliothèques spécifiques doivent également être ajoutées. Exécutez la commande suivante :

   ```bash
   composer require symfony/mailer symfony/mime symfony/event-dispatcher symfony/polyfill-mbstring symfony/polyfill-intl-normalizer
   ```

## Configuration

- Si nécessaire, configurez les fichiers `.env` ou `config/db.ini` selon vos besoins pour définir les paramètres comme la base de données ou d'autres services.

## Lancer le projet

Une fois toutes les étapes ci-dessus terminées, vous pouvez utiliser le projet sans problème.