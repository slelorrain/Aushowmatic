<?php

namespace slelorrain\Aushowmatic\Core;

class Utils
{

    public static function getVersion()
    {
        $content = file(APP_BASE_PATH . 'VERSION');
        return trim($content[0]);
    }

    public static function printLink($link, $alt = null)
    {
        if ($link) {
            if (strpos($link, 'magnet') !== false) {
                // Magnet link
                $exploded = explode("&dn=", $link);
                $exploded = explode("&", $exploded[1]);
                $exploded = $exploded[0];
            } else {
                // Normal link
                $exploded = explode("/", $link);
                $exploded = $exploded[count($exploded) - 1];
                $exploded = explode(".torrent", $exploded);
                $exploded = $exploded[0];
            }
            if (!is_null($alt) && !is_int($alt)) {
                $exploded = $alt . ' - ' . $exploded;
            }
            return '<a href="' . $link . '">' . $exploded . '</a>';
        } else {
            return '';
        }
    }

    public static function printLinks($links)
    {
        if (count($links)) {
            $to_echo = '';
            foreach ($links as $key => $link) {
                $to_echo .= self::printLink($link, $key) . '<br>';
            }
            $to_echo = 'Links that will be processed:<br>' . $to_echo;
        } else {
            $to_echo = 'No link will be processed.';
        }
        return $to_echo;
    }

    public static function getWebsiteLinkToShow($show_id)
    {
        return $_ENV['FEED_CLASS']::getWebsiteLinkToShow($show_id);
    }

    public static function launchDownloads($preview = false, $show = null)
    {
        return $_ENV['FEED_CLASS']::launchDownloads($preview, $show);
    }

    public static function downloadTorrent($url, $preview)
    {
        $added = null;
        $url = trim($url);

        if (!empty($url) && !in_array($url, FeedInfo::getDoneList())) {
            if (!$preview) {
                Transmission::call('add', $url);
                if ($_SESSION['last_cmd_status'] == "0") {
                    FeedInfo::addUrlDone($url);
                    $added = $url;
                }
            } else {
                $added = $url;
            }
        }

        return $added;
    }

    public static function setGeneratedIn()
    {
        $_SESSION['generated_in'] = round(microtime(true) - $_SESSION['start'], 4);
    }

    public static function execCommand($command)
    {
        ob_start();
        passthru(escapeshellcmd($command), $_SESSION['last_cmd_status']);
        $to_echo = ob_get_contents();
        ob_end_clean();
        return $to_echo;
    }

}
