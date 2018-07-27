<?php

use itdq\slack;

$slack = new slack();

$slack->sendMessageToChannel("Testing from a new class", slack::CHANNEL_SM_CDI);

// ob_clean();

// $ch = curl_init();

// $url = "https://hooks.slack.com/services/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";

// $ch = curl_init( $url );

// curl_setopt( $ch, CURLOPT_POSTFIELDS,'{"text":"Hello,PHP World!"}' );
// curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Content-Length: ' . strlen('{"text":"Hello,PHP World!"}')));

// curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
// curl_setopt( $ch, CURLOPT_POST, true);
// # Send request.
// $result = curl_exec($ch);

// # Print response.
// echo "<pre>$result</pre>";
// echo "completed.";


// echo curl_errno($ch);
// echo curl_error($ch);

// curl_close($ch);
