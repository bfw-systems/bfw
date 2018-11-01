<?php

namespace BFW\Core\AppSystems;

class Request extends AbstractSystem
{
    /**
     * @var \BFW\Request $request
     */
    protected $request;
    
    /**
     * Initialize the Request system and run the detection of all items
     */
    public function __construct()
    {
        $this->request = \BFW\Request::getInstance();
        $this->request->runDetect();
    }
    
    /**
     * {@inheritdoc}
     * 
     * @return \BFW\Request
     */
    public function __invoke()
    {
        return $this->request;
    }

    /**
     * Getter accessor to request property
     * 
     * @return \BFW\Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}
