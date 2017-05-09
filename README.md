BFW : Framework PHP >= 5.3.x
===

[![Build Status](https://travis-ci.org/bulton-fr/bfw.svg?branch=master)](https://travis-ci.org/bulton-fr/bfw) [![Coverage Status](https://coveralls.io/repos/bulton-fr/bfw/badge.png?branch=master)](https://coveralls.io/r/bulton-fr/bfw?branch=master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/bulton-fr/bfw/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/bulton-fr/bfw/?branch=master) [![Dependency Status](https://www.versioneye.com/user/projects/5413eee49e1622970f0000f1/badge.svg?style=flat)](https://www.versioneye.com/user/projects/5413eee49e1622970f0000f1)

[![Latest Stable Version](https://poser.pugx.org/bulton-fr/bfw/v/stable.svg)](https://packagist.org/packages/bulton-fr/bfw) [![Latest Unstable Version](https://poser.pugx.org/bulton-fr/bfw/v/unstable.svg)](https://packagist.org/packages/bulton-fr/bfw) [![License](https://poser.pugx.org/bulton-fr/bfw/license.svg)](https://packagist.org/packages/bulton-fr/bfw)

__Liens utiles :__

Doc : http://bfw.bulton.fr/doc/

How-To : https://github.com/bulton-fr/bfw/wiki

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

Récupérer le framework via composer :
```
php composer.phar install
```
Lancer l'installation afin de créer les répertoires et fichiers nécessaire :
```
sh vendor/bin/bfwInstall
```
