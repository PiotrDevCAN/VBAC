<?php
namespace itdq;

// use WorkerApi\Auth;

// include_once "WorkerAPI/class/include.php";

/*
 *  Handles Worker API.
 */
class Redis {
	
	private $hostname = null;
	private $port = null;
	private $password = null;
	
	// // $hostname = 'redis-kpes-prod.redis.cache.windows.net';
	// $hostname = 'tls://redis-kpes-dev.redis.cache.windows.net';
	// // $password = 'gKUIJwCEHUoBOfpZf6b8w7RUcqXtFPMB2AzCaJrbmVs=';
	// $password = 'cpzLpdVMLpgY3bkp5LxpeC2NwxZ0VmRNgAzCaLfYkjw=';
	// $port = 6380;

	public function __construct()
	{
		// $auth = new Auth();
		// $auth->ensureAuthorized();

		$this->hostname = 'tls://'.trim($_ENV['redis_host']);
		$this->port = $_ENV['redis_port'];
		$this->password = $_ENV['redis_password'];

		$redis = new \Redis();
		//Connecting to Redis
		try {
			$redis->pconnect($this->hostname, $this->port);
			$redis->auth($this->password);

			$GLOBALS['redis'] = $redis;
		} catch(RedisException $ex) {
			$m = $ex->getMessage();
			echo "ERROR ($m)\n";
			
			$GLOBALS['redis'] = null;
		}
	}
}