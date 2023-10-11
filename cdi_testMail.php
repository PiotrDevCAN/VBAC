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

// $response = itdq\BlueMail::send_mail(array('Piotr.Tajanowicz@kyndryl.com'), 'Resendable 4',$message, 'rob_dev@uk.ibm.com');
// echo "<pre>";
// var_dump($response);

// $to      = 'Piotr.Tajanowicz@kyndryl.com';
// $subject = 'Test from Docker';
// // $message = 'Testing 1 2 3';
// $headers = array(
//     'From' => 'Piotr.Tajanowicz@kyndryl.com',
//     'Reply-To' => 'Piotr.Tajanowicz@kyndryl.com',
//     'X-Mailer' => 'PHP/' . phpversion()
// );

echo "<div class='container'>";

// phpinfo();

// $mail = new PHPMailer();

// $mail->SMTPDebug = SMTP::DEBUG_OFF;                    // Enable verbose debug output ; SMTP::DEBUG_OFF
// $mail->isSMTP();                                       // Send using SMTP
// $mail->Host       = '9.57.199.108';                    // Set the SMTP server to send through
// $mail->SMTPAuth = false;
// $mail->SMTPAutoTLS = false;
// $mail->Port       = 25;


// $mail->setFrom('Piotr.Tajanowicz@kyndryl.com', 'Piotr Tajanowicz');
// $mail->addAddress('Piotr.Tajanowicz@kyndryl.com', 'Another Piotr');
// $mail->isHTML(true);

// $mail->Subject  = 'First HTML Message';
// $mail->Body     = $message;
// $mail->AltBody  = 'Your mail client can\'t handle HTML';

// echo "<br/><br/><br/><br/><br/>";
// echo "<p> About to show response</p>";


// if(!$mail->send()) {
//     echo 'Message was not sent.';
//     echo 'Mailer error: ' . $mail->ErrorInfo;
// } else {
//     echo 'Message has been sent.';
// }

$to[] = 'Piotr.Tajanowicz@kyndryl.com';
$to[] = 'Piotr.Tajanowicz@kyndryl.com';
// $response = BlueMail::send_mail($to, 'Testing new SendMail', '<h1>Some text</h1><p>Well this is, that was header</p>', 'vbac@noReply.co.uk');
$response = BlueMail::send_mail($to, 'Test', '<h1>Testing 1 2 3</h1>', 'vbac@noReply.co.uk');

var_dump($response);
echo "<p> response above </p>";



echo "</div>";

// $person = new personRecord();

// $person->sendPesRequest();



