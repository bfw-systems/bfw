<?php

namespace BFW\Core;

use \Exception;

class SubjectList
{
    /**
     * @const ERR_SUBJECT_NAME_NOT_EXIST Exception code if a subject name is
     * not found.
     */
    const ERR_SUBJECT_NAME_NOT_EXIST = 1206001;
    
    /**
     * @const ERR_SUBJECT_NOT_FOUND Exception code if a subject is not found.
     */
    const ERR_SUBJECT_NOT_FOUND = 1206002;
    
    /**
     * @var \SplSubject[] $subjectList List of all subjects declared
     */
    protected $subjectList = [];
    
    /**
     * Getter accessor to property subjectList
     * 
     * @return \SplSubject[]
     */
    public function getSubjectList(): array
    {
        return $this->subjectList;
    }
    
    /**
     * Obtain a subject object with this name
     * 
     * @param string $subjectName The name of the subject object
     * 
     * @return \SplSubject
     * 
     * @throws \Exception If the subject name not exist
     */
    public function getSubjectByName(string $subjectName): \SplSubject
    {
        if (!array_key_exists($subjectName, $this->subjectList)) {
            throw new Exception(
                'The subject '.$subjectName.' is not in the list.',
                self::ERR_SUBJECT_NAME_NOT_EXIST
            );
        }
        
        return $this->subjectList[$subjectName];
    }

        
    /**
     * Add a new subject to the list
     * 
     * @param \SplSubject $subject The new subject to add
     * @param string|null $subjectName (default null) The subject name, if null,
     * the name of the class will be used
     * 
     * @return $this
     */
    public function addSubject(\SplSubject $subject, $subjectName = null): self
    {
        if ($subjectName === null) {
            $subjectName = get_class($subject);
        }
        
        $this->subjectList[$subjectName] = $subject;
        
        return $this;
    }
    
    /**
     * Remove a subject from the list
     * 
     * @param \SplSubject $subject The subject to remove from the list
     * 
     * @return $this
     * 
     * @throws \Exception If the subject has not been found into the list
     */
    public function removeSubject(\SplSubject $subject): self
    {
        $key = array_search($subject, $this->subjectList, true);
        
        if ($key === false) {
            throw new Exception(
                'The subject has not been found.',
                self::ERR_SUBJECT_NOT_FOUND
            );
        }
        
        unset($this->subjectList[$key]);

        return $this;
    }
}
