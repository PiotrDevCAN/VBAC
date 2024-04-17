<?php
namespace itdq;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/*
 *  Handles PHP Mailer.
 */
class Mailer {

	public function __construct()
	{
		$mailer = new PHPMailer();
		$mailer->SMTPDebug = SMTP::DEBUG_OFF; // Enable verbose debug output ; SMTP::DEBUG_OFF
		$mailer->isSMTP(); // Send using SMTP
		// $mailer->Host = $_ENV['smtp-server']; // Set the SMTP server to send through
		$mailer->Host = $_ENV['smtp-server-new']; // Set the SMTP server to send through
		$mailer->SMTPAuth = true;
		$mailer->SMTPAutoTLS = true;
		$mailer->SMTPSecure = 'ssl';
		$mailer->Port = 465; // 25, 465, or 587
		$mailer->Username = $_ENV['smtp-user-name'];             
		$mailer->Password = $_ENV['smtp-user-pw']; 

		$GLOBALS['mailer'] = $mailer;
	}
}