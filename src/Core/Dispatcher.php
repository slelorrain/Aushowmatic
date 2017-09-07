<?php

namespace slelorrain\Aushowmatic\Core;

class Dispatcher
{

    public static function dispatch()
    {
        session_start();
        $_SESSION['start'] = microtime(true);

        if (isset($_GET['action'])) {
            if (method_exists(__NAMESPACE__ . '\Dispatcher', $_GET['action'])) {
                if (!isset($_GET['parameter'])) {
                    $to_echo = call_user_func('self::' . $_GET['action']);
                } else {
                    $to_echo = call_user_func('self::' . $_GET['action'], $_GET['parameter']);
                }
            } else {
                $to_echo = 'Action not found';
            }

            $_SESSION['result'] = $to_echo;
            Utils::setGeneratedIn();

            // Avoid unwanted call of previous action
            header('Location: ./');
        } else {
            if (!isset($_SESSION['generated_in'])) {
                Utils::setGeneratedIn();
            }
        }
    }

    /* Functions that can be called by dispatcher */

    private static function done()
    {
        $to_echo = '';
        foreach (FeedInfo::getDoneList() as $done) {
            $to_echo .= Utils::printLink($done);
            $to_echo .= ' ( ' . Link::action('&#9660;', 'redownload', bin2hex($done), 'Redownload the link', '', true);
            $to_echo .= ' | ' . Link::action('&#10007;', 'removeUrlDone', bin2hex($done), 'Delete', '', true) . ' )'. PHP_EOL;
        }
        return $to_echo;
    }

    private static function shows()
    {
        $to_echo = '';
        foreach (FeedInfo::getShowList() as $label => $show) {
            $to_echo .= Link::out($label . ' (' . $show . ')', Utils::getWebsiteLinkToShow($show));
            $to_echo .= ' ( ' . Link::action('?', 'preview', bin2hex($show), 'Preview the show', '', false);
            $to_echo .= ' | ' . Link::action('&#9660;', 'launch', bin2hex($show), 'Download the show', '', true);
            $to_echo .= ' | ' . Link::action('&#10007;', 'removeShow', bin2hex($show), 'Delete', '', true) . ' )'. PHP_EOL;
        }
        return $to_echo;
    }

    private static function addShow()
    {
        if (isset($_POST['show_name'])) {
            $name = $_POST['show_name'];
            $label = $_POST['show_label'];

            $availableShows = Feed::getAvailableShows();
            if ($availableShows && !empty($availableShows)) {
                $label = $availableShows[$name];
            }

            FeedInfo::addShow($name, $label);
        }
        return self::shows();
    }

    private static function removeShow($name)
    {
        if (isset($name) && !empty($name)) {
            FeedInfo::removeShow(hex2bin($name));
        }
        return self::shows();
    }

    private static function removeUrlDone($url)
    {
        if (isset($url) && !empty($url)) {
            FeedInfo::removeUrlDone(hex2bin($url));
        }
        return self::done();
    }

    private static function redownload($torrent)
    {
        $link = hex2bin($torrent);
        self::removeUrlDone($torrent);
        return self::download($link);
    }

    private static function addTorrent()
    {
        return self::download($_POST['torrent_link']);
    }

    private static function download($torrent)
    {
        $to_echo = 'Error: Invalid or corrupt torrent file';

        if (isset($torrent)) {
            $url_added = Utils::downloadTorrent($torrent, false);

            if (!is_null($url_added) && $_SESSION['last_cmd_status'] == "0") {
                $to_echo = 'Torrent successfully added' . PHP_EOL . PHP_EOL . self::done();
            }
        }
        return $to_echo;
    }

    private static function preview($name = null)
    {
        $links = Utils::launchDownloads(true, hex2bin($name));
        return Utils::printLinks($links);
    }

    private static function launch($name = null)
    {
        $links = Utils::launchDownloads(false, hex2bin($name));
        return Utils::printLinks($links);
    }

    private static function updateDate()
    {
        FeedInfo::updateDate(time());
    }

    private static function emptyDone()
    {
        FeedInfo::emptyDoneList();
    }

    private static function startKodi()
    {
        return System::startKodi();
    }

    private static function statusKodi()
    {
        return System::getStatusOfKodi();
    }

    private static function killKodi()
    {
        return System::killKodi();
    }

    private static function reboot()
    {
        System::reboot();
    }

    private static function shutdown()
    {
        System::shutdown();
    }

    private static function diskUsage()
    {
        return System::diskUsage();
    }

    private static function transmission($function)
    {
        $torrent_id = null;
        $exploded = explode('|id=', $function);

        if (!is_null($exploded[1]) && is_numeric($exploded[1])) {
            $function = $exploded[0];
            $torrent_id = $exploded[1];
        }

        return Transmission::call($function, $torrent_id);
    }

    private static function subtitles()
    {
        return Subtitle::download();
    }

}
