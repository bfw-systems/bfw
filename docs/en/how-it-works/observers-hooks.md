# Observers / Hooks

The framework integrate an observer system (also called hook) based on the [design pattern Observer](https://en.wikipedia.org/wiki/Observer_pattern)
To use it, it's recommended to use PHP classes [SplObserver](http://php.net/manual/en/class.splobserver.php) and [SplSubject](http://php.net/manual/en/class.splsubject.php).

## Subjects

With the design pattern Observer, subjects are systems who sent notify to all observers attached to him.

For that, the interface `SplSubject` requires to have 3 methods :
* [attach](http://php.net/manual/en/splsubject.attach.php) : To link a new observer to the subject
* [detach](http://php.net/manual/en/splsubject.detach.php) : To remove an observer of the subject
* [notify](http://php.net/manual/en/splsubject.notify.php) : To send an event to all attached observers

The framework has the class `\BFW\Subject` who implement `SplSubject` to send more data during notify.
With this class, all notify can have an action name and a context.

The action name should be a string like `bfw_run_done` (sent when framework is run).
However, the context can be anything. It's also possible to not sent context.

For more information, please refer to the [dedicated page](../others-classes/Subjects.md).

### SubjectList

This class is instantiated by a subsystem ran by the `Application` class.
`SubjectList` contain the list of all subjects declared. But subjects are not automatically added, you should add your Subject manually.
So some Subject cannot be into the list.

To access to the SubjectList instance, you should do : `\BFW\Application::getInstance()->getSubjectList();`

To add a subject to the list : 
```php
$subject = new \BFW\Subject;
\BFW\Application::getInstance()
    ->getSubjectList()
        ->addSubject($subject, 'mySubjectName')
;
```

### Using cases

The `Application` class have this own subject for actions it sent. It's the subject "bfwApp".
If you don't care about consistency (it should be reserved for events sent by Application), you can use it to send events.

#### Sent only actions

```php
\BFW\Application::getInstance()
    ->getSubjectList()
    ->getSubjectByName('bfwApp')
    ->addNotification('myHook')
;
```

#### Send an action with a context

```php
\BFW\Application::getInstance()
    ->getSubjectList()
    ->getSubjectByName('bfwApp')
    ->addNotification('myHook', debug_backtrace())
;
```

## Observers

Observers are classes who receive notify sent by the subject (if attached to the subject).

If you implement `SplObserver`, you should have the public method `update(\SplSubject $subject)`.
This is the method that will be called when an event is received from a subject.

### Using case

#### Example with bfw-controller module

The class who implement `\SplObserver` :

```php
namespace BfwController;

class BfwController implements \SplObserver
{
    // Some things...

    public function update(\SplSubject $subject)
    {
        if ($subject->getAction() === 'ctrlRouterLink_exec_execRoute') {
            $this->obtainCtrlRouterInfos($subject);
            
            if (
                $this->ctrlRouterInfos->isFound === true &&
                $this->ctrlRouterInfos->forWho === $this->execRouteSystemName
            ) {
                $this->run();
            }
        }
    }

    // Some things...
}
```

Into the runner script of the module :

```php
$bfwController = new \BfwController\BfwController($this);

$app        = \BFW\Application::getInstance();
$appSubject = $app->getSubjectList()->getSubjectByName('ctrlRouterLink');
$appSubject->attach($bfwController);
```

So, when the module is run, the class `\BfwController\BfwController` is instantiated. 
This class implement `SplObserver` and have a method `update`.
After that, the class is attached to the subject `ctrlRouterLink` to receive all events sent by this subject.

During the execution of `Application`, the subject (who are a RunTaks, but we see that later) run many events, including `ctrlRouterLink_exec_execRoute`.
The `update` method receive events, and if it's `ctrlRouterLink_exec_execRoute`, and with some conditions about router work doing before,
the method `run` will be called to execute the correct controller.

#### Example with bfw-sql module

This module send context during notify, it's for that I have added this example.

First, the runner script instantiate a new subject and add him to the list with the name `bfw-sql`.
And after, instantiate an observer (in reality, it's based on config files, but I summarise here).

```php
$app     = \BFW\Application::getInstance();
$subject = new \BFW\Subject;
$app->getSubjectList()->addSubject($subject, 'bfw-sql');

$observer = new \BfwSql\Observers\Basic($monolog);
$subject->attach($observer);
```

Next, an extract from the abstract class `\BfwSql\Executers\Common` (used to execute all requests).

```php
    protected function callObserver()
    {
        $app     = \BFW\Application::getInstance();
        $subject = $app->getSubjectList()->getSubjectByName('bfw-sql');
        $subject->addNotification('system query', clone $this);
    }
```

This method sends the event `system query` with a context that is a copy of himself (not to change instance data from observers).

Finally, the class `\BfwSql\Observers\Basic` (I have removed docblocks and many methods not useful for this example).

```php
namespace BfwSql;

class SqlObserver implements \SplObserver
{
    // Some things...

    public function update(\SplSubject $subject)
    {
        $this->action  = $subject->getAction();
        $this->context = $subject->getContext();
        
        $this->analyzeUpdate();
    }

    protected function analyzeUpdate()
    {
        if ($this->action === 'user query') {
            $this->userQuery();
        } elseif ($this->action === 'system query') {
            $this->systemQuery();
        }
    }

    protected function systemQuery()
    {
        if ($this->context instanceof \BfwSql\Executers\Common === false) {
            throw new Exception(
                '"system query" event should have an Executers\Common class'
                .' into the context.',
                self::ERR_SYSTEM_QUERY_CONTEXT_CLASS
            );
        }
        
        $query = $this->context->getQuery()->getAssembledRequest();
        $error = $this->context->getLastErrorInfos();
        
        $this->addQueryToMonoLog($query, $error);
    }

    // Some things...
}
```

So, we get the event and this context;
and we check if it's the correct event (`system query` or `user query`), and if the context has the correct format.
After that, it's the method `addQueryToMonolog` who will send the sql query to the monolog handler (who will save into a file for example).

However, you must keep in mind that everything can be into the context.
Here it's a class instance, but it can be an integer, a string, etc.
So you always need to check data receive from the context before use it.

## RunTasks

The framework also defines the class `\BFW\RunTasks` for hooks, and this class extends `BFW\Subject`.

The role of Subject is to stack all notification and sent him one after the other.
The stack is because something can ask to send a new notification while sending another.
So we stack, and we wait for the current notification is sent to all observers before send the next
(not to erase action and context data of the current event).

But RunTasks is a little different.
It's stacked all events, and it unstack only when the method `run` is called instead of Subject who always unstack.

For each event defines into RunTasks, you can define an action name and a context.

### Example : The ctrlRouterLink subject

It's a RunTasks define into an Application subsystem.

Extract of the class `\BFW\Core\AppSystems\CtrlRouterLink`

```php
<?php

namespace BFW\Core\AppSystems;

class CtrlRouterLink extends AbstractSystem
{
    //Some things...

    public function init()
    {
        //...

        $ctrlRouterTask = new \BFW\RunTasks(
            $this->obtainCtrlRouterLinkTasks(),
            'ctrlRouterLink'
        );
        
        $subjectList = \BFW\Application::getInstance()->getSubjectList();
        $subjectList->addSubject($ctrlRouterTask, 'ctrlRouterLink');
        
        $runTasks = $subjectList->getSubjectByName('ApplicationTasks');
        $runTasks->sendNotify('bfw_ctrlRouterLink_subject_added');
        
        //...
    }

    protected function obtainCtrlRouterLinkTasks(): array
    {
        return [
            'searchRoute'     => \BFW\RunTasks::generateStepItem(
                $this->ctrlRouterInfos
            ),
            'checkRouteFound' => \BFW\RunTasks::generateStepItem(
                null,
                function() {
                    if ($this->ctrlRouterInfos->isFound === false) {
                        http_response_code(404);
                    }
                }
            ),
            'execRoute'       => \BFW\RunTasks::generateStepItem(
                $this->ctrlRouterInfos
            )
        ];
    }

    //Some things...
}
```

And the extract of the method `\BFW\RunTasks::generateStepItem`

```php
    public static function generateStepItem($context = null, $callback = null)
    {
        return new class ($context, $callback) {
            public $context;
            public $callback;
            
            public function __construct($context, $callback)
            {
                $this->context  = $context;
                $this->callback = $callback;
            }
        };
    }
```

At the beginning, we instantiate `RunTasks` with a list of events to send, and a prefix (who is used for each event).

Each event has a name (the array key), and have an object for value (here create with anonymous class).
This object has two optional keys :
* `context` : It's the context that will be sent with the event
* `callback` : It's a function who will be called when this event is sent

For the RunTasks `ctrlRouterLink`, the event list who will be sent when method `run` will be called is :
* `ctrlRouterLink_start_run_tasks`
* `ctrlRouterLink_exec_searchRoute`
* `ctrlRouterLink_run_checkRouteFound`
* `ctrlRouterLink_done_checkRouteFound`
* `ctrlRouterLink_exec_execRoute`
* `ctrlRouterLink_end_run_tasks`

Each `RunTasks` send events `start_run_tasks` and `end_run_tasks` to know when it's running, and when it's done.
Around this event, we found all events defined into the array (`searchRoute`, `checkRouterFound` and `execRoute`).
All events have the prefix defined into the RunTasks constructor; for this example it's `ctrlRouterLink`.

And a little difference, if it's a callback, there are two events (`run` and `done`) instead of once (`exec`) if there is no callback defined.
