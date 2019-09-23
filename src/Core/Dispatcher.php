<?php

namespace slelorrain\Aushowmatic\Core;

use slelorrain\Aushowmatic\Config;
use slelorrain\Aushowmatic\Components\Link;
use slelorrain\Aushowmatic\Components\Template;

class Dispatcher
{

    public static function dispatch()
    {
        if (!isset($_GET['action']) && !isset($_SESSION['result'])) {
            $_GET['action'] = 'transmission';
            $_GET['parameter'] = 'listFiles';
        }

        if (isset($_GET['action']) && $_GET['action'] != __FUNCTION__) {
            $_SESSION['start'] = microtime(true);

            if (method_exists(get_class(), $_GET['action'])) {
                if (!isset($_GET['parameter'])) {
                    $toEcho = call_user_func('self::' . $_GET['action']);
                } else {
                    $toEcho = call_user_func('self::' . $_GET['action'], $_GET['parameter']);
                }
            } else {
                $toEcho = 'Action not found';
            }

            $_SESSION['result'] = $toEcho;
            $_SESSION['generated_in'] = round(microtime(true) - $_SESSION['start'], 4);

            // Avoid unwanted call of previous action
            header('Location: ./');
        }
    }

    /* Functions that can be called by dispatcher */

    private static function done()
    {
        $toEcho = '';
        foreach (FeedInfo::getDoneList() as $done) {
            $toEcho .= Utils::printLink($done);
            $toEcho .= ' ( ' . Link::action('&#9660;', 'redownload', bin2hex($done), 'Redownload the link', '', true);
            $toEcho .= ' | ' . Link::action('&#10007;', 'removeUrlDone', bin2hex($done), 'Delete', '', true) . ' )' . PHP_EOL;
        }
        return $toEcho;
    }

    private static function shows()
    {
        $toEcho = Template::get('showsForm') . PHP_EOL;
        foreach (FeedInfo::getShowList() as $label => $show) {
            $toEcho .= Link::out($label . ' (' . $show . ')', Utils::getWebsiteLinkToShow($show));
            $toEcho .= ' ( ' . Link::action('?', 'preview', bin2hex($show), 'Preview the show', '', false);
            $toEcho .= ' | ' . Link::action('&#9660;', 'launch', bin2hex($show), 'Download the show', '', true);
            $toEcho .= ' | ' . Link::action('&#10007;', 'removeShow', bin2hex($show), 'Delete', '', true) . ' )' . PHP_EOL;
        }
        return $toEcho;
    }

    private static function addShow()
    {
        if (isset($_POST['show_name'])) {
            $name = $_POST['show_name'];

            if (isset($_POST['show_label'])) {
                $label = $_POST['show_label'];
            } else {
                $availableShows = Feed::getAvailableShows();
                if ($availableShows && !empty($availableShows)) {
                    $label = $availableShows[$name];
                }
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
        $toEcho = 'Error: Invalid or corrupt torrent file';

        if (isset($torrent)) {
            $urlAdded = Utils::downloadTorrent($torrent, false);

            if (!is_null($urlAdded) && $_SESSION['last_cmd_status'] == "0") {
                $toEcho = 'Torrent successfully added' . PHP_EOL . PHP_EOL . self::done();
            }
        }
        return $toEcho;
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
        $torrentId = null;
        $exploded = explode('|id=', $function);

        if (isset($exploded[1]) && is_numeric($exploded[1])) {
            $function = $exploded[0];
            $torrentId = $exploded[1];
        }

        return Transmission::call($function, $torrentId);
    }

    private static function subtitles($torrentId = null)
    {
        $directory = null;
        if ($torrentId != null) {
            $directory = Transmission::getDirectory($torrentId);
        }
        return $_ENV['SUBTITLES_CLASS']::download($directory);
    }

    private static function uploadSubtitle($torrentId = null)
    {
        if ($torrentId != null) {
            $directory = Transmission::getDirectory($torrentId);

            if ($directory != null) {
                $directory = str_replace('[', '\[', $directory);
                $videos = glob($directory . '/*.{' . $_ENV['SUBTITLES_SEARCH_EXTENSIONS'] . '}', GLOB_BRACE);

                if (!empty($videos)) {
                    $toEcho = Subtitle::uploadSubtitle($videos[0], $_FILES['subtitle']);
                } else {
                    $toEcho = 'Error: No video found';
                }
            } else {
                $toEcho = 'Error: Unable to get the directory';
            }
        } else {
            $toEcho = 'Error: A torrent ID must be provided';
        }

        return $toEcho . PHP_EOL . PHP_EOL . self::transmission('listFiles');
    }

    private static function configuration()
    {
        return Template::get('configForm');
    }

    private static function updateEnv()
    {
        Config::createBackup();

        if (Config::updateEnv($_POST['name'], $_POST['value'])) {
            Config::deleteBackup();
            $toEcho = 'Config updated';
        } else {
            Config::restoreBackup();
            $toEcho = 'Error: A problem occured';
        }
        return $toEcho . PHP_EOL . PHP_EOL . self::configuration();
    }
}
