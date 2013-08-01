<?php
class Utils{

    public static function getShowList(){
        return self::getList(SL_FILE);
    }

    public static function getDoneList(){
        return self::getList(DL_FILE);
    }

    public static function getDateMin(){
        $date = self::getList(DATE_FILE);
        return $date[0];
    }

    private static function getList( $list_file ){
        $list = file_get_contents($list_file);
        return array_map('trim', explode("\n", $list));
    }

    public static function printLink( $link ){
        if( $link ){
        	if( strpos($link, 'magnet') !== false  ){
        		// Magnet link
	            $exploded = explode("&dn=", $link);
	            $exploded = explode("&", $exploded[1]);
	            $exploded = $exploded[0];
        	} else {
        		// Normal link
	            $exploded = explode("/", $link);
	            $exploded = $exploded[count($exploded) - 1];
        	}
            return '<a href="' . $link . '">' . $exploded . '</a>';
        } else{
            return '';
        }
    }

    public static function getClassForPercentage( $int ){
        $int = (int) $int;
        $class = 'normal';
        if( $int >= 70 ){
            $class = 'warning';
            if( $int >= 90 ){
                $class = 'alert';
            }
        }
        return $class;
    }

    public static function addShow( $name ){
        file_put_contents(SL_FILE, $name . "\n", FILE_APPEND);
    }

    public static function getWebsiteLinkToShow($show_id){
        $feed = constant('FEED_CLASS');
        return $feed::getWebsiteLinkToShow($show_id);
    }

    public static function launchDownloads( $preview = false ){
        $feed = constant('FEED_CLASS');
        return $feed::launchDownloads($preview);
    }

    public static function downloadTorrent( $url, $preview ){
        $added_now = null;

        if( !in_array($url, self::getDoneList()) ){
            if( !$preview ){
                Transmission::add($url);
                file_put_contents(DL_FILE, $url . "\n" . file_get_contents(DL_FILE));
            }
            $added_now = $url;
        }

        return $added_now;
    }

    public static function updateDate(){
        file_put_contents(DATE_FILE, date("Y-m-d H:i:s"));
    }

    public static function emptyDoneList(){
        file_put_contents(DL_FILE, '');
    }

}
?>