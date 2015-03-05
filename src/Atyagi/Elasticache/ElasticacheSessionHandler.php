<?php namespace Atyagi\Elasticache;

use Illuminate\Contracts\Foundation\Application;
use Memcached;
use SessionHandlerInterface;

class ElasticacheSessionHandler implements SessionHandlerInterface
{
    /** @var Memcached */
    protected $memcached;
    public $sessionExpiry;
    public $sessionPrefix;

    public function __construct($memcached, Application $app)
    {
        $this->memcached = $memcached;
        //force expiry to be in seconds from minutes
        $this->sessionExpiry = $app->make('config')->get('session.lifetime') * 60;
        $this->sessionPrefix = $app->make('config')->get('session.cookie');
    }

    /**
     * Re-initializes existing session, or creates a new one.
     * @see http://php.net/sessionhandlerinterface.open
     * @param string $savePath Save path
     * @param string $sessionName Session name, see http://php.net/function.session-name.php
     * @return bool true on success, false on failure
     */
    public function open($savePath, $sessionName)
    {
        if (!is_null($this->memcached)) {
            return true;
        }

        return false;
    }

    /**
     * Closes the current session.
     * @see http://php.net/sessionhandlerinterface.close
     * @return bool true on success, false on failure
     */
    public function close()
    {
        //not necessary for memcached
        return true;
    }

    /**
     * Reads the session data.
     * @see http://php.net/sessionhandlerinterface.read
     * @param string $sessionId Session ID, see http://php.net/function.session-id
     * @return string Same session data as passed in write() or empty string when non-existent or on failure
     */
    public function read($sessionId)
    {
        $sessionId = $this->getSessionId($sessionId);
        $value = $this->memcached->get($sessionId);

        return !empty($value) ? $value : '';
    }

    /**
     * Writes the session data to the storage.
     * @see http://php.net/sessionhandlerinterface.write
     * @param string $sessionId Session ID , see http://php.net/function.session-id
     * @param string $data Serialized session data to save
     * @return bool true on success, false on failure
     */
    public function write($sessionId, $data)
    {
        $sessionId = $this->getSessionId($sessionId);
        $value = $this->memcached->get($sessionId);
        if (empty($value)) {
            return $this->memcached->add($sessionId, $data, $this->sessionExpiry);
        } else {
            return $this->memcached->replace($sessionId, $data, $this->sessionExpiry);
        }
    }

    /**
     * Destroys a session.
     * @see http://php.net/sessionhandlerinterface.destroy
     * @param string $sessionId Session ID, see http://php.net/function.session-id
     * @return bool true on success, false on failure
     */
    public function destroy($sessionId)
    {
        $sessionId = $this->getSessionId($sessionId);

        return $this->memcached->delete($sessionId);
    }

    /**
     * Cleans up expired sessions (garbage collection).
     * @see http://php.net/sessionhandlerinterface.gc
     * @param string|int $maxlifetime Sessions that have not updated for the last maxlifetime seconds will be removed
     * @return bool true on success, false on failure
     */
    public function gc($maxlifetime)
    {
        //memcached automatically expires
        return true;
    }

    private function getSessionId($sessionId)
    {
        return $this->sessionPrefix . '_' . $sessionId;
    }
}