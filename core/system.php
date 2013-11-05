<?php
class System{

	const PS_AUX_CMD = "ps aux | grep 'sudo /usr/lib/xbmc/xbmc.bin'";

	private static function isXBMCStarted(){
        exec(self::PS_AUX_CMD, $ps_aux);
		if( isset($ps_aux[0]) && !strstr($ps_aux[0], self::PS_AUX_CMD) ){
			return true;
		}else{
			return false;
		}
    }

    public static function startXBMC(){
		if( !self::isXBMCStarted() ){
			pclose(popen("clear ; sudo /usr/lib/xbmc/xbmc.bin &", "r"));
			return "Done";
		}else{
			return "XBMC is already started";
		}
    }

    public static function getStatusOfXBMC(){
		if( self::isXBMCStarted() ){
			return "XBMC is started";
		}else{
			return "XBMC is not started";
		}
    }

    public static function killXBMC(){
        exec(self::PS_AUX_CMD, $ps_aux);
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