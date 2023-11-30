<?php
namespace itdq;

use WorkerApi\Auth;

include_once "WorkerAPI/class/include.php";

/*
 *  Handles Worker API.
 */
class WorkerAPI {
	
	private $auth = null;
	private $token = null;
	private $hostname = null;

	public function __construct()
	{
		$auth = new Auth();
		$this->auth = $auth;

		$auth->ensureAuthorized();

		$this->hostname = trim($_ENV['worker_api_host']);
		$this->token = $_SESSION['worker_token'];
	}

	private function createCurl($type = "GET")
	{
		// create a new cURL resource
		$ch = curl_init();
		$authorization = "Authorization: Bearer ".$this->token; // Prepare the authorisation token
		$headers = [
			'Content-type: Not defined',
			'Accept: application/json, text/json, application/xml, text/xml',
			$authorization,
		];
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		return $ch;
	}

	private function processURL($url, $type = 'GET')
	{
		// make sure script is logged in
		$authorized = $this->auth->ensureAuthorized();
		error_log('is authorized '.$authorized);

		$url = $this->hostname . $url;
		$ch = $this->createCurl($type);
		// echo "<BR>Processing";
		// echo " URL:" . $url;
		$ret = curl_setopt($ch, CURLOPT_URL, $url);
		$ret = curl_exec($ch);

		$result = json_decode($ret, true);

		if (empty($ret)) {
			// some kind of an error happened
			// die(curl_error($ch));
			curl_close($ch); // close cURL handler
		} else {
			$info = curl_getinfo($ch);
			if (empty($info['http_code'])) {
				// die("No HTTP code was returned");
			} else if ($info['http_code'] == 500) {
				echo $result['message'];
			} else {
				// So Bluegroups has processed our URL - What was the result.
				$bgapiRC  = substr($ret,0,1);
				if($bgapiRC!=0){
					// Bluegroups has NOT returned a ZERO - so there was a problem
					echo "<H3>Error processing Bluegroup URL </H3>";
					echo "<H2>Please take a screen print of this page and send to the ITDQ Team ASAP.</H2>";
					echo "<BR>URL<BR>";
					print_r($url);
					echo "<BR>Info<BR>";
					print_r($info);
					echo "<BR>";
					exit ("<B>Unsuccessful RC: $ret</B>");
				} else {
					// echo " Successful RC: $ret";
					sleep(1); // Give BG a chance to process the request.
				}
			}
		}
		return $result;
	}

	public function validateData($data)
	{
		if (
			is_array($data)
			&& array_key_exists('count', $data)
			&& $data['count'] > 0
		) {
			return true;
		} else {
			return false;
		}
	}

	public function getRecord($data)
	{
		$notFound = 'not found';
		$record = $data['results'][0];
		if (!array_key_exists('workerID', $record)) {
			$record['workerID'] = $notFound;
		}
		if (!array_key_exists('businessTitle', $record)) {
			$record['businessTitle'] = $notFound;
		}
		if (!array_key_exists('matrixManagerEmail', $record)) {
			$record['matrixManagerEmail'] = $notFound;
		}
		return $record;
	}
	
	// Individual Worker Profile Data
	// Below endpoints will return extended worker data model

	// Worker Profile By Worker Id : GET /workers/wid/{wid}
	public function getworkerByWId($wid)
	{
		$url = "/workers/wid/" . urlencode($wid);
		return $this->processURL($url);
	}

	// Worker Profile By CNUM : GET /workers/cnum/{cnum}
	public function getworkerByCNUM($cnum)
	{
		$url = "/workers/cnum/" . urlencode($cnum);
		return $this->processURL($url);
	}

	// Worker Profile By Email : GET /workers/email/{email}
	public function getworkerByEmail($email)
	{
		$url = "/workers/email/" . urlencode($email);
		return $this->processURL($url);
	}

	// Worker Profile By Dynamic Id : GET /workers/dynamicid/{Dynamic_id}
	public function getworkerByDynamicId($dynamicId)
	{
		$url = "/workers/dynamicid/" . urlencode($dynamicId);
		return $this->processURL($url);
	}

	// Manager Profile By Dynamic Id : GET /workers/dynamicid/{Dynamic_id}/manager
	public function getManager($dynamicId)
	{
		$url = "/workers/dynamicid/" . urlencode($dynamicId) . "/manager";
		return $this->processURL($url);
	}

	public function getReports($dynamicId)
	{
		$url = "/workers/dynamicid/" . urlencode($dynamicId) . "/reports";
		return $this->processURL($url);
	}

	public function getPhoto($dynamicId)
	{
		$url = "/workers/dynamicid/" . urlencode($dynamicId) . "/photo";
		return $this->processURL($url);
	}

	// Worker Profile By Attribute Search : GET /workers/search/
	/*
		attr	String	attribute from Basic Worker data model	id should match one
		val	String	Any value or an array	Array format ["val1","val2"]
		method	String	eq, in, ne, startsWith	equals, in (used for arrays), not equals, starting with
		includeManager	boolean	false / true	Optional parameter, default value is false. Include manager objects within employees' record.
		pageSize	int	number of returned employees	Optional parameter, default value is 100. The value should be between 1 and 999.
	*/
	public function getworkerByAttributeSearch($search)
	{
		$url = "/workers/search?" . urlencode($search);
		return $this->processURL($url);
	}

	// Worker Profile By Multi Attribute Search : POST /workers/search/
	/*
		includeManager	boolean	false / true	Optional parameter, default value is false. Include manager objects within employees' record.
		pageSize	int	number of returned employees	Optional parameter, default value is 100. The value should be between 1 and 999.
	*/
	public function getworkerByMultiAttributeSearch($search)
	{
		$url = "/workers/search";
		return $this->processURL($url, 'POST');
	}

	// Get list of workers by their (partial) name or email address. Only active employees are returned.
	/*
		keyword	String	partial employee name / email address	length should be at least 2 characters
		pageSize (optional)	int	number of returned employees	Default value: 20
		attributes (optional)	String	additional fields requested within response	Available attributes: isActive, firstName, lastName, businessTitle, displayName, mobilePhone, workPhone, costCenter, division, workLoc, usageLocation, countryName, workplaceIndicator, employeeType, orgCode, matrixManagerEmail, isManager, faxNumber
		nextPageToken (optional)	String	token for accessing next page	Response contains nextPageToken attribute if the query returns more employees than the pageSize parameter. If this parameter is provided, then the next page of results will be returned by the endpoint.
	*/
	public function typeaheadSearch($keyword, $pageSize = null, $attributes = null, $nextPageToken = null)
	{
		$attributes = "isActive, firstName, lastName, businessTitle, displayName, mobilePhone, workPhone, costCenter, division, workLoc, usageLocation, countryName, workplaceIndicator, employeeType, orgCode, matrixManagerEmail, isManager, faxNumber";
		$url = "/workers/typeahead?keyword=" . urlencode($keyword) . "&attributes=" . urlencode($attributes);		
		return $this->processURL($url);
	}

	// Get list of workers by their (partial) name or email address. Within the request body filter criterias can be included, this way the typeahead search will be performed only on the filtered group of employees. Examples may include searching employees within specific country (usageLocation), or costCenter, etc.. By default only active employees are returned.
	/*
		keyword	String	partial employee name / email address	length should be at least 2 characters
		pageSize (optional)	int	number of returned employees	Default value: 20
		attributes (optional)	String	additional fields requested within response	Available attributes: isActive, firstName, lastName, businessTitle, displayName, mobilePhone, workPhone, costCenter, division, workLoc, usageLocation, countryName, workplaceIndicator, employeeType, orgCode, matrixManagerEmail, isManager, faxNumber
		nextPageToken (optional)	String	token for accessing next page	Response contains nextPageToken attribute if the query returns more employees than the pageSize parameter. If this parameter is provided, then the next page of results will be returned by the endpoint.
	*/
	public function typeaheadSearchWithFilter($keyword, $pageSize = null, $attributes = null, $nextPageToken = null)
	{
		$url = "/workers/typeahead";
		// $data = 
		return $this->processURL($url, 'POST');
	}
	
	// Direct Reports Profile Data
	// To return a worker's direct reports data alone, the below endpoint will return an array of basic worker profiles for all direct reports.

	// Reports Profile By Dynamic Id : GET /workers/dynamicid/{Dynamic_id}/reports
	public function getReportsFromDynamicId($dynamicId)
	{
		$url = "/workers/dynamicid/" . urlencode($dynamicId) . "/reports";
		return $this->processURL($url);
	}

	// Bulk Data/ File Cache Return
	// To return a larger object of bulk data from the larger worker collection, the below endpoint will query a periodically-updated flat file.

	// Download Worker File : GET /tools/download_worker_file
	public function getWorkerFile()
	{
		$url = "/tools/download_worker_file";
		return $this->processURL($url);
	}

	// Delta File Cache Return
	// Return any delta in data including added, updated, deleted worker records at set intervals.

	// Download Worker File Delta : GET /tools/download_worker_file_delta
	public function getWorkerFileDelta()
	{
		$url = "/tools/download_worker_file_delta";
		return $this->processURL($url);
	}

	public function getIntranetIdFromNotesId($notesId = '')
	{
		$employeeData = explode('/', $notesId);
		$data = $this->typeaheadSearch($employeeData[0]);
		if (
			is_array($data)
			&& array_key_exists('count', $data)
			&& $data['count'] > 0
		) {
			$intranetId = $data['results'][0]['email'];
		} else {
			$intranetId = ' not found ';
		}
		return $intranetId;
	}
}