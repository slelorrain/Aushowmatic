<?php

namespace slelorrain\Aushowmatic;

use Dotenv;
use slelorrain\Aushowmatic\Core\Backupable;
use slelorrain\Aushowmatic\Core\Feed;
use slelorrain\Aushowmatic\Core\Resolution;
use slelorrain\Aushowmatic\Core\Subtitle;

class Config extends Backupable
{

    private static $isTest = false;
    private static $envFile = '.env';

    const NOT_EMPTY_VARIABLES = array(
        'FEED_NAME',
        'PREFERRED_FORMAT',
        'SUBTITLES_ENABLED',
        'SUBTITLES_NAME',
        'SYSTEM_CMDS_ENABLED',
    );

    public function __construct($test = false)
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        if ($test) {
            self::$isTest = true;
            self::$envFile = '.env.example';
        }

        if (!defined('APP_BASE_PATH')) define('APP_BASE_PATH', dirname(__DIR__) . '/');
        if (!defined('CORE_NAMESPACE')) define('CORE_NAMESPACE', __NAMESPACE__ . '\\Core\\');
        if (!defined('FEEDS_NAMESPACE')) define('FEEDS_NAMESPACE', __NAMESPACE__ . '\\Feeds\\');
        if (!defined('SUBTITLES_NAMESPACE')) define('SUBTITLES_NAMESPACE', __NAMESPACE__ . '\\Subtitles\\');

        $dotenv = new Dotenv\Dotenv(APP_BASE_PATH, self::$envFile);
        $dotenv->overload();
        $dotenv->required(self::NOT_EMPTY_VARIABLES)->notEmpty();

        $this->init();
    }

    private function init()
    {
        date_default_timezone_set(isset($_ENV['TIMEZONE']) ? $_ENV['TIMEZONE'] : 'CET');

        $_ENV['FEED_CLASS'] = FEEDS_NAMESPACE . $_ENV['FEED_NAME'];
        $_ENV['FEED_INFO'] = APP_BASE_PATH . 'resources/feeds/_' . $_ENV['FEED_NAME'] . '.json';
        $_ENV['SUBTITLES_CLASS'] = SUBTITLES_NAMESPACE . $_ENV['SUBTITLES_NAME'];

        if (Config::isEnabled('DEBUG')) {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
        }
    }

    private static function clean()
    {
        unset($_SESSION['available_shows']);
    }

    public static function getBackupableFile()
    {
        return APP_BASE_PATH . self::$envFile;
    }

    public static function updateEnv($name, $value)
    {
        if (isset($name) && isset($value) && self::isValid($name, $value)) {
            $envFile = self::getBackupableFile();

            // Prepare value
            if (!self::isBoolean($value)) {
                $value = '\'' . $value  . '\'';
            }

            // Update variable
            $lines = file($envFile);
            foreach ($lines as $key => $line) {
                if (strpos($line, $name . ' =') !== false) {
                    // Keep comment
                    $exploded = explode(' #', $line);

                    $newLine = $name . ' = ' . $value;
                    if (isset($exploded[1])) {
                        $newLine .= ' # ' . trim($exploded[1]);
                    }
                    $lines[$key] = $newLine . PHP_EOL;
                    break;
                }
            }

            // Save file and reload env or restore backup
            if (file_put_contents($envFile, $lines)) {
                self::clean();
                new Config(self::$isTest);
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    public static function isEnabled($name)
    {
        $value = (isset($_ENV[$name])) ? $_ENV[$name] : null;
        return self::isBoolean($value) && $value == 'true';
    }

    public static function isBoolean($value)
    {
        return !is_null(filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE));
    }

    private static function isValid($name, $value)
    {
        switch ($name) {
            case 'DEBUG':
            case 'SUBTITLES_ENABLED':
            case 'SYSTEM_CMDS_ENABLED':
                return self::isBoolean($value);
                break;

            case 'FEED_NAME':
                return in_array($value, Feed::getChoices());
                break;

            case 'PREFERRED_FORMAT':
                return in_array($value, Resolution::getChoices());
                break;

            case 'SUBTITLES_NAME':
                return in_array($value, Subtitle::getChoices());
                break;

        }
    }
}
