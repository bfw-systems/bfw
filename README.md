BFW : Framework PHP >= 5.3.x
===

__Liens utiles :__

Doc : http://bfw.bulton.fr/doc/

How-To : http://bfw.bulton.fr/howto/

Bug Tracker et avancement : https://github.com/bulton-fr/bfw/issues

Support & Question : support@bulton.fr


---

__Installation :__

Il est recommandé d'utiliser composer pour installer le framework :

Pour récupérer composer:
```
curl -sS https://getcomposer.org/installer | php
```

Pour installer le framework, créez un fichier "composer.json" à la racine de votre projet, et ajoutez-y ceci:
```
{
    "require": {
        "bulton-fr/bfw": "@stable"
    }
}
```

Enfin, pour lancer l'installation, 2 étapes sont nécessaires :

Récupérer les framework via composer :
```
php composer.phar install
```
Lancer l'installation afin de créer les répertoires et fichiers nécessaire :
```
sh vendor/bin/bfw_install
```
