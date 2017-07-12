<?php

namespace slelorrain\Aushowmatic\Core;

class Dispatcher
{

    public static function dispatch()
    {
        session_start();
        $_SESSION['start'] = microtime(true);

        if (isset($_GET['a'])) {
            if (method_exists('slelorrain\Aushowmatic\Core\Dispatcher', $_GET['a'])) {
                if (!isset($_GET['param'])) {
                    $to_echo = call_user_func('self::' . $_GET['a']);
                } else {
                    $to_echo = call_user_func('self::' . $_GET['a'], $_GET['param']);
                }
            } else {
                $to_echo = 'Action not Found';
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
            $to_echo .= ' ( <a title="Redownload the link" onclick="return confirm(\'Are you sure?\')" href="./?a=redownload&param=' . bin2hex($done) . '">&#9660;</a>';
            $to_echo .= ' | <a title="Delete" onclick="return confirm(\'Are you sure?\')" href="./?a=removeUrlDone&param=' . bin2hex($done) . '">&#10007;</a> )<br>';
        }
        return $to_echo;
    }

    private static function shows()
    {
        $to_echo = '';
        foreach (FeedInfo::getShowList() as $label => $show) {
            $to_echo .= '<a target="_blank" href="' . Utils::getWebsiteLinkToShow($show) . '">' . $label . ' (' . $show . ')</a> ';
            $to_echo .= '( <a title="Preview the show" href="./?a=preview&param=' . bin2hex($show) . '">?</a>';
            $to_echo .= ' | <a title="Download the show" onclick="return confirm(\'Are you sure?\')" href="./?a=launch&param=' . bin2hex($show) . '">&#9660;</a>';
            $to_echo .= ' | <a title="Delete" onclick="return confirm(\'Are you sure?\')" href="./?a=removeShow&param=' . bin2hex($show) . '">&#10007;</a> )<br>';
        }
        return $to_echo;
    }

    private static function addShow()
    {
        if (isset($_POST['show_name'])) {
            FeedInfo::addShow($_POST['show_name'], $_POST['show_label']);
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
        $to_echo = 'Error: invalid or corrupt torrent file';

        if (isset($torrent)) {
            $url_added = Utils::downloadTorrent($torrent, false);

            if (!is_null($url_added) && $_SESSION['last_cmd_status'] == "0") {
                $to_echo = 'Torrent successfully added<br>' . self::done();
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
        System::killKodi();
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
        return Transmission::call($function);
    }

}
