<?php

class DailyTV implements Feed{

    const PATH = "http://www.dailytvtorrents.org/";

    public static function getFeedStats(){
        if( !isset($GLOBALS['rss_stats']) ){
            $ctx = stream_context_create(array('http' => array('timeout' => 3)));
            $GLOBALS['rss_stats'] = @file_get_contents(self::PATH . 'rss/stats', 0, $ctx);
        }
        return $GLOBALS['rss_stats'];
    }

    public static function getFeedUsageInPercentage(){
        $stats = self::getFeedStats();
        $percentage = 0;
        preg_match('/Your bandwidth usage today \(percentage\): (.+)%/', $stats, $r);
        if( isset($r[1]) ) $percentage = $r[1];
        return $percentage;
    }

    public static function getFeedTimeRemaining(){
        $stats = self::getFeedStats();
        $remaining = '?';
        preg_match('/Time remaining from this day \(hours\): (.+)<li>Total number of http/', $stats, $r);
        if( isset($r[1]) ) $remaining = $r[1];
        return $remaining;
    }

    public static function getWebsiteLinkToShow( $show_id ){
        return self::PATH . 'show/' . $show_id;
    }

    public static function getShowFeed( $show ){
        if( isset($show) ){
            $data = file_get_contents(self::PATH . 'rss/show/' . $show . '?onlynew=yes&norar=yes&minage=8&prefer=' . PREFERRED_FORMAT);
            $xml = simplexml_load_string($data);
            return $xml;
        }
    }

    public static function launchDownloads( $preview = false ){
        $added = array();

        foreach( Utils::getShowList() as $show ){
            if( !empty($show) ){
                $xml = self::getShowFeed($show);
                foreach( $xml->channel->item as $item ){
                    if( strtotime($item->pubDate) >= strtotime(Utils::getMinDate()) ){
                        $link = $item->enclosure->attributes()->url;
                        $tmp = Utils::downloadTorrent($link, $preview);
                        if( isset($tmp) ) $added[] = $tmp;
                    }
                }
            }
        }
        return $added;
    }

}

?>