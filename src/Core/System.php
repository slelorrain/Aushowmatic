<?php

namespace slelorrain\Aushowmatic\Core;

use slelorrain\Aushowmatic\Config;

define("PS_AUX_CMD", "ps aux | grep '" . $_ENV["KODI_CMD"] . "'");

class System
{
    public static function isKodiStarted()
    {
        exec(PS_AUX_CMD, $psAux);
        if (isset($psAux[0]) && strstr($psAux[0], $_ENV['KODI_CMD']) && !strstr($psAux[0], 'grep')) {
            return true;
        } else {
            return false;
        }
    }

    public static function startKodi()
    {
        if (!self::isKodiStarted()) {
            self::executeInBackground('clear ; ' . $_ENV['KODI_CMD']);
            return 'Done';
        } else {
            return 'Kodi is already started';
        }
    }

    public static function shutdown()
    {
        if (Config::isEnabled('SYSTEM_CMDS_ENABLED')) {
            self::executeInBackground('sudo poweroff');
            return 'Done';
        } else {
            return 'System commands are disabled';
        }
    }

    public static function reboot()
    {
        if (Config::isEnabled('SYSTEM_CMDS_ENABLED')) {
            self::executeInBackground('sudo reboot');
            return 'Done';
        } else {
            return 'System commands are disabled';
        }
    }

    public static function diskUsage()
    {
        return self::execute('df -h');
    }

    public static function unzip($file, $destination)
    {
        return self::execute('unzip ' . $file . ' -d' . $destination);
    }

    public static function transmission($options)
    {
        return self::execute($_ENV['TRANSMISSION_CMD'] . ' ' . $options);
    }

    private static function execute($command)
    {
        ob_start();
        passthru(escapeshellcmd($command), $_SESSION['last_cmd_status']);
        $toEcho = ob_get_contents();
        ob_end_clean();
        return $toEcho;
    }

    private static function executeInBackground($command)
    {
        pclose(popen($command . ' &', 'r'));
    }

}
