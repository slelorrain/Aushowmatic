<?php
class Transmission {

    public static function add($torrent){
        passthru(TRANSMISSION_CMD.' -a '.$torrent);
    }

    public static function listFiles(){
        return passthru(TRANSMISSION_CMD.' -l');
    }

    public static function start(){
        return passthru(TRANSMISSION_CMD.' --torrent all --start');
    }

    public static function stop(){
        return passthru(TRANSMISSION_CMD.' --torrent all --stop');
    }

    public static function altSpeedOn(){
        return passthru(TRANSMISSION_CMD.' -as');
    }

    public static function altSpeedOff(){
        return passthru(TRANSMISSION_CMD.' -AS');
    }

    public static function info(){
        return passthru(TRANSMISSION_CMD.' -si');
    }
}
?>