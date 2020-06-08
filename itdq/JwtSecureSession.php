<?php
namespace itdq;

use ByJG\Session\JwtSession;

Class JwtSecureSession extends JwtSession
{
    /**
     * Write session data
     *
     * @link http://php.net/manual/en/sessionhandlerinterface.write.php
     * @param string $session_id The session id.
     * @param string $session_data <p>
     * The encoded session data. This data is the
     * result of the PHP internally encoding
     * the $_SESSION superglobal to a serialized
     * string and passing it as this parameter.
     * Please note sessions use an alternative serialization method.
     * </p>
     * @return bool <p>
     * The return value (usually TRUE on success, FALSE on failure).
     * Note this value is returned internally to PHP for processing.
     * </p>
     * @throws \ByJG\Util\JwtWrapperException
     * @since 5.4.0
     */
    public function write($session_id, $session_data)
    {
        $jwt = new JwtWrapper(
            $this->sessionConfig->getServerName(),
            $this->sessionConfig->getKey()
            );
        $data = $jwt->createJwtData($session_data, $this->sessionConfig->getTimeoutMinutes() * 60);
        $token = $jwt->generateToken($data);

        if (!headers_sent()) {
            setcookie(
                self::COOKIE_PREFIX . $this->sessionConfig->getSessionContext(),
                $token,
                (time()+$this->sessionConfig->getTimeoutMinutes()*60) ,
                $this->sessionConfig->getCookiePath(),
                $this->sessionConfig->getCookieDomain(),
                true,
                true
                );
            if (defined("SETCOOKIE_FORTEST")) {
                $_COOKIE[self::COOKIE_PREFIX . $this->sessionConfig->getSessionContext()] = $token;
            }
        }

        return true;
    }
}