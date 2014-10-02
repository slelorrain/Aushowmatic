<?php
require_once(dirname(__FILE__) . '/../conf/config.php');
require_once(dirname(__FILE__) . '/utils.php');
require_once(dirname(__FILE__) . '/feed.php');
require_once(dirname(__FILE__) . '/system.php');
require_once(dirname(__FILE__) . '/transmission.php');

define('FEED_INFO', dirname(__FILE__) . '/../files/_' . FEED_CLASS . '.json');

spl_autoload_register(function ( $class ){
    $class = strtolower($class);
    $path = dirname(__FILE__) . '/../feeds/' . $class . '.php';
    if( is_file($path) ) require_once($path);
});

class Dispatcher{

    public static function dispatch(){
        session_start();
        $_SESSION['start'] = microtime(true);

        if( isset($_GET['a']) ){
            if( method_exists('Dispatcher', $_GET['a']) ){
                if( !isset($_GET['param']) ){
                    $to_echo = call_user_func('self::' . $_GET['a']);
                }else{
                    $to_echo = call_user_func('self::' . $_GET['a'], $_GET['param']);
                }
            }else{
                $to_echo = 'Action not Found';
            }

            $_SESSION['result'] = $to_echo;
            Utils::setGeneratedIn();

            // Avoid unwanted call of previous action
            header('Location: ./');
        }else{
            if( !isset($_SESSION['generated_in']) ) Utils::setGeneratedIn();
        }
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
        foreach( Utils::getShowList() as $label => $show ){
            $to_echo .= '<a target="_blank" href="' . Utils::getWebsiteLinkToShow($show) . '">' . $label . ' (' . $show . ')</a> ';
            $to_echo .= '( <a title="Preview the show" href="./?a=preview&param=' . bin2hex($show) . '">?</a>';
            $to_echo .= ' | <a title="Download the show" onclick="return confirm(\'Are you sure?\')" href="./?a=launch&param=' . bin2hex($show) . '">&#9660;</a>';
            $to_echo .= ' | <a title="Delete" onclick="return confirm(\'Are you sure?\')" href="./?a=removeShow&param=' . bin2hex($show) . '">&#10007;</a> )<br>';
        }
        return $to_echo;
    }

    private static function addShow(){
        if( isset($_POST['show_name']) ){
            Utils::addShow($_POST['show_name'], $_POST['show_label']);
        }
        return self::shows();
    }

    private static function removeShow( $name ){
        if( isset($name) && !empty($name) ){
            Utils::removeShow(hex2bin($name));
        }
        return self::shows();
    }

    private static function preview( $name = null ){
        $links = Utils::launchDownloads(true, hex2bin($name));
        return Utils::printLinks($links);
    }

    private static function launch( $name = null ){
        $links = Utils::launchDownloads(false, hex2bin($name));
        return Utils::printLinks($links);
    }

    private static function updateDate(){
        Utils::updateDate();
    }

    private static function emptyDone(){
        Utils::emptyDoneList();
    }

    private static function startXbmc(){
        return System::startXBMC();
    }

    private static function statusXbmc(){
        return System::getStatusOfXBMC();
    }

    private static function killXbmc(){
        System::killXBMC();
    }

    private static function reboot(){
        System::reboot();
    }

    private static function shutdown(){
        System::shutdown();
    }

    private static function transmission( $function ){
        return Transmission::call($function);
    }

}

?>
