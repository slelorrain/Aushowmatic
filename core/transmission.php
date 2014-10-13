<?php

class Transmission{

    public static function call( $function, $param = null ){
        if( method_exists('Transmission', $function) ){
            if( $param != null ){
                $to_call = call_user_func('self::' . $function, $param);
            }else{
                $to_call = call_user_func('self::' . $function);
            }
            $to_echo = Utils::execCommand($to_call);
        }else{
            $to_echo = 'Transmission action not found';
        }
        return $to_echo;
    }

    private static function add( $torrent ){
        return TRANSMISSION_CMD . ' -a ' . $torrent;
    }

    private static function listFiles(){
        return TRANSMISSION_CMD . ' -l';
    }

    private static function start(){
        return TRANSMISSION_CMD . ' --torrent all --start';
    }

    private static function stop(){
        return TRANSMISSION_CMD . ' --torrent all --stop';
    }

    private static function altSpeedOn(){
        return TRANSMISSION_CMD . ' -as';
    }

    private static function altSpeedOff(){
        return TRANSMISSION_CMD . ' -AS';
    }

    private static function info(){
        return TRANSMISSION_CMD . ' -si';
    }

}

?>
