<?php
namespace itdq;

/*
 *  Handles OKTA Groups.
 */
class OKTAGroups {

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

		$GLOBALS['OKTAGroups'] = $this;
	}

	private function createCurl($type = "GET")
	{
		// create a new cURL resource
		$ch = curl_init();
		$authorization = "Authorization: SSWS ".$this->token; // Prepare the authorization token
		$headers = [
			'Content-Type: application/json',
			'Accept: application/json, text/json, application/xml, text/xml',
			$authorization,
		];
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		if (stripos($_ENV['environment'], 'local')) {
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		};		
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
				if($bgapiRC==''){
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
	* Group operations
	*/

	public function addGroup($groupName, $description)
	{
		$profile = [
			"name" => $groupName,
			"description" => $description
		];
		$url = "/api/v1/groups";
		return $this->processURL($url, 'POST', $profile);
	}

	public function getGroup($groupId)
	{
		$url = "/api/v1/groups/$groupId";
		return $this->processURL($url, 'GET');
	}

	public function listGroups()
	{
		$url = "/api/v1/groups";
		return $this->processURL($url, 'GET');
	}

	public function getGroupByName($groupName, $limit = 10)
	{
		$groupName = urlencode($groupName);
		$url = "/api/v1/groups?q=$groupName&limit=$limit";
		return $this->processURL($url, 'GET');
	}

	public function updateGroup($groupId, $groupName, $description)
	{
		$profile = [
			"name" => $groupName,
			"description" => $description
		];
		$url = "/api/v1/groups/$groupId";
		return $this->processURL($url, 'PUT', $profile);
	}

	public function removeGroup($groupId)
	{
		$url = "/api/v1/groups/$groupId";
		return $this->processURL($url, 'DELETE');
	}

	/*
	* Group member operations
	*/

	public function listMembers($groupId)
	{
		$url = "/api/v1/groups/$groupId/users";
		$this->url = $url;
		return $this->processURL($url, 'GET');
	}

	public function addMember($groupId, $userId)
	{
		$url = "/api/v1/groups/$groupId/users/$userId";
		return $this->processURL($url, 'PUT');
	}
	
	public function removeMember($groupId, $userId)
	{
		$url = "/api/v1/groups/$groupId/users/$userId";
		return $this->processURL($url, 'DELETE');
	}

	/*
	* Group role operations
	*/

	public function createGroupRole()
	{
		$url = "/api/v1/groups/rules";
		return $this->processURL($url, 'POST');		
	}

	public function updateGroupRole($ruleId)
	{
		$url = "/api/v1/groups/rules/$ruleId";
		return $this->processURL($url, 'PUT');
	}

	public function listGroupRules()
	{
		$url = "/api/v1/groups/rules";
		return $this->processURL($url, 'GET');
	}

	public function getGroupRule($ruleId)
	{
		$url = "/api/v1/groups/rules/$ruleId";
		return $this->processURL($url, 'GET');
	}

	public function removeGroupRule($ruleId)
	{
		$url = "/api/v1/groups/rules/$ruleId";
		return $this->processURL($url, 'DELETE');
	}

	public function activateGroupRole($ruleId)
	{
		$url = "/api/v1/groups/rules/$ruleId/lifecycle/activate";
		return $this->processURL($url, 'POST');
	}

	public function deactivateGroupRole($ruleId)
	{
		$url = "/api/v1/groups/rules/$ruleId/lifecycle/deactivate";
		return $this->processURL($url, 'POST');
	}

	/*
	* Auxiliary operations
 	*/

	public function getGroupMembersKey($groupName)
	{
		$key = $groupName.'_getGroupMembers';
		$redisKey = md5($key.'_key_'.$_ENV['environment']);
		return $redisKey;
	}

	public function getGroupMembers($groupName)
	{
		$redisKey = $this->getGroupMembersKey($groupName);
		$cacheValue = $this->redis->get($redisKey);
		if (!$cacheValue) {
			$source = 'SQL Server';

			$groupId = $this->getGroupId($groupName);
			$result = $this->listMembers($groupId);

			if (array_key_exists('errorCode', $result)) {

				$debug = array(
					'hostname' => $this->hostname,
					'url' => $this->url,
					'token' => $this->token,
					'groupId' => $groupId,
					'groupName' => $groupName,
					'result' => $result,
					'source' => $source
				);
	
				trigger_error("Failing Okta API call ".json_encode($debug), E_USER_WARNING);
				
				$result = array();
			} else {
				$this->redis->set($redisKey, json_encode($result));
				$this->redis->expire($redisKey, REDIS_EXPIRE);
			}
		} else {
			$source = 'Redis Server';
			$result = json_decode($cacheValue, true);
		}
		$data = array('users'=>$result, 'source'=>$source);
		return $data;
	}

	public function clearGroupMembersCache($groupName)
	{
		$redisKey = $this->getGroupMembersKey($groupName);
		$this->redis->del($redisKey);
	}

	public function inAGroup($groupName, $ssoEmail)
	{
		$membersData = $this->getGroupMembers($groupName);
		list('users' => $users, 'source' => $source) = $membersData;
		
		$found = false;
		foreach($users as $key => $row) {
			if (is_array($row)) {
				if (array_key_exists('profile', $row)) {
					$profile = $row['profile'];
					if (is_array($profile)) {
						if (array_key_exists('email', $profile)) {
							$email = $profile['email'];
							if (strtolower(trim($email)) == strtolower(trim($ssoEmail))) {
								$found = true;
							}
						} else {
							// trigger_error("Failing PROFILE missing EMAIL data ".json_encode($profile), E_USER_WARNING);
						}
					} else {
						// trigger_error("Failing PROFILE because it is a string (".serialize($profile).")", E_USER_WARNING);
					}
				} else {
					// trigger_error("Failing ROW missing PROFILE data ".json_encode($row), E_USER_WARNING);
				}
			} else {			
				// trigger_error("Failing ROW because it is a string (".serialize($row).")", E_USER_WARNING);
			}
		}
		return $found;
	}

	public function getGroupId($groupName)
	{
		$key = $groupName.'_getGroupId';
		$redisKey = md5($key.'_key_'.$_ENV['environment']);
		if (!$this->redis->get($redisKey)) {
			$source = 'SQL Server';

			$result = $this->getGroupByName($groupName);

			$this->redis->set($redisKey, json_encode($result));
			$this->redis->expire($redisKey, REDIS_EXPIRE);
		} else {
			$source = 'Redis Server';
			$result = json_decode($this->redis->get($redisKey), true);
		}

		$groupId = false;
		foreach($result as $key => $row) {
			if ($row['profile']['name'] == $groupName) {
				$groupId = 	$row['id'];
			}
		}
		return $groupId;
	}

	public function getGroupName($groupId)
	{
		$key = $groupId.'_getGroupName';
		$redisKey = md5($key.'_key_'.$_ENV['environment']);
		if (!$this->redis->get($redisKey)) {
			$source = 'SQL Server';

			$result = $this->getGroup($groupId);

			$this->redis->set($redisKey, json_encode($result));
			$this->redis->expire($redisKey, REDIS_EXPIRE);
		} else {
			$source = 'Redis Server';
			$result = json_decode($this->redis->get($redisKey), true);
		}

		$groupName = $result['profile']['name'];
		return $groupName;
	}
}
?>