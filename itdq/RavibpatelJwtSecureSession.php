<?php
namespace itdq;

use ravibpatel\JWTSession\JWTSession;

class RavibpatelJwtSecureSession {

    public $handler = null;

    public function __construct()
	{
        /*
        * ravibpatel session
        */
        $JWTSession = new JWTSession(120, $_ENV['jwt_token']);
        $JWTSession->setSessionHandler();
        $this->handler = $JWTSession;
    }
}