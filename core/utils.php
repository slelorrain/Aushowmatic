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

    public static function addShow( $name, $label = '' ){
        $info = self::getFeedInfo(true);
        $name = trim($name);
        if( !empty($name) ){
            $label = trim($label);
            if( !empty($label) && !isset($info['shows'][$label]) ){
                $info['shows'][$label] = $name;
            }else{
                $info['shows'][] = $name;
            }
            self::setFeedInfo($info);
        }
    }

    public static function removeShow( $name ){
        $info = self::getFeedInfo(true);
        $pos = array_search($name, $info['shows']);
        if( $pos !== false ){
            unset($info['shows'][$pos]);
            self::setFeedInfo($info);
        }
    }

    public static function addUrlDone( $url ){
        $info = self::getFeedInfo(true);
        array_unshift($info['done'], (string) $url);
        self::setFeedInfo($info);
    }

    public static function updateDate(){
        $info = self::getFeedInfo();
        $info->min_date = date("Y-m-d H:i:s");
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
        if( is_writable(FEED_INFO) ){
            file_put_contents(FEED_INFO, json_encode($info));
        }
    }

    public static function printLink( $link, $alt = null ){
        if( $link ){
            if( strpos($link, 'magnet') !== false ){
                // Magnet link
                $exploded = explode("&dn=", $link);
                $exploded = explode("&", $exploded[1]);
                $exploded = $exploded[0];
            }else{
                // Normal link
                $exploded = explode("/", $link);
                $exploded = $exploded[count($exploded) - 1];
                $exploded = explode(".torrent", $exploded);
                $exploded = $exploded[0];
            }
            if( !is_null($alt) && !is_int($alt) ) $exploded = $alt . ' - ' . $exploded;
            return '<a href="' . $link . '">' . $exploded . '</a>';
        }else{
            return '';
        }
    }

    public static function printLinks( $links ){
        if( count($links) ){
            $to_echo = '';
            foreach( $links as $key => $link ){
                $to_echo .= Utils::printLink($link, $key) . '<br>';
            }
            $to_echo = 'Links that will be processed:<br>' . $to_echo;
        }else{
            $to_echo = 'No link will be processed.';
        }
        return $to_echo;
    }

    public static function getWebsiteLinkToShow( $show_id ){
        $feed = constant('FEED_CLASS');
        return $feed::getWebsiteLinkToShow($show_id);
    }

    public static function launchDownloads( $preview = false, $show = null ){
        $feed = constant('FEED_CLASS');
        return $feed::launchDownloads($preview, $show);
    }

    public static function downloadTorrent( $url, $preview ){
        $added = null;
        $url = trim($url);

        if( !empty($url) && !in_array($url, self::getDoneList()) ){
            if( !$preview ){
                Transmission::call('add', $url);
                if( $_SESSION['last_cmd_status'] == "0" ){
                    self::addUrlDone($url);
                    $added = $url;
                }
            }else{
                $added = $url;
            }
        }

        return $added;
    }

    public static function setGeneratedIn(){
        $_SESSION['generated_in'] = round(microtime(true) - $_SESSION['start'], 4);
    }

    public static function execCommand( $command ){
        ob_start();
        passthru(escapeshellcmd($command), $_SESSION['last_cmd_status']);
        $to_echo = ob_get_contents();
        ob_end_clean();
        return $to_echo;
    }

}

?>
