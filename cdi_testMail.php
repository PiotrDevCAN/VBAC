<?php
// use vbac\personRecord;

// $message = '<table width="100%" border="0"   cellpadding="0">
//     <tr>
//         <td align="center">
//             <table width="50%">
//             <tr><th style="background-color:silver;font-size:20px">Name</th><td style="font-size:20px">Jeremy Norfolk</td></tr>
//             <tr><th style="background-color:silver;font-size:20px">Email Address</th><td style="font-size:20px">Jeremy.Norfolk@ibm.com</td></tr>
//             <tr><th style="background-color:silver;font-size:20px">Country working in </th><td style="font-size:20px">UK</td></tr>
//             <tr><th style="background-color:silver;font-size:20px">LoB</th><td style="font-size:20px">GTS</td></tr>
//             <tr><th style="background-color:silver;font-size:20px">Role on Project</th><td style="font-size:20px">Linux Systems - Admin</td></tr>
//             <tr><th style="background-color:silver;font-size:20px">Contract</th><td style="font-size:20px">Ventus</td></tr>
//             </table>
//         </td>
//     </tr>
// </table>';




// $response = itdq\BlueMail::send_mail(array('rob.daniel@uk.ibm.com'), 'Resendable 4',$message, 'rob_dev@uk.ibm.com');
// echo "<pre>";
// var_dump($response);

$to      = 'rob.daniel@uk.ibm.com';
$subject = 'Test from Docker';
$message = 'Testing 1 2 3';
$headers = array(
    'From' => 'rob.daniel@uk.ibm.com',
    'Reply-To' => 'rob.daniel@uk.ibm.com',
    'X-Mailer' => 'PHP/' . phpversion()
);

echo "<div class='container'>";

phpinfo();

$response = mail($to, $subject, $message, $headers);

echo "<br/><br/><br/>";

echo "<p> About to show response</p>";

echo "<pre>";
print_r($response);

error_get_last();

echo "</pre>";

echo "<p> response above </p>";



echo "</div>";

// $person = new personRecord();

// $person->sendPesRequest();

