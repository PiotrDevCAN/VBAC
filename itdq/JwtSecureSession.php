<?php
namespace itdq;

use ByJG\Session\JwtSession;
use ByJG\Util\JwtWrapper;

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
            $cookieProperties = array(
                'expires' => (time()+$this->sessionConfig->getTimeoutMinutes()*60),
                'path' => $this->sessionConfig->getCookiePath(),
                'domain' => $this->sessionConfig->getCookieDomain(),
                'secure' => true,
                'httponly' => true
            );
            // setcookie(
            //     self::COOKIE_PREFIX . $this->sessionConfig->getSessionContext(),
            //     $token,
            //     (time()+$this->sessionConfig->getTimeoutMinutes()*60) ,
            //     $this->sessionConfig->getCookiePath(),
            //     $this->sessionConfig->getCookieDomain(),
            //     true,
            //     true
            // );
            setcookie(
                self::COOKIE_PREFIX . $this->sessionConfig->getSessionContext(),
                $token,
                $cookieProperties
            );
            if (defined("SETCOOKIE_FORTEST")) {
                $_COOKIE[self::COOKIE_PREFIX . $this->sessionConfig->getSessionContext()] = $token;
            }
        }

        return true;
    }

    /**
     * Destroy a session
     *
     * @link http://php.net/manual/en/sessionhandlerinterface.destroy.php
     * @param string $session_id The session ID being destroyed.
     * @return bool <p>
     * The return value (usually TRUE on success, FALSE on failure).
     * Note this value is returned internally to PHP for processing.
     * </p>
     * @since 5.4.0
     */
    public function destroy($session_id)
    {
        if (!headers_sent()) {
            $clear0 = setcookie(
                self::COOKIE_PREFIX . $this->sessionConfig->getSessionContext(),
                null,
                (time()-3000),
                $this->sessionConfig->getCookiePath(),
                $this->sessionConfig->getCookieDomain()
            );

            // unset cookies
            if (count($_COOKIE) > 0) {
                $cookies = $_COOKIE;
                foreach($cookies as $name => $value) {
                    $clear1 = setcookie($name, '', time()-3000);
                    $clear2 = setcookie($name, '', time()-3000, '/');
                    echo '<br>'.$name;
                    echo '<br>'.var_dump($clear1);
                    echo '<br>'.var_dump($clear2);
                }
            }
            echo '<br>'.self::COOKIE_PREFIX . $this->sessionConfig->getSessionContext();
            echo '<br>'.var_dump($clear0);
            echo '<br>'.'session cookie removed in JwtSecureSession';
        } else {
            echo '<br>'.'unable to  remove session cookie in JwtSecureSession';
        }

        return true;
    }
}