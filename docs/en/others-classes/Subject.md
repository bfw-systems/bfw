# Subject

This class implement the interface `\SplSubject` and give to the user
an easy way to use the [design pattern observer](https://en.wikipedia.org/wiki/Observer_pattern).

For more detail about how to use it, please refer to the page [Observers / Hooks](../how-it-works/observateurs-hooks.md).

## Constants

### Errors/Exception codes

These constants are sent when an exception is thrown and used like exception code.

__`ERR_OBSERVER_NOT_FOUND`__

Exception code if the observer to detach has not been found.

## Properties

__`protected \SplObserver[] $observers = [];`__

List of all observers

__`protected object[] $notifyHeap = [];`__

List of all notify to send.
There can be some events waiting because the current event is not sent to all observers yet.

__`protected string $action = '';`__

The current event action (the name)

__`protected mixed $context = null;`__

The current event context

## Methods

### Getters

For more info about returned data, please refer to the explanation on the properties.

__`string public getAction()`__

__`mixed public getContext()`__

__`object[] public getNotifyHeap()`__

__`\SplObserver[] public getObservers()`__

### Manage observers

__`void public attach(\SplObserver $observer)`__

Add the observer `$observer` to the list on the property `$observers`.

__`void public detach(\SplObserver $observer)`__

Remove the observer `$observer` from the list on the property `$observers`.

If the observer is not found in the list, an exception will be thrown;
the exception code will be the constant `\BFW\Subject::ERR_OBSERVER_NOT_FOUND`.

### Manage events

__`void public notify()`__

Read all observers and sends them the current event.

__`self public readNotifyHeap()`__

Read the notify stack and call the method `notify` for the next event to come.
Stop the reading when all events in the stack has been sent.

__`\BFW\Subject public addNotification(string $action, [mixed $context=null])`__

Add a new event to the stack on the property `$notifyHeap`.
If it's the only event on the stack, the method `readNotifyHeap` is called.
Else, the method `readNotifyHeap` is already called (we are in the case of an event is added by another from the same subject),
so the event will be read after all others on the stack.
