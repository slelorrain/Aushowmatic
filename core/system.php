<?php
class System{

    public static function startXBMC(){
        pclose(popen("clear ; sudo /usr/lib/xbmc/xbmc.bin &", "r"));
    }

    public static function getStatusOfXBMC(){
        exec("ps aux | grep 'sudo /usr/lib/xbmc/xbmc.bin'", $ps_aux);
        return $ps_aux[0];
    }

    public static function killXBMC(){
        exec("ps aux | grep 'sudo /usr/lib/xbmc/xbmc.bin'", $ps_aux);
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

}
?>