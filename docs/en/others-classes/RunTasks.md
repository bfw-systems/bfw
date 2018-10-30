# RunTasks

This class extend of `\BFW\Subject` (so implement the interface `\SplSubject`).
With this class, you can define a list of events and run the list reading when you want (instead of Subject who read immediately the event).

For more detail about how to use it, please refer to the page [Observers / Hooks](../how-it-works/observateurs-hooks.md).

## Properties

__`protected object[] $runSteps;`__

A list of all events to send.

__`protected string $notifyPrefix;`__

The prefix to use for each event name.

## Methods

__`self public __construct(object[] $runSteps, string $notifyPrefix)`__

Keep the event list `$runSteps` on the property `$runSteps`.<br>
It's an array of objects. The array key is the event name, and the object must have properties `callback` and `context`.
You can generate this object with the static method `generateStepItem`.
Please refer you to this method to know the value to use on properties `callback` and `context`.

The second parameter `$notifyPrefix` is kept on the class property `$notifyPrefix`.
It's the prefix which will be used on each event name.
It's useful to recognise its events in the logs among all the events that can be launched by the system.

### Getters and setters

For more info about returned data, please refer to the explanation on the properties.

__`string public getNotifyPrefix()`  __

__`self public setNotifyPrefix(string $notifyPrefix)`__

__`object[] public getRunSteps()`__

__`self public setRunSteps(object[] $runSteps)`__

To know the object format, please refer to the explanation for the constructor.

### Add a new event

__`self public addToRunSteps(string $name, object $runStepsToAdd)`__

Add the new event `$name` to the list on the property `$runSteps`.

To know the object `$runStepsToAdd` format, please refer to the explanation for the constructor.

### Generate the object event

__`object public static generateStepItem([mixed $context=null, [callable|null $callback=null]])`__

Return an anonymous class with the format used by this system.

The context is data that will be sent by `\BFW\Subject` with the event. It allows you to add additional data to your event.

The callback is used by RunTasks when it read the event. You can define a callback to execute when the event is read.
Like you can see on the page [Observers / Hooks](../how-it-works/observateurs-hooks.md#runtasks),
events sent is not same if there is a callback declared.*

### Read the event list

__`void public run()`__

Read the event list on the property `$runSteps` and send each event with the prefix.

If the event has a callback, two events will be sent instead of one.
Please refer to the page [Observers / Hooks](../how-it-works/observateurs-hooks.md#runtasks) for more info about that.

__`void public sendNotify(string $action, [mixed $context=null])`__

Call the parent method `addNotification` to send the event with the context to all observers.
