uptimeRobot
===========

This is a PHP wrapper for https://uptimerobot.com/api

## Example

PHP Class for UptimeRobot-API

```PHP
require('vendor/autoload.php');

$robot = new \Mave\UptimeRobot\UptimeRobot('YOUR_API_KEY', UptimeRobot::FORMAT_JSON);

/**
 * Get all monitors
 */
try
{
    $all = $robot->getMonitors();
    print_r($all);
}
catch (Exception $e)
{
    echo $e->getMessage();
}

/**
 * Get status of one monitor by her id
 */
try
{
    $monitor = $robot->getMonitors(0000);
    echo $monitor->monitors->monitor[0]->status;
}
catch (Exception $e)
{
    echo $e->getMessage();
}

/**
 * Get one monitor with all parameters
 */
try
{
    $request = new \Mave\UptimeRobot\Request();
    $request->setCustomUptimeRatio([1, 7]);
    $request->setShowMonitorAlertContacts(true);
    $request->setLogs(true);
    $request->setResponseTimes(true);
    $request->setResponseTimesAverage(180);
    $request->setAlertContacts(true);
    $request->setShowMonitorAlertContacts(true);
    $request->setShowTimezone(true);

    $robot->setRequest($request);
    $monitor2 = $robot->getMonitors();
    print_r($monitor2);
}
catch (Exception $e)
{
    echo $e->getMessage();
}

```
## Authors

Wesley Buurke - https://www.wesleybuurke.nl/

Original Class: Watchful - https://watchful.li/

Original Class: Mark Boomaars - https://github.com/CodingOurWeb/PHP-wrapper-for-UptimeRobot-API
