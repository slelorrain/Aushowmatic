<?php
class EZTV implements Feed{

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
        if( isset($show) ){
            $dom = new DomDocument();
            @$dom->loadHTMLFile(self::PATH . 'shows/' . $show);
			return $dom;
        }
    }

	// This parsing is crappy but the DOM is crappy too :p
    public static function launchDownloads( $preview = false ){
        $added = array();

        foreach( Utils::getShowList() as $show ){
            if( !empty($show) ){
				
				$could_be_added = array();
                $xml = self::getShowFeed($show);
                $finderEpinfo = new DomXPath($xml);
                // Find lines corresponding to an episode
                $nodes = $finderEpinfo->query("//*[contains(@class, 'epinfo')]");

                foreach( $nodes as $item ){
                	// Date of realesed
					$realeased = $item->parentNode->nextSibling->nextSibling->nextSibling->nextSibling->nodeValue;
					
					if( $realeased != ">1 week" ){
						$episodeName = $item->nodeValue;
						$infoLink = $item->parentNode->previousSibling->previousSibling->firstChild->nextSibling->getAttribute('href');
						$downloadLink = $item->parentNode->nextSibling->nextSibling->firstChild->getAttribute('href');
						
						if( !array_key_exists($infoLink, $could_be_added) ){
							$could_be_added[$infoLink] = $downloadLink;	
						}else{
							// if current download link contains preferred format replace link previously set in array
							if( strpos($downloadLink, PREFERRED_FORMAT) !== false  ){
								$could_be_added[$infoLink] = $downloadLink;
							}
						}
					}
                }
                
                foreach( $could_be_added as $ep ){
                	$tmp = Utils::downloadTorrent($ep, $preview);
                    if( isset($tmp) ) $added[] = $tmp;
               	}
            }
        }
        return $added;
    }

}

?>