<?php
class Dispatcher{

    public static function dispatch(){
        $to_echo = '';

        if( isset($_GET['a']) ){
            if( method_exists('Dispatcher', $_GET['a']) ){
                $to_echo = call_user_func('self::'.$_GET['a']);
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
            $to_echo .= '<a target="_blank" href="' . Utils::getWebsiteLinkToShow($show) . '">' . $show . '</a><br>';
        }
        return $to_echo;
    }

    private static function add_show(){
        if( isset($_POST['name_of_show']) ){
            Utils::addShow($_POST['name_of_show']);
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
            foreach( $links as $link ){
                $to_echo .= Utils::printLink($link) . '<br>';
            }
            $to_echo = 'Links that will be processed:<br>' . $to_echo;
        } else{
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
        System::startXBMC();
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