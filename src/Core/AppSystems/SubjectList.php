<?php

namespace BFW\Core\AppSystems;

class SubjectList extends AbstractSystem
{
    /**
     * @var \BFW\Core\SubjectList $subjectList
     */
    protected $subjectList;
    
    /**
     * Initialize subjectList system
     */
    public function __construct()
    {
        $this->subjectList = new \BFW\Core\SubjectList;
    }
    
    /**
     * {@inheritdoc}
     * 
     * @return \BFW\Core\SubjectList
     */
    public function __invoke()
    {
        return $this->subjectList;
    }

    /**
     * Getter accessor to property subjectList
     * 
     * @return \BFW\Core\SubjectList
     */
    public function getSubjectList()
    {
        return $this->subjectList;
    }
}
