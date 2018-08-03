<?php

namespace BFW\Core\AppSystems;

class SubjectList extends AbstractSystem
{
    /**
     * @var \BFW\SubjectList|null $subjectList
     */
    protected $subjectList;
    
    /**
     * {@inheritdoc}
     * 
     * @return \BFW\SubjectList|null
     */
    public function __invoke()
    {
        return $this->subjectList;
    }

    /**
     * Getter accessor to property subjectList
     * 
     * @return \BFW\SubjectList|null
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
        $this->subjectList = new \BFW\SubjectList;
        $this->initStatus  = true;
    }
}
