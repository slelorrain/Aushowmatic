<?php
require_once(dirname(__FILE__) . '/utils.php');
require_once(dirname(__FILE__) . '/feed.php');
require_once(dirname(__FILE__) . '/system.php');
require_once(dirname(__FILE__) . '/transmission.php');

define('FEED_INFO', dirname(__FILE__) . '/../files/_' . FEED_CLASS . '.json');

function __autoload( $class ){
    $class = strtolower($class);
    $path = dirname(__FILE__) . '/../feeds/' . $class . '.php';
    if( is_file($path) ) require_once($path);
}

class Dispatcher{

    public static function dispatch(){
        $to_echo = '';

        if( isset($_GET['a']) ){
            if( method_exists('Dispatcher', $_GET['a']) ){
                if( !isset($_GET['param']) ){
                    $to_echo = call_user_func('self::' . $_GET['a']);
                }else{
                    $to_echo = call_user_func('self::' . $_GET['a'], $_GET['param']);
                }
            }else{
                $to_echo = '404 - Not Found';
            }
        }

        return $to_echo;
    }

    /* Functions that can be called by dispatcher */

    private static function done(){
        $to_echo = '';
        foreach( Utils::getDoneList() as $done ){
            $to_echo .= Utils::printLink($done) . '<br>';
        }
        return $to_echo;
    }

    private static function shows(){
        $to_echo = '';
        foreach( Utils::getShowList() as $show ){
            $to_echo .= '<a target="_blank" href="' . Utils::getWebsiteLinkToShow($show) . '">' . $show . '</a> ';
            $to_echo .= '(<a title="Delete ' . $show . '" onclick="return confirm(\'Are you sure?\')" href="./?a=remove_show&param=' . bin2hex($show) . '">&#10007;</a>)<br>';
        }
        return $to_echo;
    }

    private static function add_show(){
        if( isset($_POST['name_of_show']) ){
            Utils::addShow($_POST['name_of_show']);
        }
        return self::shows();
    }

    private static function remove_show( $name = '' ){
        if( !empty($name) ){
            Utils::removeShow(hex2bin($name));
        }
        return self::shows();
    }

    private static function preview(){
        return self::launchDownloads(true);
    }

    private static function launch(){
        return self::launchDownloads();
    }

    private static function launchDownloads( $preview = false ){
        $to_echo = '';
        $links = Utils::launchDownloads($preview);
        if( count($links) ){
            foreach( $links as $key => $link ){
                $to_echo .= Utils::printLink($link, $key) . '<br>';
            }
            $to_echo = 'Links that will be processed:<br>' . $to_echo;
        }else{
            $to_echo = 'No link will be processed.';
        }
        return $to_echo;
    }

    private static function update_date(){
        Utils::updateDate();
    }

    private static function empty_done(){
        Utils::emptyDoneList();
    }

    private static function start_xbmc(){
        return System::startXBMC();
    }

    private static function status_xbmc(){
        return System::getStatusOfXBMC();
    }

    private static function kill_xbmc(){
        System::killXBMC();
    }

    private static function reboot(){
        System::reboot();
    }

    private static function shutdown(){
        System::shutdown();
    }

    private static function transmission_start(){
        return Transmission::start();
    }

    private static function transmission_stop(){
        return Transmission::stop();
    }

    private static function transmission_list(){
        return Transmission::listFiles();
    }

    private static function transmission_info(){
        return Transmission::info();
    }

    private static function transmission_turtle_on(){
        return Transmission::altSpeedOn();
    }

    private static function transmission_turtle_off(){
        return Transmission::altSpeedOff();
    }

}

?>