<?php
namespace cFIRST;

class Auth {

	private $config = false;
	private $technology = false;

	//construct function, automatically gets which technology to use and loads its config
	function __construct() {
		$technology = $this->getTechnology();
		switch (mb_strtolower($technology)) {
			case "openidconnect":
				$this->config = $this->loadOpenIDConnectConfig();
				$this->technology = mb_strtolower($technology);
				break;
			default:
				throw new \Exception(htmlentities($technology).' not yet implemented.');
		}

		$this->authorize();
	}

	public function authorize()
	{
		switch ($this->technology) {
			case "openidconnect":
				$this->authorizeToOpenIDConnect();
				break;
		}
		return false;
	}

	//makes sure that user is authorized
	//returns boolean
	public function ensureAuthorized()
	{
		error_log('ensureAuthorized session variables ');
		if(isset($_SESSION['cfirst_access_token'])) {
			error_log($_SESSION['cfirst_access_token']);
		}

		if(isset($_SESSION['cfirst_refresh_token'])) {
			error_log($_SESSION['cfirst_refresh_token']);
		}

		if(isset($_SESSION['cfirst_exp'])) {
			error_log($_SESSION['cfirst_exp']);
		}

		// if(isset($_SESSION['cfirst_access_token']) 
		// 	&& isset($_SESSION['cfirst_refresh_token']) 
		// 	&& isset($_SESSION['cfirst_exp']) 
		// 	&& ($_SESSION['cfirst_exp']) > time()
		// ) return true;

		switch ($this->technology) {
			case "openidconnect":
				$this->requestForTokenOpenIDConnect();
				break;
		}
		return false;
	}

	//processes openid data and sets session
	//returns boolean
	private function processOpenIDConnectCallback($data)
	{
		$token_response = json_decode($data);
		$token_response_arr = json_decode($data, true);
		if($token_response)
		{
			if(isset($token_response->Error)) {
				throw new \Exception('Error happened while authenticating. Please, try again later.');
			}

			$token_response = $token_response[0];
			$token_response_arr = $token_response_arr[0];
			
			if ( isset( $token_response->AcessToken ) ) {
				// $jwt_arr = explode('.', $token_response->AcessToken );
				// $encoded = $jwt_arr[1];
				// $decoded = "";
				// for ($i=0; $i < ceil(strlen($encoded)/4); $i++)
					// $decoded = $decoded . base64_decode(substr($encoded,$i*4,4));
				// $tokenData = json_decode( $decoded, true );
				// error_log('data from cFirst API TOKEN');
				// error_log(__FILE__ . "cFirst API TOKEN:" . print_r($tokenData,true));
				error_log('TOKEN cFirst API OK');
				// && isset($tokenData['aud']) && !empty($tokenData['aud'])
				// && isset($tokenData['roles']) && !empty($tokenData['roles'])
			} else {
				error_log('WRONG cFirst API TOKEN');
				return false;
			}

			// set session from TOKEN data
			if(isset($token_response_arr) && !empty($token_response_arr)
				&& isset($token_response_arr['ExpiredIn']) && !empty($token_response_arr['ExpiredIn'])
				&& isset($token_response_arr['AcessToken']) && !empty($token_response_arr['AcessToken'])
				&& isset($token_response_arr['RefreshToken']) && !empty($token_response_arr['RefreshToken'])
				)
			{
				$_SESSION['cfirst_exp'] = time() + $token_response_arr['ExpiredIn'];
				$_SESSION['cfirst_access_token'] = $token_response_arr['AcessToken'];
				$_SESSION['cfirst_refresh_token'] = $token_response_arr['RefreshToken'];
				$_SESSION['cfirst_somethingChanged'] = false;
			} else {
				//if something in the future gets changed and the strict checking on top of this is not working any more
				//please note, that you should always use strict matching in this function on your prod app so that you can handle changes correctly and not fill in the session with all the data
				//so basically, if you get to the else below, adjust it, open an issue on github so that the strict matching can be adjusted and it doesnt get to the else below
				
				//throw new \Exception('OpenIDConnect returned values were not correct.');
				// $_SESSION = $tokenData;
				$_SESSION['cfirst_somethingChanged'] = true;
				return true;
			}
			return true;
		}
		return false;
	}

	public function getConfig()
	{
		// $cfg = new Config();
		// return $cfg;
		return $this->config;
	}

	//gets technology to use for authenticating
	//uses Config
	//returns string
	private function getTechnology()
	{
		$cfg = new Config();
		return $cfg->getTechnology();
	}

	/*
	* main authorize call
	*/
	private function authorizeToOpenIDConnect()
	{
		$url = $this->config->authorize_url;

		$fields = array(
			'UserName' => $this->config->user_id,
			'Password' => $this->config->password,
			'Apikey'   => $this->config->api_key
		);

		$vars = http_build_query($fields);
		$ch = curl_init();
		$getUrl = $url."?".$vars;
		curl_setopt($ch, CURLOPT_URL, $getUrl);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		$result = curl_exec($ch);
		$result = json_decode($result);

		curl_close($ch);

		return $this->processOpenIDConnectCallback($result);
	}

	/*
	* refresh token call
	*/
	private function requestForTokenOpenIDConnect()
	{
		$url = $this->config->token_url;

		$fields = array(
			'UserName' => $this->config->user_id,
			'Password' => $this->config->password,
			'Apikey'   => $this->config->api_key,
			'Refresh Token' => $_SESSION['cfirst_refresh_token']
		);

		$vars = http_build_query($fields);
		$ch = curl_init();
		$getUrl = $url."?".$vars;
		curl_setopt($ch, CURLOPT_URL, $getUrl);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		$result = curl_exec($ch);
		$result = json_decode($result);

		curl_close($ch);

		return $this->processOpenIDConnectCallback($result);
	}

	//loads openidconnect
	//uses Config
	//returns stdClass
	private function loadOpenIDConnectConfig()
	{
		$cfg = new Config();
		$authData = $cfg->getConfig("openidconnect");
		if($this->verifyOpenIDConnectConfig($authData))
		{
			return $authData;
		}
		else
		{
			throw new \Exception('OpenIDConnect data not correct. Please check if everything is filled out in OpenIDConnect configuration.');
		}
	}

	//verifies if all openidconnect config data are filled out correctly
	//returns boolean
	private function verifyOpenIDConnectConfig($config)
	{
		if(isset($config) && !empty($config)
			&& isset($config->authorize_url) && !empty($config->authorize_url)
			&& isset($config->token_url) && !empty($config->token_url)
			&& isset($config->userinfo_url) && !empty($config->userinfo_url)
			&& isset($config->introspect_url) && !empty($config->introspect_url)
			
			&& isset($config->user_id) && !empty($config->user_id)
			&& isset($config->password) && !empty($config->password)
			&& isset($config->api_key) && !empty($config->api_key)
			)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	// Returns information about the currently signed-in user.
	// private function getUserInfo($token)
	// {
	// 	$url = $this->config->userinfo_url;

	// 	$ch = curl_init();

	// 	curl_setopt($ch, CURLOPT_URL, $url);
	// 	$authorization = "Authorization: Bearer ".$token; // Prepare the authorisation token
	// 	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization )); // Inject the token into the header
	// 	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	// 	$result = curl_exec($ch);

	// 	curl_close($ch);

	// 	return json_decode($result, true);
	// }
}