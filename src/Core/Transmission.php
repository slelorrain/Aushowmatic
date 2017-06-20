<?php

namespace slelorrain\Aushowmatic\Core;

class Transmission
{

    private static $options = array(
        "add" => '-a',
        "listFiles" => '-l',
        "start" => '--torrent all --start',
        "stop" => '--torrent all --stop',
        "altSpeedOn" => '-as',
        "altSpeedOff" => '-AS',
        "info" => '-si',
    );

    public static function call($action, $param = null)
    {
        if (array_key_exists($action, Transmission::$options)) {
            $to_call = $_ENV['TRANSMISSION_CMD'] . ' ' . Transmission::$options[$action];
            if (!is_null($param)) {
                $to_call .= ' ' . $param;
            }
            $to_echo = Utils::execCommand($to_call);
        } else {
            $to_echo = 'Transmission action not found';
        }
        return $to_echo;
    }

}
