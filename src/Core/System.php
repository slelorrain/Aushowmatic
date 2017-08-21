<?php

namespace slelorrain\Aushowmatic\Core;

define("PS_AUX_CMD", "ps aux | grep '" . $_ENV["KODI_CMD"] . "'");

class System
{
    private static function isKodiStarted()
    {
        exec(PS_AUX_CMD, $ps_aux);
        if (isset($ps_aux[0]) && strstr($ps_aux[0], $_ENV['KODI_CMD']) && !strstr($ps_aux[0], 'grep')) {
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

    public static function getStatusOfKodi()
    {
        if (self::isKodiStarted()) {
            return 'Kodi is started';
        } else {
            return 'Kodi is not started';
        }
    }

    public static function killKodi()
    {
        if (self::isKodiStarted()) {
            exec(PS_AUX_CMD, $ps_aux);
            $ps_aux = array_filter(explode(' ', $ps_aux[0]));

            if (array_shift($ps_aux) == 'root') {
                $pid = array_shift($ps_aux);
                self::executeInBackground('sudo kill ' . $pid);
                return 'Kodi is killed';
            }
        } else {
            return 'Kodi is not started';
        }
    }

    public static function shutdown()
    {
        self::executeInBackground('sudo poweroff');
    }

    public static function reboot()
    {
        self::executeInBackground('sudo reboot');
    }

    public static function diskUsage()
    {
        return self::execute('df -h');
    }

    public static function transmission($options)
    {
        return self::execute($_ENV['TRANSMISSION_CMD'] . ' ' . $options);
    }

    private static function execute($command)
    {
        ob_start();
        passthru(escapeshellcmd($command), $_SESSION['last_cmd_status']);
        $to_echo = ob_get_contents();
        ob_end_clean();
        return $to_echo;
    }

    private static function executeInBackground($command)
    {
        pclose(popen($command . ' &', 'r'));
    }

}
