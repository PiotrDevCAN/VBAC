<?php
use itdq\WorkerAPI;

$start = microtime(true);

$workerAPI = new WorkerAPI();
$workerData = $workerAPI->getworkerByEmail($_SESSION['ssoEmail']);

$elapsed = microtime(true);
echo ("WorkerAPI call took:" . (float)($elapsed-$start));
echo '</br>';

// // $hostname = 'redis-kpes-prod.redis.cache.windows.net';
// $hostname = 'tls://redis-kpes-dev.redis.cache.windows.net';
// // $password = 'gKUIJwCEHUoBOfpZf6b8w7RUcqXtFPMB2AzCaJrbmVs=';
// $password = 'cpzLpdVMLpgY3bkp5LxpeC2NwxZ0VmRNgAzCaLfYkjw=';
// $port = 6380;

// $redis = new Redis();
// //Connecting to Redis
// try {
// 	$redis->pconnect($hostname, 6380);
// 	$redis->auth($password);

// 	echo 'SEEEEEETTTTTTTTTTTTTTTTTTT-------------';
// } catch(RedisException $ex) {
//     $m = $ex->getMessage();
//     echo "ERROR ($m)\n";
// }

// if ($redis->ping()) {
// 	echo "PONG";
// } else {
// 	echo "failed";
// }

// echo '<pre>';
// echo 'ENVIRONMENT <br>';
// var_dump($_ENV);
// echo 'SESSION <br>';
// var_dump($_SESSION);
// echo 'WORKER DATA <br>';
// var_dump($workerData);
// echo '</pre>';

echo 'Emails status: '.trim($_ENV['email']);

?>
<style type="text/css" class="init">
body {
	background: url('./public/img/vBAC2.jpg')
	no-repeat center center fixed;
	-webkit-background-size: cover;
	-moz-background-size: cover;
	-o-background-size: cover;
	background-size: cover;
}
</style>

<div class="container">
	<!--  <div class="jumbotron"> -->
		<h1 id='welcomeJumotron'>Ventus Boarding &amp; Access Control</h1>

		<button type='button' class='btn btn-default accessRestrict accessPmo accessCdi accessFm' id='onBoardingBtn'><span class="glyphicon glyphicon-log-in"></span>&nbsp;On Boarding</button>
		<button type='button' class='btn btn-default accessRestrict accessPmo accessCdi accessFm' id='offBoardingBtn'><span class="glyphicon glyphicon-log-out"></span>&nbsp;Off Boarding</button>
	<!-- </div> -->
	<div>
	<p>IMPORTANT NOTE: Personal information or personal sensitive information (such as financial or medical data) or any information identifiable to an individual other than business contact information [indicated as mandatory in this system], SHOULD NOT be entered into this system.</p>
	</div>
</div>