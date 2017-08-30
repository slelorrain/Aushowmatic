<?php

namespace slelorrain\Aushowmatic;

use Dotenv;

class Config
{

    private static $notEmptyVariables = array(
        'FEED_CLASS',
        'FEED_INFO',
        'PREFERRED_FORMAT',
        'SUBTITLES_ENABLED',
        'SYSTEM_CMDS_ENABLED',
    );

    public function __construct($test = false)
    {
        define('APP_BASE_PATH', dirname(__DIR__) . '/');
        $this->initialiseConfig($test ? '.env.example' : '.env');
    }

    private function initialiseConfig($file)
    {
        $dotenv = new Dotenv\Dotenv(APP_BASE_PATH, $file);
        $dotenv->load();
        $dotenv->required($notEmptyVariables)->notEmpty();

        date_default_timezone_set(isset($_ENV['TIMEZONE']) ? $_ENV['TIMEZONE'] : 'UTC');

        define("FEED_INFO", APP_BASE_PATH . $_ENV['FEED_INFO']);
    }
}
