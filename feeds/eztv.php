<?php

class EZTV extends Feed{

    const PATH = "http://eztv.it/";

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
        return self::PATH . 'shows/' . $show_id;
    }

    public static function getShowFeed( $show ){
        return self::PATH . 'shows/' . $show;
    }

    // This parsing is crappy but the DOM is crappy too :p
    public static function parsePage( $page, &$could_be_added, $use_min_date = true ){

        $dom = new DomDocument();
        // Warnings are muted because DOM retrieved is not valid
        @$dom->loadHTML($page);
        $finderEpinfo = new DomXPath($dom);

        // Find lines corresponding to an episode
        $nodes = $finderEpinfo->query("//*[contains(@class, 'epinfo')]");

        foreach( $nodes as $item ){
            // Date of released
            $released = $item->parentNode->nextSibling->nextSibling->nextSibling->nextSibling->nodeValue;

            // At the moment, for EZTV we only handle episodes with a release date below a week
            if( !$use_min_date || $released != ">1 week" ){

                $episodeName = $item->nodeValue;

                $infoLink = $item->parentNode->previousSibling->previousSibling->firstChild->nextSibling;
                if( $infoLink->nodeType == XML_ELEMENT_NODE && $infoLink->hasAttribute('href') ){
                    $infoLink = $infoLink->getAttribute('href');
                }else{
                    $infoLink = $episodeName;
                }

                $downloadLink = $item->parentNode->nextSibling->nextSibling->firstChild->getAttribute('href');

                if( !array_key_exists($infoLink, $could_be_added) ){
                    $could_be_added[$infoLink] = $downloadLink;
                }else{
                    // If current download link contains preferred format replace link previously set in array
                    if( strpos($downloadLink, PREFERRED_FORMAT) !== false ){
                        $could_be_added[$infoLink] = $downloadLink;
                    }
                }
            }
        }
    }

}

?>