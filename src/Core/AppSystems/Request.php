<?php

namespace BFW\Core\AppSystems;

class Request extends AbstractSystem
{
    /**
     * @var \BFW\Request|null $request
     */
    protected $request;
    
    /**
     * {@inheritdoc}
     * 
     * @return \BFW\Request|null
     */
    public function __invoke()
    {
        return $this->request;
    }

    /**
     * Getter accessor to request property
     * 
     * @return \BFW\Request|null
     */
    public function getRequest()
    {
        return $this->request;
    }
    
    /**
     * {@inheritdoc}
     * Initialize the Request system and run the detection of all items
     */
    public function init()
    {
        $this->request = \BFW\Request::getInstance();
        $this->request->runDetect();
        
        $this->initStatus = true;
    }
}
