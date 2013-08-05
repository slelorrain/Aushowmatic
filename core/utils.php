<?php
class Utils{

    public static function getShowList(){
        $info = self::getFeedInfo();
        return $info->shows;
    }

    public static function getDoneList(){
        $info = self::getFeedInfo();
        return $info->done;
    }

    public static function getMinDate(){
        $info = self::getFeedInfo();
        return $info->min_date;
    }

    public static function addShow( $name ){
    	$info = self::getFeedInfo();
    	$info->shows[] = trim($name);
    	self::setFeedInfo($info);
    }

    public static function removeShow( $name ){
    	$info = self::getFeedInfo();
    	$pos = array_search($name, $info->shows);
    	if( $pos !== false ) unset($info->shows[$pos]);
    	self::setFeedInfo($info);
    }

  	public static function addUrlDone( $url ){
    	$info = self::getFeedInfo(true);
    	array_unshift($info['done'], $url);
    	self::setFeedInfo($info);
    }

    public static function updateDate(){
    	$info = self::getFeedInfo();
    	$info->date_min = date("Y-m-d H:i:s");
    	self::setFeedInfo($info);
    }

    public static function emptyDoneList(){
        $info = self::getFeedInfo();
    	$info->done = array();
    	self::setFeedInfo($info);
    }

    private static function getFeedInfo( $assoc = false ){
    	return json_decode(file_get_contents(FEED_INFO), $assoc);
    }

    private static function setFeedInfo( $info ){
    	file_put_contents(FEED_INFO, json_encode($info));
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
                self::addUrlDone($url);
            }
            $added_now = $url;
        }

        return $added_now;
    }

}
?>