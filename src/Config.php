<?php

namespace slelorrain\Aushowmatic;

use Dotenv;

class Config
{
    public function __construct($test = false)
    {
        define('APP_BASE_PATH', dirname(__DIR__) . '/');
        $this->initialiseConfig($test ? '.env.example' : '.env');
    }

    private function initialiseConfig($file)
    {
        $dotenv = new Dotenv\Dotenv(APP_BASE_PATH, $file);
        $dotenv->load();
        $dotenv->required(['FEED_CLASS', 'SYSTEM_CMDS_ENABLED'])->notEmpty();
        $dotenv->required(['PREFERRED_FORMAT'])->notEmpty()->isInteger();

        date_default_timezone_set(isset($_ENV['TIMEZONE']) ? $_ENV['TIMEZONE'] : 'UTC');

        define("FEED_INFO", APP_BASE_PATH . $_ENV['FEED_INFO']);
    }
}
