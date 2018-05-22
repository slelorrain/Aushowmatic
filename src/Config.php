<?php

namespace slelorrain\Aushowmatic;

use Dotenv;
use slelorrain\Aushowmatic\Core\Backupable;

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
        if ($test) {
            self::$isTest = true;
            self::$envFile = '.env.example';
        }

        if (!defined('APP_BASE_PATH')) define('APP_BASE_PATH', dirname(__DIR__) . '/');
        if (!defined('CORE_PATH')) define('CORE_PATH', __NAMESPACE__ . '\\Core\\');
        if (!defined('FEEDS_PATH')) define('FEEDS_PATH', __NAMESPACE__ . '\\Feeds\\');
        if (!defined('SUBTITLES_PATH')) define('SUBTITLES_PATH', __NAMESPACE__ . '\\Subtitles\\');

        $dotenv = new Dotenv\Dotenv(APP_BASE_PATH, self::$envFile);
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
                new Config(self::$isTest);
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    private static function isBoolean($value)
    {
        return !is_null(filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE));
    }

    private static function isValid($name, $value)
    {
        switch ($name) {
            case 'SUBTITLES_NAME':
                return is_subclass_of(SUBTITLES_PATH . $value, CORE_PATH . 'Subtitle');
                break;

            case 'SUBTITLES_ENABLED':
            case 'SYSTEM_CMDS_ENABLED':
            case 'DEBUG':
                return self::isBoolean($value);
                break;
        }
    }

}
