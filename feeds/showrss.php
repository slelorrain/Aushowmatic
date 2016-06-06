<?php

class ShowRSS extends Feed{

    const PATH = "http://showrss.info/";

    public static function getWebsiteLinkToShow( $show_id ){
        return self::PATH . 'browse/' . $show_id;
    }

    public static function getShowFeed( $show_id ){
        return self::PATH . 'show/' . $show_id . '.rss';
    }

    public static function parsePage( $page, &$could_be_added, $use_min_date = true ){
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($page);
        libxml_clear_errors();
        if( $xml ){
            foreach( $xml->channel->item as $item ){
                if( !$use_min_date || strtotime($item->pubDate) >= strtotime(Utils::getMinDate()) ){

                    $epId = "";
                    $epTitle = $item->title;
                    $downloadLink = $item->link;

                    if( preg_match("(.+\s[0-9]+x[0-9]+)", $epTitle, $n) ){
                        // 2x09
                        $epId = $n[0];
                    }else if( preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})/", $epTitle, $n) ){
                        // 2014-10-20
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

}

?>
