# Memcached

This class extend the class `\Memcached` and create the connection to all servers declared in config.
This class also add some method to an easier use of memcache.

## Constants

### Errors/Exception codes

These constants are sent when an exception is thrown and used like exception code.

__`ERR_NO_SERVER_CONNECTED`__

Exception code if no server is connected.

__`ERR_A_SERVER_IS_NOT_CONNECTED`__

Exception code if a server is not connected.

__`ERR_KEY_NOT_EXIST`__

Exception code if the asked key not exist.
Actually only used into the method `updateExpire`.

## Property

__`protected array $config;`__

The config values define in bfw config file `memcached.php` for the key `memcached`.

## Methods

__`self public __construct()`__

Obtain config value for property `$config` and instantiate the parent class with (or not) the `persistentId`.

### Getter

For more info about returned data, please refer to the explanation on the properties.

__`array public getConfig()`__

### Create the connection to servers

__`void public connectToServers()`__

Obtain the list of all servers already connected (the persistence) with a call to the method `obtainConnectedServerList`
Next we loop on declared servers in config.
For each server, call the method `completeServerInfos` to have all config keys,
check some key values and, if check is ok, add the server to list which will be passed to the method
[\Memcached::addServers](http://php.net/manual/fr/memcached.addservers.php).

After that, call the method `\Memcached::addServers`, and call the method `testConnect`.

__`string[] protected obtainConnectedServerList()`__

Obtain the list of all servers already connected (because persistence) with the method
[\Memcached::getServerList](http://php.net/manual/fr/memcached.getserverlist.php).

Format the list and returns it.

__`void protected completeServerInfos(array &$infos)`__

Check if `$infos` contains all key needed by the system.
If a key is missing, it added.

__`bool protected testConnect()`__

Use the method [\Memcached::getStats()](http://php.net/manual/en/memcached.getstats.php) to obtain all servers added and their status.
For each server returned, we check that it is well connected.

If no server is connected, an exception will be thrown;
the exception code will be the constant `\BFW\Memcached::ERR_NO_SERVER_CONNECTED`.

If a server is not connected, an exception will be thrown;
the exception code will be the constant `\BFW\Memcached::ERR_A_SERVER_IS_NOT_CONNECTED`.

### Test if a key exist

`bool public ifExists(string $key)`

Check if the key `$key` exist on the memcache(d) server.

### Update the expires time of a key

`bool public updateExpire(string $key, int $expire)`

Update the expire time of the key `$key` to `$expire`.

If the key not exist on the server (checked with method `ifExists`, an exception will be thrown;
the exception code will be the constant `\BFW\Memcached::ERR_KEY_NOT_EXIST`.
