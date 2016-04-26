<?php
require("vendor/autoload.php");

use \Mave\UptimeRobot\UptimeRobot;
use \Mave\UptimeRobot\Request;

define('UPTIMEROBOT_API_KEY', '');

/**
 * Examples of how to use Ma-ve/UptimeRobot
 * 
 * Before use, replace all instances of '0000' with an actual monitor ID.
 * 
 * @version     1.0
 * @author      Wesley Buurke
 * @authorUrl   https://www.wesleybuurke.nl/
 * @license     GNU General Public License version 2 or later
 * @info		Based on code by https://www.github.com/watchfulli/uptimeRobot
 */

$robot = new UptimeRobot(UPTIMEROBOT_API_KEY, UptimeRobot::FORMAT_JSON);

/**
 * Get monitors
 */
$monitors_simple = $robot->getMonitors();
var_dump($monitors_simple->monitors->monitor); //Get all monitors, default values

$request = new Request();
$request->setCustomUptimeRatio([1, 7, 30, 90]);
$request->setShowMonitorAlertContacts(true);
$robot->setRequest($request);
$monitors_medium = $robot->getMonitors();
var_dump($monitors_medium->monitors->monitor); //Get all monitors, with additional info

/**
 * New monitor
 */
$robot->newMonitor("Google", 'https://google.com', 1); //Monitor URL
$robot->newMonitor("The W of Wikipedia", 'http://fr.wikipedia.org', 2, null, null, 2, 'W'); //Check word on a page
$robot->newMonitor("Ping DNS of Google", '8.8.8.8', 3); //Ping IP
$robot->newMonitor("Check custom port", 'exemple.com', 4, 99, 22); //Check custom port
$robot->newMonitor('Watchful.li', 'https://watchful.li', $type = 1, $subType = 2, $port = null, $keywordType = null, $keywordValue = null, $HTTPUsername = null, $HTTPPassword = null, $alertContacts = null); //All parameters

/**
 * Edit monitor
 */
$robot->editMonitor($monitorId = 0000, $monitorStatus = null, $friendlyName = 'Edit name of monitor'); //Edit name
$robot->editMonitor($monitorId = 0000, $monitorStatus = null, $friendlyName = null, $URL = null, $subType = 99, $port = 25); //Edit port of monitor
$robot->editMonitor($monitorId = 0000, $monitorStatus = null, $friendlyName = null, $URL = null, $subType = null, $port = null, $keywordType = null, $keywordValue = null, $HTTPUsername = null, $HTTPPassword = null, $alertContacts = null); //All parameters

/**
 * Delete monitor
 */
$robot->deleteMonitor(0000); //Delete monitor

/**
 * Get alert contacts
 */
$robot->getAlertContacts(); //Get all contacts
$robot->getAlertContacts(0000); //Get one contact by her id

/**
 * New alert Contact
 */
$robot->newAlertContact(2, 'YOUR-EMAIL'); //Create new alert contact

/**
 * Delete alert Contact
 */
$robot->deleteAlertContact(0000); //Delete alert contact
