# todolist


# TODOLIST

Projet 8 openclassroom

## Installation

Version minimum php 7.2.5

Clonez ou télécharger le projet github

```bash
git clone https://github.com/graboiide/p8-todolist.git
```
Pensez a modifier vos variables d'environnement dans le ficher .env

Installer les dépendances à l'aide de composer

```bash
composer install
```
Créez la base de données

```bash
php bin/console doctrine:database:create
```

Ajoutez les tables

```bash
php bin/console doctrine:migrations:migrate
```
Inserez des données fictives 

```bash
php bin/console doctrine:fixtures:load --group="no-test"
```

Lancez le serveur symfony

```bash
symfony server:start
```
## Tests

Pour lancer une commande de test
```bash
php bin/phpunit tests
```

Pour recupérer le coverage au format html aprés le test
```bash
php bin/phpunit tests --coverage-html web/test-coverage
```
