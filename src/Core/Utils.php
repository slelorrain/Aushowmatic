<?php

namespace slelorrain\Aushowmatic\Core;

use slelorrain\Aushowmatic\Components\Link;

class Utils
{

    public static function getVersion()
    {
        $content = file(APP_BASE_PATH . 'VERSION');
        return 'v' . trim($content[0]);
    }

    public static function getLastVersion()
    {
        if (!isset($_SESSION['last_version'])) {
            $content = Curl::getPage('https://api.github.com/repos/slelorrain/Aushowmatic/releases/latest', 'Aushowmatic');
            $_SESSION['last_version'] = json_decode($content)->tag_name;
        }
        return $_SESSION['last_version'];
    }

    public static function hasUpdateAvailable()
    {
        return version_compare(self::getLastVersion(), self::getVersion()) > 0;
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
            return Link::out($exploded, $link);
        } else {
            return '';
        }
    }

    public static function printLinks($links)
    {
        if (count($links)) {
            $to_echo = '';
            foreach ($links as $key => $link) {
                $to_echo .= self::printLink($link, $key) . PHP_EOL;
            }
            $to_echo = 'Links that will be processed:' . PHP_EOL . $to_echo;
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

}
