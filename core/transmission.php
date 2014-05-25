<?php

class Transmission{

    public static function call( $function ){
        if( method_exists('Transmission', $function) ){
            ob_start();
            call_user_func('self::' . $function);
            $to_echo = ob_get_contents();
            ob_end_clean();
        }else{
            $to_echo = 'Transmission action not found';
        }
        return $to_echo;
    }

    private static function add( $torrent ){
        passthru(TRANSMISSION_CMD . ' -a ' . $torrent);
    }

    private static function listFiles(){
        passthru(TRANSMISSION_CMD . ' -l');
    }

    private static function start(){
        passthru(TRANSMISSION_CMD . ' --torrent all --start');
    }

    private static function stop(){
        passthru(TRANSMISSION_CMD . ' --torrent all --stop');
    }

    private static function altSpeedOn(){
        passthru(TRANSMISSION_CMD . ' -as');
    }

    private static function altSpeedOff(){
        passthru(TRANSMISSION_CMD . ' -AS');
    }

    private static function info(){
        passthru(TRANSMISSION_CMD . ' -si');
    }

}

?>