<?php

namespace slelorrain\Aushowmatic\Core;

use slelorrain\Aushowmatic\Components\Link;
use slelorrain\Aushowmatic\Components\Template;
use slelorrain\Aushowmatic\Components\Upload;

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
        'delete' => '--torrent all --remove-and-delete',
    );

    public static function call($action, $torrent = null)
    {
        if (array_key_exists($action, Transmission::$options)) {
            $continue = true;
            $torrentId = null;
            $toCall = Transmission::$options[$action];

            if (!is_null($torrent)) {
                if (is_numeric($torrent)) {
                    $torrentId = $torrent;
                    $toCall = str_replace('all', $torrentId, $toCall);
                } else {
                    $toCall .= ' ' . $torrent;
                }
            }

            $before = 'before' . ucfirst($action);
            if (method_exists(__NAMESPACE__ . '\Transmission', $before)) {
                $continue = call_user_func('self::' . $before, $torrentId);
                usleep(self::SLEEP_TIME);
            }

            if ($continue) {
                $toEcho = System::transmission($toCall);

                $after = 'after' . ucfirst($action);
                if (method_exists(__NAMESPACE__ . '\Transmission', $after)) {
                    usleep(self::SLEEP_TIME);
                    $toEcho = call_user_func('self::' . $after, $toEcho);
                }
            } else {
                $toEcho = 'Error: Execution impossible';
            }
        } else {
            $toEcho = 'Error: Transmission action not found';
        }

        return $toEcho;
    }

    public static function isTurtleActivated()
    {
        $toFind = 'Download speed limit: Unlimited';
        $info = self::call('sessionInfo');
        return !strstr($info, $toFind);
    }

    public static function getDirectory($torrentId = null)
    {
        if ($torrentId != null) {
            $info = self::call('info', $torrentId);

            preg_match('/Name: (.+)/', $info, $matches);
            $folder = $matches[1];
            preg_match('/Location: (.+)/', $info, $matches);
            $location = $matches[1];

            if (!empty($location) && !empty($folder)) {
                return $location . '/' . $folder;
            }
        }

        return false;
    }

    // Before methods

    private static function beforeVerify($torrentId = null)
    {
        if ($torrentId != null) {
            self::call('start', $torrentId);

            if ($_SESSION['last_cmd_status'] == '0') {
                return true;
            }
        }

        return false;
    }

    private static function beforeDelete($torrentId = null)
    {
        $directory = self::getDirectory($torrentId);

        if ($directory != null) {
            return Subtitle::removeSubtitles($directory);
        }

        return false;
    }

    // After methods

    private static function afterListFiles($commandResult)
    {
        $res = Template::get('torrentForm') . PHP_EOL;
        $lines = explode(PHP_EOL, $commandResult);

        foreach ($lines as $line) {
            $columns = explode(' ', preg_replace('/\s+/', ' ', trim($line)));
            // Character '*' is appended when downloaded file is corrupted
            $id = str_replace('*', '', $columns[0]);

            if (is_numeric($id)) {
                $uploadForm = Upload::modal('subtitle', 'uploadSubtitle', $id);
                $stop = Link::action('&#9632;', 'transmission', 'stop|id=' . $id, 'Stop torrent');
                $start = Link::action('&#9658;', 'transmission', 'start|id=' . $id, 'Start torrent');
                $verify = Link::action('&check;', 'transmission', 'verify|id=' . $id, 'Verify torrent');
                $delete = Link::action('&#10007;', 'transmission', 'delete|id=' . $id, 'Delete', 'danger', true);

                $res .= $line . ' ' . $uploadForm . ' ( ' . $stop . ' | ' . $start . ' | ' . $verify . ' ) ' . $delete . PHP_EOL;
            } else {
                $res .= $line . PHP_EOL;
            }
        }

        return $res;
    }

    private static function afterAltSpeedOn()
    {
        return self::call('listFiles');
    }

    private static function afterAltSpeedOff()
    {
        return self::call('listFiles');
    }

    private static function afterStart()
    {
        return self::call('listFiles');
    }

    private static function afterStop()
    {
        return self::call('listFiles');
    }

    private static function afterVerify()
    {
        return self::call('listFiles');
    }

    private static function afterDelete()
    {
        return self::call('listFiles');
    }

}
