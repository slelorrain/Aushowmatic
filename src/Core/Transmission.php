<?php

namespace slelorrain\Aushowmatic\Core;

class Transmission
{

    private static $options = array(
        "add" => '--add',
        "listFiles" => '--list',
        "start" => '--torrent all --start',
        "stop" => '--torrent all --stop',
        "altSpeedOn" => '--alt-speed',
        "altSpeedOff" => '--no-alt-speed',
        "info" => '--session-info',
        "verify" => '--torrent all --verify',
    );

    public static function call($action, $torrent_id = null)
    {
        if (array_key_exists($action, Transmission::$options)) {
            $to_call = $_ENV['TRANSMISSION_CMD'] . ' ' . Transmission::$options[$action];
            if (!is_null($torrent_id) && is_numeric($torrent_id)) {
                $to_call = str_replace('all', $torrent_id, $to_call);
            }

            $to_echo = Utils::execCommand($to_call);

            $after = 'after' . ucfirst($action);
            if (method_exists(__NAMESPACE__ . '\Transmission', $after)) {
                $to_echo = call_user_func('self::' . $after, $to_echo);
            }
        } else {
            $to_echo = 'Transmission action not found';
        }

        return $to_echo;
    }

    private static function afterListFiles($command_result) {
        $res = '';
        $lines = explode(PHP_EOL, $command_result);

        foreach ($lines as $line) {
            $columns = explode(' ', preg_replace('/\s+/', ' ', trim($line)));
            // Character '*' is appended when downloaded file is corrupted
            $id = str_replace('*', '', $columns[0]);

            if (is_numeric($id)) {
                $stop = Link::action('&#9632;', 'transmission', 'stop|id=' . $id, 'Stop torrent');
                $start = Link::action('&#9658;', 'transmission', 'start|id=' . $id, 'Start torrent');
                $verify = Link::action('&check;', 'transmission', 'verify|id=' . $id, 'Verify torrent');
                $res .= $line . ' ( ' . $stop . ' | ' . $start . ' | ' . $verify . ' )' . PHP_EOL;
            } else {
                $res .= $line . PHP_EOL;
            }
        }

        return $res;
    }

    private static function afterStart(){
        usleep(10000);
        return self::call('listFiles');
    }

    private static function afterStop(){
        usleep(10000);
        return self::call('listFiles');
    }

    private static function afterVerify(){
        usleep(10000);
        return self::call('listFiles');
    }

}
