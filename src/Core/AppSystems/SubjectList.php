<?php

namespace BFW\Core\AppSystems;

class SubjectList extends AbstractSystem
{
    /**
     * @var \BFW\Core\SubjectList|null $subjectList
     */
    protected $subjectList;
    
    /**
     * {@inheritdoc}
     * 
     * @return \BFW\Core\SubjectList|null
     */
    public function __invoke()
    {
        return $this->subjectList;
    }

    /**
     * Getter accessor to property subjectList
     * 
     * @return \BFW\Core\SubjectList|null
     */
    public function getSubjectList()
    {
        return $this->subjectList;
    }
    
    /**
     * {@inheritdoc}
     * Initialize subjectList system
     */
    public function init()
    {
        $this->subjectList = new \BFW\Core\SubjectList;
        $this->initStatus  = true;
    }
}
