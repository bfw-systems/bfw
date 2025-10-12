<?php

namespace BFW\Core\AppSystems;

use BFW\Core\SubjectList as CoreSubjectList;

class SubjectList extends AbstractSystem
{
    /**
     * @var CoreSubjectList $subjectList
     */
    protected $subjectList;

    /**
     * Initialize subjectList system
     */
    public function __construct()
    {
        $this->subjectList = new CoreSubjectList();
    }

    /**
     * {@inheritdoc}
     *
     * @return CoreSubjectList
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
