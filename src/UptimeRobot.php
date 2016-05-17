<?php

namespace Mave\UptimeRobot;

/**
 * This project is an open source implementation for acessing the UptimeRobot api.
 * Full documentation: http://uptimerobot.com/api
 *
 * @version     1.1
 * @date        2016-04-26
 * @author      Ma-ve
 * @filesource  From mb2o <https://github.com/CodingOurWeb/PHP-wrapper-for-UptimeRobot-API>
 * @filesource  From watchfulli <https://github.com/watchfulli/uptimeRobot>
 * @license     GNU General Public License version 2 or later
 */
class UptimeRobot
{
	/**
	 * Constants
	 */
	// Format types
	const FORMAT_JSON = 'json';
	const FORMAT_XML = 'xml';
	const FORMAT_JSONP = 'jsonp';
	
	// API
	const API_BASE_URI = 'https://api.uptimerobot.com';

	private static $request = null;
	/**
	 * API key
	 * @var string
	 */
	private $apiKey;

	/**
	 * Format for API responses
	 * @var string
	 */
	private $format = self::FORMAT_JSON;

	/**
	 * Whether or not response should be wrapper in JSONP callback
	 * @var int
	 */
	private $noJsonCallback = 1;

	/**
	 * Set your API key
	 *
	 * @param string $apiKey require   Set your main API Key or Monitor-Specific API Keys (only getMonitors)
	 * @param string $format optional  Define if the function wrapper to be removed
	 *
	 */
	public function __construct($apiKey, $format = self::FORMAT_JSON)
	{
		$this->apiKey = $apiKey;
		$this->format = $format;
		if($format === self::FORMAT_JSONP) {
			$this->noJsonCallback = 0;
		}
	}

	/**
	 * @param Request $request
	 */
	public function setRequest(Request $request) {
		self::$request = $request;
	}

	/**
	 * @return Request
	 */
	public function getRequest() {
		return self::$request;
	}

	/**
	 * Validate request parameter combinations
	 * @throws \Exception
	 */
	private function validateRequest() {
		if($this->getRequest()->getAlertContacts() && !$this->getRequest()->getLogs()) {
			throw new \Exception('alertContacts requires logs to be true');
		}

		return $this->validateResponseTimesDates();
	}

	/**
	 * Validate ResponseTimes(Start|End)Dates. Must both be empty, or must both be set
	 * @return bool
	 * @throws \Exception
	 */
	private function validateResponseTimesDates() {
		$url = '';
		$start_date = $this->getRequest()->getResponseTimesStartDate();
		$end_date = $this->getRequest()->getResponseTimesEndDate();

		if($start_date xor $end_date) {
			throw new \Exception('Must use neither or both responseTimesStartDate and responseTimesEndDate');
		}

		if($start_date == '' || $end_date = '') {
			return '';
		}

		$startDate = new \DateTime($start_date);
		$endDate = new \DateTime($end_date);

		if($startDate > $endDate) {
			throw new \Exception('Start date cannot be later than end date');
		}

		if($endDate->diff($startDate)->format('%a') > 7) {
			throw new \Exception('Difference between start and end date is more than 7 days');
		}

		$url .= '&responseTimesStartDate=' . $start_date;
		$url .= '&responseTimesEndDate=' . $end_date;

		return $url;
	}

	/**
	 * Validate arrays and return them as imploded values
	 * @return string
	 */
	private function validateArrays() {
		$url = '';
		$arrays = ['monitors', 'types', 'statuses', 'customUptimeRatio',];
		foreach($arrays as $array) {
			$function = 'get' . ucfirst($array);
			$value = $this->getRequest()->$function();
			if(!empty($value)) {
				$url .= '&' . $array . '=' . $this->getImplode($value);
			}
		}

		return $url;
	}

	/**
	 * Validate and return booleans casted to ints (0/1)
	 * @return string
	 */
	private function validateBools() {
		$url = '';
		$bools = ['logs', 'responseTimes', 'alertContacts', 'showMonitorAlertContacts', 'showTimezone',];
		foreach($bools as $bool) {
			$function = 'get' . ucfirst($bool);
			if($value = $this->getRequest()->$function()) {
				$url .= '&' . $bool . '=' . (int) $value;
			}
		}

		return $url;
	}

	/**
	 * Validate and return integers
	 * @return string
	 */
	private function validateInts() {
		$url = '';
		$ints = ['responseTimesLimit', 'responseTimesAverage',];
		foreach($ints as $int) {
			$function = 'get' . ucfirst($int);
			if($value = $this->getRequest()->$function()) {
				$url .= '&' . $int . '=' . (int) $value;
			}
		}

		return $url;
	}

	/**
	 * Validate and return search
	 * @return string
	 */
	private function validateSearch() {
		$url = '';
		$search = $this->getRequest()->getSearch();
		if(is_string($search) && !empty($search)) {
			$url .= '&search=' . $search;
		}

		return $url;
	}

	/**
	 * @link https://uptimerobot.com/api#getMonitors
	 * This is a Swiss-Army knife type of a method for getting any information on monitors
	 * Please see @link for more info
	 *
	 * This method uses a private static instance of @see \Mave\UptimeRobot\Request to create the API request
	 *
	 * @return bool|mixed
	 * @throws \Exception
	 */
	public function getMonitors() {
		$url = self::API_BASE_URI . '/getMonitors?1=1';

		if(!is_null($this->getRequest())) {
			$url .= $this->validateRequest();
			$url .= $this->validateArrays();
			$url .= $this->validateBools();
			$url .= $this->validateInts();
			$url .= $this->validateSearch();
		}

		$result = $this->__fetch($url);
		$limit = $result->limit;
		$offset = $result->offset;
		$total = $result->total;

		while(($limit * $offset) + $limit < $total) {
			$result->limit = ($limit * $offset) + $limit;
			$offset++;
			$append = $this->__fetch($url . '&offset=' . ($offset * $limit));
			$result->monitors->monitor = array_merge($result->monitors->monitor, $append->monitors->monitor);
		}
		$result->limit = ($limit * $offset) + $limit;

		return $result;
	}

	/**
	 * Array or int to string with separator (-)
	 *
	 * @param array|int $var
	 * @return string
	 */
	private function getImplode($var)
	{
		if(is_array($var)) {
			return implode('-', $var);
		}

		return $var;
	}

	/**
	 * Returns the result of the API calls
	 *
	 * @param mixed $url required
	 * @return bool|mixed
	 * @throws \Exception
	 */
	private function __fetch($url)
	{
		if(empty($url)) {
			throw new \Exception('Value not specified: url', 1);
		}

		if(preg_match("/\?/", $url)) {
			$url .= '&apiKey=' . $this->getApiKey();
		} else {
			$url .= '?apiKey=' . $this->getApiKey();
		}

		$url .= '&format=' . $this->format;
		$url .= '&noJsonCallback=' . $this->noJsonCallback;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		$file_contents = curl_exec($ch);
		curl_close($ch);

		if($this->format == 'xml') {
			return $file_contents;
		} else {
			if($this->noJsonCallback) {
				$contents = json_decode($file_contents);
				if($contents->stat === 'fail') {
					throw new \Exception('Error ID ' . $contents->id . ': ' . $contents->message);
				}
				return $contents;
			} else {
				return $file_contents;
			}
		}
	}

	/**
	 * Returns the API key
	 *
	 */
	public function getApiKey()
	{
		if(empty($this->apiKey)) {
			throw new \Exception('Value not specified: apiKey');
		}

		return $this->apiKey;
	}

	/**
	 * New monitors of any type can be created using this method
	 *
	 * @param string 	$friendlyName 		required
	 * @param string 	$URL 				required
	 * @param int 		$type 				required
	 * @param int 		$subType			optional (required for port monitoring)
	 * @param int 		$port 				optional (required for port monitoring)
	 * @param int 		$keywordType 		optional (required for keyword monitoring)
	 * @param string 	$keywordValue 		optional (required for keyword monitoring)
	 * @param string 	$HTTPUsername 		optional
	 * @param string 	$HTTPPassword 		optional
	 * @param array|int $alertContacts 		optional The alert contacts to be notified Multiple alertContactIDs can be sent
	 * @param int|string $monitorInterval 	optional interval in min
	 * @return bool|mixed
	 * @throws \Exception
	 */
	public function newMonitor($friendlyName, $URL, $type, $subType = null, $port = null, $keywordType = null, $keywordValue = null, $HTTPUsername = null, $HTTPPassword = null, $alertContacts = null, $monitorInterval = 5)
	{
		if(empty($friendlyName) || empty($URL) || empty($type)) {
			throw new \Exception('Required key "name", "uri" or "type" not specified', 3);
		}

		$friendlyName = urlencode($friendlyName);
		$url = self::API_BASE_URI . '/newMonitor?monitorFriendlyName=' . $friendlyName . '&monitorURL=' . $URL . '&monitorType=' . $type;

		if(!empty($subType)) {
			$url .= '&monitorSubType=' . $subType;
		}
		if(!empty($port)) {
			$url .= '&monitorPort=' . $port;
		}
		if(isset($keywordType)) {
			$url .= '&monitorKeywordType=' . $keywordType;
		}
		if(isset($keywordValue)) {
			$url .= '&monitorKeywordValue=' . urlencode($keywordValue);
		}
		if(isset($HTTPUsername)) {
			$url .= '&monitorHTTPUsername=' . urlencode($HTTPUsername);
		}
		if(isset($HTTPPassword)) {
			$url .= '&monitorHTTPPassword=' . urlencode($HTTPPassword);
		}
		if(!empty($alertContacts)) {
			$url .= '&monitorAlertContacts=' . $this->getImplode($alertContacts);
		}
		if(!empty($monitorInterval)) {
			$url .= '&monitorInterval=' . $monitorInterval;
		}

		return $this->__fetch($url);
	}

	/**
	 * Monitors can be edited using this method.
	 *
	 * Important: The type of a monitor can not be edited (like changing a HTTP monitor into a Port monitor).
	 * For such cases, deleting the monitor and re-creating a new one is adviced.
	 *
	 * @param int $monitorId required
	 * @param bool $monitorStatus optional
	 * @param string $friendlyName optional
	 * @param string $URL optional
	 * @param int $subType optional (used only for port monitoring)
	 * @param int $port optional (used only for port monitoring)
	 * @param int $keywordType optional (used only for keyword monitoring)
	 * @param string $keywordValue optional (used only for keyword monitoring)
	 * @param string $HTTPUsername optional (in order to remove any previously added username, simply send the value empty)
	 * @param string $HTTPPassword optional (in order to remove any previously added password, simply send the value empty)
	 * @param array|int $alertContacts optional   The alert contacts to be notified Multiple alertContactIDs can be sent
	 *                                              (in order to remove any previously added alert contacts, simply send the value empty like '')
	 * @return bool|mixed
	 */
	public function editMonitor($monitorId, $monitorStatus = null, $friendlyName = null, $URL = null, $subType = null, $port = null, $keywordType = null, $keywordValue = null, $HTTPUsername = null, $HTTPPassword = null, $alertContacts = null)
	{

		$url = self::API_BASE_URI . '/editMonitor?monitorID=' . $monitorId;

		if(isset($monitorStatus)) {
			$url .= '&monitorStatus=' . $monitorStatus;
		}
		if(isset($friendlyName)) {
			$url .= '&monitorFriendlyName=' . urlencode($friendlyName);
		}
		if(isset($URL)) {
			$url .= '&monitorURL=' . $URL;
		}
		if(isset($subType)) {
			$url .= '&monitorSubType=' . $subType;
		}
		if(isset($port)) {
			$url .= '&monitorPort=' . $port;
		}
		if(isset($keywordType)) {
			$url .= '&monitorKeywordType=' . $keywordType;
		}
		if(isset($keywordValue)) {
			$url .= '&monitorKeywordValue=' . urlencode($keywordValue);
		}
		if(isset($HTTPUsername)) {
			$url .= '&monitorHTTPUsername=' . urlencode($HTTPUsername);
		}
		if(isset($HTTPPassword)) {
			$url .= '&monitorHTTPPassword=' . urlencode($HTTPPassword);
		}
		if(!empty($alertContacts)) {
			$url .= '&monitorAlertContacts=' . $this->getImplode($alertContacts);
		}

		return $this->__fetch($url);
	}

	/**
	 * Monitors can be deleted using this method.
	 *
	 * @param int $monitorId required
	 * @return bool|mixed
	 * @throws \Exception
	 */
	public function deleteMonitor($monitorId)
	{
		if(empty($monitorId)) {
			throw new \Exception('Value not specified: monitorId', 1);
		}

		$url = self::API_BASE_URI . '/deleteMonitor?monitorID=' . $monitorId;

		return $this->__fetch($url);
	}

	/**
	 * The list of alert contacts can be called with this method.
	 *
	 * @param array|int $alertcontacts optional    if not used, will return all alert contacts in an account.
	 *                                              Else, it is possible to define any number of alert contacts with their IDs
	 * @return bool|mixed
	 */
	public function getAlertContacts($alertcontacts = null)
	{
		$url = self::API_BASE_URI . '/getAlertContacts';

		if(!empty($alertcontacts)) {
			$url .= '?alertcontacts=' . $this->getImplode($alertcontacts);
		}

		return $this->__fetch($url);
	}

	/**
	 * New alert contacts of any type (mobile/SMS alert contacts are not supported yet) can be created using this method.
	 *
	 * @param int $alertContactType required
	 * @param string $alertContactValue required
	 * @return bool|mixed
	 * @throws \Exception
	 */
	public function newAlertContact($alertContactType, $alertContactValue)
	{
		if(empty($alertContactType) || empty($alertContactValue)) {
			throw new \Exception('Required params "$alertContactValue" or "$alertContactValue" not specified', 3);
		}

		$alertContactValue = urlencode($alertContactValue);

		$url = self::API_BASE_URI . '/newAlertContact?alertContactType=' . $alertContactType . '&alertContactValue=' . $alertContactValue;

		return $this->__fetch($url);
	}

	/**
	 * Alert contacts can be deleted using this method.
	 *
	 * @param int $alertContactID required
	 * @return bool|mixed
	 * @throws \Exception
	 */
	public function deleteAlertContact($alertContactID)
	{
		if(empty($alertContactID)) {
			throw new \Exception('Required params "$alertContactID" not specified', 3);
		}

		$url = self::API_BASE_URI . '/deleteAlertContact?alertContactID=' . $alertContactID;

		return $this->__fetch($url);
	}

}
