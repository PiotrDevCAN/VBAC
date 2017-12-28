<?php
// $vcapServices = json_decode($_SERVER['VCAP_SERVICES']);

// echo "<pre>";
// print_r($vcapServices->bluemailservice);
// print_r($vcapServices->bluemailservice[0]->credentials);

// print_r($vcapServices->bluemailservice[0]->credentials->username);
// echo "<br/>";
// print_r($vcapServices->bluemailservice[0]->credentials->password);
// echo "<br/>";
// print_r($vcapServices->bluemailservice[0]->credentials->emailUrl);


// $data = array('contact'=> "vbacApp@ibm.com",
//               'recipients'=>array(array('recipient'=>'daniero@uk.ibm.com'),array('recipient'=>'rob.daniel@uk.ibm.com')),
//               'subject'=>'Another email style ?',
//               'message'=>'<body style="background-color: powderblue">
//                             <span>Wishing you a safe and merry holiday seas on!</span>
//                             <h1 style="color:blue">Title</h1><p>Some words <b>in bold</b></p>
//                           </body>');

// $data_json = json_encode($data);

// $data_json = '{
// 	"contact": "vbacApp@ibm.com",
// 	"recipients": [
// 		{"recipient": "daniero@uk.ibm.com"}
// 	],
// 	"subject": "Bluemix BlueMail Test ",
// 	"message": "Testing the email service. Defaults selected."
//    }';


//     $ch = curl_init();
//     curl_setopt($ch, CURLOPT_HEADER,         1);
//     curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//     curl_setopt($ch, CURLOPT_TIMEOUT,        240);
//     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 240);
//     curl_setopt($ch, CURLOPT_HTTPAUTH,  CURLAUTH_BASIC);
//     curl_setopt($ch, CURLOPT_HEADER,    FALSE);

//     $userpwd = $vcapServices->bluemailservice[0]->credentials->username . ':' . $vcapServices->bluemailservice[0]->credentials->password;
//     curl_setopt($ch, CURLOPT_USERPWD,        $userpwd);

//     curl_setopt($ch, CURLOPT_URL, $vcapServices->bluemailservice[0]->credentials->emailUrl);

//     curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data_json)));
//     curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);


//     $resp = curl_exec($ch);

//     echo "<hr/>";

//     $responseObject = json_decode($resp);
//     echo "<pre>";
//     print_r($responseObject);

//     $info = curl_getinfo($ch);

//     var_dump($info);



$response = itdq\BlueMail::send_mail(array('rob.daniel@uk.ibm.com'), 'new test 14:19','simple message', 'rob_dev@uk.ibm.com',array(),array(),false);
echo "<pre>";
var_dump($response);

