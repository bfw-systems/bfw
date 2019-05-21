<?php

namespace BFW\Install;

use Exception;
use BFW\Install\ModuleManager\Actions;
use bultonFr\Utils\Cli\BasicMsg;

class ModuleManager
{
    /**
     * The action to do on modules
     *
     * @var string
     */
    protected $action = '';

    /**
     * Declare if the system should reinstall the module or not
     *
     * @var bool
     */
    protected $reinstall = false;

    /**
     * Declare if we do the action for all module, or just one
     *
     * @var bool
     */
    protected $allModules = false;

    /**
     * Declare if we do the action for a specific module
     *
     * @var string
     */
    protected $specificModule = '';

    /**
     * Getter for property action
     *
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * Setter for property action
     *
     * @param string $action The action to do on modules
     *
     * @return self
     */
    public function setAction(string $action): self
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Getter for property reinstall
     *
     * @return bool
     */
    public function getReinstall(): bool
    {
        return $this->reinstall;
    }

    /**
     * Setter for property reinstall
     *
     * @param bool $reinstall If we do a complete reinstall of the module
     *
     * @return self
     */
    public function setReinstall(bool $reinstall): self
    {
        $this->reinstall = $reinstall;

        return $this;
    }

    /**
     * Getter for property allModule
     *
     * @return bool
     */
    public function getAllModules(): bool
    {
        return $this->allModules;
    }

    /**
     * Setter for property allModule
     *
     * @param bool $allModules If we do action for all modules
     *
     * @return self
     */
    public function setAllModules(bool $allModules): self
    {
        $this->allModules = $allModules;

        return $this;
    }

    /**
     * Getter for property specificModule
     *
     * @return string
     */
    public function getSpecificModule(): string
    {
        return $this->specificModule;
    }

    /**
     * Setter for property specificModule
     *
     * @param string $specificModule The module name
     *
     * @return self
     */
    public function setSpecificModule(string $specificModule): self
    {
        $this->specificModule = $specificModule;

        return $this;
    }

    /**
     * Instanciate the Actions class and call the method doAction on it to
     * execute the current action tasks.
     *
     * @return void
     */
    public function doAction()
    {
        try {
            $actionClass = $this->obtainActionClass();
            $actionClass->doAction();
        } catch (Exception $e) {
            $msg = 'Error #'.$e->getCode().' : '.$e->getMessage();
            BasicMsg::displayMsgNL($msg, 'red', 'bold');
        }
    }

    /**
     * Return the Action class to use for do asked actions
     *
     * @return \BFW\Install\ModuleManager\Actions
     */
    protected function obtainActionClass(): Actions
    {
        return new Actions($this);
    }
}
