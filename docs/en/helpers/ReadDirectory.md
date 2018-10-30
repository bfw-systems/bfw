# Helpers\ReadDirectory

This helper allows you to **recursively** read a directory and to define what to do for each found file.

## Constants

### Errors/Exception codes

These constants are sent when an exception is thrown and used like exception code.

__`ERR_RUN_OPENDIR`__

Exception code if the [opendir](http://php.net/manual/en/function.opendir.php) function fail

## Properties

__`protected string $calledClass = '';`__

Name of the current class. Used to recall the correct class when it's extended.

It's used by method `dirAction` when the class is re-instantiate for the new directory to read.

Good to know, it works with anonymous class ([tested here](https://gist.github.com/bulton-fr/eedbf7b3656fe2626cab034cd70319de)) :)

__`protected array &$list;`__

It's an array with the idea of all files found.
By default if you not override the method `itemAction`, the array will stay empty.

__`protected string[] $ignore = ['.', '..'];`__

List of items to ignore during the directories reading.

## Methods

__`self public __construct(array &$listFiles)`__

The argument `$listFiles` is an array passed by reference and put on the property `$list`.

### Getters

For more info about returned data, please refer to the explanation on the properties.

__`string public getCalledClass()`__

__`array public getIgnore()`__

__`array public getList()`__

The `getList` method is +/- useless because the `$list` property is a reference to the array passed to the constructor;
so you already have the updated list.

### Run the reading

__`void public run(string $path)`__

This method start the reading of the directory `$path`.

For each item founded, the method `itemAction` will be called.<br>
If the return of `itemAction` is `continue`, the next file will be directly called.<br>
If the return of `itemAction` is `break`, the current reading will be stopped.

After that, if it's a directory, the method `dirAction` will be called to read the subdirectory.

You should keep in mind that when a subdirectory is found, this reading is immediately and the reading of the parent directory is paused.
So when `itemAction` return a break, it's only for the current directory, not for the parent directory (if it's into a subdirectory).

### During the reading

__`string protected itemAction(string $fileName, string $pathToFile)`__

Called for each item found in the directory.

This method only check if the item is in the property `$ignore` or not.
If the item is in it, the method will return the value `continue`.

By default, nothing more is doing. You should override this method to define what you want to do more.

The returned value is used by the method `run` to know what to do during the reading.
Please refer to the explain (just before) about the method `run` to understand the impact of the value that will be returned.

__`void protected dirAction(string $dirPath)`__

Called if a directory is found during the reading.
It will re-instantiate the current class and call the method `run` of the new
instance with the path of the subdirectory to immediately start this reading.

## How to override the itemAction method with an anonymous class

In this example, all files that are not directories are added to the list.

```php
$list        = [];
$searchFiles = new class($list) extends \BFW\Helpers\ReadDirectory
{
    protected function itemAction(string $fileName, string $pathToFile): string
    {
        $parentFilter = parent::itemAction($fileName, $pathToFile);
        if (!empty($parentFilter)) {
            return $parentFilter;
        }
        
        if (!is_dir($pathToFile.'/'.$fileName)) {
            $this->list[] = realpath($pathToFile.'/'.$fileName);
        }
    }
};

$searchFiles->run();

var_dump($list);
```
