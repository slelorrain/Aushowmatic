<?php

// Currently down
class DailyTV extends Feed{

    const PATH = "http://www.dailytvtorrents.org/";

    public static function getWebsiteLinkToShow( $show_id ){
        return self::PATH . 'show/' . $show_id;
    }

    public static function getShowFeed( $show ){
        return self::PATH . 'rss/show/' . $show . '?onlynew=yes&norar=yes&minage=8&prefer=' . PREFERRED_FORMAT;
    }

    public static function parsePage( $page, &$could_be_added, $use_min_date = true ){
        $xml = simplexml_load_string($page);
        foreach( $xml->channel->item as $item ){
            if( !$use_min_date || strtotime($item->pubDate) >= strtotime(Utils::getMinDate()) ){

                $epTitle = $item->title;
                $downloadLink = $item->link;

                if( !array_key_exists($epTitle, $could_be_added) ){
                    $could_be_added[$epTitle] = $downloadLink;
                }else{
                    // If current download link contains preferred format replace link previously set in array
                    if( strpos($downloadLink, PREFERRED_FORMAT) !== false ){
                        $could_be_added[$epTitle] = $downloadLink;
                    }
                }

            }
        }
    }

}

?>
