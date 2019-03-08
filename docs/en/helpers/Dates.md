# Helpers\Dates

This helper who extends [DateTime](http://php.net/manual/en/class.datetime.php) give methods to an easier access to some dates formats.

## Shortcut method for date formats

__`string public getDate()`__

Return the full date; use the format `Y-m-d H:i:sO`.

__`int public getDay()`__

Return the day; use the format `d`.

__`int public getHour()`__

Return the hour; use the format `H`.

__`int public getMinute()`__

Return the miute; use the format `i`.

__`int public getMonth()`__

Return the month; use the format `m`.

__`int public getSecond()`__

Return the second; use the format `s`.

__`int public getYear()`__

Return the year; use the format `Y`.

__`string public getZone()`__

Return the zone; use the format `O`.

__`string[]|string public getSqlFormat([bool $returnArray=false, [bool $withZone=false]])`__

Return the date to a format that can be used with the database.

Because some people split date and time into their database, the argument `bool $returnArray` define the returned format.
If it value is false, you will get a string with the full date.
If it value is true, you will get an array where first item the date, and the second item is the time.

And because some SGDB not manage zone into datetime column type, the last argument `bool $withZone` define if the zone will be into time value.
If it is false, the zone will not be into the time value. If it is true, the zone will be into it.
The default value is false because some major databases like mysql not allow that (cf [mysql doc](https://dev.mysql.com/doc/refman/8.0/en/datetime.html)).

## List TimeZone

__`string[] public lstTimeZone()`__

Return the list of all existing timezone into the current PHP version.
It's a shortcut to `DateTime->getTimezone()->listIdentifiers();`

__`string[] public lstTimeZoneContinent()`__

Return the list of all existing continents that can be used for timezone.

__`string[] public lstTimeZoneCountries(string $continent)`__

Return the list of all available timezone countries for a specific continent.

## Human readable system

This system is based on the difference between current date and the instance date.
With him, you can display to the user a date who are easier to read.
For example, for the date 5 minutes ago, if you write the full date (Y-m-d H:i), it's not easy to understand there is 5 minutes ago.
But if you write "5 min ago", it's easier to understand.

__`string public humanReadable([bool $returnDateAndTime=true])`__

It's this method who return you the instance date into a readable format.

The parameter `bool $returnDateAndTime` is to define if the time will be into the returned value (if `true`), or not (if `false`).

Many cases are managed :
* If it's just now
* if it's in the last 24 hours
* if it's in the next 24 hours
* If it's yesterday
* If it's tomorrow
* All other cases

### Personalized format

The format used by the system can be personalized.

There are two static properties for that :
* `$humanReadableI18n` : For word used into the date
* `$humanReadableFormats` : For date format used

#### humanReadableI18n

This property is used to define the text to write for all cases.

The default value is : 
```php
[
    'now'          => 'now',
    'today_past'   => '{time} ago',
    'today_future' => 'in {time}',
    'yesterday'    => 'yesterday',
    'tomorrow'     => 'tomorrow',
    'others'       => 'the {date}',
    'time_part'    => ' at {time}'
];
```

Each keys except `time_part` are a case which be managed by the system.

The `{date}` value is replaced by date format.<br>
The `{time}` value is replaced by the time format.<br>
The format used is defined in the property `humanReadableFormats`;
except for `today_past` and `today_future` where the format is defined into the method `humanDateToday`.

There are getter and setters for this property :

__`string[] public static getHumanReadableI18n()`__

Return the full array with all values.

__`void public static setHumanReadableI18n(string[] $value)`__

To set a new value for the full array.
Be careful to have all default keys. You can have errors if a used key not exist.

__`void public static setHumanReadableI18nKey(string $key, string $value)`__

To set a new value for a specific key into the array.
If the key not exist, it will create.

#### humanReadableFormats

This property is used to define the date format to use in some cases.

The default value is : 
```php
[
    'dateSameYear'      => 'm-d',
    'dateDifferentYear' => 'Y-m-d',
    'time'              => 'H:i'
];
```

They are the format used for `{date}` and `{time}` values into `humanReadableI18n`; except for the case `today_past` and `today_future`.


There are getter and setters for this property :

__`string[] public static getHumanReadableFormats()`__

Return the full array with all values.

__`void public static setHumanReadableFormats(string[] $value)`__

To set a new value for the full array.
Be careful to have all default keys. You can have errors if a used key not exist.

__`void public static setHumanReadableFormatsKey(string $key, string $value)`__

To set a new value for a specific key into the array.
If the key not exist, it will create.

### Internal system

The method `humanReadable` use internally many methods to format the date.
There is one method by case.

For all methods, the object `$parsedTxt` is an object with properties `date` and `time` who are the final text to use.
It's two properties and not a simple string because the time cannot be returned (cf argument of `humanReadable`).


__`void protected humanDateNow(object $parsedTxt)`__

When the date is just now (same second).

Use the key `now` of `humanReadableI18n`.

__`void protected humanDateToday(object $parsedTxt, \DateInterval $diff)`__

When it's the last or next 24 hours.

Use keys `today_past` and `today_future` of `humanReadableI18n`.<br>
The format for `{time}` can be `1h` or `1min` or `1s`.

__`void protected humanDateTomorrow(object $parsedTxt)`__

When it's after the next 24 hours but always tomorrow day.

Use keys `tomorrow` and `time_part` of `humanReadableI18n`.

__`void protected humanDateYesterday(object $parsedTxt)`__

When it's before the last 24 hours but always yesterday day.

Use keys `yesterday` and `time_part` of `humanReadableI18n`.

__`void protected humanDateOther(object $parsedTxt, \DateTime $current)`__

All other cases.

Use keys `others` and `time_part` of `humanReadableI18n`.

__`bool protected humanDateIsYesterdayOrTomorrow(\DateInterval $diff)`__

To determine if we are in the case of yesterday or tomorrow.

We need a method because `\DateTimeInterval` take some data, but not enough to know if we are always into the day before or after.

__`void protected humanParseDateAndTimeText(object $parsedTxt, string $date, string $time)`__

To parse the date and time text into properties of the `$parsedTxt` object.
The expression `{date}` will be replaced by the `$date` value.
And the expression `{time}` will be replaced by the `$time` value.

### Example

I create a cli script (with bfw-cli module, and into `/src/cli/`) for example.
I add into comments the output.

```php
use \BFW\Helpers\Cli;

$date = new \BFW\Helpers\Dates;
Cli::displayMsgNL($date->format('Y-m-d H:i:s')); //2018-10-05 17:40:03

$date->modify('-5 minutes');
Cli::displayMsgNL($date->format('Y-m-d H:i:s')); //2018-10-05 17:35:03
Cli::displayMsgNL($date->humanReadable()); //5min ago

$date->modify('-2 hours');
Cli::displayMsgNL($date->format('Y-m-d H:i:s')); //2018-10-05 15:35:03
Cli::displayMsgNL($date->humanReadable()); //2h ago

$date->modify('-1 day');
Cli::displayMsgNL($date->format('Y-m-d H:i:s')); //2018-10-04 15:35:03
Cli::displayMsgNL($date->humanReadable()); //yesterday at 15:35

$date->modify('-1 day');
Cli::displayMsgNL($date->format('Y-m-d H:i:s')); //2018-10-03 15:35:03
Cli::displayMsgNL($date->humanReadable()); //the 10-03 at 15:35

$date->modify('-1 month');
Cli::displayMsgNL($date->format('Y-m-d H:i:s')); //2018-09-03 15:35:03
Cli::displayMsgNL($date->humanReadable()); //the 09-03 at 15:35

$date->modify('-1 year');
Cli::displayMsgNL($date->format('Y-m-d H:i:s')); //2017-09-03 15:35:03
Cli::displayMsgNL($date->humanReadable()); //the 2017-09-03 at 15:35
```
