<?php
class Session {
    private $sessionId;
    private $sessionData;
    private $userModell;
    
    public function __construct($userModell) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->sessionId = session_id();
        $this->sessionData = &$_SESSION;
        $this->userModell = $userModell;
    }
    
    public function set($key, $value) {
        $this->sessionData[$key] = $value;
    }
    
    public function get($key, $default = null) {
        return isset($this->sessionData[$key]) ? $this->sessionData[$key] : $default;
    }
    
    public function remove($key) {
        if (isset($this->sessionData[$key])) {
            unset($this->sessionData[$key]);
        }
    }
    
    public function clear() {
        session_unset();
        session_destroy();
    }

    public function getId() {
        return $this->sessionId;
    }

    public function checkLogin() {
        if ($this->get('userId') != null) {
            return true;
        } else {
            return false;
        }
    }
    
    public function checkAdmin() {
        $currentUser = $this->userModell->get($this->get('userId'));

        $currentUser = $currentUser[0];

        if ($currentUser != null | empty($currentUser)) {
            if ($currentUser['isAdmin'] == 1) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}