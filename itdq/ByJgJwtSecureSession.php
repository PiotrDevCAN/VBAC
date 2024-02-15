<?php
namespace itdq;

use ByJG\Session\JwtSession;
use ByJG\Session\SessionConfig;

class ByJgJwtSecureSession {

    public $handler = null;

    public function __construct()
	{
        /*
        * ByJG session
        */
        $sessionConfig = (new SessionConfig($_SERVER['SERVER_NAME']));
        $sessionConfig->withTimeoutMinutes(120);
        $sessionConfig->withSecret($_ENV['jwt_token']);
        $sessionConfig->replaceSessionHandler();
        $this->handler = new JwtSession($sessionConfig);
    }
}