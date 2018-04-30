<?php

namespace slelorrain\Aushowmatic;

use Dotenv;

class Config
{

    const NOT_EMPTY_VARIABLES = array(
        'FEED_NAME',
        'PREFERRED_FORMAT',
        'SUBTITLES_ENABLED',
        'SUBTITLES_NAME',
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
        $dotenv->required(self::NOT_EMPTY_VARIABLES)->notEmpty();

        date_default_timezone_set(isset($_ENV['TIMEZONE']) ? $_ENV['TIMEZONE'] : 'UTC');

        $_ENV['FEED_CLASS'] = __NAMESPACE__ . '\\Feeds\\' . $_ENV['FEED_NAME'];
        define('FEED_INFO', APP_BASE_PATH . 'resources/feeds/_' . $_ENV['FEED_NAME'] . '.json');

        $_ENV['SUBTITLES_CLASS'] = __NAMESPACE__ . '\\Subtitles\\' . $_ENV['SUBTITLES_NAME'];

        if ($_ENV['DEBUG'] == 'true') {
            ini_set('display_errors', 1);
            ini_set('', 1);
            error_reporting(E_ALL);
        }
    }
}
