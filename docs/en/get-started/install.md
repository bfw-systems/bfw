# Installation

The easier is to use [Composer](https://getcomposer.org/).

If you not have Composer, refers you to [the download page](https://getcomposer.org/download/) of Composer.
Many ways to install it is suggested.

## Get the framework

Run the command `composer require bulton-fr/bfw:3.*`

## Create the structure

Next, to generate the directories structure used by the framework, run the command `./vendor/bin/bfwInstall`.

The output should be like that :
```
Run BFW Install

> Create app directory ... Done
> Create app/config directory ... Done
> Create app/config/bfw directory ... Done
> Create app/modules directory ... Done
> Create src directory ... Done
> Create src/cli directory ... Done
> Create src/controllers directory ... Done
> Create src/modeles directory ... Done
> Create src/view directory ... Done
> Create web directory ... Done

> Search BFW vendor directory path ... Found
BFW path : /opt/dev/bfw/test/vendor/bulton-fr/bfw

> Copy skel/cli.php file to cli.php ... Done
> Copy skel/app/config/bfw/errors.php file to app/config/bfw/errors.php ... Done
> Copy skel/app/config/bfw/global.php file to app/config/bfw/global.php ... Done
> Copy skel/app/config/bfw/manifest.json file to app/config/bfw/manifest.json ... Done
> Copy skel/app/config/bfw/memcached.php file to app/config/bfw/memcached.php ... Done
> Copy skel/app/config/bfw/modules.php file to app/config/bfw/modules.php ... Done
> Copy skel/app/config/bfw/monolog.php file to app/config/bfw/monolog.php ... Done
> Copy skel/src/cli/exemple.php file to src/cli/exemple.php ... Done
> Copy skel/web/.htaccess file to web/.htaccess ... Done
> Copy skel/web/index.php file to web/index.php ... Done

BFW install status : Success
```
