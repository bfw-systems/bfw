# Install\ReadDirLoadModule

Override the class [\BFW\Helpers\ReadDirectory](../helpers/ReadDirectory.md) to add an action on the method `itemAction`.

## Method

__`string protected itemAction()`__

Search if the item is the file `bfwModulesInfos.json`.
If it's him, return the string `break` because we don't need to find another file.
