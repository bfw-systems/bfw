# Example scripts

## Cli script

There is a script called `cli.php` into the root directory of your application.
This file will execute cli script with the framework loaded.

When you use it, you should indicate the parameter `-f` to say what file (into directory `src/cli/`) to execute.
The argument value must never have the script extension. The `.php` extension is added by the system.

There is an example cli file provided.
```
$ ls src/cli/
exemple.php
```

To play the script, you should do :
```
$ php cli.php -f=exemple
CLI Exemple file
```

Note : We use the function [getopt()](http://php.net/manual/en/function.getopt.php), so take the time to understand how to pass parameters values.

The parameters `-f` is mandatory for `/cli.php` script, but all cli script executed by him can also have their own parameters.

For example :
```
$ php cli.php -f=exemple -h
CLI Exemple file

Helping Informations : Parameters script
* -v --version : Version of test script
* -p --parameters : Display args array
* -h --help : View this message

$ php cli.php -f=exemple -p
CLI Exemple file
Array
(
    [f] => exemple
    [p] => 
)
```

## Web script

For this example, we need to have a controller and router modules.
I will take modules I use for my projects.

Get and install it :
```
$ composer require bulton-fr/bfw-controller 3.0.0
./composer.json has been updated
Loading composer repositories with package information
Updating dependencies (including require-dev)
Package operations: 1 install, 0 updates, 0 removals
  - Installing bulton-fr/bfw-controller (3.0.0) Loading from cache
Writing lock file
Generating autoload files

$ composer require bulton-fr/bfw-fastroute 2.0.0
./composer.json has been updated
Loading composer repositories with package information
Updating dependencies (including require-dev)
Package operations: 2 installs, 0 updates, 0 removals
  - Installing nikic/fast-route (v1.3.0) Loading from cache
  - Installing bulton-fr/bfw-fastroute (2.0.0) Loading from cache
Writing lock file
Generating autoload files

$ ./vendor/bin/bfwInstallModules 
Run BFW Modules Install
bfw-controller : Run install.
 > Create symbolic link ... Done
 > Copy config files : 
 >> Create config directory for this module ... Done
 >> Copy manifest.json ... Done
 >> Copy config.php ... Done
 > Check install specific script :
 >> No specific script declared. Pass
bfw-fastroute : Run install.
 > Create symbolic link ... Done
 > Copy config files : 
 >> Create config directory for this module ... Done
 >> Copy manifest.json ... Done
 >> Copy routes.php ... Done
 > Check install specific script :
 >> No specific script declared. Pass
Read all modules to run install script...
 > Read for module bfw-controller
 >> No script to run.
 > Read for module bfw-fastroute
 >> No script to run.
All modules have been read.
```

We configure it :

For the file `app/config/bfw/modules.php`
```php
<?php

return [
    'modules' => [
        'db'         => [
            'name'    => '',
            'enabled' => false
        ],
        'controller' => [
            'name'    => 'bfw-controller',
            'enabled' => true
        ],
        'router'     => [
            'name'    => 'bfw-fastroute',
            'enabled' => true
        ],
        'template'   => [
            'name'    => '',
            'enabled' => false
        ]
    ]
];
```

For the file `app/config/bfw-controller/config.php`
```php
<?php
return [
    'useClass' => true
];
```

For the file `app/config/bfw-fastroute/routes.php`
```php
<?php
return [
    'routes' => [
        '/test' => [
            'target' => ['\Controller\Test', 'index']
        ]
    ]
];
```

And we create the controller file  `src/controllers/Test.php`
```php
<?php

namespace Controller;

class Test extends \BfwController\Controller
{
    public function index()
    {
        var_dump($this->request->getRequest());
    }
}
```

Next call the framework via the web.

For this example, I will use the web server integrated to PHP (As a reminder not to use it in production).

For me, the command is `php -S localhost:8000 -t web web/index.php`.
And into my browser, the url `http://localhost:8000/test` will return me :
```
object(stdClass)#27 (8) {
  ["scheme"]=>
  string(4) "http"
  ["host"]=>
  string(14) "localhost:8000"
  ["port"]=>
  string(4) "8000"
  ["user"]=>
  string(0) ""
  ["pass"]=>
  string(0) ""
  ["path"]=>
  string(5) "/test"
  ["query"]=>
  string(0) ""
  ["fragment"]=>
  string(0) ""
}
```
