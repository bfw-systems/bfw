<?php

namespace BFW\Core\AppSystems;

use BFW\RunTasks;

class CtrlRouterLink extends AbstractSystem
{
    /**
     * @var object $ctrlRouterInfos Infos from router for controller
     * system
     */
    protected $ctrlRouterInfos;

    /**
     * Initialize the ctrlRouterInfos property
     * Create the new runTasks ctrlRouterLink, add him to subjectList and send
     * the notify to inform the adding.
     */
    public function __construct()
    {
        //Others properties can be dynamically added by modules
        $this->ctrlRouterInfos = new class {
            public $isFound = false;
            public $forWho = null;
            public $target = null;
            public $datas = null;
        };

        $ctrlRouterTask = new RunTasks(
            $this->obtainCtrlRouterLinkTasks(),
            'ctrlRouterLink'
        );

        $subjectList = \BFW\Application::getInstance()->getSubjectList();
        $subjectList->addSubject($ctrlRouterTask, 'ctrlRouterLink');

        $runTasks = $subjectList->getSubjectByName('ApplicationTasks');
        $runTasks->sendNotify('bfw_ctrlRouterLink_subject_added');
    }

    /**
     * {@inheritdoc}
     *
     * @return object
     */
    public function __invoke()
    {
        return $this->ctrlRouterInfos;
    }

    /**
     * Getter accessor for property ctrlRouterInfos
     *
     * @return object
     */
    public function getCtrlRouterInfos()
    {
        return $this->ctrlRouterInfos;
    }

    /**
     * List all tasks runned by ctrlRouterLink
     *
     * @return array
     */
    protected function obtainCtrlRouterLinkTasks(): array
    {
        return [
            'searchRoute'     => RunTasks::generateStepItem(
                $this->ctrlRouterInfos
            ),
            'checkRouteFound' => RunTasks::generateStepItem(
                null,
                function () {
                    if ($this->ctrlRouterInfos->isFound === false) {
                        http_response_code(404);
                    }
                }
            ),
            'execRoute'       => RunTasks::generateStepItem(
                $this->ctrlRouterInfos
            )
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function toRun(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     * Execute the ctrlRouter tasks
     */
    public function run()
    {
        $this->runCtrlRouterLink();
        $this->runStatus = true;
    }

    /**
     * Execute the ctrlRouter task to find the route and the controller.
     * If nothing is found (context object), return an 404 error.
     * Not executed in cli.
     *
     * @return void
     */
    protected function runCtrlRouterLink()
    {
        if (PHP_SAPI === 'cli') {
            return;
        }

        \BFW\Application::getInstance()
            ->getSubjectList()
            ->getSubjectByName('ctrlRouterLink')
            ->run();
    }
}
