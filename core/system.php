<?php
define("PS_AUX_CMD", "ps aux | grep '" . constant("KODI_CMD") . "'");

class System{

    private static function isKodiStarted(){
        exec(PS_AUX_CMD, $ps_aux);
        if( isset($ps_aux[0]) && strstr($ps_aux[0], KODI_CMD) && !strstr($ps_aux[0], "grep") ){
            return true;
        }else{
            return false;
        }
    }

    public static function startKodi(){
        if( !self::isKodiStarted() ){
            pclose(popen("clear ; " . KODI_CMD . " &", "r"));
            return "Done";
        }else{
            return "Kodi is already started";
        }
    }

    public static function getStatusOfKodi(){
        if( self::isKodiStarted() ){
            return "Kodi is started";
        }else{
            return "Kodi is not started";
        }
    }

    public static function killKodi(){
        exec(PS_AUX_CMD, $ps_aux);
        $ps_aux = array_filter(explode(" ", $ps_aux[0]));
        if( array_shift($ps_aux) == 'root' ){
            $pid = array_shift($ps_aux);
            pclose(popen("sudo kill " . $pid . " &", "r"));
        }
    }

    public static function shutdown(){
        pclose(popen("sudo poweroff &", "r"));
    }

    public static function reboot(){
        pclose(popen("sudo reboot &", "r"));
    }

    public static function diskUsage(){
        return Utils::execCommand("df -h");
    }

    public static function isTurtleActivated(){
        $toFind = 'Download speed limit: Unlimited';
        $info = Transmission::call('info');
        return !strstr($info, $toFind);
    }

}

?>
