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
        if (!defined('APP_BASE_PATH')) define('APP_BASE_PATH', dirname(__DIR__) . '/');
        if (!defined('CORE_PATH')) define('CORE_PATH', __NAMESPACE__ . '\\Core\\');
        if (!defined('FEEDS_PATH')) define('FEEDS_PATH', __NAMESPACE__ . '\\Feeds\\');
        if (!defined('SUBTITLES_PATH')) define('SUBTITLES_PATH', __NAMESPACE__ . '\\Subtitles\\');

        $dotenv = new Dotenv\Dotenv(APP_BASE_PATH, $test ? '.env.example' : '.env');
        $dotenv->overload();
        $dotenv->required(self::NOT_EMPTY_VARIABLES)->notEmpty();

        $this->init();
    }

    private function init()
    {
        date_default_timezone_set(isset($_ENV['TIMEZONE']) ? $_ENV['TIMEZONE'] : 'UTC');

        $_ENV['FEED_CLASS'] = FEEDS_PATH . $_ENV['FEED_NAME'];
        $_ENV['FEED_INFO'] = APP_BASE_PATH . 'resources/feeds/_' . $_ENV['FEED_NAME'] . '.json';
        $_ENV['SUBTITLES_CLASS'] = SUBTITLES_PATH . $_ENV['SUBTITLES_NAME'];

        if ($_ENV['DEBUG'] == 'true') {
            ini_set('display_errors', 1);
            ini_set('', 1);
            error_reporting(E_ALL);
        }
    }
}
