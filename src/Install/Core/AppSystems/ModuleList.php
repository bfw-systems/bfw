<?php

namespace BFW\Install\Core\AppSystems;

class ModuleList extends \BFW\Core\AppSystems\ModuleList
{
    /**
     * {@inheritdoc}
     * Run only loadAllModules, not more.
     */
    public function run()
    {
        $this->loadAllModules();
        $this->runStatus = true;
    }
}
