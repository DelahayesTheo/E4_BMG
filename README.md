BMG
===

Adaptation du projet PHP MVC BMG en applicatif Symfony.

Installation
============

- Cloner le repository 
- Par le biais de la console ou de PhpMyAdmin importer le fichier `bd_dump`.
- Ouvrir une invite de commande dans le dossier et entrer :

```
composer install
```
ou 
```
php composer.phar install
```
selon l'installation de composer (global ou localisé).
(entrer le nom `bmg` dans `database_name`, ou si vous apportez un changement le nom adapté) 

- Entrer la commande 
```
php bin/console server:run
```
