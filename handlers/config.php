<?php
class Config {

    private $configPath;
    public function __construct($configPath) {
        $this->configPath = $configPath;
    }

    public function get($key) {
        $config = parse_ini_file($this->configPath);
        return $config[$key] ?? null;
    }
}