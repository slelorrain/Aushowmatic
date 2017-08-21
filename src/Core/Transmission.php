<?php

namespace slelorrain\Aushowmatic\Core;

class Transmission
{

    const SLEEP_TIME = 20000;

    private static $options = array(
        'add' => '--add',
        'listFiles' => '--list',
        'altSpeedOn' => '--alt-speed',
        'altSpeedOff' => '--no-alt-speed',
        'sessionInfo' => '--session-info',
        'start' => '--torrent all --start',
        'stop' => '--torrent all --stop',
        'info' => '--torrent all --info',
        'verify' => '--torrent all --verify',
    );

    public static function call($action, $torrent = null)
    {
        if (array_key_exists($action, Transmission::$options)) {
            $continue = true;
            $torrent_id = null;
            $to_call = Transmission::$options[$action];

            if (!is_null($torrent)) {
                if (is_numeric($torrent)) {
                    $torrent_id = $torrent;
                    $to_call = str_replace('all', $torrent_id, $to_call);
                } else {
                    $to_call .= ' ' . $torrent;
                }
            }

            $before = 'before' . ucfirst($action);
            if (method_exists(__NAMESPACE__ . '\Transmission', $before)) {
                $continue = call_user_func('self::' . $before, $torrent_id);
                usleep(self::SLEEP_TIME);
            }

            if ($continue) {
                $to_echo = System::transmission($to_call);

                $after = 'after' . ucfirst($action);
                if (method_exists(__NAMESPACE__ . '\Transmission', $after)) {
                    usleep(self::SLEEP_TIME);
                    $to_echo = call_user_func('self::' . $after, $to_echo);
                }
            } else {
                $to_echo = 'Execution impossible';
            }
        } else {
            $to_echo = 'Transmission action not found';
        }

        return $to_echo;
    }

    public static function isTurtleActivated()
    {
        $to_find = 'Download speed limit: Unlimited';
        $info = self::call('info');
        return !strstr($info, $to_find);
    }

    // Before methods

    private static function beforeVerify($torrent_id = null){
        if ($torrent_id != null) {
            self::call('start', $torrent_id);

            if ($_SESSION['last_cmd_status'] == '0') {
            	return true;
        	}
        }

        return false;
    }

    // After methods

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
        return self::call('listFiles');
    }

    private static function afterStop(){
        return self::call('listFiles');
    }

    private static function afterVerify(){
        return self::call('listFiles');
    }

}
