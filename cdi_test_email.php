<?php
$vcapServices = json_decode($_SERVER['VCAP_SERVICES']);

$data_json = '   {
	"contact": "rob.daniel@us.ibm.com",
	"recipients": [
		{"recipient": "rob.daniel@us.ibm.com"}
	],
	"subject": "Bluemix PTF Mon May 04 11:44:51 EDT 2015",
	"message": "Test email service with a text file attachment, in base64 encode over json. Default server and port.",
	"attachments": [
		{
			"attachment": {
				"filename": "test.txt",
				"content_type":"text/plain",
				"data": "VGhpcyBpcyBhIGJhc2U2NCBlbmNvZGVkIHRleHQ="
			}
		}
	]
   }';



// $sendResponse = BlueMail::send_mail(array('rob.daniel@uk.ibm.com'), 'test email', 'test email',
//     'rob.daniel@uk.ibm.com'); 

$ch = curl_init();

curl_setopt($ch, CURLOPT_HEADER,         1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT,        240);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 240);
curl_setopt($ch, CURLOPT_HTTPAUTH,  CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_HEADER,    FALSE);

$userpwd = $vcapServices->bluemailservice[0]->credentials->username . ':' . $vcapServices->bluemailservice[0]->credentials->password;
curl_setopt($ch, CURLOPT_USERPWD,        $userpwd);

curl_setopt($ch, CURLOPT_URL, $vcapServices->bluemailservice[0]->credentials->emailUrl);

curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data_json)));
curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);


$resp = curl_exec($ch);


$responseObject = json_decode($resp);

$statusUrl = $responseObject->link[0]->href;
$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_URL, $statusUrl);

$status = curl_exec($ch);

$statusObject = json_decode($status);

echo "<pre>";
var_dump($responseObject);
var_dump($statusObject);
echo "</pre>";