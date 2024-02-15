<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use itdq\BlueMail;

// use vbac\personRecord;

$message = '<table width="100%" border="0"   cellpadding="0">
    <tr>
        <td align="center">
            <table width="50%">
            <tr><th style="background-color:silver;font-size:20px">Name</th><td style="font-size:20px">Jeremy Norfolk</td></tr>
            <tr><th style="background-color:silver;font-size:20px">Email Address</th><td style="font-size:20px">Jeremy.Norfolk@ibm.com</td></tr>
            <tr><th style="background-color:silver;font-size:20px">Country working in </th><td style="font-size:20px">UK</td></tr>
            <tr><th style="background-color:silver;font-size:20px">LoB</th><td style="font-size:20px">GTS</td></tr>
            <tr><th style="background-color:silver;font-size:20px">Role on Project</th><td style="font-size:20px">Linux Systems - Admin</td></tr>
            <tr><th style="background-color:silver;font-size:20px">Contract</th><td style="font-size:20px">Ventus</td></tr>
            </table>
        </td>
    </tr>
</table>';

// $response = itdq\BlueMail::send_mail(array($_ENV['devemailid']), 'Resendable 4',$message, 'rob_dev@uk.ibm.com');
// echo "<pre>";
// var_dump($response);

// $to      = $_ENV['devemailid'];
// $subject = 'Test from Docker';
// // $message = 'Testing 1 2 3';
// $headers = array(
//     'From' => $_ENV['devemailid'],
//     'Reply-To' => $_ENV['devemailid'],
//     'X-Mailer' => 'PHP/' . phpversion()
// );

echo "<div class='container'>";

$to[] = $_ENV['devemailid'];
// $response = BlueMail::send_mail($to, 'Testing new SendMail', '<h1>Some text</h1><p>Well this is, that was header</p>', 'vbac@noReply.co.uk');
$response = BlueMail::send_mail($to, 'Test', '<h1>Testing 1 2 3</h1>', 'vbac@noReply.co.uk');

var_dump($response);
echo "<p> response above </p>";
echo "</div>";