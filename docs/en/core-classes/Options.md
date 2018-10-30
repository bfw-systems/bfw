# Core\Options

This class which extends `\BFW\Options` manage options given to `\BFW\Application::initSystems` method.

The reason to extend it, it's because we can need to find paths if they're not declared.

## Methods

### Search paths not declared

__`self public searchPaths()`__

Check if `rootDir` and/or `vendorDir` is declared, and call methods `searchRootDir()` and/or `searchVendorDir()` if not.

__`string protected searchVendorDir()`__

Search the path of the vendor directory from the current path of this file (into vendor).

__`string protected searchRootDir()`__

Search the path of the root application directory with the path returned by `searchVendorDir`.
By default, it's considered that the vendor directory is in the root application directory.

### Check paths

__`self public checkPaths()`__

Check if paths have the expected format.
For the moment, we only check if paths have an ending slashes, and add it if not.
