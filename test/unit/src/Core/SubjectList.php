<?php

namespace BFW\Core\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class SubjectList extends atoum
{
    //use \BFW\Test\Helpers\Application;
    
    protected $mock;
    
    public function beforeTestMethod($testMethod)
    {
        //$this->createApp();
        //$this->initApp();
        
        $this->mock = new \mock\BFW\Core\SubjectList;
    }
    
    public function testGetSubjectList()
    {
        $this->assert('test Core\SubjectList::getSubjectList for default value')
            ->array($this->mock->getSubjectList())
                ->isEmpty()
        ;
    }
    
    public function testGetSubjectByName()
    {
        $this->assert('test Core\SubjectList::getSubjectByName with not existing subject')
            ->exception(function() {
                $this->mock->getSubjectByName('UnitTest');
            })
                ->hasCode(\BFW\Core\SubjectList::ERR_SUBJECT_NAME_NOT_EXIST)
        ;
        
        $this->assert('test Core\SubjectList::getSubjectByName with existing subject')
            ->given($subject = new \BFW\Subject)
            ->if($this->mock->addSubject($subject, 'UnitTest'))
            ->then
            
            ->object($this->mock->getSubjectByName('UnitTest'))
                ->isIdenticalTo($subject)
        ;
    }
    
    public function testAddSubject()
    {
        $this->assert('test Core\SubjectList::addSubject')
            ->given($subject = new \BFW\Subject)
            ->then
            
            ->object($this->mock->addSubject($subject, 'UnitTest'))
                ->isIdenticalTo($this->mock)
            ->array($subjectList = $this->mock->getSubjectList())
                ->hasKey('UnitTest')
            ->object($subjectList['UnitTest'])
                ->isIdenticalTo($subject)
        ;
        
        $this->assert('test Core\SubjectList::addSubject without name')
            ->object($this->mock->addSubject($subject))
                ->isIdenticalTo($this->mock)
            ->array($subjectList = $this->mock->getSubjectList())
                ->hasKey('BFW\Subject')
        ;
        
        $this->assert('test Core\SubjectList::addSubject with existing name and same instance')
            ->object($this->mock->addSubject($subject, 'UnitTest'))
                ->isIdenticalTo($this->mock)
            ->object($this->mock->getSubjectByName('UnitTest'))
                ->isIdenticalTo($subject)
        ;
        
        $this->assert('test Core\SubjectList::addSubject with existing name but not the same instance')
            ->exception(function() {
                $subject2 = new \BFW\Subject;
                $this->mock->addSubject($subject2, 'UnitTest');
            })
                ->hasCode(\BFW\Core\SubjectList::ERR_ADD_SUBJECT_ALREADY_EXIST)
        ;
    }
    
    public function testRemoveSubject()
    {
        $this->assert('test Core\SubjectList::removeSubject with existing subject')
            ->given($subject = new \BFW\Subject)
            ->if($this->mock->addSubject($subject, 'UnitTest'))
            //Fast check to adding
            ->array($this->mock->getSubjectList())
                ->hasKey('UnitTest')
            
            ->then
            ->object($this->mock->removeSubject($subject))
                ->isIdenticalTo($this->mock)
            ->array($this->mock->getSubjectList())
                ->notHasKey('UnitTest')
        ;
        
        $this->assert('test removeSubject with not existing subject')
            ->exception(function() use ($subject) {
                $this->mock->removeSubject($subject);
            })
                ->hasCode(\BFW\Core\SubjectList::ERR_SUBJECT_NOT_FOUND)
        ;
    }
}