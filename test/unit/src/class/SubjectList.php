<?php

namespace BFW\test\unit;

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
        
        $this->mock = new \mock\BFW\SubjectList;
    }
    
    public function testGetSubjectList()
    {
        $this->assert('test SubjectList::getSubjectList for default value')
            ->array($this->mock->getSubjectList())
                ->isEmpty()
        ;
    }
    
    public function testGetSubjectForName()
    {
        $this->assert('test SubjectList::getSubjectForName with not existing subject')
            ->exception(function() {
                $this->mock->getSubjectForName('UnitTest');
            })
                ->hasCode(\BFW\SubjectList::ERR_SUBJECT_NAME_NOT_EXIST)
        ;
        
        $this->assert('test SubjectList::getSubjectForName with existing subject')
            ->given($subject = new \BFW\Subject)
            ->if($this->mock->addSubject($subject, 'UnitTest'))
            ->then
            
            ->object($this->mock->getSubjectForName('UnitTest'))
                ->isIdenticalTo($subject)
        ;
    }
    
    public function testAddSubject()
    {
        $this->assert('test SubjectList::addSubject')
            ->given($subject = new \BFW\Subject)
            ->then
            
            ->object($this->mock->addSubject($subject, 'UnitTest'))
                ->isIdenticalTo($this->mock)
            ->array($subjectList = $this->mock->getSubjectList())
                ->hasKey('UnitTest')
            ->object($subjectList['UnitTest'])
                ->isIdenticalTo($subject)
        ;
        
        $this->assert('test SubjectList::addSubject without name')
            ->object($this->mock->addSubject($subject))
                ->isIdenticalTo($this->mock)
            ->array($subjectList = $this->mock->getSubjectList())
                ->hasKey('BFW\Subject')
        ;
    }
    
    public function testRemoveSubject()
    {
        $this->assert('test SubjectList::removeSubject with existing subject')
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
                ->hasCode(\BFW\SubjectList::ERR_SUBJECT_NOT_FOUND)
        ;
    }
}