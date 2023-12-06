<?php
namespace WorkerApi;

class Auth {

	private $config = false;
	private $technology = false;
	
	private $token = null;
	private $tokenType = null;
	private $tokenExp = null;

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
	}

	//makes sure that user is authorized
	//returns boolean
	public function ensureAuthorized()
	{
		error_log('ensureAuthorized session variables ');
		if(isset($_SESSION['worker_token'])) {
			error_log($_SESSION['worker_token']);
		}

		if(isset($_SESSION['worker_exp'])) {
			error_log($_SESSION['worker_exp']);
		}

		if(isset($_SESSION['worker_token']) && isset($_SESSION['worker_exp']) && ($_SESSION['worker_exp']) > time()) return true;

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
		$token_response = json_decode($data,);
		$token_response_arr = json_decode($data, true);
		if($token_response)
		{
			if(isset($token_response->error)) {
				throw new \Exception('Error happened while authenticating. Please, try again later.');
			}
			if ( isset( $token_response->access_token ) ) {
				$jwt_arr = explode('.', $token_response->access_token );
				$encoded = $jwt_arr[1];
				$decoded = "";
				for ($i=0; $i < ceil(strlen($encoded)/4); $i++)
					$decoded = $decoded . base64_decode(substr($encoded,$i*4,4));
				$tokenData = json_decode( $decoded, true );
				error_log('data from Worker API TOKEN');
				error_log(__FILE__ . "Worker API TOKEN:" . print_r($tokenData,true));
				error_log('TOKEN Worker API OK');
				// && isset($tokenData['aud']) && !empty($tokenData['aud'])
				// && isset($tokenData['roles']) && !empty($tokenData['roles'])
			} else {
				error_log('WRONG Worker API TOKEN');
				return false;
			}

			// set session from TOKEN data
			if(isset($token_response_arr) && !empty($token_response_arr)
				&& isset($token_response_arr['token_type']) && !empty($token_response_arr['token_type'])
				&& isset($token_response_arr['expires_in']) && !empty($token_response_arr['expires_in'])
				&& isset($token_response_arr['access_token']) && !empty($token_response_arr['access_token'])
				)
			{
				$_SESSION['worker_token_type'] = $token_response_arr['token_type'];
				$_SESSION['worker_exp'] = time() + $token_response_arr['expires_in'];
				$_SESSION['worker_token'] = $token_response_arr['access_token'];
				$_SESSION['worker_somethingChanged'] = false;

				$this->token = $token_response_arr['access_token'];
				$this->tokenType = $token_response_arr['token_type'];
				$this->tokenExp = $token_response_arr['expires_in'];
			} else {
				//if something in the future gets changed and the strict checking on top of this is not working any more
				//please note, that you should always use strict matching in this function on your prod app so that you can handle changes correctly and not fill in the session with all the data
				//so basically, if you get to the else below, adjust it, open an issue on github so that the strict matching can be adjusted and it doesnt get to the else below
				
				//throw new \Exception('OpenIDConnect returned values were not correct.');
				$_SESSION = $tokenData;
				$_SESSION['worker_somethingChanged'] = true;

				$this->token = null;
				$this->tokenType = null;
				$this->tokenExp = null;
				
				return true;
			}
			return true;
		}
		return false;
	}
	
	public function setToken($token = null)
	{
		$this->token = $token;
	}

	public function getToken()
	{
		return $this->token;
	}

	public function setTokenType($type = null)
	{
		$this->tokenType = $type;
	}

	public function getTokenType()
	{
		return $this->tokenType;
	}

	public function setTokenExp($exp = null)
	{
		$this->tokenExp = $exp;
	}

	public function getTokenExp()
	{
		return $this->tokenExp;
	}

	//gets technology to use for authenticating
	//uses Config
	//returns string
	public function getTechnology()
	{
		$cfg = new Config();
		return $cfg->getTechnology();
	}

	public function getConfig($technology = '')
	{
		$cfg = new Config();
		// $technology = $cfg->getTechnology();
		return $cfg->getConfig($technology);
	}

	private function requestForTokenOpenIDConnect()
	{
		$url = $this->config->token_url;

		$fields = array(
			'client_id' => $this->config->client_id,
			'client_secret' => $this->config->client_secret,
			'grant_type' => 'client_credentials',
			'scope' => $this->config->token_scope
		);

		$postvars = http_build_query($fields);
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, count($fields));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);

		$result = curl_exec($ch);

		curl_close($ch);

		return $this->processOpenIDConnectCallback($result);
	}

	// loads openidconnect
	// uses Config
	// returns stdClass
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
			&& isset($config->hostname) && !empty($config->hostname)
			&& isset($config->authority) && !empty($config->authority)
			&& isset($config->client_id) && !empty($config->client_id)
			&& isset($config->client_secret) && !empty($config->client_secret)
			&& isset($config->token_scope) && !empty($config->token_scope)
			&& isset($config->authorize_url) && !empty($config->authorize_url)
			&& isset($config->token_url) && !empty($config->token_url)
			&& isset($config->userinfo_url) && !empty($config->userinfo_url)
			&& isset($config->introspect_url) && !empty($config->introspect_url)
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
	private function getUserInfo($token)
	{
		$url = $this->config->userinfo_url;

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		$authorization = "Authorization: Bearer ".$token; // Prepare the authorization token
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization )); // Inject the token into the header
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$result = curl_exec($ch);

		curl_close($ch);

		return json_decode($result, true);
	}
}