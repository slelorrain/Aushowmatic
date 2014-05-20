<?php

class showRSS extends Feed{

    const PATH = "http://showrss.info/";

    public static function getFeedStats(){
        return "n/a";
    }

    public static function getFeedUsageInPercentage(){
        return -1;
    }

    public static function getFeedTimeRemaining(){
        return "n/a";
    }

    public static function getWebsiteLinkToShow( $show_id ){
        return self::PATH . '?cs=browse&show=' . $show_id;
    }

    public static function getShowFeed( $show_id ){
        return self::PATH . 'feeds/' . $show_id . '.rss';
    }

    public static function parsePage( $page, &$could_be_added ){
        $xml = simplexml_load_string($page);
        foreach( $xml->channel->item as $item ){
            if( strtotime($item->pubDate) >= strtotime(Utils::getMinDate()) ){

                $epId = "";
                $epTitle = $item->title;
                $downloadLink = $item->link;

                if( preg_match("(.+\s[0-9]+x[0-9]+)", $epTitle, $n) ){
                    $epId = $n[0];
                }

                if( !array_key_exists($epId, $could_be_added) ){
                    $could_be_added[$epId] = $downloadLink;
                }else{
                    // If current download link contains preferred format replace link previously set in array
                    if( strpos($downloadLink, PREFERRED_FORMAT) !== false ){
                        $could_be_added[$epId] = $downloadLink;
                    }
                }

            }
        }
    }

}

?>