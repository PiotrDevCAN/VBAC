<?php
namespace itdq;

use cFIRST\Auth;

include_once "cFIRST/class/include.php";

/*
 *  Handles Worker API.
 */
class cFIRST {

	private $hostNameBase = null;
	private $hostNameAux = null;
	
	private $accessToken = null;
	private $refreshToken = null;

	public function __construct()
	{
		$auth = new Auth();
		$auth->ensureAuthorized();

		// read auth config
		$cfg = $auth->getConfig();

		$this->hostNameBase = $cfg->base_host;
		$this->hostNameAux = $cfg->aux_host;
		
		$this->accessToken = $_SESSION['cfirst_access_token'];
		$this->refreshToken = $_SESSION['cfirst_refresh_token'];

		// var_dump($this->accessToken).'<br>';
		// var_dump($this->refreshToken).'<br>';
	}

	private function processGET_URL($url = '', $data = null)
	{
		$url = $this->hostNameBase . $url;

		$fields = array(
			'accesstoken' => $this->accessToken
		);
		if ($data) {
			$fields = array_merge($fields, $data);
		}
		$vars = http_build_query($fields);
		$getUrl = $url."?".$vars;

		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $getUrl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
		curl_setopt($ch, CURLOPT_FAILONERROR, TRUE);
		
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	
		$headers = array();
		$headers[] = 'Content-Type: application/json';
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	
		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		}
		curl_close($ch);

		$result = json_decode($result, true);
	
		return $result;
	}

	private function processPOST_URL($url = '', $data = null)
	{
		$url = $this->hostNameBase . $url;

		$ch = curl_init();
    
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
		curl_setopt($ch, CURLOPT_FAILONERROR, TRUE);
		
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	
		$headers = array();
		$headers[] = 'Content-Type: application/json';
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	
		$result = curl_exec($ch);
	
		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		}
		curl_close($ch);
		
		$result = json_decode($result, true);
	
		return $result;
	}

	public function getPackages()
	{
		// Method: Get
		// Data:
		// accesstoken – “Valid Access Token”

		$url = "/GetPackages";
		$response = $this->processGET_URL($url);

		$return = false;
		if (is_array($response)) {
			if (array_key_exists('BGVErrors', $response) && is_array($response['BGVErrors'])) {
				$error = $response['BGVErrors'][0];
				trigger_error("cFIRST ".__CLASS__." ".__METHOD__." ".$error['Errordescription']." ".$error['Errorcode']." ".$error['Timestamp'], E_USER_WARNING);
			} else {
				$return = $response;
			}
		}
		return $return;
	}

	function prepareCandidateData($data) {
		$dataStr = json_encode($data);
		$dataStrURLEnc = urlencode('['.$dataStr.']');
		return $dataStrURLEnc;
	}

	public function addCandidate($candidate = '')
	{
		// POST
		// POST: http://developer.api.cfirstcorp.com/v1/IBgvCandidate.svc/json/AddCandidate
			// Post Data: Candidate & accesstoken
			// Candidate: URL Encoded JSON String having Candidate Object Data
			// accesstoken: Valid Access Token received from the refresh token request
		
		$url = "/AddCandidate";

		$candidateDataURLEncoded = $this->prepareCandidateData($candidate);

		$data = array(
			'accesstoken' => $this->accessToken,
			'Candidate' => $candidateDataURLEncoded
		);
		$dataJson = json_encode($data);
		$response = $this->processPOST_URL($url, $dataJson);

echo '<pre>';
var_dump($response);
echo '</pre>';

		$return = false;
    	if (is_array($response)) {
			if (array_key_exists('AddCandidateResult', $response) && is_array($response['AddCandidateResult'])) {
				$result = $response['AddCandidateResult'][0];
				
				if (array_key_exists('BGVErrors', $result) && is_array($result['BGVErrors'])) {
					$error = $result['BGVErrors'][0];
					trigger_error("cFIRST ".__CLASS__." ".__METHOD__." ".$error['Errordescription']." ".$error['Errorcode']." ".$error['Timestamp'], E_USER_WARNING);
				} else {
					
					// $result['APIReferenceCode'];
					// $result["CandidateId"];
					// $result["Sucess"];
					// $result["Sucesscode"];
					// $result["Sucessdescription"];
					// $result["Timestamp"];

					$return = $result;
				}
			}
		}
		return $return;
	}

	public function getCandidateStatus($id)
	{
		// Host: http://developer.api.cfirstcorp.com/v1/IBgvCandidate.svc/json/GetCandidateStatus
		// Method: Get
		// Data:
		// accesstoken – “Valid Access Token”
		// candidateId – if passed, only that candidate’s status will be provided, else status of all candidates will be provided
		// date – if passed, only status changed after that date will be provided, else all data will be provided.
	
		$url = "/GetCandidateStatus";
		$data = array(
			'candidateId' => $id
		);
		$response = $this->processGET_URL($url, $data);
		
		$return = false;
    	if (is_array($response)) {
			if (array_key_exists('BGVErrors', $response) && is_array($response['BGVErrors'])) {
				$error = $response['BGVErrors'][0];
				trigger_error("cFIRST ".__CLASS__." ".__METHOD__." ".$error['Errordescription']." ".$error['Errorcode']." ".$error['Timestamp'], E_USER_WARNING);
			} else {
				$return = $response;
			}
		}
		return $return;
	}

	public function getBackgroundCheckRequestList($fromDate = null, $toDate = null, $start = '1', $email = null, $refCode = null, $name = null)
	{
		// URL: <BaseURL>/GetBackgroundCheckRequestList?AccessToken={{AccessToken}}

		// Method: POST
		
		// Query String Parameter: AccessToken – valid access token obtained through the authentication step
		
		// Post Data:
		// {
		//   "FromDate": "2023-11-16",
		//   "ToDate": "2023-11-17",
		//   "StartPage": "1",
		//   "Email": "",
		//   "APIReferenceCode": "",
		//   "ProfileName": ""
		// }
		
		// •	From Date & To Date: The system will retrieve records initiated between these two dates.
		// •	Start Page: Page number to fetch records, starting from 1 (each request fetching 50 records).
		// •	Email: The system will attempt to fetch records matching the candidate's email address.
		// •	APIReferenceCode: Unique code of the candidate sent through the APIReferenceCode field in the Add Candidate API for which data needs to be retrieved.
		// •	Profile Name: The system will try to fetch records matching the name.
		
		$url = "/GetBackgroundCheckRequestList?AccessToken=".$this->accessToken;

		$data = array(
			"FromDate" => $fromDate,
			"ToDate" => $toDate,
			"StartPage" => $start,
			"Email" => $email,
			"APIReferenceCode" => $refCode,
			"ProfileName" => $name
		);
		$dataJson = json_encode($data);
		$response = $this->processPOST_URL($url, $dataJson);

		return $response;
		// echo '<pre>';
		// var_dump($response);
		// echo '</pre>';
	}
}