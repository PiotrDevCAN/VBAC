<?php
namespace itdq;

/*
 *  Handles OKTA Users.
 */
class OKTAUsers {

	private $token = null;
	private $hostname = null;

	private $url = null;

	private $redis = null;

	public function __construct()
	{
		$oAuthPrefix = '/oauth2/v1';
		$envHostName = trim($_ENV['sso_host']);

		$this->hostname = str_replace($oAuthPrefix, '', $envHostName);
		$this->token = trim($_ENV['sso_api_token']);
		
		$this->redis = $GLOBALS['redis'];
	}

	private function createCurl($type = "GET")
	{
		// create a new cURL resource
		$ch = curl_init();
		$authorization = "Authorization: SSWS ".$this->token; // Prepare the authorisation token
		$headers = [
			'Content-Type: application/json',
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
		$url = $this->hostname . $url;
		$ch = $this->createCurl($type);
		// echo "<BR>Processing";
		// echo " URL:" . $url;
		$ret = curl_setopt($ch, CURLOPT_URL, $url);
		$ret = curl_exec($ch);

		$info = curl_getinfo($ch);

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
					echo "<H3>Error processing OKTA Groups URL </H3>";
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

	/*
	* User operations
	*/

	/*
	* Create User
	*/

	// public function createUser()
	// {

	// }

	/*
	* Get User
	*/

	public function getCurrentUser()
	{
		$url = "/api/v1/users/me";
		return $this->processURL($url, 'GET');
	}

	public function getUserWithID($id)
	{
		$url = "/api/v1/users/$id";
		return $this->processURL($url, 'GET');
	}

	public function getUserWithLogin($email)
	{
		$login = urlencode($email);
		$url = "/api/v1/users/$login";
		return $this->processURL($url, 'GET');
	}

	public function getUserWithLoginShortname($shortName)
	{
		$name = urlencode($shortName);
		$url = "/api/v1/users/$name";
		return $this->processURL($url, 'GET');
	}

	/*
	* List User
	*/

	public function listUsers()
	{
		// $url = "/api/v1/groups/rules";
		// return $this->processURL($url, 'POST');
	}

	/*
	* Update User
	*/

	public function updateUser()
	{
		// $url = "/api/v1/groups/rules";
		// return $this->processURL($url, 'POST');
	}

	/*
	* Auxiliary operations
 	*/

	public function getUserID($email)
	{
		$userData = $this->getUserWithLogin($email);
		return $userData['id'];
	}
}
?>