# Configuration

## The framework

There are many config files.
They are all into the directory `app/config/bfw/`.
Only the file `manifest.json` is not a config file and should not be modified.

The `manifest.json` file is intended to be used during framework (and modules) update for automatically update configs files.
However, this system is not implemented yet,  so you will need to continue to manually update config files.

All configuration has been split into many files :
* errors.php : Used by the system who catch errors
* global.php : Global configurations
* memcached.php : Used to know memcache(d) server to connect (if there are)
* modules.php : Used to define "core" modules
* monolog.php : Used to define Monolog handlers to instantiate

All files have comments to know expected values.
But with all comment, files can be a little hard to read.
So there is a version of files without comment on [gist](https://gist.github.com/bulton-fr/88cded4f084c1aba74f9fb00c8020f7b).

To get it (be careful, it will erase your existing config files) :
```bash
wget -O app/config/bfw/errors.php https://gist.github.com/bulton-fr/88cded4f084c1aba74f9fb00c8020f7b/raw/f949f73ef6d78dae478fc7ae43096728b5b43c84/errors.php
wget -O app/config/bfw/global.php https://gist.github.com/bulton-fr/88cded4f084c1aba74f9fb00c8020f7b/raw/f949f73ef6d78dae478fc7ae43096728b5b43c84/global.php
wget -O app/config/bfw/memcached.php https://gist.github.com/bulton-fr/88cded4f084c1aba74f9fb00c8020f7b/raw/f949f73ef6d78dae478fc7ae43096728b5b43c84/memcached.php
wget -O app/config/bfw/modules.php https://gist.github.com/bulton-fr/88cded4f084c1aba74f9fb00c8020f7b/raw/f949f73ef6d78dae478fc7ae43096728b5b43c84/modules.php
wget -O app/config/bfw/monolog.php https://gist.github.com/bulton-fr/88cded4f084c1aba74f9fb00c8020f7b/raw/f949f73ef6d78dae478fc7ae43096728b5b43c84/monolog.php
```

### errors.php

#### errorRenderFct

To define and enable functions will be called if there is a PHP error caught.

`cli` To define the function to call if it's an error into cli script<br>
`default` To all others case, and also for cli too if `cli` is empty.

#### exceptionRenderFct

Same of `errorRenderFct` but for not caught exceptions.

### global.php

#### debug

To enable a `debug` status into your application.
Can be used by a debug bar module for example.

Without module, the only utility is to enable personal errors pages (defined into `errors.php` config files).

### memcached.php

To use a memcache(d) server.
Obviously, if you want, you can not use this system and connect to the memcache(d) server yourself.

#### enabled

To enable the system and instantiate the class `\BFW\Memcached` during framework loading.
The class instance can be get with the method `getMemcached()` from `\BFW\Application` instance.

#### persistentId

To connect to the memcache(d) server with the parameter `persistent_id` sent to `\Memcached` constructor.

Thanks to refer you to the PHP doc about the [Memcached constructor](http://php.net/manual/en/memcached.construct.php).

#### server

The list of all memcache(d) server to connect.

For each server of the list, keys `host` and `port` should not be empty.
And for the key `weight`, it's the third parameter of the method [Memcached::addServer](http://php.net/manual/en/memcached.addserver.php).

### modules.php

This part is to define "core" modules. i.e. modules used for MVC pattern and router system.

You not need to have all modules defined (It's your choice not to use MVC pattern, not mine).
However, router and controller modules can be helpful for a web application :)

For each module, `name` is the module name and `enabled` this status (enabled or not).

#### cli

Used for the module who manages cli files

#### db

Used for the module who manages the link with the database, models, etc...

#### controller

Used for the module who load the controller file find by router module.

#### router

Used for the module who manages the link between a url and file/method to execute.

#### template

Used for the module who executes (and sometimes parse) the view.

### monolog.php

Sometimes, it can be useful to have a log of internal functioning of the framework.
I have integrated [Monolog](https://github.com/Seldaek/monolog) into the framework for that.

Into this config file, you can declare all [handlers](https://github.com/Seldaek/monolog/blob/master/doc/02-handlers-formatters-processors.md#handlers)
you want to use.
Keep in mind that some modules use the same Monolog instance (so same handlers) to send messages about their internal functioning too.

Into current version of Monolog (1.x), if no handler is declared, all message is sent to output (approved for cli, not sure with web script).
It's why there always the TestHandler defined into default config file.
This handler keep all receives messages into his properties and never send messages anywhere.

Each handler declared have the same format, an array with two keys :
* `name` : The handler class name (with namespace)
* `args` : An array with all parameters to send to the handler constructor

## Apache 2.x

It's recommended to define the `DocumentRoot` to the `web` directory.
Doing that will protect your application of many hacks (because php scripts never can be directly called from outside).

A file `.htaccess` is provided with the framework.
His role is to redirect all requests (when the file not exist into the `web` directory) to the file `index.php`.

### Apache < 2.2.16 or with an Alias

The provided `.htaccess` file use the directive `FallbackResource`.
This directive has been added into Apache 2.2.16.
If you use an older version (not recommended by Apache), this line will not work.

Same thing if you use an alias, the line will not work like intended.

In these cases, you can replace the `.htaccess` content by this :

```apache
#FallbackResource /index.php

RewriteEngine On
RewriteBase /
#For an alias, write RewriteBase /myAlias

RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^.*$ /path/to/my/application/web/index.php [PT]
```

Note: I'm not sure about the `PT` flag is the best choice. To confirm...

### File .htaccess not allowed

If your Apache configuration not allowed you to use `.htaccess` files,
the solution I can recommend you is to rename the file and included it into your apache config.

If you can not edit the Apache configuration, I have not solution for you :/

For example : 

Rename the file `.htaccess` and move it outside of the `web` directory : `mv web/.htaccess ./rewrites.conf`<br>
Include the file into Apache configuration (with a vhost) :

```apache
<VirtualHost *:80>
    ServerName myprojet.com

    DocumentRoot /path/to/my/application/web
    <Directory /path/to/my/application/web/>
        Options Indexes FollowSymLinks MultiViews
        AllowOverride None
        Require all granted

        Include /path/to/my/application/rewrites.conf
    </Directory>
<VirtualHost>
```

### The error ERR\_INCOMPLETE\_CHUNKED\_ENCODING

It's possible to obtain this error for all routes except the home.
In this case, add the directive `DirectoryIndex index.php` into the `Directory` part of the vhost.

## Nginx

I never tested the framework with nginx (because I use apache with php-fpm).
So I don't know the config to use with nginx.

If someone has tested and known config to use, you are free to edit this doc to add info. ;)
