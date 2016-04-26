<?php
/**
 * See @link https://uptimerobot.com/api#getMonitors for more info on these parameters
 */
namespace Mave\UptimeRobot;

class Request
{
	private $monitors = [],
		$types = [],
		$statuses = [],
		$customUptimeRatio = [],
		$logs = false,
		$logsLimit = 0,
		$responseTimes = false,
		$responseTimesLimit = 0,
		$responseTimesAverage = 0,
		$responseTimesStartDate = null,
		$responseTimesEndDate = null,
		$alertContacts = false,
		$showMonitorAlertContacts = false,
		$showTimezone = false,
		$search = '';

	public function getMonitors() {
		return $this->monitors;
	}

	public function setMonitors($monitors = []) {
		$this->monitors = $monitors;
	}

	public function setTypes($types = []) {
		$this->types = $types;
	}

	public function getTypes() {
		return $this->types;
	}

	public function setStatuses($statuses = []) {
		$this->statuses = $statuses;
	}

	public function getStatuses() {
		return $this->statuses;
	}

	public function setCustomUptimeRatio($customUptimeRatio = []) {
		$this->customUptimeRatio = $customUptimeRatio;
	}

	public function getCustomUptimeRatio() {
		return $this->customUptimeRatio;
	}

	public function setLogs($logs = false) {
		$this->logs = $logs;
	}

	public function getLogs() {
		return $this->logs;
	}

	public function setLogsLimit($logsLimit = 0) {
		$this->logsLimit = $logsLimit;
	}

	public function getLogsLimit() {
		return $this->logsLimit;
	}

	public function setResponseTimes($responseTimes = false) {
		$this->responseTimes = $responseTimes;
	}

	public function getResponseTimes() {
		return $this->responseTimes;
	}

	public function setResponseTimesLimit($responseTimesLimit = 0) {
		$this->responseTimesLimit = $responseTimesLimit;
	}

	public function getResponseTimesLimit() {
		return $this->responseTimesLimit;
	}

	public function setResponseTimesAverage($responseTimesAverage = 0) {
		$this->responseTimesAverage = $responseTimesAverage;
	}

	public function getResponseTimesAverage() {
		return $this->responseTimesAverage;
	}

	public function setResponseTimesStartDate($responseTimesStartDate = null) {
		$this->responseTimesStartDate = $responseTimesStartDate;
	}

	public function getResponseTimesStartDate() {
		return $this->responseTimesStartDate;
	}

	public function setResponseTimesEndDate($responseTimesEndDate = null) {
		$this->responseTimesEndDate = $responseTimesEndDate;
	}

	public function getResponseTimesEndDate() {
		return $this->responseTimesEndDate;
	}

	public function setAlertContacts($alertContacts = false) {
		$this->alertContacts = $alertContacts;
	}

	public function getAlertContacts() {
		return $this->alertContacts;
	}

	public function setShowMonitorAlertContacts($showMonitorAlertContacts = false) {
		$this->showMonitorAlertContacts = $showMonitorAlertContacts;
	}

	public function getShowMonitorAlertContacts() {
		return $this->showMonitorAlertContacts;
	}

	public function setShowTimezone($showTimezone = false) {
		$this->showTimezone = $showTimezone;
	}

	public function getShowTimezone() {
		return $this->showTimezone;
	}

	public function setSearch($search = null) {
		$this->search = $search;
	}

	public function getSearch() {
		return $this->search;
	}

}
