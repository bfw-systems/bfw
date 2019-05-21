# Install\Application

This class override `\BFW\Application` to load a part of the framework for installer systems.

## Methods

__`void protected defineCoreSystemList()`__

Redefine load subsystems.

The new subsystems list is :
* `\BFW\Core\AppSystems\Options`
* `\BFW\Core\AppSystems\Constants`
* `\BFW\Core\AppSystems\ComposerLoader`
* `\BFW\Core\AppSystems\SubjectList`
* `\BFW\Core\AppSystems\Config`
* `\BFW\Core\AppSystems\Monolog`
* `\BFW\Core\AppSystems\Memcached`
* `\BFW\Install\Core\AppSystems\ModuleList`
* `\BFW\Install\Core\AppSystems\ModuleManager`

__`void public run()`__

To change the Monolog message and the ending event name.

Changes are :
* Monolog message : `running framework install` instead of `running framework`
* Ending event name : `bfw_install_done` instead of `bfw_run_done`
